<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchasePaymentRequest;
use App\Http\Resources\PurchaseResource;
use App\Http\Responses\ApiResponse;
use App\Models\Purchase;
use App\Services\PurchaseService;
use App\Services\SupplierPaymentService;
use Illuminate\Http\JsonResponse;

class PurchasePaymentController extends Controller
{
    public function __construct(
        private readonly SupplierPaymentService $paymentService,
        private readonly PurchaseService $purchaseService,
    ) {}

    /**
     * Record a (possibly partial) bill payment against a purchase. It is saved
     * as a supplier payment, so it posts to the chart of accounts and appears
     * (and can be reversed) under Supplier Payments.
     */
    public function store(StorePurchasePaymentRequest $request, Purchase $purchase): JsonResponse
    {
        $this->paymentService->create([
            'supplier_id' => $purchase->supplier_id,
            'purchase_id' => $purchase->id,
            'payment_account_id' => $request->integer('payment_account_id'),
            'payment_date' => $request->validated('payment_date'),
            'bill_amount' => (float) $request->validated('amount'),
            'remarks' => $request->validated('notes'),
        ]);

        return ApiResponse::success(new PurchaseResource($this->purchaseService->find($purchase->refresh())), 'Payment recorded successfully.', 201);
    }
}
