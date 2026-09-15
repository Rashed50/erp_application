<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerTransactionService
{
    public function listForCustomer(Customer $customer, int $perPage = 15, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $customer->transactions()
            ->when($fromDate && $toDate, fn ($query) => $query->whereBetween('transaction_date', [$fromDate, $toDate]))
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    /**
     * @param  array{transaction_type: string, invoice_no?: ?string, debit?: ?float, credit?: ?float, transaction_date: string, notes?: ?string}  $data
     */
    public function create(Customer $customer, array $data): CustomerTransaction
    {
        return DB::transaction(function () use ($customer, $data) {
            $transaction = $customer->transactions()->create([
                'transaction_type' => $data['transaction_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'debit' => $data['debit'] ?? 0,
                'credit' => $data['credit'] ?? 0,
                'transaction_date' => $data['transaction_date'],
                'notes' => $data['notes'] ?? null,
                'branch_office_id' => $customer->branch_office_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->recalculateBalance($customer);

            return $transaction;
        });
    }

    /**
     * Reverse a transaction's effect on the customer's balance instead of
     * deleting it, so the ledger keeps a full, auditable history.
     */
    public function reverse(CustomerTransaction $transaction): CustomerTransaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->status = false;
            $transaction->notes = trim(($transaction->notes ? $transaction->notes."\n" : '')."Reversed on {$this->today()}.");
            $transaction->updated_by = Auth::id();
            $transaction->save();

            $this->recalculateBalance($transaction->customer);

            return $transaction;
        });
    }

    /**
     * Recompute the customer's running balance from its opening balance plus
     * every non-reversed ledger entry, rather than trusting incremental
     * updates that could drift out of sync.
     */
    private function recalculateBalance(Customer $customer): void
    {
        $totals = $customer->transactions()
            ->where('status', true)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $customer->update([
            'current_balance' => $customer->opening_balance + ($totals->total_debit ?? 0) - ($totals->total_credit ?? 0),
        ]);
    }

    private function today(): string
    {
        return now()->toDateString();
    }
}
