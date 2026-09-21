<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(private readonly SupplierTransactionService $supplierTransactionService) {}

    public function paginate(int $perPage = 15, ?string $search = null, ?int $supplierId = null): LengthAwarePaginator
    {
        return Purchase::query()
            ->with('supplier:id,name')
            ->when($search, fn ($query) => $query->where('invoice_number', 'like', "%{$search}%"))
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->latest('purchase_date')
            ->paginate($perPage);
    }

    public function find(Purchase $purchase): Purchase
    {
        return $purchase->load(['items', 'supplier']);
    }

    /**
     * Create a purchase invoice, its line items, and the "Purchase" ledger
     * entry it posts to the supplier's subsidiary ledger — all totals are
     * computed here from `items` rather than trusted from the request.
     *
     * @param  array{supplier_id: int, purchase_type: string, invoice_number: string, description?: ?string, issue_date: string, purchase_date: string, notes?: ?string, items: array<int, array{item_name: string, description?: ?string, qty: float, unit_price: float, discount?: ?float, vat?: ?float}>}  $data
     */
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $totals = LineItemTotalCalculator::calculate($data['items']);

            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'purchase_type' => $data['purchase_type'],
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'issue_date' => $data['issue_date'],
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => $totals['total_amount'],
                'discount_amount' => $totals['discount_amount'],
                'vat_amount' => $totals['vat_amount'],
                'net_total' => $totals['net_total'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $purchase->items()->createMany($totals['items']);

            $supplier = Supplier::findOrFail($data['supplier_id']);

            $transaction = $this->supplierTransactionService->create($supplier, [
                'transaction_type' => 'Purchase',
                'invoice_no' => $data['invoice_number'],
                'credit' => $totals['net_total'],
                'transaction_date' => $data['purchase_date'],
                'notes' => $data['description'] ?? null,
            ]);

            $purchase->update(['supplier_transaction_id' => $transaction->id]);

            return $purchase->load(['items', 'supplier']);
        });
    }

    /**
     * Update a purchase invoice. When `items` is supplied, its line items are
     * replaced and every total is recomputed; either way, the linked ledger
     * entry is kept in sync in place, rather than left to drift the way the
     * source module's purchase edit did.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $totals = isset($data['items']) ? LineItemTotalCalculator::calculate($data['items']) : null;

            if ($totals && $totals['net_total'] < $purchase->paid_amount) {
                throw ValidationException::withMessages([
                    'items' => "The new total ({$totals['net_total']}) cannot be less than the amount already paid ({$purchase->paid_amount}).",
                ]);
            }

            $purchase->fill([
                ...collect($data)->except('items')->all(),
                'updated_by' => Auth::id(),
            ]);

            if ($totals) {
                $purchase->total_amount = $totals['total_amount'];
                $purchase->discount_amount = $totals['discount_amount'];
                $purchase->vat_amount = $totals['vat_amount'];
                $purchase->net_total = $totals['net_total'];
            }

            $purchase->save();

            if ($totals) {
                $purchase->items()->delete();
                $purchase->items()->createMany($totals['items']);
            }

            if ($purchase->ledgerTransaction) {
                $this->supplierTransactionService->updateAmounts($purchase->ledgerTransaction, [
                    'invoice_no' => $purchase->invoice_number,
                    'credit' => $purchase->net_total,
                    'transaction_date' => $purchase->purchase_date,
                    'notes' => $purchase->description,
                ]);
            }

            return $purchase->load(['items', 'supplier']);
        });
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

            $purchase->delete();
        });
    }

    /**
     * Record a payment against a purchase (a "Bill Payment"), posting a debit
     * ledger entry that reduces what's owed to the supplier and tracking how
     * much of the purchase has been paid so far.
     *
     * @param  array{amount: float, payment_date: string, notes?: ?string}  $data
     */
    public function recordPayment(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $this->supplierTransactionService->create($purchase->supplier, [
                'transaction_type' => 'Bill Payment',
                'invoice_no' => $purchase->invoice_number,
                'debit' => $data['amount'],
                'transaction_date' => $data['payment_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $purchase->update([
                'paid_amount' => $purchase->paid_amount + $data['amount'],
                'updated_by' => Auth::id(),
            ]);

            return $purchase->load(['items', 'supplier']);
        });
    }
}
