<?php

namespace App\Services\Inventory;

use App\Models\EmployeeInventory;
use App\Models\EmployeeInventoryReturn;
use App\Models\InventoryAsset;
use App\Models\InventoryItem;
use App\Models\InventoryItemSerial;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class EmployeeInventoryService
{
    public function issue(
        int $warehouseId,
        int $employeeAutoId,
        int $itemId,
        float $quantity,
        int $issuedBy,
        ?int $projectId = null,
        ?int $serialId = null,
        ?string $expectedReturnDate = null,
        ?string $remarks = null
    ): EmployeeInventory {
        return DB::transaction(function () use (
            $warehouseId, $employeeAutoId, $itemId, $quantity,
            $issuedBy, $projectId, $serialId, $expectedReturnDate, $remarks
        ) {
            $item = InventoryItem::query()->findOrFail($itemId);

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Issue quantity must be greater than zero.',
                ]);
            }

            $stock = InventoryStock::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->available_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Insufficient stock for {$item->name}.",
                ]);
            }

            if ($item->track_type === 'serial') {
                if (!$serialId) {
                    throw ValidationException::withMessages([
                        'serial_id' => 'A serial number is required for this item.',
                    ]);
                }

                $serial = InventoryItemSerial::query()
                    ->whereKey($serialId)
                    ->where('item_id', $itemId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($serial->current_location_id !== $warehouseId || $serial->status !== 'in_stock') {
                    throw ValidationException::withMessages([
                        'serial_id' => 'The selected serial item is not available in this warehouse.',
                    ]);
                }

                if ($quantity != 1) {
                    throw ValidationException::withMessages([
                        'quantity' => 'A serial-tracked item must be issued one unit at a time.',
                    ]);
                }

                $stock->quantity -= 1;
                $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
                $stock->save();

                $record = EmployeeInventory::create([
                    'employee_auto_id' => $employeeAutoId,
                    'item_id' => $itemId,
                    'serial_id' => $serial->id,
                    'project_id' => $projectId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => 1,
                    'issue_date' => now()->toDateString(),
                    'issued_by' => $issuedBy,
                    'status' => 'issued',
                    'condition' => $serial->condition,
                    'expected_return_date' => $expectedReturnDate,
                    'remarks' => $remarks,
                ]);

                $serial->update([
                    'current_location_id' => null,
                    'current_employee_auto_id' => $employeeAutoId,
                    'status' => 'assigned',
                ]);

                $asset = InventoryAsset::query()
                    ->where('serial_id', $serial->id)
                    ->lockForUpdate()
                    ->first();

                if ($asset) {
                    $asset->update([
                        'current_warehouse_id' => null,
                        'current_employee_auto_id' => $employeeAutoId,
                        'status' => 'assigned',
                    ]);
                }

                $this->ledger(
                    $itemId, $serial->id, 1, $warehouseId, null,
                    $record, $issuedBy, 'Item issued to employee.'
                );

                return $record->fresh(['employee', 'item', 'serial', 'project']);
            }

            $stock->quantity -= $quantity;
            $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
            $stock->save();

            $record = EmployeeInventory::create([
                'employee_auto_id' => $employeeAutoId,
                'item_id' => $itemId,
                'serial_id' => null,
                'project_id' => $projectId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'issue_date' => now()->toDateString(),
                'issued_by' => $issuedBy,
                'status' => 'issued',
                'condition' => 'good',
                'expected_return_date' => $expectedReturnDate,
                'remarks' => $remarks,
            ]);

            $this->ledger(
                $itemId, null, $quantity, $warehouseId, null,
                $record, $issuedBy, 'Non-asset item issued to employee.'
            );

            return $record->fresh(['employee', 'item', 'project']);
        });
    }

    public function return(
        EmployeeInventory $issuedItem,
        float $quantity,
        int $destinationWarehouseId,
        int $receivedBy,
        string $condition = 'good',
        ?string $reason = null,
        ?string $remarks = null
    ): EmployeeInventoryReturn {
        return DB::transaction(function () use (
            $issuedItem, $quantity, $destinationWarehouseId,
            $receivedBy, $condition, $reason, $remarks
        ) {
            $issuedItem = EmployeeInventory::query()
                ->with('item', 'serial')
                ->lockForUpdate()
                ->findOrFail($issuedItem->id);

            if ($issuedItem->status !== 'issued') {
                throw new RuntimeException('Only currently issued inventory can be returned.');
            }

            if ($quantity <= 0 || $quantity > (float) $issuedItem->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Invalid return quantity.',
                ]);
            }

            $return = EmployeeInventoryReturn::create([
                'employee_inventory_id' => $issuedItem->id,
                'employee_auto_id' => $issuedItem->employee_auto_id,
                'item_id' => $issuedItem->item_id,
                'serial_id' => $issuedItem->serial_id,
                'quantity' => $quantity,
                'return_date' => now()->toDateString(),
                'return_condition' => $condition,
                'return_reason' => $reason,
                'received_by' => $receivedBy,
                'destination_warehouse_id' => $destinationWarehouseId,
                'remarks' => $remarks,
            ]);

            $stock = InventoryStock::query()
                ->where('warehouse_id', $destinationWarehouseId)
                ->where('item_id', $issuedItem->item_id)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = new InventoryStock([
                    'warehouse_id' => $destinationWarehouseId,
                    'item_id' => $issuedItem->item_id,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => 0,
                ]);
            }

            $stock->quantity += $quantity;
            $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
            $stock->save();

            if ($issuedItem->serial_id) {
                $serial = InventoryItemSerial::query()
                    ->lockForUpdate()
                    ->findOrFail($issuedItem->serial_id);

                $serial->update([
                    'current_location_id' => $destinationWarehouseId,
                    'current_employee_auto_id' => null,
                    'status' => $condition === 'beyond_repair' ? 'damaged' : 'in_stock',
                    'condition' => $condition,
                ]);

                $asset = InventoryAsset::query()
                    ->where('serial_id', $serial->id)
                    ->lockForUpdate()
                    ->first();

                if ($asset) {
                    $asset->update([
                        'current_warehouse_id' => $destinationWarehouseId,
                        'current_employee_auto_id' => null,
                        'status' => $condition === 'beyond_repair' ? 'damaged' : 'in_stock',
                        'condition' => $condition,
                    ]);
                }
            }

            $issuedItem->update([
                'status' => 'returned',
                'condition' => $condition,
            ]);

            $this->ledger(
                $issuedItem->item_id,
                $issuedItem->serial_id,
                $quantity,
                null,
                $destinationWarehouseId,
                $return,
                $receivedBy,
                'Inventory returned by employee.'
            );

            return $return->fresh(['employee', 'item', 'serial', 'destinationWarehouse']);
        });
    }

    private function ledger(
        int $itemId,
        ?int $serialId,
        float $quantity,
        ?int $fromLocationId,
        ?int $toLocationId,
        object $reference,
        int $performedBy,
        string $remarks
    ): void {
        InventoryTransaction::create([
            'transaction_no' => 'IT-' . now()->format('YmdHisv') . '-' . random_int(100, 999),
            'item_id' => $itemId,
            'serial_id' => $serialId,
            'transaction_type' => $toLocationId ? 'employee_return' : 'employee_issue',
            'quantity' => $quantity,
            'from_location_id' => $fromLocationId,
            'to_location_id' => $toLocationId,
            'reference_type' => $reference->getMorphClass(),
            'reference_id' => $reference->id,
            'transaction_date' => now(),
            'performed_by' => $performedBy,
            'remarks' => $remarks,
        ]);
    }
}
