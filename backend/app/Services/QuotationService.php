<?php

namespace App\Services;

use App\Models\Quotation;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
     * Create a quotation and its line items. The items total is computed here from
     * `items`; discount and VAT are quotation-level amounts, and the net total is
     * always rebuilt on the server.
     *
     * Unlike a sale, a quotation is only an offer: it posts nothing to the
     * general ledger or the customer's ledger, and it has no payments.
     *
     * @param  array{customer_id: int, invoice_number: string, issue_date: string, notes?: ?string, discount_amount?: ?float, vat_amount?: ?float, file?: ?UploadedFile, items: array<int, array{item_name: string, description?: ?string, unit_id?: ?int, count?: ?float, weight?: ?float, qty: float, unit_price: float, discount?: ?float, vat?: ?float}>}  $data
     */
    public function create(array $data): Quotation
    {
        $filePath = $this->storeFile($data['file'] ?? null);

        try {
            return DB::transaction(function () use ($data, $filePath) {
                $totals = LineItemTotalCalculator::calculate($data['items']);

                $discount = round((float) ($data['discount_amount'] ?? 0), 2);
                $vat = round((float) ($data['vat_amount'] ?? 0), 2);
                $this->assertDiscountWithinTotal($discount, (float) $totals['total_amount']);

                $quotation = Quotation::create([
                    'customer_id' => $data['customer_id'],
                    'invoice_number' => $data['invoice_number'],
                    'description' => $data['description'] ?? null,
                    'issue_date' => $data['issue_date'],
                    'notes' => $data['notes'] ?? null,
                    'file_path' => $filePath,
                    'total_amount' => $totals['total_amount'],
                    'discount_amount' => $discount,
                    'vat_amount' => $vat,
                    'net_total' => round((float) $totals['total_amount'] - $discount + $vat, 2),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                $quotation->items()->createMany($totals['items']);

                return $quotation->load(['items.unit', 'customer']);
            });
        } catch (\Throwable $e) {
            // The record was not saved, so do not leave an orphan upload behind.
            $this->deleteFile($filePath);

            throw $e;
        }
    }

    /**
     * Update a quotation. When `items` is supplied, its line items are
     * replaced and the totals are recomputed. A new `file` replaces the old
     * attachment; `remove_file` deletes it without a replacement.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Quotation $quotation, array $data): Quotation
    {
        $newPath = $this->storeFile($data['file'] ?? null);
        $oldPath = $quotation->file_path;

        try {
            $quotation = DB::transaction(function () use ($quotation, $data, $newPath) {
                $totals = isset($data['items']) ? LineItemTotalCalculator::calculate($data['items']) : null;

                $quotation->fill([
                    ...collect($data)->except(['items', 'file', 'remove_file'])->all(),
                    'updated_by' => Auth::id(),
                ]);

                if ($newPath) {
                    $quotation->file_path = $newPath;
                } elseif (! empty($data['remove_file'])) {
                    $quotation->file_path = null;
                }

                if ($totals) {
                    $quotation->total_amount = $totals['total_amount'];
                }

                // fill() already holds the new discount/VAT when they were sent, or the old
                // values when they were not, so the net total is always rebuilt from them.
                $discount = round((float) $quotation->discount_amount, 2);
                $vat = round((float) $quotation->vat_amount, 2);
                $this->assertDiscountWithinTotal($discount, (float) $quotation->total_amount);

                $quotation->discount_amount = $discount;
                $quotation->vat_amount = $vat;
                $quotation->net_total = round((float) $quotation->total_amount - $discount + $vat, 2);

                $quotation->save();

                if ($totals) {
                    $quotation->items()->delete();
                    $quotation->items()->createMany($totals['items']);
                }

                return $quotation->load(['items.unit', 'customer']);
            });
        } catch (\Throwable $e) {
            $this->deleteFile($newPath);

            throw $e;
        }

        // The old file is removed only after the new data is safely saved.
        if ($quotation->file_path !== $oldPath) {
            $this->deleteFile($oldPath);
        }

        return $quotation;
    }

    /**
     * Soft-delete a quotation. There is no ledger entry to reverse, and the
     * attachment stays so the record can be restored.
     */
    public function delete(Quotation $quotation): void
    {
        $quotation->delete();
    }

    /**
     * A discount larger than the items total would make the net total negative.
     */
    private function assertDiscountWithinTotal(float $discount, float $total): void
    {
        if ($discount > $total) {
            throw ValidationException::withMessages([
                'discount_amount' => __('The discount cannot be greater than the total amount.'),
            ]);
        }
    }

    /**
     * Store an uploaded attachment on the public disk and return its path.
     * (Run `php artisan storage:link` once so the files are reachable by URL.)
     */
    private function storeFile(?UploadedFile $file): ?string
    {
        return $file?->store('quotations', 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
