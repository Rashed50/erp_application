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
}
