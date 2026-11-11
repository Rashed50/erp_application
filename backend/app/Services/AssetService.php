<?php

namespace App\Services;

use App\Models\AssetAssignment;
use App\Models\AssetReturn;
use App\Models\InventoryAsset;
use App\Models\InventoryItemSerial;
use App\Models\InventoryTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The asset register: hands an asset from its store to an employee and takes
 * it back, keeping stock, serial location and custody history in step.
 */
class AssetService
{
    public function __construct(private readonly InventoryStockService $stockService) {}

    /**
     * @param  array{search?: ?string, status?: ?string, item_id?: ?int, employee_id?: ?int, warehouse_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryAsset::query()
            ->with(['item', 'serial', 'currentWarehouse', 'currentEmployee'])
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->search($search))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->whereSerial('status', $status))
            ->when($filters['item_id'] ?? null, fn ($query, int $id) => $query->whereSerial('item_id', $id))
            ->when($filters['employee_id'] ?? null, fn ($query, int $id) => $query->whereSerial('current_employee_id', $id))
            ->when($filters['warehouse_id'] ?? null, fn ($query, int $id) => $query->whereSerial('current_warehouse_id', $id))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{employee_id: int, work_order_id?: ?int, expected_return_date?: ?string, remarks?: ?string}  $data
     */
    public function assign(InventoryAsset $asset, array $data): AssetAssignment
    {
        return DB::transaction(function () use ($asset, $data) {
            $asset = InventoryAsset::query()->with('item')->findOrFail($asset->id);
            $serial = InventoryItemSerial::query()->lockForUpdate()->findOrFail($asset->serial_id);

            if ($serial->status !== InventoryAsset::STATUS_IN_STOCK || ! $serial->current_warehouse_id) {
                throw ValidationException::withMessages([
                    'status' => __('Only an in-stock asset can be assigned.'),
                ]);
            }

            $assignment = $asset->assignments()->create([
                'employee_id' => $data['employee_id'],
                'warehouse_id' => $serial->current_warehouse_id,
                'work_order_id' => $data['work_order_id'] ?? null,
                'assigned_date' => now()->toDateString(),
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'status' => AssetAssignment::STATUS_ASSIGNED,
                'remarks' => $data['remarks'] ?? null,
                'assigned_by' => Auth::id(),
            ]);

            $this->stockService->decrease($serial->current_warehouse_id, $asset->item, 1, InventoryTransaction::TYPE_EMPLOYEE_ISSUE, $assignment, [
                'serial_id' => $serial->id,
                'employee_id' => $data['employee_id'],
                'remarks' => __('Asset assigned to employee.'),
            ]);

            $serial->update([
                'current_warehouse_id' => null,
                'current_employee_id' => $data['employee_id'],
                'status' => InventoryAsset::STATUS_ASSIGNED,
            ]);

            return $assignment->load(['asset.item', 'asset.serial', 'employee', 'warehouse', 'workOrder']);
        });
    }

    /**
     * @param  array{destination_warehouse_id: int, condition: string, reason?: ?string, remarks?: ?string}  $data
     */
    public function return(AssetAssignment $assignment, array $data): AssetReturn
    {
        return DB::transaction(function () use ($assignment, $data) {
            $assignment = AssetAssignment::query()->lockForUpdate()->findOrFail($assignment->id);

            if ($assignment->status !== AssetAssignment::STATUS_ASSIGNED) {
                throw ValidationException::withMessages([
                    'status' => __('This asset assignment is no longer active.'),
                ]);
            }

            $asset = InventoryAsset::query()->with('item')->findOrFail($assignment->asset_id);
            $serial = InventoryItemSerial::query()->lockForUpdate()->findOrFail($asset->serial_id);

            $assetReturn = AssetReturn::create([
                'asset_assignment_id' => $assignment->id,
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'return_date' => now()->toDateString(),
                'condition' => $data['condition'],
                'return_reason' => $data['reason'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'received_by' => Auth::id(),
            ]);

            $this->stockService->increase($data['destination_warehouse_id'], $asset->item, 1, InventoryTransaction::TYPE_EMPLOYEE_RETURN, $assetReturn, [
                'serial_id' => $serial->id,
                'employee_id' => $assignment->employee_id,
                'remarks' => __('Asset returned by employee.'),
            ]);

            $serial->update([
                'current_warehouse_id' => $data['destination_warehouse_id'],
                'current_employee_id' => null,
                'status' => InventoryAsset::statusForCondition($data['condition']),
                'condition' => $data['condition'],
            ]);

            $assignment->update([
                'returned_date' => now()->toDateString(),
                'status' => AssetAssignment::STATUS_RETURNED,
            ]);

            return $assetReturn->load(['assignment', 'asset.item', 'asset.serial', 'employee', 'destinationWarehouse']);
        });
    }
}
