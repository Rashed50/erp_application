<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Employee;
use App\Models\InventoryAsset;
use App\Models\InventoryItemSerial;
use App\Models\InventoryWarehouse;
use App\Models\ItemName;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dropdown data for the inventory screens, so they do not need the
 * item / supplier / employee view permissions of the setup screens.
 */
class InventoryLookupController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success([
            'items' => ItemName::query()
                ->active()
                ->with('unit')
                ->orderBy('item_name')
                ->get()
                ->map(fn (ItemName $item) => [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'itype_id' => $item->itype_id,
                    'track_type' => $item->track_type,
                    'unit_name' => $item->unit?->short_name,
                ]),
            'warehouses' => InventoryWarehouse::query()
                ->active()
                ->orderBy('name')
                ->get(['id', 'warehouse_code', 'name', 'warehouse_type']),
            'suppliers' => Supplier::query()->active()->orderBy('name')->get(['id', 'name']),
            'employees' => Employee::query()
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(['id', 'employee_code', 'name']),
            'conditions' => InventoryAsset::CONDITIONS,
        ]);
    }

    /**
     * Units of a serial-tracked item currently held in a store.
     */
    public function serials(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'warehouse_id' => ['required', 'integer'],
            'item_id' => ['required', 'integer'],
        ]);

        return ApiResponse::success(
            InventoryItemSerial::query()
                ->where('item_id', $filters['item_id'])
                ->where('current_warehouse_id', $filters['warehouse_id'])
                ->whereIn('status', [InventoryAsset::STATUS_IN_STOCK, InventoryAsset::STATUS_DAMAGED])
                ->orderBy('serial_number')
                ->get(['id', 'serial_number', 'status', 'condition']),
        );
    }
}
