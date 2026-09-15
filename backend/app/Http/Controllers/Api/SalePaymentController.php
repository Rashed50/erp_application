<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSalePaymentRequest;
use App\Http\Resources\SaleResource;
use App\Http\Responses\ApiResponse;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;

class SalePaymentController extends Controller
{
    public function __construct(private readonly SaleService $saleService) {}

    /**
     * Record a (possibly partial) payment received against a sale.
     */
    public function store(StoreSalePaymentRequest $request, Sale $sale): JsonResponse
    {
        $sale = $this->saleService->recordPayment($sale, $request->validated());

        return ApiResponse::success(new SaleResource($sale), 'Payment recorded successfully.', 201);
    }
}
