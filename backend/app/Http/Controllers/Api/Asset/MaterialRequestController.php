<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\ApproveMaterialRequest;
use App\Http\Requests\Asset\StoreMaterialRequest;
use App\Http\Resources\MaterialRequestResource;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\MaterialRequest;
use App\Services\MaterialRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    public function __construct(private readonly MaterialRequestService $materialRequestService) {}

    public function index(Request $request): JsonResponse
    {
        $requests = $this->materialRequestService->paginate((int) $request->integer('per_page', 15), [
            'status' => $request->string('status')->value() ?: null,
            'warehouse_id' => $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
            'work_order_id' => $request->filled('work_order_id') ? $request->integer('work_order_id') : null,
        ]);

        return ApiResponse::success([
            'material_requests' => MaterialRequestResource::collection($requests),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
                'last_page' => $requests->lastPage(),
            ],
            'statuses' => MaterialRequest::STATUSES,
            'priorities' => MaterialRequest::PRIORITIES,
        ]);
    }

    public function show(MaterialRequest $materialRequest): JsonResponse
    {
        $materialRequest->load(['warehouse', 'workOrder', 'requester', 'approver', 'items' => fn ($query) => $query->withOrderedQuantity(), 'items.item', 'purchaseOrders.supplier']);

        return ApiResponse::success([
            'material_request' => new MaterialRequestResource($materialRequest),
            'purchase_orders' => PurchaseOrderResource::collection($materialRequest->purchaseOrders),
        ]);
    }

    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $materialRequest = $this->materialRequestService->create($request->validated());

        return ApiResponse::success(new MaterialRequestResource($materialRequest), __('Material request submitted successfully.'), 201);
    }

    public function approve(ApproveMaterialRequest $request, MaterialRequest $materialRequest): JsonResponse
    {
        $materialRequest = $this->materialRequestService->approve($materialRequest, $request->validated('items'));

        return ApiResponse::success(new MaterialRequestResource($materialRequest), __('Material request approved successfully.'));
    }

    public function reject(Request $request, MaterialRequest $materialRequest): JsonResponse
    {
        $remarks = $request->validate(['remarks' => ['nullable', 'string']])['remarks'] ?? null;

        $materialRequest = $this->materialRequestService->reject($materialRequest, $remarks);

        return ApiResponse::success(new MaterialRequestResource($materialRequest), __('Material request rejected.'));
    }
}
