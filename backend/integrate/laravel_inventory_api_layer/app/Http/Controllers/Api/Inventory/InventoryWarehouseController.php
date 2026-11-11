<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryWarehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryWarehouseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InventoryWarehouse::query()
            ->with(['parent', 'project', 'manager'])
            ->withCount('stocks')
            ->where('status', true)
            ->orderBy('warehouse_type')
            ->orderBy('name');

        if ($request->filled('warehouse_type')) {
            $query->where('warehouse_type', $request->string('warehouse_type'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        return response()->json($query->get());
    }

    public function show(InventoryWarehouse $inventoryWarehouse): JsonResponse
    {
        return response()->json([
            'data' => $inventoryWarehouse->load([
                'parent', 'children', 'project', 'manager', 'stocks.item'
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'warehouse_code' => ['required', 'string', 'max:50', 'unique:inventory_warehouses,warehouse_code'],
            'name' => ['required', 'string', 'max:200'],
            'warehouse_type' => ['required', 'in:center,zonal,project_store'],
            'parent_id' => ['nullable', 'integer', 'exists:inventory_warehouses,id'],
            'zone_id' => ['nullable', 'integer'],
            'project_id' => ['nullable', 'integer', 'exists:project_infos,proj_id'],
            'manager_employee_auto_id' => ['nullable', 'integer', 'exists:employee_infos,emp_auto_id'],
            'address' => ['nullable', 'string'],
        ]);

        $warehouse = InventoryWarehouse::create($data);

        return response()->json([
            'message' => 'Warehouse created successfully.',
            'data' => $warehouse->load(['parent', 'project', 'manager']),
        ], 201);
    }
}
