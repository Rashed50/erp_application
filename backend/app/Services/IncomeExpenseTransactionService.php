<?php

namespace App\Services;

use App\Models\IncomeExpenseTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class IncomeExpenseTransactionService
{
    public function paginate(int $perPage = 15, ?string $type = null, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return IncomeExpenseTransaction::query()
            ->with(['account:id,name,type', 'paymentAccount:id,name,type'])
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
        $transaction = IncomeExpenseTransaction::create([
            ...$data,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $transaction->load(['account', 'paymentAccount']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(IncomeExpenseTransaction $transaction, array $data): IncomeExpenseTransaction
    {
        $transaction->fill([...$data, 'updated_by' => Auth::id()]);
        $transaction->save();

        return $transaction->load(['account', 'paymentAccount']);
    }

    public function delete(IncomeExpenseTransaction $transaction): void
    {
        $transaction->delete();
    }
}
