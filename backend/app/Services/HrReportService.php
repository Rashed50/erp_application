<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryDetail;
use App\Models\SalaryHistory;
use Illuminate\Support\Collection;

class HrReportService
{
    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $month = now()->startOfMonth();
        $currentMonth = SalaryHistory::query()->live()->whereDate('salary_month', $month->toDateString());
        $eligibleCount = Employee::query()->payrollEligible($month)->count();
        $generatedCount = (clone $currentMonth)->count();

        return [
            'month' => $month->format('Y-m'),
            'total_employees' => Employee::query()->count(),
            'active_employees' => Employee::query()->where('status', 'Active')->count(),
            'inactive_employees' => Employee::query()->where('status', '!=', 'Active')->count(),
            'payroll_employees' => $eligibleCount,
            'current_month_salary' => round((float) (clone $currentMonth)->sum('net_salary'), 2),
            'generated_salaries' => $generatedCount,
            'pending_salaries' => max(0, $eligibleCount - $generatedCount),
            'status_counts' => (clone $currentMonth)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'year' => (int) $month->format('Y'),
            'total_payroll_amount' => round((float) SalaryHistory::query()
                ->live()
                ->whereYear('salary_month', $month->year)
                ->sum('net_salary'), 2),
        ];
    }

    /**
     * Payroll totals per department (the employee's current one) for a month.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function departmentWise(string $month, ?string $status = null): Collection
    {
        return SalaryHistory::query()
            ->live()
            ->forMonth($month)
            ->when($status, fn ($query) => $query->where('salary_history.status', $status))
            ->join('employee_info', 'employee_info.id', '=', 'salary_history.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employee_info.department_id')
            ->selectRaw('departments.name as department, COUNT(*) as employees, SUM(basic_salary) as basic_salary, SUM(total_allowance) as total_allowance, SUM(overtime_amount) as overtime_amount, SUM(gross_salary) as gross_salary, SUM(total_deduction) as total_deduction, SUM(net_salary) as net_salary')
            ->groupBy('departments.name')
            ->orderBy('departments.name')
            ->get()
            ->map(fn ($row) => [
                'department' => $row->department ?: 'Unassigned',
                'employees' => (int) $row->employees,
                'basic_salary' => round((float) $row->basic_salary, 2),
                'total_allowance' => round((float) $row->total_allowance, 2),
                'overtime_amount' => round((float) $row->overtime_amount, 2),
                'gross_salary' => round((float) $row->gross_salary, 2),
                'total_deduction' => round((float) $row->total_deduction, 2),
                'net_salary' => round((float) $row->net_salary, 2),
            ]);
    }

    /**
     * Month-by-month totals for a year, split into paid and unpaid.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function salarySummary(int $year, ?string $department = null): Collection
    {
        return SalaryHistory::query()
            ->live()
            ->whereYear('salary_month', $year)
            ->when($department, fn ($query) => $query->inDepartment($department))
            ->select(['id', 'salary_month', 'status', 'gross_salary', 'total_deduction', 'net_salary'])
            ->withPaidAmount()
            ->get()
            ->groupBy(fn (SalaryHistory $salary) => $salary->salary_month->format('Y-m'))
            ->sortKeys()
            ->map(fn (Collection $salaries, string $month) => [
                'month' => $month,
                'employees' => $salaries->count(),
                'gross_salary' => round((float) $salaries->sum('gross_salary'), 2),
                'total_deduction' => round((float) $salaries->sum('total_deduction'), 2),
                'net_salary' => round((float) $salaries->sum('net_salary'), 2),
                'paid_amount' => round((float) $salaries->sum('paid_amount'), 2),
                'unpaid_amount' => round((float) $salaries->sum('net_salary') - (float) $salaries->sum('paid_amount'), 2),
            ])
            ->values();
    }

    /**
     * Every salary increment and decrement (an active revision compared with
     * the one before it) effective within the dates, newest first, with the
     * amounts that changed field by field.
     *
     * @return array{rows: array<int, array<string, mixed>>, totals: array{changes: int, increments: int, decrements: int}}
     */
    public function salaryChanges(?string $from = null, ?string $to = null, ?string $department = null, ?int $employeeId = null): array
    {
        // Each employee's full revision list is needed to find the revision
        // before the first one inside the dates.
        $rows = SalaryDetail::query()
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->when($department, fn ($query) => $query->whereHas('employee', fn ($query) => $query->inDepartment($department)))
            ->with(['employee:id,employee_code,name,department_id', 'employee.department:id,name', 'designation:id,name'])
            ->get()
            ->groupBy('employee_id')
            ->flatMap(fn (Collection $revisions) => SalaryDetail::linkRevisions($revisions))
            ->filter(fn (SalaryDetail $revision) => in_array($revision->changeType(), [SalaryDetail::CHANGE_INCREMENT, SalaryDetail::CHANGE_DECREMENT, SalaryDetail::CHANGE_NONE], true))
            ->filter(fn (SalaryDetail $revision) => (! $from || $revision->effective_date->toDateString() >= $from)
                && (! $to || $revision->effective_date->toDateString() <= $to))
            ->sortByDesc(fn (SalaryDetail $revision) => [$revision->effective_date->toDateString(), $revision->id])
            ->map(fn (SalaryDetail $revision) => [
                'id' => $revision->id,
                'employee_id' => $revision->employee_id,
                'employee_code' => $revision->employee?->employee_code,
                'employee_name' => $revision->employee?->name,
                'department' => $revision->employee?->department?->name,
                'effective_date' => $revision->effective_date->toDateString(),
                'change_type' => $revision->changeType(),
                'change_reason' => $revision->change_reason,
                'designation' => $revision->designation?->name,
                'previous_monthly_gross' => $revision->previousRevision->monthly_gross,
                'monthly_gross' => $revision->monthly_gross,
                'change_amount' => $revision->changeAmount(),
                'field_changes' => $revision->fieldChanges(),
                'remarks' => $revision->remarks,
            ])
            ->values();

        return [
            'rows' => $rows->all(),
            'totals' => [
                'changes' => $rows->count(),
                'increments' => $rows->where('change_type', SalaryDetail::CHANGE_INCREMENT)->count(),
                'decrements' => $rows->where('change_type', SalaryDetail::CHANGE_DECREMENT)->count(),
            ],
        ];
    }

    /**
     * Every approved salary that is not fully paid (for months up to `$month`
     * when given, so older dues show too), oldest first, with the total due
     * per employee and overall.
     *
     * @return array{rows: array<int, array<string, mixed>>, employees: array<int, array{employee_id: int, employee_code: string, employee_name: string, months: int, due_amount: float}>, totals: array{count: int, net_salary: float, paid_amount: float, due_amount: float}}
     */
    public function salaryDue(?string $month = null, ?string $department = null, ?int $employeeId = null): array
    {
        $salaries = SalaryHistory::query()
            ->live()
            ->whereIn('status', SalaryHistory::PAYABLE_STATUSES)
            ->unpaid()
            ->when($month, fn ($query) => $query->whereDate('salary_month', '<=', $month.'-01'))
            ->when($department, fn ($query) => $query->inDepartment($department))
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->with(['employee:id,employee_code,name,department_id,designation_id', 'employee.department:id,name', 'employee.designation:id,name'])
            ->orderBy('salary_month')
            ->orderByEmployeeCode()
            ->select(['id', 'employee_id', 'salary_month', 'status', 'net_salary'])
            ->withPaidAmount()
            ->get();

        $rows = $salaries->map(fn (SalaryHistory $salary) => [
            'id' => $salary->id,
            'employee_id' => $salary->employee_id,
            'employee_code' => $salary->employee?->employee_code,
            'employee_name' => $salary->employee?->name,
            'department' => $salary->employee?->department?->name,
            'designation' => $salary->employee?->designation?->name,
            'salary_month' => $salary->salary_month->format('Y-m'),
            'status' => $salary->status,
            'net_salary' => (float) $salary->net_salary,
            'paid_amount' => (float) $salary->paid_amount,
            'due_amount' => $salary->dueAmount(),
        ]);

        $employees = $rows->groupBy('employee_id')
            ->map(fn (Collection $dues) => [
                'employee_id' => $dues->first()['employee_id'],
                'employee_code' => $dues->first()['employee_code'],
                'employee_name' => $dues->first()['employee_name'],
                'months' => $dues->count(),
                'due_amount' => round($dues->sum('due_amount'), 2),
            ])
            ->sortByDesc('due_amount')
            ->values();

        return [
            'rows' => $rows->values()->all(),
            'employees' => $employees->all(),
            'totals' => [
                'count' => $rows->count(),
                'net_salary' => round($rows->sum('net_salary'), 2),
                'paid_amount' => round($rows->sum('paid_amount'), 2),
                'due_amount' => round($rows->sum('due_amount'), 2),
            ],
        ];
    }
}
