<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerTransactionRequest;
use App\Http\Resources\CustomerTransactionResource;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Services\CustomerTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerTransactionController extends Controller
{
    public function __construct(private readonly CustomerTransactionService $transactionService) {}

    public function index(Request $request, Customer $customer): JsonResponse
    {
        $transactions = $this->transactionService->listForCustomer(
            $customer,
            (int) $request->integer('per_page', 15),
            $request->string('from_date')->value() ?: null,
            $request->string('to_date')->value() ?: null,
        );

        return ApiResponse::success([
            'transactions' => CustomerTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function store(StoreCustomerTransactionRequest $request, Customer $customer): JsonResponse
    {
        $transaction = $this->transactionService->create($customer, $request->validated());

        return ApiResponse::success(new CustomerTransactionResource($transaction), 'Transaction recorded successfully.', 201);
    }

    public function destroy(Customer $customer, CustomerTransaction $transaction): JsonResponse
    {
        if ($transaction->customer_id !== $customer->id) {
            return ApiResponse::error('Transaction not found for this customer.', 404);
        }

        if (! $transaction->status) {
            return ApiResponse::error('This transaction has already been reversed.', 422);
        }

        $transaction = $this->transactionService->reverse($transaction);

        return ApiResponse::success(new CustomerTransactionResource($transaction), 'Transaction reversed successfully.');
    }
}
