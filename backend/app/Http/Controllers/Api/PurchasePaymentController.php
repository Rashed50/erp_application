<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchasePaymentRequest;
use App\Http\Resources\PurchaseResource;
use App\Http\Responses\ApiResponse;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchasePaymentController extends Controller
{
    public function __construct(private readonly PurchaseService $purchaseService) {}

    /**
     * Record a (possibly partial) bill payment against a purchase.
     */
    public function store(StorePurchasePaymentRequest $request, Purchase $purchase): JsonResponse
    {
        $purchase = $this->purchaseService->recordPayment($purchase, $request->validated());

        return ApiResponse::success(new PurchaseResource($purchase), 'Payment recorded successfully.', 201);
    }
}
