<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\AssignAssetRequest;
use App\Http\Requests\Asset\ReturnAssetRequest;
use App\Http\Resources\AssetAssignmentResource;
use App\Http\Resources\AssetReturnResource;
use App\Http\Resources\InventoryAssetResource;
use App\Http\Responses\ApiResponse;
use App\Models\AssetAssignment;
use App\Models\InventoryAsset;
use App\Services\AssetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The asset register: every unit of an asset item received into a store,
 * with its current location / custodian and assignment history.
 */
class AssetController extends Controller
{
    public function __construct(private readonly AssetService $assetService) {}

    public function index(Request $request): JsonResponse
    {
        $assets = $this->assetService->paginate((int) $request->integer('per_page', 15), [
            'search' => $request->string('search')->value() ?: null,
            'status' => $request->string('status')->value() ?: null,
            'item_id' => $request->filled('item_id') ? $request->integer('item_id') : null,
            'employee_id' => $request->filled('employee_id') ? $request->integer('employee_id') : null,
            'warehouse_id' => $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
        ]);

        return ApiResponse::success([
            'assets' => InventoryAssetResource::collection($assets),
            'meta' => [
                'current_page' => $assets->currentPage(),
                'per_page' => $assets->perPage(),
                'total' => $assets->total(),
                'last_page' => $assets->lastPage(),
            ],
            'statuses' => InventoryAsset::STATUSES,
            'conditions' => InventoryAsset::CONDITIONS,
        ]);
    }

    public function show(InventoryAsset $asset): JsonResponse
    {
        return ApiResponse::success(new InventoryAssetResource($asset->load([
            'item', 'serial', 'currentWarehouse', 'currentEmployee',
            'assignments.employee', 'assignments.warehouse', 'assignments.workOrder', 'assignments.assigner',
            'returns.assignment', 'returns.employee', 'returns.destinationWarehouse', 'returns.receiver',
        ])));
    }

    public function assign(AssignAssetRequest $request, InventoryAsset $asset): JsonResponse
    {
        $assignment = $this->assetService->assign($asset, $request->validated());

        return ApiResponse::success(new AssetAssignmentResource($assignment), __('Asset assigned successfully.'), 201);
    }

    public function returnAsset(ReturnAssetRequest $request, AssetAssignment $assetAssignment): JsonResponse
    {
        $assetReturn = $this->assetService->return($assetAssignment, $request->validated());

        return ApiResponse::success(new AssetReturnResource($assetReturn), __('Asset returned successfully.'));
    }
}
