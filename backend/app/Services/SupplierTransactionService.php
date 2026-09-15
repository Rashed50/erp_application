<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierTransactionService
{
    public function listForSupplier(Supplier $supplier, int $perPage = 15, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $supplier->transactions()
            ->when($fromDate && $toDate, fn ($query) => $query->whereBetween('transaction_date', [$fromDate, $toDate]))
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    /**
     * Post a new ledger entry for a supplier and refresh its running balance.
     *
     * Used both for a manually recorded transaction (e.g. a standalone bill
     * payment) and internally by PurchaseService to post the "Purchase" entry
     * a new purchase invoice generates.
     *
     * @param  array{transaction_type: string, invoice_no?: ?string, debit?: ?float, credit?: ?float, transaction_date: string, notes?: ?string}  $data
     */
    public function create(Supplier $supplier, array $data): SupplierTransaction
    {
        return DB::transaction(function () use ($supplier, $data) {
            $transaction = $supplier->transactions()->create([
                'transaction_type' => $data['transaction_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'debit' => $data['debit'] ?? 0,
                'credit' => $data['credit'] ?? 0,
                'transaction_date' => $data['transaction_date'],
                'notes' => $data['notes'] ?? null,
                // Set explicitly rather than relying on the DB column default,
                // which the in-memory model wouldn't see until a fresh fetch.
                'status' => true,
                'branch_office_id' => $supplier->branch_office_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->recalculateBalance($supplier);

            return $transaction;
        });
    }

    /**
     * Update an existing ledger entry's amounts in place (rather than
     * reversing and recreating it) and refresh the owning supplier's balance.
     * Used by PurchaseService to keep a purchase's linked ledger entry in
     * sync when the purchase itself is edited.
     *
     * @param  array{transaction_type?: string, invoice_no?: ?string, debit?: ?float, credit?: ?float, transaction_date?: string, notes?: ?string}  $data
     */
    public function updateAmounts(SupplierTransaction $transaction, array $data): SupplierTransaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $transaction->fill([...$data, 'updated_by' => Auth::id()]);
            $transaction->save();

            $this->recalculateBalance($transaction->supplier);

            return $transaction;
        });
    }

    /**
     * Reverse a transaction's effect on the supplier's balance instead of
     * deleting it, so the ledger keeps a full, auditable history.
     */
    public function reverse(SupplierTransaction $transaction): SupplierTransaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->status = false;
            $transaction->notes = trim(($transaction->notes ? $transaction->notes."\n" : '')."Reversed on {$this->today()}.");
            $transaction->updated_by = Auth::id();
            $transaction->save();

            $this->recalculateBalance($transaction->supplier);

            return $transaction;
        });
    }

    /**
     * Recompute the supplier's running balance from its opening balance plus
     * every non-reversed ledger entry, rather than trusting incremental
     * updates that could drift out of sync.
     *
     * A credit increases what is owed to the supplier (a purchase); a debit
     * decreases it (a bill payment) — the inverse of the customer ledger,
     * where a debit is what is owed *to* the business.
     */
    private function recalculateBalance(Supplier $supplier): void
    {
        $totals = $supplier->transactions()
            ->where('status', true)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $supplier->update([
            'current_balance' => $supplier->opening_balance + ($totals->total_credit ?? 0) - ($totals->total_debit ?? 0),
        ]);
    }

    private function today(): string
    {
        return now()->toDateString();
    }
}
