<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\AssignAssetRequest;
use App\Http\Requests\Inventory\ReturnAssetRequest;
use App\Models\AssetAssignment;
use App\Models\InventoryAsset;
use App\Services\Inventory\AssetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(
        private readonly AssetService $assetService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = InventoryAsset::query()
            ->with(['item', 'serial', 'currentWarehouse', 'currentEmployee'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('employee_auto_id')) {
            $query->where('current_employee_auto_id', $request->integer('employee_auto_id'));
        }

        if ($request->filled('warehouse_id')) {
            $query->where('current_warehouse_id', $request->integer('warehouse_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function show(InventoryAsset $inventoryAsset): JsonResponse
    {
        return response()->json([
            'data' => $inventoryAsset->load([
                'item', 'serial', 'currentWarehouse', 'currentEmployee',
                'assignments.employee', 'assignments.project',
                'returns.employee', 'returns.destinationWarehouse',
            ]),
        ]);
    }

    public function assign(
        AssignAssetRequest $request,
        InventoryAsset $inventoryAsset
    ): JsonResponse {
        $data = $request->validated();

        $assignment = $this->assetService->assign(
            $inventoryAsset,
            $data['employee_auto_id'],
            $request->user()?->emp_auto_id ?? $data['assigned_by'],
            $data['project_id'] ?? null,
            $data['warehouse_id'] ?? null,
            $data['expected_return_date'] ?? null,
            $data['remarks'] ?? null
        );

        return response()->json([
            'message' => 'Asset assigned successfully.',
            'data' => $assignment,
        ], 201);
    }

    public function return(
        ReturnAssetRequest $request,
        AssetAssignment $assetAssignment
    ): JsonResponse {
        $data = $request->validated();

        $assetReturn = $this->assetService->return(
            $assetAssignment,
            $request->user()?->emp_auto_id ?? $data['received_by'],
            $data['destination_warehouse_id'],
            $data['condition'],
            $data['reason'] ?? null,
            $data['remarks'] ?? null
        );

        return response()->json([
            'message' => 'Asset returned successfully.',
            'data' => $assetReturn,
        ]);
    }
}
