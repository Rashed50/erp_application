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
 *
 * The money is also kept in the general ledger: giving an advance debits
 * "Advance to Employees" and credits the cash/bank account it was paid from,
 * and a cash repayment does the reverse. Salary installments are posted when
 * the salary is paid (see PayrollService).
 */
class EmployeeAdvanceService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

    /**
     * @param  array{employee_id?: ?int, status?: ?string, search?: ?string, from_date?: ?string, to_date?: ?string}  $filters
     */
    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['employee:id,employee_code,name,department_id,designation_id', 'employee.department', 'employee.designation', 'creator:id,name'])
            ->withRecoveredAmount()
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
            ->selectRaw('COUNT(*) as count, SUM(amount) as amount, SUM('.EmployeeAdvance::RECOVERED_SQL.') as recovered_amount')
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
        return DB::transaction(function () use ($data) {
            $advance = EmployeeAdvance::create([
                ...$data,
                'installment_amount' => $data['installment_amount'] ?? $this->defaultInstallment($data['amount'], $data['installment_count']),
                'deduction_start_month' => $data['deduction_start_month'].'-01',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->ledgerPostingService->post($advance, $this->advanceJournalHeader($advance), $this->advanceJournalLines($advance));

            return $this->withDetail($advance);
        });
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

            // Rewrite the journal entry in place so it always matches the advance.
            if ($entry = $this->ledgerPostingService->entryFor($advance)) {
                $this->ledgerPostingService->repost($entry, $this->advanceJournalHeader($advance), $this->advanceJournalLines($advance));
            } else {
                $this->ledgerPostingService->post($advance, $this->advanceJournalHeader($advance), $this->advanceJournalLines($advance));
            }

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

        DB::transaction(function () use ($advance) {
            $this->ledgerPostingService->reverse($advance);
            $advance->delete();
        });
    }

    /**
     * Record cash paid back by the employee into a cash/bank account.
     *
     * @param  array{amount: float|int|string, recovery_date: string, payment_account_id: int, remarks?: ?string}  $data
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

            $recovery = $advance->recoveries()->create([
                'payment_account_id' => $data['payment_account_id'],
                'type' => EmployeeAdvanceRecovery::TYPE_CASH,
                'recovery_date' => $data['recovery_date'],
                'amount' => $data['amount'],
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->postCashRecovery($recovery);

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
            $this->ledgerPostingService->reverse($recovery);
            $recovery->delete();

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
            ->whereHas('salaryHistory', fn (Builder $query) => $query->whereDate('salary_month', $monthStart));

        return $query
            ->whereDate('deduction_start_month', '<=', $monthStart)
            ->where(fn (Builder $query) => $query
                ->running()
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
        $salary->advanceRecoveries()->delete();

        foreach ($allocations as $allocation) {
            $salary->advanceRecoveries()->create([
                'employee_advance_id' => $allocation['employee_advance_id'],
                'type' => EmployeeAdvanceRecovery::TYPE_SALARY,
                'recovery_date' => $salary->salary_month->copy()->endOfMonth()->toDateString(),
                'amount' => $allocation['amount'],
                'created_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Give back the installments of a cancelled salary.
     */
    public function removeSalaryRecoveries(SalaryHistory $salary): void
    {
        $this->syncSalaryRecoveries($salary, []);
    }

    private function defaultInstallment(float|int|string $amount, int|string $installmentCount): float
    {
        return round((float) $amount / max(1, (int) $installmentCount), 2);
    }

    /**
     * @return array{date: string, general_particular: string}
     */
    private function advanceJournalHeader(EmployeeAdvance $advance): array
    {
        return [
            'date' => $advance->advance_date->toDateString(),
            'general_particular' => 'Advance Salary - '.$advance->employee->name,
        ];
    }

    /**
     * The advance leaves the cash/bank account and becomes money the employee owes.
     *
     * @return array<int, array{account_id: int, debit?: float, credit?: float, particular: string}>
     */
    private function advanceJournalLines(EmployeeAdvance $advance): array
    {
        $amount = (float) $advance->amount;

        return [
            ['account_id' => $this->employeeAdvanceAccountId(), 'debit' => $amount, 'particular' => 'advance salary'],
            ['account_id' => $advance->payment_account_id, 'credit' => $amount, 'particular' => 'advance salary'],
        ];
    }

    /**
     * Cash paid back comes into the cash/bank account and clears what the employee owes.
     */
    private function postCashRecovery(EmployeeAdvanceRecovery $recovery): void
    {
        $amount = (float) $recovery->amount;

        $this->ledgerPostingService->post($recovery, [
            'date' => $recovery->recovery_date->toDateString(),
            'general_particular' => 'Advance Salary Repayment - '.$recovery->advance->employee->name,
        ], [
            ['account_id' => $recovery->payment_account_id, 'debit' => $amount, 'particular' => 'advance salary repayment'],
            ['account_id' => $this->employeeAdvanceAccountId(), 'credit' => $amount, 'particular' => 'advance salary repayment'],
        ]);
    }

    /**
     * The request validates the account exists, so a missing one here is a bug.
     */
    private function employeeAdvanceAccountId(): int
    {
        return $this->ledgerPostingService->predefinedAccount(LedgerPostingService::EMPLOYEE_ADVANCE_NUMBER)?->id
            ?? throw new \RuntimeException('Predefined account '.LedgerPostingService::EMPLOYEE_ADVANCE_NUMBER.' is missing.');
    }

    /**
     * Load what the detail view shows: the employee and every recovery.
     */
    public function withDetail(EmployeeAdvance $advance): EmployeeAdvance
    {
        return $advance->load([
            'employee.department',
            'employee.designation',
            'paymentAccount:id,account_number,name',
            'recoveries' => fn ($query) => $query->orderBy('recovery_date')->orderBy('id'),
            'recoveries.creator:id,name',
            'recoveries.paymentAccount:id,account_number,name',
            'recoveries.salaryHistory:id,salary_month',
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        return EmployeeAdvance::query()
            ->when($filters['employee_id'] ?? null, fn (Builder $query, int $employeeId) => $query->where('employee_id', $employeeId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->inStatus($status))
            ->when($filters['from_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('advance_date', '>=', $date))
            ->when($filters['to_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('advance_date', '<=', $date))
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query
                ->whereHas('employee', fn (Builder $query) => $query->search($search)));
    }
}
