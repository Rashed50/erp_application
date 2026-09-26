<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\CancelSalaryRequest;
use App\Http\Requests\Hr\GeneratePayrollRequest;
use App\Http\Requests\Hr\SalaryStatusRequest;
use App\Http\Resources\SalaryHistoryResource;
use App\Http\Responses\ApiResponse;
use App\Models\SalaryHistory;
use App\Services\PayrollService;
use App\Services\SalaryCalculationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payrollService,
        private readonly SalaryCalculationService $calculator,
    ) {}

    /**
     * Calculate the month's salaries without saving anything.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate(['month' => ['required', 'date_format:Y-m']]);

        $rows = $this->calculator->previewMonth(CarbonImmutable::createFromFormat('Y-m-d', $request->string('month')->value().'-01'));

        return ApiResponse::success([
            'month' => $request->string('month')->value(),
            'rows' => $rows->map(fn (array $row) => [
                'employee_id' => $row['employee']->id,
                'employee_code' => $row['employee']->employee_code,
                'employee_name' => $row['employee']->name,
                'department' => $row['employee']->department,
                'designation' => $row['employee']->designation,
                'existing_salary' => $row['existing_salary'] ? [
                    'id' => $row['existing_salary']->id,
                    'status' => $row['existing_salary']->status,
                    'net_salary' => (float) $row['existing_salary']->net_salary,
                ] : null,
                'calculation' => $row['calculation'],
                'issues' => $row['issues'],
                'can_generate' => $row['can_generate'],
            ]),
            'totals' => [
                'employees' => $rows->count(),
                'generatable' => $rows->where('can_generate', true)->count(),
                'gross_salary' => round($rows->sum(fn ($row) => $row['calculation']['gross_salary'] ?? 0), 2),
                'total_deduction' => round($rows->sum(fn ($row) => $row['calculation']['total_deduction'] ?? 0), 2),
                'net_salary' => round($rows->sum(fn ($row) => $row['calculation']['net_salary'] ?? 0), 2),
            ],
        ]);
    }

    public function generate(GeneratePayrollRequest $request): JsonResponse
    {
        $result = $this->payrollService->generate($request->validated('month'), $request->validated('employee_ids'));

        $message = "{$result['created']} salaries generated, {$result['regenerated']} regenerated, ".count($result['skipped']).' skipped.';

        return ApiResponse::success($result, $message);
    }

    public function index(Request $request): JsonResponse
    {
        $filters = [
            ...$request->only(['month', 'department', 'designation', 'status']),
            'employee_id' => $request->integer('employee_id') ?: null,
            'include_cancelled' => $request->boolean('include_cancelled'),
        ];

        $salaries = $this->payrollService->paginate((int) $request->integer('per_page', 15), $filters);

        return ApiResponse::success([
            'salaries' => SalaryHistoryResource::collection($salaries),
            'totals' => $this->payrollService->totals($filters),
            'meta' => [
                'current_page' => $salaries->currentPage(),
                'per_page' => $salaries->perPage(),
                'total' => $salaries->total(),
                'last_page' => $salaries->lastPage(),
            ],
        ]);
    }

    public function show(SalaryHistory $salaryHistory): JsonResponse
    {
        return ApiResponse::success(new SalaryHistoryResource($salaryHistory));
    }

    public function approve(SalaryStatusRequest $request): JsonResponse
    {
        $count = $this->payrollService->approve($request->validated('ids'));

        return ApiResponse::success(['updated' => $count], "{$count} salaries approved.");
    }

    public function pay(SalaryStatusRequest $request): JsonResponse
    {
        $count = $this->payrollService->markPaid($request->validated('ids'));

        return ApiResponse::success(['updated' => $count], "{$count} salaries marked as paid.");
    }

    public function cancel(CancelSalaryRequest $request, SalaryHistory $salaryHistory): JsonResponse
    {
        $salary = $this->payrollService->cancel($salaryHistory, $request->validated('reason'));

        return ApiResponse::success(new SalaryHistoryResource($salary), 'Salary cancelled. The month can now be generated again for this employee.');
    }
}
