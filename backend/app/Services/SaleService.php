<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(private readonly CustomerTransactionService $customerTransactionService) {}

    public function paginate(int $perPage = 15, ?string $search = null, ?int $customerId = null): LengthAwarePaginator
    {
        return Sale::query()
            ->with('customer:id,name')
            ->when($search, fn ($query) => $query->where('invoice_number', 'like', "%{$search}%"))
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId))
            ->latest('issue_date')
            ->paginate($perPage);
    }

    public function find(Sale $sale): Sale
    {
        return $sale->load(['items', 'customer']);
    }

    /**
     * Create a sale invoice, its line items, and the "Invoice" ledger entry
     * it posts to the customer's subsidiary ledger — all totals are computed
     * here from `items` rather than trusted from the request.
     *
     * @param  array{customer_id: int, invoice_number: string, description?: ?string, issue_date: string, due_date?: ?string, notes?: ?string, items: array<int, array{item_name: string, description?: ?string, qty: float, unit_price: float, discount?: ?float, vat?: ?float}>}  $data
     */
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $totals = LineItemTotalCalculator::calculate($data['items']);

            $sale = Sale::create([
                'customer_id' => $data['customer_id'],
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => $totals['total_amount'],
                'discount_amount' => $totals['discount_amount'],
                'vat_amount' => $totals['vat_amount'],
                'net_total' => $totals['net_total'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $sale->items()->createMany($totals['items']);

            $customer = Customer::findOrFail($data['customer_id']);

            $transaction = $this->customerTransactionService->create($customer, [
                'transaction_type' => 'Invoice',
                'invoice_no' => $data['invoice_number'],
                'debit' => $totals['net_total'],
                'transaction_date' => $data['issue_date'],
                'notes' => $data['description'] ?? null,
            ]);

            $sale->update(['customer_transaction_id' => $transaction->id]);

            return $sale->load(['items', 'customer']);
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
                    'items' => "The new total ({$totals['net_total']}) cannot be less than the amount already paid ({$sale->paid_amount}).",
                ]);
            }

            $sale->fill([
                ...collect($data)->except('items')->all(),
                'updated_by' => Auth::id(),
            ]);

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

            if ($sale->ledgerTransaction) {
                $this->customerTransactionService->updateAmounts($sale->ledgerTransaction, [
                    'invoice_no' => $sale->invoice_number,
                    'debit' => $sale->net_total,
                    'transaction_date' => $sale->issue_date,
                    'notes' => $sale->description,
                ]);
            }

            return $sale->load(['items', 'customer']);
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

            $sale->delete();
        });
    }

    /**
     * Record a payment received against a sale, posting a credit ledger
     * entry that reduces what the customer owes and tracking how much of
     * the sale has been paid so far.
     *
     * @param  array{amount: float, payment_date: string, notes?: ?string}  $data
     */
    public function recordPayment(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $this->customerTransactionService->create($sale->customer, [
                'transaction_type' => 'Payment Received',
                'invoice_no' => $sale->invoice_number,
                'credit' => $data['amount'],
                'transaction_date' => $data['payment_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $sale->update([
                'paid_amount' => $sale->paid_amount + $data['amount'],
                'updated_by' => Auth::id(),
            ]);

            return $sale->load(['items', 'customer']);
        });
    }
}
