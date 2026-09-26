<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use App\Models\SalaryHistory;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * The single place monthly salaries are calculated, shared by the payroll
 * preview, salary generation and reports.
 *
 * Rules:
 * - Basic and allowances are prorated by the calendar days the employee was
 *   employed in the month (joining date / last working date).
 * - Overtime = overtime hours x the configured overtime rate.
 * - Gross = basic + allowances + overtime + bonus + other additions.
 * - Per-day rate = deduction basis (basic, or basic + allowances) / working days.
 * - Absence and unpaid leave are deducted at the per-day rate; the configured
 *   fixed deduction and the month's other deduction are added on top.
 * - Net = gross - total deductions.
 */
class SalaryCalculationService
{
    /**
     * Calculate one employee's salary for a month from a salary configuration
     * and a work record. The result uses salary_history column names, so it
     * can be stored as the salary snapshot as-is.
     *
     * @return array<string, mixed>
     */
    public function calculate(Employee $employee, CarbonInterface $month, SalaryDetail $config, EmployeeWork $work): array
    {
        $monthStart = CarbonImmutable::parse($month)->startOfMonth();
        $monthEnd = $monthStart->endOfMonth()->startOfDay();
        $daysInMonth = $monthStart->daysInMonth;

        $employedFrom = $employee->joining_date->gt($monthStart) ? CarbonImmutable::parse($employee->joining_date) : $monthStart;
        $employedTo = $employee->last_working_date && $employee->last_working_date->lt($monthEnd)
            ? CarbonImmutable::parse($employee->last_working_date)
            : $monthEnd;
        $employedDays = $employedTo->lt($employedFrom) ? 0 : (int) $employedFrom->diffInDays($employedTo) + 1;
        $factor = $employedDays / $daysInMonth;

        $prorate = fn ($amount): float => round((float) $amount * $factor, 2);

        $basicSalary = $prorate($config->basic_salary);
        $allowances = collect(SalaryDetail::ALLOWANCES)->mapWithKeys(fn (string $column) => [$column => $prorate($config->{$column})])->all();
        $totalAllowance = round(array_sum($allowances), 2);

        $overtimeAmount = round((float) $work->overtime_hours * (float) $config->overtime_rate, 2);
        $bonus = round((float) $work->bonus, 2);
        $otherAddition = round((float) $work->other_addition, 2);
        $grossSalary = round($basicSalary + $totalAllowance + $overtimeAmount + $bonus + $otherAddition, 2);

        $deductionBase = $config->deduction_basis === 'gross' ? $basicSalary + $totalAllowance : $basicSalary;
        $workingDays = (float) $work->working_days;
        // Kept unrounded for the deductions so absence on every working day
        // deducts exactly the base, not a rounding cent more.
        $perDayRate = $workingDays > 0 ? $deductionBase / $workingDays : 0.0;

        $absenceDeduction = round($perDayRate * (float) $work->absent_days, 2);
        $unpaidLeaveDeduction = round($perDayRate * (float) $work->unpaid_leave_days, 2);
        $otherDeduction = round((float) $config->other_deduction + (float) $work->other_deduction, 2);
        $totalDeduction = round($absenceDeduction + $unpaidLeaveDeduction + $otherDeduction, 2);

        return [
            'employee_id' => $employee->id,
            'salary_detail_id' => $config->id,
            'emp_work_id' => $work->id,
            'salary_month' => $monthStart->toDateString(),

            'employee_code' => $employee->employee_code,
            'employee_name' => $employee->name,
            'department' => $employee->department,
            'designation' => $employee->designation,

            'days_in_month' => $daysInMonth,
            'employed_days' => $employedDays,
            'working_days' => $workingDays,
            'present_days' => (float) $work->present_days,
            'absent_days' => (float) $work->absent_days,
            'paid_leave_days' => (float) $work->paid_leave_days,
            'unpaid_leave_days' => (float) $work->unpaid_leave_days,
            'overtime_hours' => (float) $work->overtime_hours,
            'overtime_rate' => (float) $config->overtime_rate,

            'basic_salary' => $basicSalary,
            ...$allowances,
            'total_allowance' => $totalAllowance,
            'overtime_amount' => $overtimeAmount,
            'bonus' => $bonus,
            'other_addition' => $otherAddition,
            'gross_salary' => $grossSalary,

            'deduction_basis' => $config->deduction_basis,
            'per_day_rate' => round($perDayRate, 2),
            'absence_deduction' => $absenceDeduction,
            'unpaid_leave_deduction' => $unpaidLeaveDeduction,
            'other_deduction' => $otherDeduction,
            'total_deduction' => $totalDeduction,
            'net_salary' => round($grossSalary - $totalDeduction, 2),
        ];
    }

    /**
     * Calculate the month's salary for every payroll-eligible employee,
     * flagging anyone who cannot be generated (missing configuration or work
     * record, deductions above gross, or a salary already approved/paid).
     *
     * @return Collection<int, array{employee: Employee, existing_salary: ?SalaryHistory, calculation: ?array<string, mixed>, issues: array<int, string>, can_generate: bool}>
     */
    public function previewMonth(CarbonInterface $month): Collection
    {
        $monthStart = CarbonImmutable::parse($month)->startOfMonth();

        $employees = Employee::query()
            ->payrollEligible($monthStart)
            ->with([
                'salaryDetails' => fn ($query) => $query->where('status', true)
                    ->whereDate('effective_date', '<=', $monthStart->endOfMonth()->toDateString())
                    ->orderByDesc('effective_date'),
                'works' => fn ($query) => $query->whereDate('salary_month', $monthStart->toDateString()),
            ])
            ->orderBy('employee_code')
            ->get();

        $existingSalaries = SalaryHistory::query()
            ->live()
            ->whereDate('salary_month', $monthStart->toDateString())
            ->get()
            ->keyBy('employee_id');

        return $employees->map(function (Employee $employee) use ($monthStart, $existingSalaries) {
            $config = $employee->salaryDetails->first();
            $work = $employee->works->first();
            $existingSalary = $existingSalaries->get($employee->id);

            $issues = [];
            if (! $config) {
                $issues[] = 'No salary configuration effective for this month.';
            }
            if (! $work) {
                $issues[] = 'No work record for this month.';
            }

            $calculation = $config && $work ? $this->calculate($employee, $monthStart, $config, $work) : null;

            if ($calculation && $calculation['net_salary'] < 0) {
                $issues[] = 'Total deductions exceed the gross salary.';
            }
            if ($existingSalary?->isLocked()) {
                $issues[] = "Salary already {$existingSalary->status}.";
            }

            return [
                'employee' => $employee,
                'existing_salary' => $existingSalary,
                'calculation' => $calculation,
                'issues' => $issues,
                'can_generate' => $issues === [],
            ];
        })->values();
    }
}
