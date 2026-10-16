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
 * - Basic and allowances are first prorated by the calendar days the employee
 *   was employed in the month (joining date / last working date).
 * - Per-day rate = that basic / the month's working days
 *   (e.g. 12,000 basic, 30 working days = 400 per day).
 * - Basic is paid only for the paid days (present + paid leave):
 *   per-day rate x paid days. Absent and unpaid leave days are simply not paid.
 * - Allowances are added in full. When the salary is configured on gross
 *   (deduction_basis = gross), allowances are paid by the paid days too.
 * - Overtime = overtime hours x the configured overtime rate.
 * - Gross = earned basic + allowances + overtime + bonus + other additions.
 * - The configured fixed deduction and the month's other deduction are
 *   deducted from gross.
 * - Advance salary installments due for the month are deducted last, only as
 *   far as the remaining salary allows; the rest stays outstanding.
 * - Net = gross - total deductions.
 */
class SalaryCalculationService
{
    public function __construct(private readonly EmployeeAdvanceService $advanceService) {}

    /**
     * Calculate one employee's salary for a month from a salary configuration
     * and a work record. The result uses salary_history column names, so it
     * can be stored as the salary snapshot as-is.
     *
     * $advanceDue is the total of the advance installments due this month.
     *
     * @return array<string, mixed>
     */
    public function calculate(Employee $employee, CarbonInterface $month, SalaryDetail $config, EmployeeWork $work, float $advanceDue = 0.0): array
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

        $prorate = fn ($amount): float => (float) $amount * $factor;

        $workingDays = (float) $work->working_days;
        $paidDays = min((float) $work->present_days + (float) $work->paid_leave_days, $workingDays);
        // Share of the month's working days that is paid; amounts are
        // multiplied by it (not by a rounded day rate) so a full month pays
        // exactly the full salary.
        $paidRatio = $workingDays > 0 ? $paidDays / $workingDays : 0.0;

        $monthlyBasic = $prorate($config->basic_salary);
        $perDayRate = $workingDays > 0 ? $monthlyBasic / $workingDays : 0.0;
        $basicSalary = round($monthlyBasic * $paidRatio, 2);

        $allowanceRatio = $config->deduction_basis === 'gross' ? $paidRatio : 1.0;
        $allowances = collect(SalaryDetail::ALLOWANCES)
            ->mapWithKeys(fn (string $column) => [$column => round($prorate($config->{$column}) * $allowanceRatio, 2)])
            ->all();
        $totalAllowance = round(array_sum($allowances), 2);

        $overtimeAmount = round((float) $work->overtime_hours * (float) $config->overtime_rate, 2);
        $bonus = round((float) $work->bonus, 2);
        $otherAddition = round((float) $work->other_addition, 2);
        $grossSalary = round($basicSalary + $totalAllowance + $overtimeAmount + $bonus + $otherAddition, 2);

        // Unpaid days are already left out of the earned basic, so nothing
        // more is deducted for absence or unpaid leave.
        $absenceDeduction = 0.0;
        $unpaidLeaveDeduction = 0.0;
        $otherDeduction = round((float) $config->other_deduction + (float) $work->other_deduction, 2);
        $deductionBeforeAdvance = $otherDeduction;
        $advanceDeduction = round(min($advanceDue, max(0, $grossSalary - $deductionBeforeAdvance)), 2);
        $totalDeduction = round($deductionBeforeAdvance + $advanceDeduction, 2);

        return [
            'employee_id' => $employee->id,
            'salary_detail_id' => $config->id,
            'emp_work_id' => $work->id,
            'salary_month' => $monthStart->toDateString(),

            'employee_code' => $employee->employee_code,
            'employee_name' => $employee->name,
            'department' => $employee->department?->name,
            'designation' => $employee->designation?->name,

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
            'advance_deduction' => $advanceDeduction,
            'total_deduction' => $totalDeduction,
            'net_salary' => round($grossSalary - $totalDeduction, 2),
        ];
    }

    /**
     * Calculate the month's salary for every payroll-eligible employee,
     * flagging anyone who cannot be generated (missing configuration or work
     * record, deductions above gross, or a salary already approved/paid).
     * `$employeeIds` limits the calculation to those employees.
     *
     * @param  array<int, int>|null  $employeeIds
     * @return Collection<int, array{employee: Employee, existing_salary: ?SalaryHistory, calculation: ?array<string, mixed>, advance_allocations: array<int, array{employee_advance_id: int, amount: float}>, issues: array<int, string>, can_generate: bool}>
     */
    public function previewMonth(CarbonInterface $month, ?array $employeeIds = null): Collection
    {
        $monthStart = CarbonImmutable::parse($month)->startOfMonth();

        $employees = Employee::query()
            ->payrollEligible($monthStart)
            ->when($employeeIds !== null, fn ($query) => $query->whereIn('id', $employeeIds))
            ->with([
                'department',
                'designation',
                'salaryDetails' => fn ($query) => $query->where('status', true)
                    ->whereDate('effective_date', '<=', $monthStart->endOfMonth()->toDateString())
                    ->orderByDesc('effective_date'),
                'works' => fn ($query) => $query->whereDate('salary_month', $monthStart->toDateString()),
                'advances' => fn ($query) => $this->advanceService->constrainDueInMonth($query, $monthStart),
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

            $installments = $this->advanceService->dueInstallments($employee->advances);
            $calculation = $config && $work
                ? $this->calculate($employee, $monthStart, $config, $work, array_sum(array_column($installments, 'amount')))
                : null;

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
                'advance_allocations' => $calculation ? $this->advanceService->allocate($installments, $calculation['advance_deduction']) : [],
                'issues' => $issues,
                'can_generate' => $issues === [],
            ];
        })->values();
    }
}
