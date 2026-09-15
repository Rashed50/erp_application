<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeExpense\StoreIncomeExpenseTransactionRequest;
use App\Http\Requests\IncomeExpense\UpdateIncomeExpenseTransactionRequest;
use App\Http\Resources\IncomeExpenseTransactionResource;
use App\Http\Responses\ApiResponse;
use App\Models\IncomeExpenseTransaction;
use App\Services\IncomeExpenseTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeExpenseTransactionController extends Controller
{
    public function __construct(private readonly IncomeExpenseTransactionService $transactionService) {}

    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('type')->value() ?: null,
            $request->string('from_date')->value() ?: null,
            $request->string('to_date')->value() ?: null,
        );

        return ApiResponse::success([
            'transactions' => IncomeExpenseTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function show(IncomeExpenseTransaction $income_expense): JsonResponse
    {
        return ApiResponse::success(new IncomeExpenseTransactionResource($this->transactionService->find($income_expense)));
    }

    public function store(StoreIncomeExpenseTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->create($request->validated());

        return ApiResponse::success(new IncomeExpenseTransactionResource($transaction), 'Transaction recorded successfully.', 201);
    }

    public function update(UpdateIncomeExpenseTransactionRequest $request, IncomeExpenseTransaction $income_expense): JsonResponse
    {
        $transaction = $this->transactionService->update($income_expense, $request->validated());

        return ApiResponse::success(new IncomeExpenseTransactionResource($transaction), 'Transaction updated successfully.');
    }

    public function destroy(IncomeExpenseTransaction $income_expense): JsonResponse
    {
        $this->transactionService->delete($income_expense);

        return ApiResponse::success(message: 'Transaction deleted successfully.');
    }
}
