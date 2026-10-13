<?php

namespace App\Services;

use App\Models\EmployeeInventory;
use App\Models\EmployeeInventoryReturn;
use App\Models\InventoryAsset;
use App\Models\InventoryItemSerial;
use App\Models\InventoryTransaction;
use App\Models\ItemName;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Hands non-asset items (consumables, tools) from a store to an employee and
 * takes them back. Asset items go through AssetService instead so the asset
 * register keeps their custody history.
 */
class EmployeeInventoryService
{
    public function __construct(private readonly InventoryStockService $stockService) {}

    /**
     * @param  array{employee_id?: ?int, status?: ?string, warehouse_id?: ?int, work_order_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return EmployeeInventory::query()
            ->with(['employee', 'item', 'serial', 'warehouse', 'workOrder', 'issuer'])
            ->when($filters['employee_id'] ?? null, fn ($query, int $id) => $query->where('employee_id', $id))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['warehouse_id'] ?? null, fn ($query, int $id) => $query->where('warehouse_id', $id))
            ->when($filters['work_order_id'] ?? null, fn ($query, int $id) => $query->where('work_order_id', $id))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{warehouse_id: int, employee_id: int, item_id: int, quantity: float, serial_id?: ?int, work_order_id?: ?int, expected_return_date?: ?string, remarks?: ?string}  $data
     */
    public function issue(array $data): EmployeeInventory
    {
        return DB::transaction(function () use ($data) {
            $item = ItemName::query()->findOrFail($data['item_id']);
            $quantity = (float) $data['quantity'];
            $serial = null;

            if ($item->isAsset()) {
                throw ValidationException::withMessages([
                    'item_id' => __('Asset items are handed over from the asset register (assign asset).'),
                ]);
            }

            if ($item->isSerialTracked()) {
                $serial = $this->availableSerial($item, $data, $quantity);
            }

            $record = EmployeeInventory::create([
                'employee_id' => $data['employee_id'],
                'item_id' => $item->item_id,
                'serial_id' => $serial?->id,
                'warehouse_id' => $data['warehouse_id'],
                'work_order_id' => $data['work_order_id'] ?? null,
                'quantity' => $quantity,
                'issue_date' => now()->toDateString(),
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'status' => EmployeeInventory::STATUS_ISSUED,
                'condition' => $serial?->condition ?? 'good',
                'remarks' => $data['remarks'] ?? null,
                'issued_by' => Auth::id(),
            ]);

            $this->stockService->decrease($data['warehouse_id'], $item, $quantity, InventoryTransaction::TYPE_EMPLOYEE_ISSUE, $record, [
                'serial_id' => $serial?->id,
                'employee_id' => $data['employee_id'],
                'remarks' => __('Issued to employee.'),
            ]);

            $serial?->update([
                'current_warehouse_id' => null,
                'current_employee_id' => $data['employee_id'],
                'status' => InventoryAsset::STATUS_ASSIGNED,
            ]);

            return $record->load(['employee', 'item', 'serial', 'warehouse', 'workOrder']);
        });
    }

    /**
     * @param  array{quantity: float, destination_warehouse_id: int, condition: string, reason?: ?string, remarks?: ?string}  $data
     */
    public function return(EmployeeInventory $issued, array $data): EmployeeInventoryReturn
    {
        return DB::transaction(function () use ($issued, $data) {
            $issued = EmployeeInventory::query()->with('item')->lockForUpdate()->findOrFail($issued->id);
            $quantity = (float) $data['quantity'];

            if ($issued->status !== EmployeeInventory::STATUS_ISSUED) {
                throw ValidationException::withMessages([
                    'status' => __('This item has already been returned.'),
                ]);
            }

            if ($quantity > $issued->outstandingQuantity()) {
                throw ValidationException::withMessages([
                    'quantity' => __('Return quantity cannot exceed the :quantity still held.', ['quantity' => $issued->outstandingQuantity()]),
                ]);
            }

            $return = $issued->returns()->create([
                'employee_id' => $issued->employee_id,
                'item_id' => $issued->item_id,
                'serial_id' => $issued->serial_id,
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'quantity' => $quantity,
                'return_date' => now()->toDateString(),
                'return_condition' => $data['condition'],
                'return_reason' => $data['reason'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'received_by' => Auth::id(),
            ]);

            $this->stockService->increase($data['destination_warehouse_id'], $issued->item, $quantity, InventoryTransaction::TYPE_EMPLOYEE_RETURN, $return, [
                'serial_id' => $issued->serial_id,
                'employee_id' => $issued->employee_id,
                'remarks' => __('Returned by employee.'),
            ]);

            if ($issued->serial_id) {
                InventoryItemSerial::query()->whereKey($issued->serial_id)->update([
                    'current_warehouse_id' => $data['destination_warehouse_id'],
                    'current_employee_id' => null,
                    'status' => InventoryAsset::statusForCondition($data['condition']),
                    'condition' => $data['condition'],
                ]);
            }

            $issued->returned_qty = (float) $issued->returned_qty + $quantity;
            $issued->condition = $data['condition'];

            if ($issued->outstandingQuantity() <= 0) {
                $issued->status = EmployeeInventory::STATUS_RETURNED;
            }

            $issued->save();

            return $return->load(['employee', 'item', 'destinationWarehouse']);
        });
    }

    /**
     * @param  array{warehouse_id: int, serial_id?: ?int}  $data
     */
    private function availableSerial(ItemName $item, array $data, float $quantity): InventoryItemSerial
    {
        if (empty($data['serial_id']) || $quantity != 1) {
            throw ValidationException::withMessages([
                'serial_id' => __('A serial-tracked item is issued one unit at a time with its serial number.'),
            ]);
        }

        $serial = InventoryItemSerial::query()
            ->whereKey($data['serial_id'])
            ->where('item_id', $item->item_id)
            ->lockForUpdate()
            ->first();

        if (! $serial || $serial->current_warehouse_id !== (int) $data['warehouse_id'] || $serial->status !== InventoryAsset::STATUS_IN_STOCK) {
            throw ValidationException::withMessages([
                'serial_id' => __('The selected unit is not in stock in this store.'),
            ]);
        }

        return $serial;
    }
}
