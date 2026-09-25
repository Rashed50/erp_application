<?php

namespace App\Services;

class LineItemTotalCalculator
{
    /**
     * Compute per-line and invoice-level totals from a set of line items.
     * Shared by SaleService and PurchaseService so the same money math can't
     * drift between the two — a client-supplied total is never trusted.
     *
     * @param  array<int, array{product_id?: ?int, item_name: string, description?: ?string, qty: float, unit_price: float, discount?: ?float, vat?: ?float}>  $items
     * @return array{items: array<int, array<string, mixed>>, total_amount: float, discount_amount: float, vat_amount: float, net_total: float}
     */
    public static function calculate(array $items): array
    {
        $totalAmount = 0;
        $discountAmount = 0;
        $vatAmount = 0;
        $computedItems = [];

        foreach ($items as $item) {
            $qty = (float) $item['qty'];
            $unitPrice = (float) $item['unit_price'];
            $discount = (float) ($item['discount'] ?? 0);
            $vat = (float) ($item['vat'] ?? 0);
            $gross = $qty * $unitPrice;
            $lineTotal = $gross - $discount + $vat;

            $totalAmount += $gross;
            $discountAmount += $discount;
            $vatAmount += $vat;

            $computedItems[] = [
                'product_id' => $item['product_id'] ?? null,
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'vat' => $vat,
                'total_amount' => $lineTotal,
            ];
        }

        return [
            'items' => $computedItems,
            'total_amount' => $totalAmount,
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'net_total' => $totalAmount - $discountAmount + $vatAmount,
        ];
    }
}
