<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\IncomeExpenseAccountResource;
use App\Http\Resources\IncomeExpenseTransactionResource;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\SaleResource;
use App\Http\Resources\SupplierResource;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use App\Models\IncomeExpenseAccount;
use App\Models\IncomeExpenseTransaction;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Services\ApprovalService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalController extends Controller
{
    public function __construct(private readonly ApprovalService $approvalService) {}

    public function customer(Customer $customer): JsonResponse
    {
        return $this->approve($customer, CustomerResource::class);
    }

    public function supplier(Supplier $supplier): JsonResponse
    {
        return $this->approve($supplier, SupplierResource::class);
    }

    public function purchase(Purchase $purchase): JsonResponse
    {
        return $this->approve($purchase, PurchaseResource::class);
    }

    public function sale(Sale $sale): JsonResponse
    {
        return $this->approve($sale, SaleResource::class);
    }

    public function ledgerAccount(IncomeExpenseAccount $ledger_account): JsonResponse
    {
        return $this->approve($ledger_account, IncomeExpenseAccountResource::class);
    }

    public function incomeExpense(IncomeExpenseTransaction $income_expense): JsonResponse
    {
        return $this->approve($income_expense, IncomeExpenseTransactionResource::class);
    }

    /**
     * @param  class-string<JsonResource>  $resource
     */
    private function approve(Model $model, string $resource): JsonResponse
    {
        if ($model->isApproved()) {
            return ApiResponse::error('This record has already been approved.', 422);
        }

        return ApiResponse::success(new $resource($this->approvalService->approve($model)), 'Record approved successfully.');
    }
}
