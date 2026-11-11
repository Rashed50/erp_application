<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreSalaryDetailRequest;
use App\Http\Requests\Hr\UpdateSalaryDetailRequest;
use App\Http\Resources\SalaryDetailLogResource;
use App\Http\Resources\SalaryDetailResource;
use App\Http\Responses\ApiResponse;
use App\Models\Employee;
use App\Models\SalaryDetail;
use App\Services\SalaryDetailService;
use Illuminate\Http\JsonResponse;

class SalaryDetailController extends Controller
{
    public function __construct(private readonly SalaryDetailService $salaryDetailService) {}

    public function index(Employee $employee): JsonResponse
    {
        $revisions = SalaryDetail::linkRevisions($employee->salaryDetails()
            ->with('designation:id,name')
            ->withCount('salaryHistories')
            ->orderByDesc('effective_date')
            ->get());

        return ApiResponse::success(SalaryDetailResource::collection($revisions));
    }

    /**
     * Every add, edit and delete of the employee's salary revisions, newest first.
     */
    public function logs(Employee $employee): JsonResponse
    {
        $logs = $employee->salaryDetailLogs()
            ->with('creator:id,name')
            ->latest('created_at')
            ->latest('id')
            ->get();

        return ApiResponse::success(SalaryDetailLogResource::collection($logs));
    }

    public function store(StoreSalaryDetailRequest $request, Employee $employee): JsonResponse
    {
        $salaryDetail = $this->salaryDetailService->create($employee, $request->validated());

        return ApiResponse::success(new SalaryDetailResource($salaryDetail), __('Salary configuration saved successfully.'), 201);
    }

    public function update(UpdateSalaryDetailRequest $request, SalaryDetail $salaryDetail): JsonResponse
    {
        $salaryDetail = $this->salaryDetailService->update($salaryDetail, $request->validated());

        return ApiResponse::success(new SalaryDetailResource($salaryDetail), __('Salary configuration updated successfully.'));
    }

    public function destroy(SalaryDetail $salaryDetail): JsonResponse
    {
        $this->salaryDetailService->delete($salaryDetail);

        return ApiResponse::success(message: __('Salary configuration deleted successfully.'));
    }
}
