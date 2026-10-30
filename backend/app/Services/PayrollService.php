<?php

namespace App\Services;

use App\Models\SalaryHistory;
use App\Models\SalaryPayment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Monthly salary generation and the salary status workflow:
 * Generated -> Approved -> (Partially Paid ->) Paid, with Cancelled for
 * corrections.
 *
 * A Generated salary is recalculated in place when the month is generated
 * again. Approved and Paid salaries are never recalculated; an approved
 * salary is corrected by cancelling it and generating again, a paid one by
 * an adjustment in a later month's work record.
 *
 * Advance installments deducted by a salary are recorded as advance
 * recoveries on generation and given back when the salary is cancelled.
 *
 * In the general ledger, approving a salary books the expense against
 * Salary Payable, and each payment (a salary can be paid in parts) clears
 * Salary Payable from a cash/bank account. Cancelling an approved salary
 * reverses its entry; a salary with payments can no longer be cancelled.
 */
class PayrollService
{
    public function __construct(
        private readonly SalaryCalculationService $calculator,
        private readonly EmployeeAdvanceService $advanceService,
        private readonly LedgerPostingService $ledgerPostingService,
    ) {}

    /**
     * `payment_status` is "paid" (fully paid) or "unpaid" (anything still due).
     *
     * @param  array{month?: ?string, employee_id?: ?int, department?: ?string, designation?: ?string, status?: ?string, payment_status?: ?string, include_cancelled?: bool}  $filters
     */
    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['employee:id,employee_code,name,department_id,designation_id', 'employee.department:id,name', 'employee.designation:id,name', 'generator:id,name', 'approver:id,name'])
            ->withPaidAmount()
            ->orderByDesc('salary_month')
            ->orderByEmployeeCode()
            ->paginate($perPage);
    }

    /**
     * Sums for the same filters as the list, for report footers.
     *
     * @param  array<string, mixed>  $filters
     * @return array{count: int, gross_salary: float, total_deduction: float, net_salary: float, paid_amount: float, due_amount: float}
     */
    public function totals(array $filters): array
    {
        $totals = $this->filteredQuery($filters)
            ->selectRaw('COUNT(*) as count, SUM(gross_salary) as gross_salary, SUM(total_deduction) as total_deduction, SUM(net_salary) as net_salary, SUM('.SalaryHistory::PAID_SQL.') as paid_amount')
            ->first();

        return [
            'count' => (int) $totals->count,
            'gross_salary' => round((float) $totals->gross_salary, 2),
            'total_deduction' => round((float) $totals->total_deduction, 2),
            'net_salary' => round((float) $totals->net_salary, 2),
            'paid_amount' => round((float) $totals->paid_amount, 2),
            'due_amount' => round((float) $totals->net_salary - (float) $totals->paid_amount, 2),
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

                foreach ($this->calculator->previewMonth($monthStart, $employeeIds) as $row) {
                    $employee = $row['employee'];

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
                        $salary = tap($row['existing_salary'])->update($attributes);
                        $result['regenerated']++;
                    } else {
                        $salary = SalaryHistory::create($attributes);
                        $result['created']++;
                    }

                    $this->advanceService->syncSalaryRecoveries($salary, $row['advance_allocations']);
                }

                return $result;
            });
        } catch (UniqueConstraintViolationException) {
            // Another generation for the same month committed first.
            throw ValidationException::withMessages([
                'month' => __('Salary for this month was generated at the same time by another user. Please reload and try again.'),
            ]);
        }
    }

    /**
     * Approve every listed generated salary and book it as owed to the
     * employee. Others are left untouched. Returns how many were approved.
     *
     * @param  array<int, int>  $ids
     */
    public function approve(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $salaries = $this->lockedSalaries($ids, [SalaryHistory::STATUS_GENERATED]);

            foreach ($salaries as $salary) {
                $salary->update([
                    'status' => SalaryHistory::STATUS_APPROVED,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                $this->accrue($salary);
            }

            return $salaries->count();
        });
    }

    /**
     * Pay the whole due amount of every listed approved or partly paid
     * salary from one cash/bank account. Returns how many were paid.
     *
     * @param  array<int, int>  $ids
     */
    public function markPaid(array $ids, int $paymentAccountId, string $paymentDate): int
    {
        return DB::transaction(function () use ($ids, $paymentAccountId, $paymentDate) {
            $salaries = $this->lockedSalaries($ids, SalaryHistory::PAYABLE_STATUSES);

            foreach ($salaries as $salary) {
                $this->recordPayment($salary, [
                    'amount' => $salary->dueAmount(),
                    'payment_account_id' => $paymentAccountId,
                    'payment_date' => $paymentDate,
                ]);
            }

            return $salaries->count();
        });
    }

    /**
     * Pay all or part of one salary's due amount.
     *
     * @param  array{amount: float|int|string, payment_account_id: int, payment_date: string, remarks?: ?string}  $data
     */
    public function pay(SalaryHistory $salary, array $data): SalaryHistory
    {
        return DB::transaction(function () use ($salary, $data) {
            $salary = SalaryHistory::query()->lockForUpdate()->findOrFail($salary->id);

            if (! $salary->is_active_record || ! in_array($salary->status, SalaryHistory::PAYABLE_STATUSES, true)) {
                throw ValidationException::withMessages(['status' => __('Only an approved salary that is not fully paid can be paid.')]);
            }

            if (round((float) $data['amount'], 2) > $salary->dueAmount()) {
                throw ValidationException::withMessages([
                    'amount' => __('The amount cannot exceed the due salary of :amount.', ['amount' => number_format($salary->dueAmount(), 2)]),
                ]);
            }

            $this->recordPayment($salary, $data);

            return $this->withPayments($salary);
        });
    }

    /**
     * Remove a payment made in error and its journal entry; the salary is
     * due again by that amount.
     */
    public function deletePayment(SalaryPayment $payment): SalaryHistory
    {
        return DB::transaction(function () use ($payment) {
            $salary = SalaryHistory::query()->lockForUpdate()->findOrFail($payment->salary_history_id);

            $this->ledgerPostingService->reverse($payment);
            $payment->delete();
            $this->refreshPaid($salary);

            return $this->withPayments($salary);
        });
    }

    public function withPayments(SalaryHistory $salary): SalaryHistory
    {
        return $salary->load([
            'employee:id,employee_code,name,department_id,designation_id',
            'employee.department:id,name',
            'employee.designation:id,name',
            'generator:id,name',
            'approver:id,name',
            'payments' => fn ($query) => $query->orderBy('payment_date')->orderBy('id'),
            'payments.paymentAccount:id,account_number,name',
            'payments.creator:id,name',
        ]);
    }

    /**
     * A payment of zero (a salary fully covered by its advance installment)
     * only marks the salary paid.
     *
     * @param  array{amount: float|int|string, payment_account_id: int, payment_date: string, remarks?: ?string}  $data
     */
    private function recordPayment(SalaryHistory $salary, array $data): void
    {
        // Salaries approved before approval booked the expense are booked now.
        if (! $this->ledgerPostingService->entryFor($salary)) {
            $this->accrue($salary);
        }

        $amount = round((float) $data['amount'], 2);

        if ($amount > 0) {
            $payment = $salary->payments()->create([
                'payment_account_id' => $data['payment_account_id'],
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->postPayment($salary, $payment);
        }

        $this->refreshPaid($salary);
    }

    /**
     * Move the status between Approved, Partially Paid and Paid to match
     * the payments made.
     */
    private function refreshPaid(SalaryHistory $salary): void
    {
        $paid = round((float) $salary->payments()->sum('amount'), 2);
        $isPaid = $paid >= (float) $salary->net_salary;

        $salary->update([
            'status' => match (true) {
                $isPaid => SalaryHistory::STATUS_PAID,
                $paid > 0 => SalaryHistory::STATUS_PARTIALLY_PAID,
                default => SalaryHistory::STATUS_APPROVED,
            },
            'paid_by' => $isPaid ? ($salary->paid_by ?? Auth::id()) : null,
            'paid_at' => $isPaid ? ($salary->paid_at ?? now()) : null,
        ]);
    }

    /**
     * Approving a salary books what the employee earned for the month as
     * Salaries & Wages, at the month end: the advance installment deducted
     * from it clears "Advance to Employees" and the net salary is owed as
     * Salary Payable until it is paid. Absence, unpaid leave and other
     * deductions were never earned, so they are not part of the expense.
     *
     * Skipped while the predefined accounts are not seeded, as sales and
     * purchases are; the first payment books it then.
     */
    private function accrue(SalaryHistory $salary): void
    {
        $netSalary = (float) $salary->net_salary;
        $advanceDeduction = (float) $salary->advance_deduction;
        $accounts = $this->predefinedAccountIds([
            LedgerPostingService::SALARY_EXPENSE_NUMBER,
            LedgerPostingService::EMPLOYEE_ADVANCE_NUMBER,
            LedgerPostingService::SALARY_PAYABLE_NUMBER,
        ]);

        if ($netSalary + $advanceDeduction <= 0 || ! $accounts) {
            return;
        }

        $month = $salary->salary_month->format('F Y');

        $this->ledgerPostingService->post($salary, [
            'tr_no' => $salary->employee->employee_code,
            'date' => $salary->salary_month->copy()->endOfMonth()->toDateString(),
            'general_particular' => "Salary - {$salary->employee->name} ({$month})",
        ], [
            ['account_id' => $accounts[LedgerPostingService::SALARY_EXPENSE_NUMBER], 'debit' => $netSalary + $advanceDeduction, 'particular' => "salary {$month}"],
            ['account_id' => $accounts[LedgerPostingService::EMPLOYEE_ADVANCE_NUMBER], 'credit' => $advanceDeduction, 'particular' => 'advance salary installment'],
            ['account_id' => $accounts[LedgerPostingService::SALARY_PAYABLE_NUMBER], 'credit' => $netSalary, 'particular' => "salary payable {$month}"],
        ]);
    }

    /**
     * Each payment clears that much Salary Payable from the cash/bank account.
     */
    private function postPayment(SalaryHistory $salary, SalaryPayment $payment): void
    {
        $amount = (float) $payment->amount;
        $month = $salary->salary_month->format('F Y');
        $payableId = $this->predefinedAccountIds([LedgerPostingService::SALARY_PAYABLE_NUMBER])[LedgerPostingService::SALARY_PAYABLE_NUMBER]
            ?? throw new \RuntimeException('Predefined account '.LedgerPostingService::SALARY_PAYABLE_NUMBER.' is missing.');

        $this->ledgerPostingService->post($payment, [
            'tr_no' => $salary->employee->employee_code,
            'date' => $payment->payment_date->toDateString(),
            'general_particular' => "Salary Payment - {$salary->employee->name} ({$month})",
        ], [
            ['account_id' => $payableId, 'debit' => $amount, 'particular' => "salary payment {$month}"],
            ['account_id' => $payment->payment_account_id, 'credit' => $amount, 'particular' => "salary payment {$month}"],
        ]);
    }

    /**
     * The ids of the predefined accounts, keyed by number, or an empty array
     * when any of them is missing.
     *
     * @param  array<int, string>  $accountNumbers
     * @return array<string, int>
     */
    private function predefinedAccountIds(array $accountNumbers): array
    {
        $ids = [];

        foreach ($accountNumbers as $accountNumber) {
            $id = $this->ledgerPostingService->predefinedAccount($accountNumber)?->id;

            if (! $id) {
                return [];
            }

            $ids[$accountNumber] = $id;
        }

        return $ids;
    }

    /**
     * The listed live salaries in one of the statuses, locked for the update.
     *
     * @param  array<int, int>  $ids
     * @param  array<int, string>  $statuses
     * @return Collection<int, SalaryHistory>
     */
    private function lockedSalaries(array $ids, array $statuses): Collection
    {
        return SalaryHistory::query()
            ->live()
            ->whereIn('id', $ids)
            ->whereIn('status', $statuses)
            ->lockForUpdate()
            ->get();
    }

    /**
     * Cancel a generated or approved salary so the month can be generated
     * again. The cancelled record is kept for audit.
     */
    public function cancel(SalaryHistory $salary, string $reason): SalaryHistory
    {
        if (in_array($salary->status, [SalaryHistory::STATUS_PAID, SalaryHistory::STATUS_PARTIALLY_PAID], true)) {
            throw ValidationException::withMessages([
                'status' => __('A paid salary cannot be cancelled. Record an adjustment in a later month instead.'),
            ]);
        }

        if ($salary->status === SalaryHistory::STATUS_CANCELLED) {
            throw ValidationException::withMessages(['status' => __('This salary is already cancelled.')]);
        }

        return DB::transaction(function () use ($salary, $reason) {
            $salary->update([
                'status' => SalaryHistory::STATUS_CANCELLED,
                'is_active_record' => null,
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            $this->advanceService->removeSalaryRecoveries($salary);
            // An approved salary was booked as owed; it no longer is.
            $this->ledgerPostingService->reverse($salary);

            return $salary;
        });
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
            ->when($filters['department'] ?? null, fn (Builder $query, string $department) => $query->inDepartment($department))
            ->when($filters['designation'] ?? null, fn (Builder $query, string $designation) => $query->withDesignation($designation))
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            ->when(($filters['payment_status'] ?? null) === 'paid', fn (Builder $query) => $query->where('status', SalaryHistory::STATUS_PAID))
            ->when(($filters['payment_status'] ?? null) === 'unpaid', fn (Builder $query) => $query
                ->whereNot('status', SalaryHistory::STATUS_PAID)
                ->unpaid())
            // Cancelled salaries only show up when explicitly asked for.
            ->when(! $status && ! ($filters['include_cancelled'] ?? false), fn (Builder $query) => $query->live());
    }
}
