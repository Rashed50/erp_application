<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierTransactionService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

    public function listForSupplier(Supplier $supplier, int $perPage = 15, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $supplier->transactions()
            ->with('creator:id,name')
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
     * `account_transaction_id` links the entry to the journal entry that
     * posted it to the general ledger, if any.
     *
     * @param  array{transaction_type: string, invoice_no?: ?string, debit?: ?float, credit?: ?float, payment_account_id?: ?int, account_transaction_id?: ?int, transaction_date: string, notes?: ?string}  $data
     */
    public function create(Supplier $supplier, array $data): SupplierTransaction
    {
        return DB::transaction(function () use ($supplier, $data) {
            $transaction = $supplier->transactions()->create([
                'transaction_type' => $data['transaction_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'debit' => $data['debit'] ?? 0,
                'credit' => $data['credit'] ?? 0,
                'payment_account_id' => $data['payment_account_id'] ?? null,
                'account_transaction_id' => $data['account_transaction_id'] ?? null,
                'transaction_date' => $data['transaction_date'],
                'notes' => $data['notes'] ?? null,
                // Set explicitly rather than relying on the DB column default,
                // which the in-memory model wouldn't see until a fresh fetch.
                'status' => true,
                'branch_office_id' => $supplier->branch_office_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->postBillPaymentToLedger($transaction);

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
            if ($transaction->status && $transaction->payment_account_id) {
                $this->ledgerPostingService->reverse($transaction);
            }

            $transaction->status = false;
            $transaction->notes = trim(($transaction->notes ? $transaction->notes."\n" : '')."Reversed on {$this->today()}.");
            $transaction->updated_by = Auth::id();
            $transaction->save();

            return $transaction;
        });
    }

    private function today(): string
    {
        return now()->toDateString();
    }

    /**
     * Post a standalone bill payment to the general ledger and link it to the
     * supplier ledger entry.
     */
    private function postBillPaymentToLedger(SupplierTransaction $transaction): void
    {
        if (! $transaction->payment_account_id) {
            return;
        }

        $amount = (float) $transaction->debit;
        $payable = $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER)
            ?? throw new \RuntimeException('Predefined account '.LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER.' is missing.');

        $entry = $this->ledgerPostingService->post($transaction, [
            'tr_no' => $transaction->invoice_no,
            'date' => $transaction->transaction_date->toDateString(),
            'general_particular' => $transaction->notes,
        ], [
            ['account_id' => $payable->id, 'debit' => $amount, 'particular' => "{$amount} dr {$payable->id}"],
            ['account_id' => $transaction->payment_account_id, 'credit' => $amount, 'particular' => "{$amount} cr {$transaction->payment_account_id}"],
        ]);

        $transaction->update(['account_transaction_id' => $entry->id]);
    }
}
