<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreInventoryUnitRequest;
use App\Http\Requests\Asset\UpdateInventoryUnitRequest;
use App\Http\Resources\InventoryUnitResource;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryUnit;
use App\Services\InventoryUnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryUnitController extends Controller
{
    public function __construct(private readonly InventoryUnitService $unitService) {}

    public function index(Request $request): JsonResponse
    {
        $units = $this->unitService->paginate((int) $request->integer('per_page', 15), [
            'search' => $request->string('search')->value() ?: null,
            'status' => $request->filled('status') ? $request->boolean('status') : null,
        ]);

        return ApiResponse::success([
            'units' => InventoryUnitResource::collection($units),
            'meta' => [
                'current_page' => $units->currentPage(),
                'per_page' => $units->perPage(),
                'total' => $units->total(),
                'last_page' => $units->lastPage(),
            ],
        ]);
    }

    public function getInventoryUnit(): JsonResponse
    {
        $units = InventoryUnit::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'short_name']);

        return ApiResponse::success(['units' => $units]);
    }
    /**
     * Active units for the item form's dropdown.
     */
    public function options(): JsonResponse
    {
        return ApiResponse::success(
            InventoryUnit::query()->active()->orderBy('name')->get(['id', 'name', 'short_name']),
        );
    }

    public function show(InventoryUnit $inventoryUnit): JsonResponse
    {
        return ApiResponse::success(new InventoryUnitResource($inventoryUnit));
    }

    public function store(StoreInventoryUnitRequest $request): JsonResponse
    {
        $unit = $this->unitService->create($request->validated());

        return ApiResponse::success(new InventoryUnitResource($unit), __('Unit created successfully.'), 201);
    }

    public function update(UpdateInventoryUnitRequest $request, InventoryUnit $inventoryUnit): JsonResponse
    {
        $unit = $this->unitService->update($inventoryUnit, $request->validated());

        return ApiResponse::success(new InventoryUnitResource($unit), __('Unit updated successfully.'));
    }
}
