<?php

namespace App\Services;

use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvanceRecovery;
use App\Models\SalaryHistory;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Advance salary, as in the payroll_software advance module: an employee
 * takes an advance, and a monthly installment is deducted from each salary
 * generated from the deduction start month until it is fully recovered. The
 * employee may also pay back in cash.
 *
 * Every repayment is an EmployeeAdvanceRecovery. Salary installments are
 * written when a salary is generated, rewritten when it is regenerated and
 * removed when it is cancelled, so an advance's recovered amount always
 * matches the live salaries.
 */
class EmployeeAdvanceService
{
    /**
     * @param  array{employee_id?: ?int, status?: ?string, search?: ?string, from_date?: ?string, to_date?: ?string}  $filters
     */
    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['employee:id,employee_code,name,department_id,designation_id', 'employee.department', 'employee.designation'])
            ->orderByDesc('advance_date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Sums for the same filters as the list.
     *
     * @param  array<string, mixed>  $filters
     * @return array{count: int, amount: float, recovered_amount: float, outstanding_amount: float}
     */
    public function totals(array $filters): array
    {
        $totals = $this->filteredQuery($filters)
            ->selectRaw('COUNT(*) as count, SUM(amount) as amount, SUM(recovered_amount) as recovered_amount')
            ->first();

        return [
            'count' => (int) $totals->count,
            'amount' => round((float) $totals->amount, 2),
            'recovered_amount' => round((float) $totals->recovered_amount, 2),
            'outstanding_amount' => round((float) $totals->amount - (float) $totals->recovered_amount, 2),
        ];
    }

    /**
     * @param  array<string, mixed>  $data  `deduction_start_month` as Y-m.
     */
    public function create(array $data): EmployeeAdvance
    {
        $advance = EmployeeAdvance::create([
            ...$data,
            'installment_amount' => $data['installment_amount'] ?? $this->defaultInstallment($data['amount'], $data['installment_count']),
            'deduction_start_month' => $data['deduction_start_month'].'-01',
            'recovered_amount' => 0,
            'status' => EmployeeAdvance::STATUS_RUNNING,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $this->withDetail($advance);
    }

    /**
     * Once anything is recovered, the deduction start month is fixed and the
     * amount cannot drop below what is already recovered.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(EmployeeAdvance $advance, array $data): EmployeeAdvance
    {
        return DB::transaction(function () use ($advance, $data) {
            $advance = EmployeeAdvance::query()->lockForUpdate()->findOrFail($advance->id);
            $hasRecoveries = $advance->recoveries()->exists();
            $startMonth = $data['deduction_start_month'].'-01';

            if ($hasRecoveries && $startMonth !== $advance->deduction_start_month->toDateString()) {
                throw ValidationException::withMessages([
                    'deduction_start_month' => __('Installments have already been recovered, so the deduction start month can no longer change.'),
                ]);
            }

            if ((float) $data['amount'] < (float) $advance->recovered_amount) {
                throw ValidationException::withMessages([
                    'amount' => __('The amount cannot be less than the :amount already recovered.', ['amount' => number_format((float) $advance->recovered_amount, 2)]),
                ]);
            }

            $advance->fill([
                ...$data,
                'installment_amount' => $data['installment_amount'] ?? $this->defaultInstallment($data['amount'], $data['installment_count']),
                'deduction_start_month' => $startMonth,
                'updated_by' => Auth::id(),
            ]);
            $advance->save();

            $this->refreshRecovery($advance);

            return $this->withDetail($advance);
        });
    }

    public function delete(EmployeeAdvance $advance): void
    {
        if ($advance->recoveries()->exists()) {
            throw ValidationException::withMessages([
                'advance' => __('This advance has recoveries. Cancel the salaries and delete the cash repayments that recovered it first.'),
            ]);
        }

        $advance->delete();
    }

    /**
     * Record cash paid back by the employee.
     *
     * @param  array{amount: float|int|string, recovery_date: string, remarks?: ?string}  $data
     */
    public function recordCashRecovery(EmployeeAdvance $advance, array $data): EmployeeAdvance
    {
        return DB::transaction(function () use ($advance, $data) {
            $advance = EmployeeAdvance::query()->lockForUpdate()->findOrFail($advance->id);

            if ((float) $data['amount'] > $advance->outstandingAmount()) {
                throw ValidationException::withMessages([
                    'amount' => __('The amount cannot exceed the outstanding balance of :amount.', ['amount' => number_format($advance->outstandingAmount(), 2)]),
                ]);
            }

            $advance->recoveries()->create([
                'employee_id' => $advance->employee_id,
                'type' => EmployeeAdvanceRecovery::TYPE_CASH,
                'recovery_date' => $data['recovery_date'],
                'amount' => $data['amount'],
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->refreshRecovery($advance);

            return $this->withDetail($advance);
        });
    }

    /**
     * Only cash repayments are deleted here; salary installments follow their
     * salary and go away when it is cancelled.
     */
    public function deleteRecovery(EmployeeAdvanceRecovery $recovery): EmployeeAdvance
    {
        if ($recovery->type !== EmployeeAdvanceRecovery::TYPE_CASH) {
            throw ValidationException::withMessages([
                'recovery' => __('A salary installment cannot be deleted. Cancel the salary it was deducted from instead.'),
            ]);
        }

        return DB::transaction(function () use ($recovery) {
            $advance = $recovery->advance;
            $recovery->delete();
            $this->refreshRecovery($advance);

            return $this->withDetail($advance);
        });
    }

    /**
     * Limit an employee's advances to those that may have an installment due
     * in the month, with what was recovered from them outside that month's
     * salary as `recovered_outside_month`, so regenerating a month sees the
     * same balance as the first generation.
     */
    public function constrainDueInMonth(HasMany|Builder $query, CarbonInterface $month): HasMany|Builder
    {
        $monthStart = CarbonImmutable::parse($month)->startOfMonth()->toDateString();
        $isThisMonthsInstallment = fn (Builder $query) => $query
            ->where('type', EmployeeAdvanceRecovery::TYPE_SALARY)
            ->whereDate('salary_month', $monthStart);

        return $query
            ->whereDate('deduction_start_month', '<=', $monthStart)
            ->where(fn (Builder $query) => $query
                ->where('status', EmployeeAdvance::STATUS_RUNNING)
                ->orWhereHas('recoveries', $isThisMonthsInstallment))
            ->withSum(['recoveries as recovered_outside_month' => fn (Builder $query) => $query
                ->whereNot($isThisMonthsInstallment)], 'amount')
            ->orderBy('advance_date')
            ->orderBy('id');
    }

    /**
     * The installment due from each advance for the month: the installment
     * amount, or the remaining balance when that is smaller. The advances
     * must have been loaded through constrainDueInMonth().
     *
     * @param  Collection<int, EmployeeAdvance>  $advances
     * @return array<int, array{employee_advance_id: int, amount: float}>
     */
    public function dueInstallments(Collection $advances): array
    {
        return $advances
            ->map(fn (EmployeeAdvance $advance) => [
                'employee_advance_id' => $advance->id,
                'amount' => round(min(
                    (float) $advance->installment_amount,
                    max(0, (float) $advance->amount - (float) $advance->recovered_outside_month),
                ), 2),
            ])
            ->filter(fn (array $installment) => $installment['amount'] > 0)
            ->values()
            ->all();
    }

    /**
     * Spread the amount actually deducted from salary over the due
     * installments, oldest advance first. Whatever could not be deducted
     * stays outstanding for later months.
     *
     * @param  array<int, array{employee_advance_id: int, amount: float}>  $installments
     * @return array<int, array{employee_advance_id: int, amount: float}>
     */
    public function allocate(array $installments, float $deducted): array
    {
        $remaining = round($deducted, 2);
        $allocations = [];

        foreach ($installments as $installment) {
            if ($remaining <= 0) {
                break;
            }

            $amount = round(min($installment['amount'], $remaining), 2);
            $allocations[] = ['employee_advance_id' => $installment['employee_advance_id'], 'amount' => $amount];
            $remaining = round($remaining - $amount, 2);
        }

        return $allocations;
    }

    /**
     * Replace the installments recovered by a (re)generated salary.
     *
     * @param  array<int, array{employee_advance_id: int, amount: float}>  $allocations
     */
    public function syncSalaryRecoveries(SalaryHistory $salary, array $allocations): void
    {
        $advanceIds = $salary->advanceRecoveries()->pluck('employee_advance_id');
        $salary->advanceRecoveries()->delete();

        foreach ($allocations as $allocation) {
            $salary->advanceRecoveries()->create([
                'employee_advance_id' => $allocation['employee_advance_id'],
                'employee_id' => $salary->employee_id,
                'type' => EmployeeAdvanceRecovery::TYPE_SALARY,
                'recovery_date' => $salary->salary_month->copy()->endOfMonth()->toDateString(),
                'salary_month' => $salary->salary_month->toDateString(),
                'amount' => $allocation['amount'],
                'created_by' => Auth::id(),
            ]);
        }

        $this->refreshAdvances($advanceIds->merge(array_column($allocations, 'employee_advance_id'))->unique()->all());
    }

    /**
     * Give back the installments of a cancelled salary.
     */
    public function removeSalaryRecoveries(SalaryHistory $salary): void
    {
        $this->syncSalaryRecoveries($salary, []);
    }

    /**
     * @param  array<int, int>  $advanceIds
     */
    private function refreshAdvances(array $advanceIds): void
    {
        EmployeeAdvance::query()->whereIn('id', $advanceIds)->get()
            ->each(fn (EmployeeAdvance $advance) => $this->refreshRecovery($advance));
    }

    /**
     * Recalculate the recovered amount from the recoveries and mark the
     * advance completed once nothing is outstanding.
     */
    private function refreshRecovery(EmployeeAdvance $advance): void
    {
        $recovered = round((float) $advance->recoveries()->sum('amount'), 2);

        $advance->update([
            'recovered_amount' => $recovered,
            'status' => $recovered >= (float) $advance->amount ? EmployeeAdvance::STATUS_COMPLETED : EmployeeAdvance::STATUS_RUNNING,
        ]);
    }

    private function defaultInstallment(float|int|string $amount, int|string $installmentCount): float
    {
        return round((float) $amount / max(1, (int) $installmentCount), 2);
    }

    /**
     * Load what the detail view shows: the employee and every recovery.
     */
    public function withDetail(EmployeeAdvance $advance): EmployeeAdvance
    {
        return $advance->load([
            'employee.department',
            'employee.designation',
            'recoveries' => fn ($query) => $query->orderBy('recovery_date')->orderBy('id'),
            'recoveries.creator:id,name',
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        return EmployeeAdvance::query()
            ->when($filters['employee_id'] ?? null, fn (Builder $query, int $employeeId) => $query->where('employee_id', $employeeId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['from_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('advance_date', '>=', $date))
            ->when($filters['to_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('advance_date', '<=', $date))
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query
                ->whereHas('employee', fn (Builder $query) => $query->search($search)));
    }
}
