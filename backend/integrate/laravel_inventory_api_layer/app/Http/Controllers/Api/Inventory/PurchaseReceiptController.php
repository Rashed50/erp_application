<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ReceivePurchaseRequest;
use App\Models\PurchaseReceipt;
use App\Services\Inventory\PurchaseReceivingService;
use Illuminate\Http\JsonResponse;

class PurchaseReceiptController extends Controller
{
    public function __construct(
        private readonly PurchaseReceivingService $receivingService
    ) {}

    public function show(PurchaseReceipt $purchaseReceipt): JsonResponse
    {
        return response()->json([
            'data' => $purchaseReceipt->load([
                'purchaseOrder.supplier',
                'warehouse',
                'items.item',
                'items.purchaseOrderItem',
            ]),
        ]);
    }

    public function receive(
        ReceivePurchaseRequest $request,
        PurchaseReceipt $purchaseReceipt
    ): JsonResponse {
        $receipt = $this->receivingService->receive(
            $purchaseReceipt,
            $request->validated()['items'],
            $request->user()?->emp_auto_id ?? $request->integer('received_by')
        );

        return response()->json([
            'message' => 'Purchase received successfully into the warehouse.',
            'data' => $receipt,
        ]);
    }
}
