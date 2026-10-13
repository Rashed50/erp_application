<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(
        private readonly CustomerTransactionService $customerTransactionService,
        private readonly LedgerPostingService $ledgerPostingService,
    ) {}

    public function paginate(int $perPage = 15, ?string $search = null, ?int $customerId = null): LengthAwarePaginator
    {
        return Sale::query()
            ->with(['customer:id,name', 'workOrder:id,work_order_no'])
            ->when($search, fn ($query) => $query->where('invoice_number', 'like', "%{$search}%"))
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId))
            ->latest('issue_date')
            ->paginate($perPage);
    }

    public function find(Sale $sale): Sale
    {
        return $sale->load(['items', 'customer', 'workOrder']);
    }

    /**
     * Create a sale invoice, its line items, its journal entry, and the
     * "Invoice" ledger entry it posts to the customer's subsidiary ledger —
     * all totals are computed here from `items` rather than trusted from the
     * request. As on the payroll module's sale form, the debit and credit
     * accounts can be chosen; they default to Accounts Receivable and Sales
     * Revenue.
     *
     * @param  array{customer_id: int, work_order_id?: ?int, invoice_number: string, description?: ?string, issue_date: string, due_date?: ?string, notes?: ?string, debit_account_id?: ?int, credit_account_id?: ?int, items: array<int, array{item_name: string, description?: ?string, qty: float, unit_price: float, discount?: ?float, vat?: ?float}>}  $data
     */
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $totals = LineItemTotalCalculator::calculate($data['items']);

            $sale = Sale::create([
                'customer_id' => $data['customer_id'],
                'work_order_id' => $data['work_order_id'] ?? null,
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'debit_account_id' => $data['debit_account_id'] ?? $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_RECEIVABLE_NUMBER)?->id,
                'credit_account_id' => $data['credit_account_id'] ?? $this->ledgerPostingService->predefinedAccount(LedgerPostingService::SALES_REVENUE_NUMBER)?->id,
                'total_amount' => $totals['total_amount'],
                'discount_amount' => $totals['discount_amount'],
                'vat_amount' => $totals['vat_amount'],
                'net_total' => $totals['net_total'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $sale->items()->createMany($totals['items']);

            $customer = Customer::findOrFail($data['customer_id']);
            $entry = $this->postToLedger($sale);

            $transaction = $this->customerTransactionService->create($customer, [
                'transaction_type' => 'Invoice',
                'work_order_id' => $sale->work_order_id,
                'invoice_no' => $data['invoice_number'],
                'debit' => $totals['net_total'],
                'account_transaction_id' => $entry?->id,
                'transaction_date' => $data['issue_date'],
                'notes' => $data['description'] ?? null,
            ]);

            $sale->update(['customer_transaction_id' => $transaction->id]);

            return $sale->load(['items', 'customer', 'workOrder']);
        });
    }

    /**
     * Update a sale invoice. When `items` is supplied, its line items are
     * replaced and every total is recomputed; either way, the linked ledger
     * entry is kept in sync in place.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $totals = isset($data['items']) ? LineItemTotalCalculator::calculate($data['items']) : null;

            if ($totals && $totals['net_total'] < $sale->paid_amount) {
                throw ValidationException::withMessages([
                    'items' => __('The new total (:total) cannot be less than the amount already paid (:paid).', ['total' => $totals['net_total'], 'paid' => $sale->paid_amount]),
                ]);
            }

            $sale->fill([
                ...collect($data)->except('items')->all(),
                'updated_by' => Auth::id(),
            ]);

            $sale->debit_account_id ??= $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_RECEIVABLE_NUMBER)?->id;
            $sale->credit_account_id ??= $this->ledgerPostingService->predefinedAccount(LedgerPostingService::SALES_REVENUE_NUMBER)?->id;

            if ($totals) {
                $sale->total_amount = $totals['total_amount'];
                $sale->discount_amount = $totals['discount_amount'];
                $sale->vat_amount = $totals['vat_amount'];
                $sale->net_total = $totals['net_total'];
            }

            $sale->save();

            if ($totals) {
                $sale->items()->delete();
                $sale->items()->createMany($totals['items']);
            }

            // Rewrite the journal entry in place, or post it now if the sale was
            // created before the predefined accounts were seeded.
            if ($entry = $this->ledgerPostingService->entryFor($sale)) {
                $this->ledgerPostingService->repost($entry, $this->journalHeader($sale), $this->journalLines($sale));
            } else {
                $entry = $this->postToLedger($sale);
            }

            if ($sale->ledgerTransaction) {
                $this->customerTransactionService->updateAmounts($sale->ledgerTransaction, [
                    'work_order_id' => $sale->work_order_id,
                    'invoice_no' => $sale->invoice_number,
                    'debit' => $sale->net_total,
                    'account_transaction_id' => $entry?->id,
                    'transaction_date' => $sale->issue_date,
                    'notes' => $sale->description,
                ]);
            }

            return $sale->load(['items', 'customer', 'workOrder']);
        });
    }

    /**
     * Soft-delete a sale and reverse the ledger entry it posted, so the
     * customer's balance no longer reflects an invoice that no longer exists.
     */
    public function delete(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            if ($sale->ledgerTransaction && $sale->ledgerTransaction->status) {
                $this->customerTransactionService->reverse($sale->ledgerTransaction);
            }

            $this->ledgerPostingService->reverse($sale);

            $sale->delete();
        });
    }

    /**
     * Record a payment received against a sale, posting a credit ledger
     * entry that reduces what the customer owes and tracking how much of
     * the sale has been paid so far. The payment is also posted to the
     * general ledger: debit the cash/bank account, credit Accounts Receivable.
     *
     * @param  array{payment_account_id: int, amount: float, payment_date: string, notes?: ?string}  $data
     */
    public function recordPayment(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $this->customerTransactionService->create($sale->customer, [
                'transaction_type' => 'Payment Received',
                'work_order_id' => $sale->work_order_id,
                'invoice_no' => $sale->invoice_number,
                'credit' => $data['amount'],
                'payment_account_id' => $data['payment_account_id'],
                'sale_id' => $sale->id,
                'transaction_date' => $data['payment_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $sale->update([
                'paid_amount' => $sale->paid_amount + $data['amount'],
                'updated_by' => Auth::id(),
            ]);

            return $sale->load(['items', 'customer', 'workOrder']);
        });
    }

    /**
     * Post the sale's journal entry: debit its debit account (Accounts
     * Receivable by default) and credit its credit account (Sales Revenue by
     * default) with the net total, mirroring the debit the sale adds to the
     * customer ledger.
     *
     * Returns null without posting when either account is unknown (the
     * predefined accounts have not been seeded), so sales still work before
     * the chart is set up, as they do in the payroll module.
     */
    private function postToLedger(Sale $sale): ?AccountTransaction
    {
        if (! $sale->debit_account_id || ! $sale->credit_account_id) {
            return null;
        }

        return $this->ledgerPostingService->post($sale, $this->journalHeader($sale), $this->journalLines($sale));
    }

    /**
     * @return array{tr_no: string, date: string, general_particular: ?string, sale_id: int}
     */
    private function journalHeader(Sale $sale): array
    {
        return [
            'tr_no' => $sale->invoice_number,
            'date' => $sale->issue_date->toDateString(),
            'general_particular' => $sale->notes,
            'sale_id' => $sale->id,
        ];
    }

    /**
     * @return array<int, array{account_id: int, debit?: float, credit?: float, particular: string}>
     */
    private function journalLines(Sale $sale): array
    {
        $amount = (float) $sale->net_total;

        return [
            ['account_id' => $sale->debit_account_id, 'debit' => $amount, 'particular' => "{$amount} amount debit from {$sale->debit_account_id}"],
            ['account_id' => $sale->credit_account_id, 'credit' => $amount, 'particular' => "{$amount} amount credit from {$sale->credit_account_id}"],
        ];
    }
}
