<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChartOfAccount\StoreChartOfAccountRequest;
use App\Http\Requests\ChartOfAccount\UpdateChartOfAccountRequest;
use App\Http\Resources\ChartOfAccountResource;
use App\Http\Responses\ApiResponse;
use App\Models\ChartOfAccount;
use App\Services\ChartOfAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function __construct(private readonly ChartOfAccountService $accountService) {}

    public function index(Request $request): JsonResponse
    {
        $accounts = $this->accountService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('account_type_id') ? $request->integer('account_type_id') : null,
            $request->filled('parent_id') ? $request->integer('parent_id') : null,
            $request->filled('active_status') ? $request->boolean('active_status') : null,
            $request->filled('is_transaction') ? $request->boolean('is_transaction') : null,
            $request->filled('is_closed') ? $request->boolean('is_closed') : null,
        );

        return ApiResponse::success([
            'accounts' => ChartOfAccountResource::collection($accounts),
            'meta' => [
                'current_page' => $accounts->currentPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
                'last_page' => $accounts->lastPage(),
            ],
        ]);
    }

    public function show(ChartOfAccount $ledger_account): JsonResponse
    {
        return ApiResponse::success(new ChartOfAccountResource($this->accountService->find($ledger_account)));
    }

    public function store(StoreChartOfAccountRequest $request): JsonResponse
    {
        $account = $this->accountService->create($request->validated());

        return ApiResponse::success(new ChartOfAccountResource($account), 'Account created successfully.', 201);
    }

    public function update(UpdateChartOfAccountRequest $request, ChartOfAccount $ledger_account): JsonResponse
    {
        $account = $this->accountService->update($ledger_account, $request->validated());

        return ApiResponse::success(new ChartOfAccountResource($account), 'Account updated successfully.');
    }

    public function nextAccountNumber(ChartOfAccount $ledger_account): JsonResponse
    {
        return ApiResponse::success([
            'account_number' => $this->accountService->nextChildAccountNumber($ledger_account),
        ]);
    }

    public function destroy(ChartOfAccount $ledger_account): JsonResponse
    {
        if (! $ledger_account->canBeDeleted()) {
            return ApiResponse::error('This account is predefined, has child accounts, or has transactions posted against it and cannot be deleted.', 422);
        }

        $this->accountService->delete($ledger_account);

        return ApiResponse::success(message: 'Account deleted successfully.');
    }
}
