<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ChartOfAccount;
use App\Services\AccountReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The payroll_software Account module's ledger reports, as JSON.
 */
class AccountReportController extends Controller
{
    public function __construct(private readonly AccountReportService $reportService) {}

    public function generalLedger(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => ['required', Rule::exists('chart_of_accounts', 'id')],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        return ApiResponse::success($this->reportService->generalLedger(
            ChartOfAccount::withTrashed()->findOrFail($request->integer('account_id')),
            $request->date('from_date')->toDateString(),
            $request->date('to_date')->toDateString(),
        ));
    }

    public function trialBalance(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'report_type' => ['required', Rule::in(['as_of_date', 'as_of_period'])],
        ]);

        return ApiResponse::success($this->reportService->trialBalance(
            $request->date('from_date')->toDateString(),
            $request->date('to_date')->toDateString(),
            $request->string('report_type')->value(),
        ));
    }

    public function profitAndLoss(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        return ApiResponse::success($this->reportService->profitAndLoss(
            $request->date('from_date')?->toDateString(),
            $request->date('to_date')?->toDateString(),
        ));
    }

    public function balanceSheet(Request $request): JsonResponse
    {
        $request->validate(['to_date' => ['nullable', 'date']]);

        return ApiResponse::success($this->reportService->balanceSheet($request->date('to_date')?->toDateString()));
    }

    public function supplierStatement(Request $request): JsonResponse
    {
        $request->validate([
            ...$this->dateRangeRules(),
            'supplier_ids' => ['nullable', 'array'],
            'supplier_ids.*' => ['integer', Rule::exists('suppliers', 'id')],
        ]);

        return ApiResponse::success($this->reportService->supplierStatement(
            $this->ids($request, 'supplier_ids'),
            ...$this->dateRange($request),
        ));
    }

    public function supplierBalances(): JsonResponse
    {
        return ApiResponse::success($this->reportService->supplierBalances());
    }

    public function customerStatement(Request $request): JsonResponse
    {
        $request->validate([
            ...$this->dateRangeRules(),
            'customer_ids' => ['nullable', 'array'],
            'customer_ids.*' => ['integer', Rule::exists('customers', 'id')],
        ]);

        return ApiResponse::success($this->reportService->customerStatement(
            $this->ids($request, 'customer_ids'),
            ...$this->dateRange($request),
        ));
    }

    public function cashTransactions(Request $request): JsonResponse
    {
        $request->validate([
            ...$this->dateRangeRules(),
            'account_ids' => ['nullable', 'array'],
            'account_ids.*' => ['integer', Rule::exists('chart_of_accounts', 'id')],
        ]);

        return ApiResponse::success($this->reportService->cashTransactions(
            $this->ids($request, 'account_ids'),
            ...$this->dateRange($request),
        ));
    }

    public function expenseDetails(Request $request): JsonResponse
    {
        $request->validate([
            ...$this->dateRangeRules(),
            'account_ids' => ['nullable', 'array'],
            'account_ids.*' => ['integer', Rule::exists('chart_of_accounts', 'id')],
        ]);

        return ApiResponse::success($this->reportService->expenseDetails(
            $this->ids($request, 'account_ids'),
            ...$this->dateRange($request),
        ));
    }

    public function salesPurchaseSummary(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        return ApiResponse::success($this->reportService->salesPurchaseSummary(
            $request->date('from_date')->toDateString(),
            $request->date('to_date')->toDateString(),
        ));
    }

    public function salesRegister(Request $request): JsonResponse
    {
        $request->validate([
            ...$this->dateRangeRules(),
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
        ]);

        return ApiResponse::success($this->reportService->salesRegister(
            $request->integer('customer_id') ?: null,
            ...$this->dateRange($request),
        ));
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function dateRangeRules(): array
    {
        return [
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ];
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function dateRange(Request $request): array
    {
        return [$request->date('from_date')?->toDateString(), $request->date('to_date')?->toDateString()];
    }

    /**
     * @return array<int, int>
     */
    private function ids(Request $request, string $key): array
    {
        return array_map('intval', array_filter((array) $request->input($key, [])));
    }
}
