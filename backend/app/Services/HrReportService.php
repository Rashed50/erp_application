<?php

namespace App\Services;

use App\Models\Employee;
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
     * Payroll totals per department for a month, from the salary snapshot
     * (so a later department change does not move past salaries).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function departmentWise(string $month, ?string $status = null): Collection
    {
        return SalaryHistory::query()
            ->live()
            ->forMonth($month)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->selectRaw('department, COUNT(*) as employees, SUM(basic_salary) as basic_salary, SUM(total_allowance) as total_allowance, SUM(overtime_amount) as overtime_amount, SUM(gross_salary) as gross_salary, SUM(total_deduction) as total_deduction, SUM(net_salary) as net_salary')
            ->groupBy('department')
            ->orderBy('department')
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
            ->when($department, fn ($query) => $query->where('department', $department))
            ->get(['salary_month', 'status', 'gross_salary', 'total_deduction', 'net_salary'])
            ->groupBy(fn (SalaryHistory $salary) => $salary->salary_month->format('Y-m'))
            ->sortKeys()
            ->map(fn (Collection $salaries, string $month) => [
                'month' => $month,
                'employees' => $salaries->count(),
                'gross_salary' => round((float) $salaries->sum('gross_salary'), 2),
                'total_deduction' => round((float) $salaries->sum('total_deduction'), 2),
                'net_salary' => round((float) $salaries->sum('net_salary'), 2),
                'paid_amount' => round((float) $salaries->where('status', SalaryHistory::STATUS_PAID)->sum('net_salary'), 2),
                'unpaid_amount' => round((float) $salaries->where('status', '!=', SalaryHistory::STATUS_PAID)->sum('net_salary'), 2),
            ])
            ->values();
    }
}
