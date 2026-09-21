<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\IncomeExpenseTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomeExpenseTransactionService
{
    public function paginate(int $perPage = 15, ?string $type = null, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return IncomeExpenseTransaction::query()
            ->with(['account:id,name,account_type_id', 'paymentAccount:id,name,account_type_id'])
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($fromDate && $toDate, fn ($query) => $query->whereBetween('transaction_date', [$fromDate, $toDate]))
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    public function find(IncomeExpenseTransaction $transaction): IncomeExpenseTransaction
    {
        return $transaction->load(['account', 'paymentAccount']);
    }

    /**
     * @param  array{type: string, income_expense_account_id: int, payment_account_id: int, amount: float, transaction_date: string, reference_no?: ?string, description?: ?string}  $data
     */
    public function create(array $data): IncomeExpenseTransaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = IncomeExpenseTransaction::create([
                ...$data,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->post($transaction, 1);

            return $transaction->load(['account', 'paymentAccount']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(IncomeExpenseTransaction $transaction, array $data): IncomeExpenseTransaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // Reverse what the old entry posted, then post the edited one, so
            // account balances stay correct however the entry changes.
            $this->post($transaction, -1);

            $transaction->fill([...$data, 'updated_by' => Auth::id()]);
            $transaction->save();

            $this->post($transaction, 1);

            return $transaction->load(['account', 'paymentAccount']);
        });
    }

    public function delete(IncomeExpenseTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $this->post($transaction, -1);
            $transaction->delete();
        });
    }

    /**
     * Applies (direction 1) or reverses (direction -1) the entry's double
     * entry on the account balances. Income debits the payment (asset)
     * account and credits the revenue account; expense debits the expense
     * account and credits the payment account.
     */
    private function post(IncomeExpenseTransaction $transaction, int $direction): void
    {
        $amount = (float) $transaction->amount * $direction;

        [$debitAccountId, $creditAccountId] = $transaction->type === 'income'
            ? [$transaction->payment_account_id, $transaction->income_expense_account_id]
            : [$transaction->income_expense_account_id, $transaction->payment_account_id];

        $this->move($debitAccountId, debit: true, amount: $amount);
        $this->move($creditAccountId, debit: false, amount: $amount);
    }

    /**
     * A debit raises a debit-normal account (asset, expense) and lowers a
     * credit-normal one; a credit does the opposite.
     */
    private function move(int $accountId, bool $debit, float $amount): void
    {
        $account = ChartOfAccount::with('accountType')->findOrFail($accountId);

        $raises = $debit === $account->accountType->increasesOnDebit();

        $account->increment('balance', $raises ? $amount : -$amount);
    }
}
