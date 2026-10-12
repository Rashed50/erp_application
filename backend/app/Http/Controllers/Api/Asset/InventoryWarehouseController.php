<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreInventoryWarehouseRequest;
use App\Http\Requests\Asset\UpdateInventoryWarehouseRequest;
use App\Http\Resources\InventoryStockResource;
use App\Http\Resources\InventoryWarehouseResource;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryWarehouse;
use App\Services\InventoryWarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryWarehouseController extends Controller
{
    public function __construct(private readonly InventoryWarehouseService $warehouseService) {}

    public function index(Request $request): JsonResponse
    {
        $warehouses = $this->warehouseService->paginate((int) $request->integer('per_page', 15), [
            'search' => $request->string('search')->value() ?: null,
            'status' => $request->filled('status') ? $request->boolean('status') : null,
            'warehouse_type' => $request->string('warehouse_type')->value() ?: null,
            'parent_id' => $request->filled('parent_id') ? $request->integer('parent_id') : null,
        ]);

        return ApiResponse::success([
            'warehouses' => InventoryWarehouseResource::collection($warehouses),
            'meta' => [
                'current_page' => $warehouses->currentPage(),
                'per_page' => $warehouses->perPage(),
                'total' => $warehouses->total(),
                'last_page' => $warehouses->lastPage(),
            ],
            'types' => collect(InventoryWarehouse::TYPES)
                ->map(fn (string $name, string $id) => ['id' => $id, 'name' => $name])
                ->values(),
        ]);
    }

    /**
     * Active stores for the dropdowns on the stock movement screens.
     */
    public function options(): JsonResponse
    {
        return ApiResponse::success(
            InventoryWarehouse::query()->active()->orderBy('name')->get(['id', 'warehouse_code', 'name', 'warehouse_type', 'parent_id']),
        );
    }

    public function show(InventoryWarehouse $warehouse): JsonResponse
    {
        $warehouse->load(['parent', 'workOrder', 'manager', 'stocks.item.unit']);

        return ApiResponse::success([
            'warehouse' => new InventoryWarehouseResource($warehouse),
            'stocks' => InventoryStockResource::collection($warehouse->stocks),
        ]);
    }

    public function store(StoreInventoryWarehouseRequest $request): JsonResponse
    {
        $warehouse = $this->warehouseService->create($request->validated());

        return ApiResponse::success(new InventoryWarehouseResource($warehouse), __('Store created successfully.'), 201);
    }

    public function update(UpdateInventoryWarehouseRequest $request, InventoryWarehouse $warehouse): JsonResponse
    {
        $warehouse = $this->warehouseService->update($warehouse, $request->validated());

        return ApiResponse::success(new InventoryWarehouseResource($warehouse), __('Store updated successfully.'));
    }
}
