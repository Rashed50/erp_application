<?php

namespace App\Services\Inventory;

use App\Models\InventoryAsset;
use App\Models\InventoryDisposal;
use App\Models\InventoryDisposalItem;
use App\Models\InventoryItem;
use App\Models\InventoryItemSerial;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class InventoryDisposalService
{
    public function dispose(
        InventoryDisposal $disposal,
        array $items,
        int $disposedBy
    ): InventoryDisposal {
        return DB::transaction(function () use ($disposal, $items, $disposedBy) {
            $disposal = InventoryDisposal::query()
                ->lockForUpdate()
                ->findOrFail($disposal->id);

            if ($disposal->status !== 'approved') {
                throw new RuntimeException('Only approved disposals can be processed.');
            }

            foreach ($items as $line) {
                $item = InventoryItem::query()->findOrFail($line['item_id']);
                $quantity = (float) $line['quantity'];

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'quantity' => "Invalid disposal quantity for {$item->name}.",
                    ]);
                }

                $stock = InventoryStock::query()
                    ->where('warehouse_id', $disposal->warehouse_id)
                    ->where('item_id', $item->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->available_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for {$item->name}.",
                    ]);
                }

                $serialId = $line['serial_id'] ?? null;

                if ($item->track_type === 'serial') {
                    if (!$serialId || $quantity != 1) {
                        throw ValidationException::withMessages([
                            'serial_id' => 'One valid serial is required for each serial-tracked disposal.',
                        ]);
                    }

                    $serial = InventoryItemSerial::query()
                        ->whereKey($serialId)
                        ->where('item_id', $item->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($serial->current_location_id !== $disposal->warehouse_id) {
                        throw ValidationException::withMessages([
                            'serial_id' => 'The selected serial is not in the disposal warehouse.',
                        ]);
                    }

                    $serial->update([
                        'current_location_id' => null,
                        'current_employee_auto_id' => null,
                        'status' => 'disposed',
                    ]);

                    $asset = InventoryAsset::query()
                        ->where('serial_id', $serial->id)
                        ->lockForUpdate()
                        ->first();

                    if ($asset) {
                        $asset->update([
                            'current_warehouse_id' => null,
                            'current_employee_auto_id' => null,
                            'status' => 'disposed',
                        ]);
                    }
                }

                $disposalItem = InventoryDisposalItem::create([
                    'disposal_id' => $disposal->id,
                    'item_id' => $item->id,
                    'serial_id' => $serialId,
                    'quantity' => $quantity,
                    'reason' => $line['reason'] ?? null,
                ]);

                $stock->quantity -= $quantity;
                $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
                $stock->save();

                InventoryTransaction::create([
                    'transaction_no' => 'IT-' . now()->format('YmdHisv') . '-' . random_int(100, 999),
                    'item_id' => $item->id,
                    'serial_id' => $serialId,
                    'transaction_type' => 'disposal',
                    'quantity' => $quantity,
                    'from_location_id' => $disposal->warehouse_id,
                    'to_location_id' => null,
                    'reference_type' => $disposal->getMorphClass(),
                    'reference_id' => $disposal->id,
                    'transaction_date' => now(),
                    'performed_by' => $disposedBy,
                    'remarks' => 'Inventory item disposed.',
                ]);
            }

            $disposal->update([
                'status' => 'disposed',
                'disposed_by' => $disposedBy,
            ]);

            return $disposal->fresh('items');
        });
    }
}
