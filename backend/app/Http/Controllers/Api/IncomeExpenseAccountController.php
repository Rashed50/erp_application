<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeExpense\StoreIncomeExpenseAccountRequest;
use App\Http\Requests\IncomeExpense\UpdateIncomeExpenseAccountRequest;
use App\Http\Resources\IncomeExpenseAccountResource;
use App\Http\Responses\ApiResponse;
use App\Models\IncomeExpenseAccount;
use App\Services\IncomeExpenseAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeExpenseAccountController extends Controller
{
    public function __construct(private readonly IncomeExpenseAccountService $accountService) {}

    public function index(Request $request): JsonResponse
    {
        $accounts = $this->accountService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('type')->value() ?: null,
            $request->filled('active_status') ? $request->boolean('active_status') : null,
        );

        return ApiResponse::success([
            'accounts' => IncomeExpenseAccountResource::collection($accounts),
            'meta' => [
                'current_page' => $accounts->currentPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
                'last_page' => $accounts->lastPage(),
            ],
        ]);
    }

    public function show(IncomeExpenseAccount $ledger_account): JsonResponse
    {
        return ApiResponse::success(new IncomeExpenseAccountResource($this->accountService->find($ledger_account)));
    }

    public function store(StoreIncomeExpenseAccountRequest $request): JsonResponse
    {
        $account = $this->accountService->create($request->validated());

        return ApiResponse::success(new IncomeExpenseAccountResource($account), 'Account created successfully.', 201);
    }

    public function update(UpdateIncomeExpenseAccountRequest $request, IncomeExpenseAccount $ledger_account): JsonResponse
    {
        $account = $this->accountService->update($ledger_account, $request->validated());

        return ApiResponse::success(new IncomeExpenseAccountResource($account), 'Account updated successfully.');
    }

    public function destroy(IncomeExpenseAccount $ledger_account): JsonResponse
    {
        if (! $ledger_account->canBeDeleted()) {
            return ApiResponse::error('This account has transactions posted against it and cannot be deleted.', 422);
        }

        $this->accountService->delete($ledger_account);

        return ApiResponse::success(message: 'Account deleted successfully.');
    }
}
