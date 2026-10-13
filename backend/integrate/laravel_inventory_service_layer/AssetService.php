<?php

namespace App\Services\Inventory;

use App\Models\AssetAssignment;
use App\Models\AssetReturn;
use App\Models\InventoryAsset;
use App\Models\InventoryItemSerial;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AssetService
{
    public function assign(
        InventoryAsset $asset,
        int $employeeAutoId,
        int $assignedBy,
        ?int $projectId = null,
        ?int $warehouseId = null,
        ?string $expectedReturnDate = null,
        ?string $remarks = null
    ): AssetAssignment {
        return DB::transaction(function () use (
            $asset, $employeeAutoId, $assignedBy, $projectId,
            $warehouseId, $expectedReturnDate, $remarks
        ) {
            $asset = InventoryAsset::query()
                ->with('serial')
                ->lockForUpdate()
                ->findOrFail($asset->id);

            if (!in_array($asset->status, ['in_stock'], true)) {
                throw new RuntimeException('Only an in-stock asset can be assigned.');
            }

            if ($asset->serial->status !== 'in_stock') {
                throw new RuntimeException('The asset serial is not currently available.');
            }

            $assignment = AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_auto_id' => $employeeAutoId,
                'project_id' => $projectId,
                'warehouse_id' => $warehouseId,
                'assigned_date' => now()->toDateString(),
                'expected_return_date' => $expectedReturnDate,
                'assigned_by' => $assignedBy,
                'status' => 'assigned',
                'remarks' => $remarks,
            ]);

            $asset->update([
                'current_warehouse_id' => null,
                'current_employee_auto_id' => $employeeAutoId,
                'status' => 'assigned',
            ]);

            $asset->serial->update([
                'current_location_id' => null,
                'current_employee_auto_id' => $employeeAutoId,
                'status' => 'assigned',
            ]);

            return $assignment->fresh(['asset.item', 'employee', 'project']);
        });
    }

    public function return(
        AssetAssignment $assignment,
        int $receivedBy,
        int $destinationWarehouseId,
        string $condition,
        ?string $reason = null,
        ?string $remarks = null
    ): AssetReturn {
        return DB::transaction(function () use (
            $assignment, $receivedBy, $destinationWarehouseId,
            $condition, $reason, $remarks
        ) {
            $assignment = AssetAssignment::query()
                ->with('asset.serial')
                ->lockForUpdate()
                ->findOrFail($assignment->id);

            if ($assignment->status !== 'assigned') {
                throw ValidationException::withMessages([
                    'assignment' => 'This asset assignment is no longer active.',
                ]);
            }

            $asset = InventoryAsset::query()
                ->with('serial')
                ->lockForUpdate()
                ->findOrFail($assignment->asset_id);

            $assetReturn = AssetReturn::create([
                'asset_id' => $asset->id,
                'employee_auto_id' => $assignment->employee_auto_id,
                'project_id' => $assignment->project_id,
                'return_date' => now()->toDateString(),
                'condition' => $condition,
                'return_reason' => $reason,
                'received_by' => $receivedBy,
                'destination_warehouse_id' => $destinationWarehouseId,
                'remarks' => $remarks,
            ]);

            $newStatus = $condition === 'beyond_repair'
                ? 'damaged'
                : 'in_stock';

            $asset->update([
                'current_warehouse_id' => $destinationWarehouseId,
                'current_employee_auto_id' => null,
                'status' => $newStatus,
                'condition' => $condition,
            ]);

            $asset->serial->update([
                'current_location_id' => $destinationWarehouseId,
                'current_employee_auto_id' => null,
                'status' => $newStatus,
                'condition' => $condition,
            ]);

            $assignment->update([
                'returned_date' => now()->toDateString(),
                'status' => 'returned',
            ]);

            return $assetReturn->fresh(['asset.item', 'employee', 'project', 'destinationWarehouse']);
        });
    }
}
