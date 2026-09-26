<?php

namespace App\Services;

use App\Models\SalaryHistory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Monthly salary generation and the salary status workflow:
 * Generated -> Approved -> Paid, with Cancelled for corrections.
 *
 * A Generated salary is recalculated in place when the month is generated
 * again. Approved and Paid salaries are never recalculated; an approved
 * salary is corrected by cancelling it and generating again, a paid one by
 * an adjustment in a later month's work record.
 */
class PayrollService
{
    public function __construct(private readonly SalaryCalculationService $calculator) {}

    /**
     * @param  array{month?: ?string, employee_id?: ?int, department?: ?string, designation?: ?string, status?: ?string, include_cancelled?: bool}  $filters
     */
    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->orderByDesc('salary_month')
            ->orderBy('employee_code')
            ->paginate($perPage);
    }

    /**
     * Sums for the same filters as the list, for report footers.
     *
     * @param  array<string, mixed>  $filters
     * @return array{count: int, gross_salary: float, total_deduction: float, net_salary: float}
     */
    public function totals(array $filters): array
    {
        $totals = $this->filteredQuery($filters)
            ->selectRaw('COUNT(*) as count, SUM(gross_salary) as gross_salary, SUM(total_deduction) as total_deduction, SUM(net_salary) as net_salary')
            ->first();

        return [
            'count' => (int) $totals->count,
            'gross_salary' => round((float) $totals->gross_salary, 2),
            'total_deduction' => round((float) $totals->total_deduction, 2),
            'net_salary' => round((float) $totals->net_salary, 2),
        ];
    }

    /**
     * Save the month's salaries for every generatable employee (or only the
     * given ones) as a snapshot, all inside one transaction.
     *
     * @param  array<int, int>|null  $employeeIds
     * @return array{created: int, regenerated: int, skipped: array<int, array{employee_code: string, name: string, reason: string}>}
     */
    public function generate(string $month, ?array $employeeIds = null): array
    {
        $monthStart = CarbonImmutable::createFromFormat('Y-m-d', $month.'-01')->startOfDay();

        try {
            return DB::transaction(function () use ($monthStart, $employeeIds) {
                $result = ['created' => 0, 'regenerated' => 0, 'skipped' => []];

                foreach ($this->calculator->previewMonth($monthStart) as $row) {
                    $employee = $row['employee'];

                    if ($employeeIds !== null && ! in_array($employee->id, $employeeIds, true)) {
                        continue;
                    }

                    if (! $row['can_generate']) {
                        $result['skipped'][] = [
                            'employee_code' => $employee->employee_code,
                            'name' => $employee->name,
                            'reason' => implode(' ', $row['issues']),
                        ];

                        continue;
                    }

                    $attributes = [
                        ...$row['calculation'],
                        'status' => SalaryHistory::STATUS_GENERATED,
                        'is_active_record' => true,
                        'generated_by' => Auth::id(),
                        'generated_at' => now(),
                    ];

                    if ($row['existing_salary']) {
                        $row['existing_salary']->update($attributes);
                        $result['regenerated']++;
                    } else {
                        SalaryHistory::create($attributes);
                        $result['created']++;
                    }
                }

                return $result;
            });
        } catch (UniqueConstraintViolationException) {
            // Another generation for the same month committed first.
            throw ValidationException::withMessages([
                'month' => 'Salary for this month was generated at the same time by another user. Please reload and try again.',
            ]);
        }
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function approve(array $ids): int
    {
        return $this->transition($ids, SalaryHistory::STATUS_GENERATED, [
            'status' => SalaryHistory::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function markPaid(array $ids): int
    {
        return $this->transition($ids, SalaryHistory::STATUS_APPROVED, [
            'status' => SalaryHistory::STATUS_PAID,
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Cancel a generated or approved salary so the month can be generated
     * again. The cancelled record is kept for audit.
     */
    public function cancel(SalaryHistory $salary, string $reason): SalaryHistory
    {
        if ($salary->status === SalaryHistory::STATUS_PAID) {
            throw ValidationException::withMessages([
                'status' => 'A paid salary cannot be cancelled. Record an adjustment in a later month instead.',
            ]);
        }

        if ($salary->status === SalaryHistory::STATUS_CANCELLED) {
            throw ValidationException::withMessages(['status' => 'This salary is already cancelled.']);
        }

        $salary->update([
            'status' => SalaryHistory::STATUS_CANCELLED,
            'is_active_record' => null,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancel_reason' => $reason,
        ]);

        return $salary;
    }

    /**
     * Move every listed salary that is currently in `$fromStatus` forward;
     * others are left untouched. Returns how many were updated.
     *
     * @param  array<int, int>  $ids
     * @param  array<string, mixed>  $attributes
     */
    private function transition(array $ids, string $fromStatus, array $attributes): int
    {
        return DB::transaction(fn () => SalaryHistory::query()
            ->live()
            ->whereIn('id', $ids)
            ->where('status', $fromStatus)
            ->update($attributes));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        $status = $filters['status'] ?? null;

        return SalaryHistory::query()
            ->when($filters['month'] ?? null, fn (Builder $query, string $month) => $query->forMonth($month))
            ->when($filters['employee_id'] ?? null, fn (Builder $query, int $employeeId) => $query->where('employee_id', $employeeId))
            ->when($filters['department'] ?? null, fn (Builder $query, string $department) => $query->where('department', $department))
            ->when($filters['designation'] ?? null, fn (Builder $query, string $designation) => $query->where('designation', $designation))
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            // Cancelled salaries only show up when explicitly asked for.
            ->when(! $status && ! ($filters['include_cancelled'] ?? false), fn (Builder $query) => $query->live());
    }
}
