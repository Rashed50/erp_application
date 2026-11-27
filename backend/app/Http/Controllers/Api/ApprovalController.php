<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChartOfAccountResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\FundTransferResource;
use App\Http\Resources\IncomeExpenseTransactionResource;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\SaleResource;
use App\Http\Resources\SupplierPaymentResource;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\WorkOrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\FundTransfer;
use App\Models\IncomeExpenseTransaction;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\WorkOrder;
use App\Services\ApprovalService;
use App\Services\ChartOfAccountService;
use App\Services\CustomerService;
use App\Services\FundTransferService;
use App\Services\IncomeExpenseTransactionService;
use App\Services\PurchaseService;
use App\Services\SaleService;
use App\Services\SupplierPaymentService;
use App\Services\SupplierService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Approves a pending record. A record cannot be used by any other activity
 * until it is approved, and records that post to the ledger (sales,
 * purchases, payments, opening balances, ...) are posted on approval.
 */
class ApprovalController extends Controller
{
    public function __construct(private readonly ApprovalService $approvalService) {}

    public function customer(Customer $customer, CustomerService $service): JsonResponse
    {
        return $this->approve($customer, CustomerResource::class, fn () => $service->approve($customer));
    }

    public function workOrder(WorkOrder $work_order): JsonResponse
    {
        return $this->approve($work_order, WorkOrderResource::class, fn () => $this->approvalService->approve($work_order));
    }

    public function supplier(Supplier $supplier, SupplierService $service): JsonResponse
    {
        return $this->approve($supplier, SupplierResource::class, fn () => $service->approve($supplier));
    }

    public function purchase(Purchase $purchase, PurchaseService $service): JsonResponse
    {
        return $this->approve($purchase, PurchaseResource::class, fn () => $service->approve($purchase));
    }

    public function sale(Sale $sale, SaleService $service): JsonResponse
    {
        return $this->approve($sale, SaleResource::class, fn () => $service->approve($sale));
    }

    public function ledgerAccount(ChartOfAccount $ledger_account, ChartOfAccountService $service): JsonResponse
    {
        return $this->approve($ledger_account, ChartOfAccountResource::class, fn () => $service->approve($ledger_account));
    }

    public function incomeExpense(IncomeExpenseTransaction $income_expense, IncomeExpenseTransactionService $service): JsonResponse
    {
        return $this->approve($income_expense, IncomeExpenseTransactionResource::class, fn () => $service->approve($income_expense));
    }

    public function fundTransfer(FundTransfer $fund_transfer, FundTransferService $service): JsonResponse
    {
        return $this->approve($fund_transfer, FundTransferResource::class, fn () => $service->approve($fund_transfer));
    }

    public function supplierPayment(SupplierPayment $supplier_payment, SupplierPaymentService $service): JsonResponse
    {
        return $this->approve($supplier_payment, SupplierPaymentResource::class, fn () => $service->approve($supplier_payment));
    }

    /**
     * @param  class-string<JsonResource>  $resource
     * @param  Closure(): Model  $approve
     */
    private function approve(Model $model, string $resource, Closure $approve): JsonResponse
    {
        if ($model->isApproved()) {
            return ApiResponse::error(__('This record has already been approved.'), 422);
        }

        return ApiResponse::success(new $resource($approve()), __('Record approved successfully.'));
    }
}
