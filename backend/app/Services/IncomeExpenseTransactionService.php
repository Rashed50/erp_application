<?php

namespace App\Services;

use App\Models\IncomeExpenseTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomeExpenseTransactionService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

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

            $this->post($transaction);

            return $transaction->load(['account', 'paymentAccount']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(IncomeExpenseTransaction $transaction, array $data): IncomeExpenseTransaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $transaction->fill([...$data, 'updated_by' => Auth::id()]);
            $transaction->save();

            // Rewrite the journal entry in place so it always matches the entry.
            if ($entry = $this->ledgerPostingService->entryFor($transaction)) {
                $this->ledgerPostingService->repost($entry, $this->journalHeader($transaction), $this->journalLines($transaction));
            } else {
                $this->post($transaction);
            }

            return $transaction->load(['account', 'paymentAccount']);
        });
    }

    public function delete(IncomeExpenseTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $this->ledgerPostingService->reverse($transaction);
            $transaction->delete();
        });
    }

    private function post(IncomeExpenseTransaction $transaction): void
    {
        $this->ledgerPostingService->post($transaction, $this->journalHeader($transaction), $this->journalLines($transaction));
    }

    /**
     * @return array{tr_no: ?string, date: string, general_particular: ?string}
     */
    private function journalHeader(IncomeExpenseTransaction $transaction): array
    {
        return [
            'tr_no' => $transaction->reference_no,
            'date' => $transaction->transaction_date->toDateString(),
            'general_particular' => $transaction->description,
        ];
    }

    /**
     * Income debits the payment (asset) account and credits the revenue
     * account; expense debits the expense account and credits the payment
     * account, as the payroll module's daily expense and cash receive do.
     *
     * @return array<int, array{account_id: int, debit?: float, credit?: float, particular: string}>
     */
    private function journalLines(IncomeExpenseTransaction $transaction): array
    {
        $amount = (float) $transaction->amount;

        [$debitAccountId, $creditAccountId] = $transaction->type === 'income'
            ? [$transaction->payment_account_id, $transaction->income_expense_account_id]
            : [$transaction->income_expense_account_id, $transaction->payment_account_id];

        $particular = $transaction->type === 'income' ? 'daily income' : 'daily expense';

        return [
            ['account_id' => $debitAccountId, 'debit' => $amount, 'particular' => $particular],
            ['account_id' => $creditAccountId, 'credit' => $amount, 'particular' => $particular],
        ];
    }
}
