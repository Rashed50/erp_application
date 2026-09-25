<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierPaymentRequest;
use App\Http\Resources\SupplierPaymentResource;
use App\Http\Responses\ApiResponse;
use App\Models\SupplierPayment;
use App\Services\SupplierPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function __construct(private readonly SupplierPaymentService $paymentService) {}

    public function index(Request $request): JsonResponse
    {
        $payments = $this->paymentService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('supplier_id') ? $request->integer('supplier_id') : null,
            $request->string('from_date')->value() ?: null,
            $request->string('to_date')->value() ?: null,
        );

        return ApiResponse::success([
            'payments' => SupplierPaymentResource::collection($payments),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage(),
            ],
        ]);
    }

    public function show(SupplierPayment $supplier_payment): JsonResponse
    {
        return ApiResponse::success(new SupplierPaymentResource($this->paymentService->find($supplier_payment)));
    }

    public function store(StoreSupplierPaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->create($request->validated());

        return ApiResponse::success(new SupplierPaymentResource($payment), 'Bill payment saved successfully.', 201);
    }

    public function destroy(SupplierPayment $supplier_payment): JsonResponse
    {
        $this->paymentService->delete($supplier_payment);

        return ApiResponse::success(message: 'Bill payment deleted and reversed successfully.');
    }
}
