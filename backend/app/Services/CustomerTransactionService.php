<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerTransactionService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

    public function listForCustomer(Customer $customer, int $perPage = 15, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $customer->transactions()
            ->with('workOrder:id,work_order_no')
            ->when($fromDate && $toDate, fn ($query) => $query->whereBetween('transaction_date', [$fromDate, $toDate]))
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    /**
     * When `payment_account_id` is given (a payment received), the credit is
     * also posted to the general ledger as a journal entry: debit that
     * cash/bank account and credit Accounts Receivable. `account_transaction_id`
     * links an entry to a journal entry posted elsewhere (a sale invoice), and
     * `sale_id` tags a payment's journal entry with the sale it settles.
     *
     * @param  array{transaction_type: string, work_order_id?: ?int, invoice_no?: ?string, debit?: ?float, credit?: ?float, payment_account_id?: ?int, account_transaction_id?: ?int, sale_id?: ?int, transaction_date: string, notes?: ?string}  $data
     */
    public function create(Customer $customer, array $data): CustomerTransaction
    {
        return DB::transaction(function () use ($customer, $data) {
            $transaction = $customer->transactions()->create([
                'transaction_type' => $data['transaction_type'],
                'work_order_id' => $data['work_order_id'] ?? null,
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
                'branch_office_id' => $customer->branch_office_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->postPaymentToLedger($transaction, $data['sale_id'] ?? null);
            $this->recalculateBalance($customer);

            return $transaction;
        });
    }

    /**
     * Update an existing ledger entry's amounts in place (rather than
     * reversing and recreating it) and refresh the owning customer's balance.
     * Used by SaleService to keep a sale's linked ledger entry in sync when
     * the sale itself is edited.
     *
     * @param  array{transaction_type?: string, work_order_id?: ?int, invoice_no?: ?string, debit?: ?float, credit?: ?float, transaction_date?: string, notes?: ?string}  $data
     */
    public function updateAmounts(CustomerTransaction $transaction, array $data): CustomerTransaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $transaction->fill([...$data, 'updated_by' => Auth::id()]);
            $transaction->save();

            $this->recalculateBalance($transaction->customer);

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
            if ($transaction->status && $transaction->payment_account_id) {
                $this->ledgerPostingService->reverse($transaction);
            }

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

    /**
     * Post a received payment's journal entry and link it to the ledger
     * entry. Entries without a payment account are not posted.
     */
    private function postPaymentToLedger(CustomerTransaction $transaction, ?int $saleId): void
    {
        if (! $transaction->payment_account_id) {
            return;
        }

        $amount = (float) $transaction->credit;
        $receivable = $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_RECEIVABLE_NUMBER)
            ?? throw new \RuntimeException('Predefined account '.LedgerPostingService::ACCOUNTS_RECEIVABLE_NUMBER.' is missing.');

        $entry = $this->ledgerPostingService->post($transaction, [
            'tr_no' => $transaction->invoice_no,
            'date' => $transaction->transaction_date->toDateString(),
            'general_particular' => $transaction->notes,
            'sale_id' => $saleId,
        ], [
            ['account_id' => $transaction->payment_account_id, 'debit' => $amount, 'particular' => "{$amount} dr {$transaction->payment_account_id}"],
            ['account_id' => $receivable->id, 'credit' => $amount, 'particular' => "{$amount} cr {$receivable->id}"],
        ]);

        $transaction->update(['account_transaction_id' => $entry->id]);
    }

    private function today(): string
    {
        return now()->toDateString();
    }
}
