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
}
