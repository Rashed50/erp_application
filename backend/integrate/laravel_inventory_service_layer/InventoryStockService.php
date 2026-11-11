<?php

namespace App\Services\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryStockService
{
    public function increase(
        int $warehouseId,
        int $itemId,
        float $quantity,
        ?Model $reference = null,
        ?int $performedBy = null,
        string $transactionType = 'adjustment_in',
        ?string $remarks = null
    ): InventoryStock {
        if ($quantity <= 0) {
            throw new RuntimeException('Increase quantity must be greater than zero.');
        }

        return DB::transaction(function () use (
            $warehouseId, $itemId, $quantity, $reference,
            $performedBy, $transactionType, $remarks
        ) {
            $item = InventoryItem::query()->findOrFail($itemId);

            $stock = InventoryStock::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = new InventoryStock([
                    'warehouse_id' => $warehouseId,
                    'item_id' => $itemId,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => 0,
                ]);
            }

            $stock->quantity += $quantity;
            $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
            $stock->save();

            $this->ledger(
                item: $item,
                quantity: $quantity,
                transactionType: $transactionType,
                toLocationId: $warehouseId,
                reference: $reference,
                performedBy: $performedBy,
                remarks: $remarks
            );

            return $stock->fresh();
        });
    }

    public function decrease(
        int $warehouseId,
        int $itemId,
        float $quantity,
        ?Model $reference = null,
        ?int $performedBy = null,
        string $transactionType = 'adjustment_out',
        ?string $remarks = null
    ): InventoryStock {
        if ($quantity <= 0) {
            throw new RuntimeException('Decrease quantity must be greater than zero.');
        }

        return DB::transaction(function () use (
            $warehouseId, $itemId, $quantity, $reference,
            $performedBy, $transactionType, $remarks
        ) {
            $item = InventoryItem::query()->findOrFail($itemId);

            $stock = InventoryStock::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->available_quantity < $quantity) {
                throw new RuntimeException(
                    "Insufficient available stock for item [{$item->item_code}]."
                );
            }

            $stock->quantity -= $quantity;
            $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
            $stock->save();

            $this->ledger(
                item: $item,
                quantity: $quantity,
                transactionType: $transactionType,
                fromLocationId: $warehouseId,
                reference: $reference,
                performedBy: $performedBy,
                remarks: $remarks
            );

            return $stock->fresh();
        });
    }

    public function reserve(int $warehouseId, int $itemId, float $quantity): InventoryStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Reservation quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($warehouseId, $itemId, $quantity) {
            $stock = InventoryStock::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->available_quantity < $quantity) {
                throw new RuntimeException('Insufficient available stock to reserve.');
            }

            $stock->reserved_quantity += $quantity;
            $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
            $stock->save();

            return $stock->fresh();
        });
    }

    public function releaseReservation(int $warehouseId, int $itemId, float $quantity): InventoryStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Release quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($warehouseId, $itemId, $quantity) {
            $stock = InventoryStock::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stock->reserved_quantity < $quantity) {
                throw new RuntimeException('Cannot release more than reserved quantity.');
            }

            $stock->reserved_quantity -= $quantity;
            $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
            $stock->save();

            return $stock->fresh();
        });
    }

    private function ledger(
        InventoryItem $item,
        float $quantity,
        string $transactionType,
        ?int $fromLocationId = null,
        ?int $toLocationId = null,
        ?Model $reference = null,
        ?int $performedBy = null,
        ?string $remarks = null
    ): void {
        $transaction = new \App\Models\InventoryTransaction([
            'transaction_no' => 'IT-' . now()->format('YmdHisv') . '-' . random_int(100, 999),
            'item_id' => $item->id,
            'quantity' => $quantity,
            'transaction_type' => $transactionType,
            'from_location_id' => $fromLocationId,
            'to_location_id' => $toLocationId,
            'transaction_date' => now(),
            'performed_by' => $performedBy,
            'remarks' => $remarks,
        ]);

        if ($reference) {
            $transaction->reference()->associate($reference);
        }

        $transaction->save();
    }
}
