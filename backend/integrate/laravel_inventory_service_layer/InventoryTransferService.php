<?php

namespace App\Services\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryItemSerial;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class InventoryTransferService
{
    public function create(
        int $fromWarehouseId,
        int $toWarehouseId,
        array $items,
        int $requestedBy,
        ?string $transferDate = null,
        ?string $remarks = null
    ): InventoryTransfer {
        if ($fromWarehouseId === $toWarehouseId) {
            throw ValidationException::withMessages([
                'to_warehouse_id' => 'Source and destination warehouses must be different.',
            ]);
        }

        return DB::transaction(function () use (
            $fromWarehouseId, $toWarehouseId, $items,
            $requestedBy, $transferDate, $remarks
        ) {
            $transfer = InventoryTransfer::create([
                'transfer_no' => 'TR-' . now()->format('YmdHisv') . '-' . random_int(100, 999),
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'transfer_date' => $transferDate ?: now()->toDateString(),
                'requested_by' => $requestedBy,
                'status' => 'requested',
                'remarks' => $remarks,
            ]);

            foreach ($items as $line) {
                $item = InventoryItem::query()->findOrFail($line['item_id']);
                $quantity = (float) $line['quantity'];

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'quantity' => "Transfer quantity for {$item->name} must be greater than zero.",
                    ]);
                }

                $stock = InventoryStock::query()
                    ->where('warehouse_id', $fromWarehouseId)
                    ->where('item_id', $item->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->available_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for {$item->name}.",
                    ]);
                }

                $transfer->items()->create([
                    'item_id' => $item->id,
                    'requested_qty' => $quantity,
                    'approved_qty' => $quantity,
                    'sent_qty' => 0,
                    'received_qty' => 0,
                ]);
            }

            return $transfer->fresh('items');
        });
    }

    public function dispatch(InventoryTransfer $transfer, int $performedBy): InventoryTransfer
    {
        return DB::transaction(function () use ($transfer, $performedBy) {
            $transfer = InventoryTransfer::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if (!in_array($transfer->status, ['requested', 'approved'], true)) {
                throw new RuntimeException('Only requested or approved transfers can be dispatched.');
            }

            foreach ($transfer->items as $line) {
                $quantity = (float) $line->approved_qty;

                if ($quantity <= 0) {
                    continue;
                }

                $stock = InventoryStock::query()
                    ->where('warehouse_id', $transfer->from_warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->available_quantity < $quantity) {
                    throw new RuntimeException("Insufficient stock for item ID {$line->item_id}.");
                }

                $stock->quantity -= $quantity;
                $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
                $stock->save();

                $line->update(['sent_qty' => $quantity]);

                InventoryTransaction::create([
                    'transaction_no' => $this->transactionNo(),
                    'item_id' => $line->item_id,
                    'transaction_type' => 'transfer_out',
                    'quantity' => $quantity,
                    'from_location_id' => $transfer->from_warehouse_id,
                    'to_location_id' => $transfer->to_warehouse_id,
                    'reference_type' => $transfer->getMorphClass(),
                    'reference_id' => $transfer->id,
                    'transaction_date' => now(),
                    'performed_by' => $performedBy,
                    'remarks' => 'Inventory transfer dispatched.',
                ]);
            }

            $transfer->update(['status' => 'in_transit']);

            return $transfer->fresh('items');
        });
    }

    public function receive(InventoryTransfer $transfer, int $receivedBy): InventoryTransfer
    {
        return DB::transaction(function () use ($transfer, $receivedBy) {
            $transfer = InventoryTransfer::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if ($transfer->status !== 'in_transit') {
                throw new RuntimeException('Only in-transit transfers can be received.');
            }

            foreach ($transfer->items as $line) {
                $quantity = (float) $line->sent_qty;

                if ($quantity <= 0) {
                    continue;
                }

                $stock = InventoryStock::query()
                    ->where('warehouse_id', $transfer->to_warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = new InventoryStock([
                        'warehouse_id' => $transfer->to_warehouse_id,
                        'item_id' => $line->item_id,
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'available_quantity' => 0,
                    ]);
                }

                $stock->quantity += $quantity;
                $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
                $stock->save();

                if (InventoryItem::findOrFail($line->item_id)->track_type === 'serial') {
                    $serials = InventoryItemSerial::query()
                        ->where('item_id', $line->item_id)
                        ->where('current_location_id', $transfer->from_warehouse_id)
                        ->where('status', 'in_stock')
                        ->limit((int) $quantity)
                        ->lockForUpdate()
                        ->get();

                    if ($serials->count() !== (int) $quantity) {
                        throw new RuntimeException(
                            "Not enough serial-tracked units available for item ID {$line->item_id}."
                        );
                    }

                    foreach ($serials as $serial) {
                        $serial->update([
                            'current_location_id' => $transfer->to_warehouse_id,
                        ]);

                        InventoryTransaction::create([
                            'transaction_no' => $this->transactionNo(),
                            'item_id' => $line->item_id,
                            'serial_id' => $serial->id,
                            'transaction_type' => 'transfer_in',
                            'quantity' => 1,
                            'from_location_id' => $transfer->from_warehouse_id,
                            'to_location_id' => $transfer->to_warehouse_id,
                            'reference_type' => $transfer->getMorphClass(),
                            'reference_id' => $transfer->id,
                            'transaction_date' => now(),
                            'performed_by' => $receivedBy,
                            'remarks' => 'Inventory transfer received.',
                        ]);
                    }
                } else {
                    InventoryTransaction::create([
                        'transaction_no' => $this->transactionNo(),
                        'item_id' => $line->item_id,
                        'transaction_type' => 'transfer_in',
                        'quantity' => $quantity,
                        'from_location_id' => $transfer->from_warehouse_id,
                        'to_location_id' => $transfer->to_warehouse_id,
                        'reference_type' => $transfer->getMorphClass(),
                        'reference_id' => $transfer->id,
                        'transaction_date' => now(),
                        'performed_by' => $receivedBy,
                        'remarks' => 'Inventory transfer received.',
                    ]);
                }

                $line->update(['received_qty' => $quantity]);
            }

            $transfer->update([
                'status' => 'received',
                'received_by' => $receivedBy,
            ]);

            return $transfer->fresh('items');
        });
    }

    private function transactionNo(): string
    {
        return 'IT-' . now()->format('YmdHisv') . '-' . random_int(100, 999);
    }
}
