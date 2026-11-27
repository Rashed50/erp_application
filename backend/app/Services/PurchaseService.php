<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\Purchase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(
        private readonly SupplierTransactionService $supplierTransactionService,
        private readonly LedgerPostingService $ledgerPostingService,
        private readonly AttachmentService $attachmentService,
        private readonly ApprovalService $approvalService,
    ) {}

    public function paginate(int $perPage = 15, ?string $search = null, ?int $supplierId = null, ?bool $approved = null): LengthAwarePaginator
    {
        return Purchase::query()
            ->with(['supplier:id,name', 'workOrder:id,work_order_no', 'purchasedBy:id,employee_code,name', 'creator:id,name', 'approver:id,name'])
            ->withLineTotals()
            ->withPaidAmount()
            ->when($search, fn ($query) => $query->where('invoice_number', 'like', "%{$search}%"))
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->approvalStatus($approved)
            ->latest('purchase_date')
            ->paginate($perPage);
    }

    public function find(Purchase $purchase): Purchase
    {
        return $purchase->load(['items.unit', 'supplier', 'workOrder', 'purchasedBy', 'creator:id,name']);
    }

    /**
     * Create a purchase invoice and its line items — all totals are computed
     * here from `items` rather than trusted from the request. It waits for
     * approval: its journal entry and the "Purchase" entry on the supplier's
     * subsidiary ledger are posted by approve(). As on the payroll module's bill form, the debit and credit
     * accounts can be chosen; the debit defaults to Inventory/Stock for a
     * product purchase and Purchase for a service one, the credit to
     * Accounts Payable.
     *
     * @param  array{supplier_id: int, work_order_id?: ?int, purchased_by_id?: ?int, purchase_type: string, invoice_number: string, description?: ?string, issue_date: string, purchase_date: string, notes?: ?string, debit_account_id?: ?int, credit_account_id?: ?int, overall_discount?: ?float, overall_vat?: ?float, attachment?: ?UploadedFile, items: array<int, array{item_name: string, description?: ?string, qty: float, unit_id?: ?int, unit_price: float, discount?: ?float, vat?: ?float}>}  $data
     */
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $totals = LineItemTotalCalculator::calculate(
                $data['items'],
                (float) ($data['overall_discount'] ?? 0),
                (float) ($data['overall_vat'] ?? 0),
            );

            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'work_order_id' => $data['work_order_id'] ?? null,
                'purchased_by_id' => $data['purchased_by_id'] ?? null,
                'purchase_type' => $data['purchase_type'],
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'issue_date' => $data['issue_date'],
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes'] ?? null,
                'attachment' => $this->attachmentService->store($data['attachment'] ?? null, 'purchases'),
                'debit_account_id' => $data['debit_account_id'] ?? $this->defaultDebitAccountId($data['purchase_type']),
                'credit_account_id' => $data['credit_account_id'] ?? $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER)?->id,
                'overall_discount' => $totals['overall_discount'],
                'overall_vat' => $totals['overall_vat'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $purchase->items()->createMany($totals['items']);

            return $purchase->load(['items.unit', 'supplier', 'workOrder', 'purchasedBy']);
        });
    }

    /**
     * Update a purchase invoice. When `items` is supplied, its line items are
     * replaced and every total is recomputed. The edit sends the purchase back
     * for approval, so its journal entry and supplier ledger entry are taken
     * off the books until it is approved again.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $totals = $this->recalculatedTotals($purchase, $data);

            if ($totals && $totals['net_total'] < $purchase->paid_amount) {
                throw ValidationException::withMessages([
                    'items' => __('The new total (:total) cannot be less than the amount already paid (:paid).', ['total' => $totals['net_total'], 'paid' => $purchase->paid_amount]),
                ]);
            }

            $purchase->fill([
                ...collect($data)->except(['items', 'overall_discount', 'overall_vat', ...AttachmentService::FIELDS])->all(),
                'updated_by' => Auth::id(),
            ]);

            $this->attachmentService->sync($purchase, $data, 'purchases');

            $purchase->debit_account_id ??= $this->defaultDebitAccountId($purchase->purchase_type);
            $purchase->credit_account_id ??= $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER)?->id;

            if ($totals) {
                $purchase->overall_discount = $totals['overall_discount'];
                $purchase->overall_vat = $totals['overall_vat'];
            }

            // The edit sends the purchase back for approval and takes it off the books.
            $purchase->resetApproval();
            $purchase->save();

            if (isset($data['items'])) {
                $purchase->items()->delete();
                $purchase->items()->createMany($totals['items']);
                $purchase->unsetRelation('items');
            }

            $this->unpost($purchase);

            return $purchase->load(['items.unit', 'supplier', 'workOrder', 'purchasedBy']);
        });
    }

    /**
     * Approve the purchase and post it: its journal entry (debit Inventory or
     * Purchase, credit Accounts Payable by default) and the "Purchase" credit
     * on the supplier's subsidiary ledger.
     */
    public function approve(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $this->approvalService->approve($purchase);

            // Rewrite the journal entry in place, or post it now.
            if ($entry = $this->ledgerPostingService->entryFor($purchase)) {
                $this->ledgerPostingService->repost($entry, $this->journalHeader($purchase), $this->journalLines($purchase));
            } else {
                $entry = $this->postToLedger($purchase);
            }

            $bill = [
                'invoice_no' => $purchase->invoice_number,
                'credit' => $purchase->net_total,
                'account_transaction_id' => $entry?->id,
                'transaction_date' => $purchase->purchase_date->toDateString(),
                'notes' => $purchase->description,
            ];

            if ($purchase->ledgerTransaction) {
                $this->supplierTransactionService->updateAmounts($purchase->ledgerTransaction, [...$bill, 'status' => true]);
            } else {
                $transaction = $this->supplierTransactionService->create($purchase->supplier, ['transaction_type' => 'Purchase', ...$bill]);
                $purchase->update(['supplier_transaction_id' => $transaction->id]);
            }

            return $purchase->load(['items.unit', 'supplier', 'workOrder', 'purchasedBy']);
        });
    }

    /**
     * Take a purchase that is no longer approved off the books: remove its
     * journal entry and switch off its supplier ledger entry.
     */
    private function unpost(Purchase $purchase): void
    {
        $this->ledgerPostingService->reverse($purchase);

        if ($purchase->ledgerTransaction?->status) {
            $this->supplierTransactionService->updateAmounts($purchase->ledgerTransaction, ['status' => false, 'account_transaction_id' => null]);
        }
    }

    /**
     * Totals recomputed for an update: when the line items or the overall
     * discount/VAT change. Whichever of them is not sent is taken from the
     * purchase as it is now. Returns null when none of them changed.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    private function recalculatedTotals(Purchase $purchase, array $data): ?array
    {
        if (! isset($data['items']) && ! array_key_exists('overall_discount', $data) && ! array_key_exists('overall_vat', $data)) {
            return null;
        }

        return LineItemTotalCalculator::calculate(
            $data['items'] ?? $purchase->items->toArray(),
            (float) (array_key_exists('overall_discount', $data) ? $data['overall_discount'] : $purchase->overall_discount),
            (float) (array_key_exists('overall_vat', $data) ? $data['overall_vat'] : $purchase->overall_vat),
        );
    }

    /**
     * Soft-delete a purchase and reverse the ledger entry it posted, so the
     * supplier's balance no longer reflects an invoice that no longer exists.
     */
    public function delete(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->ledgerTransaction && $purchase->ledgerTransaction->status) {
                $this->supplierTransactionService->reverse($purchase->ledgerTransaction);
            }

            $this->ledgerPostingService->reverse($purchase);

            $purchase->delete();
        });
    }

    /**
     * The payroll bill form preselects Inventory/Stock for a product purchase
     * and the first expense account (Purchase) for a service purchase.
     */
    private function defaultDebitAccountId(string $purchaseType): ?int
    {
        $accountNumber = $purchaseType === 'product' ? LedgerPostingService::INVENTORY_NUMBER : LedgerPostingService::PURCHASE_NUMBER;

        return $this->ledgerPostingService->predefinedAccount($accountNumber)?->id;
    }

    /**
     * Post the purchase's journal entry: debit its debit account and credit
     * its credit account (Accounts Payable by default) with the net total,
     * mirroring the credit the purchase adds to the supplier ledger.
     *
     * Returns null without posting when either account is unknown (the
     * predefined accounts have not been seeded), so purchases still work
     * before the chart is set up, as they do in the payroll module.
     */
    private function postToLedger(Purchase $purchase): ?AccountTransaction
    {
        if (! $purchase->debit_account_id || ! $purchase->credit_account_id) {
            return null;
        }

        return $this->ledgerPostingService->post($purchase, $this->journalHeader($purchase), $this->journalLines($purchase));
    }

    /**
     * @return array{tr_no: string, date: string, general_particular: ?string}
     */
    private function journalHeader(Purchase $purchase): array
    {
        return [
            'tr_no' => $purchase->invoice_number,
            'date' => $purchase->purchase_date->toDateString(),
            'general_particular' => $purchase->notes,
        ];
    }

    /**
     * @return array<int, array{account_id: int, debit?: float, credit?: float, particular: string}>
     */
    private function journalLines(Purchase $purchase): array
    {
        $amount = (float) $purchase->net_total;

        return [
            ['account_id' => $purchase->debit_account_id, 'debit' => $amount, 'particular' => "{$amount} amount debit from {$purchase->debit_account_id}"],
            ['account_id' => $purchase->credit_account_id, 'credit' => $amount, 'particular' => "{$amount} cr from {$purchase->credit_account_id}"],
        ];
    }
}
