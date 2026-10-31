<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\ReceivePurchaseOrderRequest;
use App\Http\Requests\Asset\StorePurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\PurchaseReceiptResource;
use App\Http\Responses\ApiResponse;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(private readonly PurchaseOrderService $purchaseOrderService) {}

    public function index(Request $request): JsonResponse
    {
        $orders = $this->purchaseOrderService->paginate((int) $request->integer('per_page', 15), [
            'status' => $request->string('status')->value() ?: null,
            'supplier_id' => $request->filled('supplier_id') ? $request->integer('supplier_id') : null,
            'material_request_id' => $request->filled('material_request_id') ? $request->integer('material_request_id') : null,
        ]);

        return ApiResponse::success([
            'purchase_orders' => PurchaseOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
            'statuses' => PurchaseOrder::STATUSES,
        ]);
    }

    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        return ApiResponse::success(new PurchaseOrderResource($purchaseOrder->load([
            'supplier', 'materialRequest', 'creator', 'approver', 'items' => fn ($query) => $query->withReceivedQuantity(), 'items.item',
            'receipts.warehouse', 'receipts.receiver', 'receipts.items.item', 'receipts.items.serials',
        ])));
    }

    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $order = $this->purchaseOrderService->create($request->validated());

        return ApiResponse::success(new PurchaseOrderResource($order), __('Purchase order created successfully.'), 201);
    }

    public function approve(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $order = $this->purchaseOrderService->approve($purchaseOrder);

        return ApiResponse::success(new PurchaseOrderResource($order), __('Purchase order approved successfully.'));
    }

    public function receive(ReceivePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $receipt = $this->purchaseOrderService->receive($purchaseOrder, $request->validated());

        return ApiResponse::success(new PurchaseReceiptResource($receipt), __('Goods received into the store successfully.'), 201);
    }
}
