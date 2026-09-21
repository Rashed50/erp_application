<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierTransactionRequest;
use App\Http\Resources\SupplierTransactionResource;
use App\Http\Responses\ApiResponse;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Services\SupplierTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierTransactionController extends Controller
{
    public function __construct(private readonly SupplierTransactionService $transactionService) {}

    public function index(Request $request, Supplier $supplier): JsonResponse
    {
        $transactions = $this->transactionService->listForSupplier(
            $supplier,
            (int) $request->integer('per_page', 15),
            $request->string('from_date')->value() ?: null,
            $request->string('to_date')->value() ?: null,
        );

        return ApiResponse::success([
            'transactions' => SupplierTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function store(StoreSupplierTransactionRequest $request, Supplier $supplier): JsonResponse
    {
        $transaction = $this->transactionService->create($supplier, $request->validated());

        return ApiResponse::success(new SupplierTransactionResource($transaction), 'Transaction recorded successfully.', 201);
    }

    public function destroy(Supplier $supplier, SupplierTransaction $transaction): JsonResponse
    {
        if ($transaction->supplier_id !== $supplier->id) {
            return ApiResponse::error('Transaction not found for this supplier.', 404);
        }

        if (! $transaction->status) {
            return ApiResponse::error('This transaction has already been reversed.', 422);
        }

        $transaction = $this->transactionService->reverse($transaction);

        return ApiResponse::success(new SupplierTransactionResource($transaction), 'Transaction reversed successfully.');
    }
}
