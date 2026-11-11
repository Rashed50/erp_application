<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\HrReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrReportController extends Controller
{
    public function __construct(private readonly HrReportService $reportService) {}

    public function dashboard(): JsonResponse
    {
        return ApiResponse::success($this->reportService->dashboard());
    }

    public function departmentWise(Request $request): JsonResponse
    {
        $request->validate(['month' => ['required', 'date_format:Y-m']]);

        return ApiResponse::success([
            'rows' => $this->reportService->departmentWise($request->string('month')->value(), $request->string('status')->value() ?: null),
        ]);
    }

    public function salarySummary(Request $request): JsonResponse
    {
        $request->validate(['year' => ['required', 'integer', 'between:2000,2100']]);

        return ApiResponse::success([
            'rows' => $this->reportService->salarySummary($request->integer('year'), $request->string('department')->value() ?: null),
        ]);
    }

    /**
     * Salary increments and decrements effective within a date range.
     */
    public function salaryChanges(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'employee_id' => ['nullable', 'integer'],
        ]);

        return ApiResponse::success($this->reportService->salaryChanges(
            $request->string('from')->value() ?: null,
            $request->string('to')->value() ?: null,
            $request->string('department')->value() ?: null,
            $request->integer('employee_id') ?: null,
        ));
    }

    /**
     * Approved salaries not yet fully paid, up to a month when one is given.
     */
    public function salaryDue(Request $request): JsonResponse
    {
        $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'employee_id' => ['nullable', 'integer'],
        ]);

        return ApiResponse::success($this->reportService->salaryDue(
            $request->string('month')->value() ?: null,
            $request->string('department')->value() ?: null,
            $request->integer('employee_id') ?: null,
        ));
    }
}
