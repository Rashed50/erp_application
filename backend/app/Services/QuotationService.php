<?php

namespace App\Services;

use App\Models\Quotation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function paginate(int $perPage = 15, ?string $search = null, ?int $customerId = null): LengthAwarePaginator
    {
        return Quotation::query()
            ->with(['customer:id,name'])
            ->when($search, fn($query) => $query->where('invoice_number', 'like', "%{$search}%"))
            ->when($customerId, fn($query) => $query->where('customer_id', $customerId))
            ->latest('issue_date')
            ->paginate($perPage);
    }

    public function find(Quotation $quotation): Quotation
    {
        return $quotation->load(['items.unit', 'customer']);
    }

    /**
     * Create a quotation and its line items. All totals are computed here from
     * `items` rather than trusted from the request.
     *
     * Unlike a sale, a quotation is only an offer: it posts nothing to the
     * general ledger or the customer's ledger, and it has no payments.
     *
     * @param  array{customer_id: int, invoice_number: string, description?: ?string, issue_date: string, notes?: ?string, items: array<int, array{item_name: string, description?: ?string, unit_id?: ?int, count?: ?float, weight?: ?float, qty: float, unit_price: float, discount?: ?float, vat?: ?float}>}  $data
     */
    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $totals = LineItemTotalCalculator::calculate($data['items']);

            $quotation = Quotation::create([
                'customer_id' => $data['customer_id'],
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'issue_date' => $data['issue_date'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => $totals['total_amount'],
                'discount_amount' => $totals['discount_amount'],
                'vat_amount' => $totals['vat_amount'],
                'net_total' => $totals['net_total'],
                'branch_office_id' => $data['branch_office_id'],

                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $quotation->items()->createMany($totals['items']);

            return $quotation->load(['items.unit', 'customer']);
        });
    }

    /**
     * Update a quotation. When `items` is supplied, its line items are
     * replaced and every total is recomputed.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            $totals = isset($data['items']) ? LineItemTotalCalculator::calculate($data['items']) : null;

            $quotation->fill([
                ...collect($data)->except('items')->all(),
                'updated_by' => Auth::id(),
            ]);

            if ($totals) {
                $quotation->total_amount = $totals['total_amount'];
                $quotation->discount_amount = $totals['discount_amount'];
                $quotation->vat_amount = $totals['vat_amount'];
                $quotation->net_total = $totals['net_total'];
            }

            $quotation->save();

            if ($totals) {
                $quotation->items()->delete();
                $quotation->items()->createMany($totals['items']);
            }

            return $quotation->load(['items.unit', 'customer']);
        });
    }

    /**
     * Soft-delete a quotation. There is no ledger entry to reverse.
     */
    public function delete(Quotation $quotation): void
    {
        $quotation->delete();
    }
}
