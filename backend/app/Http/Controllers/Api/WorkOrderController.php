<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkOrder\StoreWorkOrderRequest;
use App\Http\Requests\WorkOrder\UpdateWorkOrderRequest;
use App\Http\Resources\WorkOrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function __construct(private readonly WorkOrderService $workOrderService) {}

    public function index(Request $request): JsonResponse
    {
        $workOrders = $this->workOrderService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->integer('customer_id') ?: null,
            $request->string('status')->value() ?: null,
        );

        return ApiResponse::success([
            'work_orders' => WorkOrderResource::collection($workOrders),
            'meta' => [
                'current_page' => $workOrders->currentPage(),
                'per_page' => $workOrders->perPage(),
                'total' => $workOrders->total(),
                'last_page' => $workOrders->lastPage(),
            ],
        ]);
    }

    public function forCustomer(Customer $customer): JsonResponse
    {
        return ApiResponse::success(WorkOrderResource::collection($this->workOrderService->forCustomer($customer)));
    }

    public function show(WorkOrder $workOrder): JsonResponse
    {
        return ApiResponse::success(new WorkOrderResource($this->workOrderService->find($workOrder)));
    }

    public function store(StoreWorkOrderRequest $request): JsonResponse
    {
        $workOrder = $this->workOrderService->create($request->validated());

        return ApiResponse::success(new WorkOrderResource($workOrder), 'Work order created successfully.', 201);
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder): JsonResponse
    {
        $workOrder = $this->workOrderService->update($workOrder, $request->validated());

        return ApiResponse::success(new WorkOrderResource($workOrder), 'Work order updated successfully.');
    }

    public function destroy(WorkOrder $workOrder): JsonResponse
    {
        if ($workOrder->sales()->exists() || $workOrder->transactions()->exists()) {
            return ApiResponse::error('This work order has sales or payments and cannot be deleted.', 422);
        }

        $this->workOrderService->delete($workOrder);

        return ApiResponse::success(message: 'Work order deleted successfully.');
    }
}
