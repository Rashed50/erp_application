<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreSalaryDetailRequest;
use App\Http\Requests\Hr\UpdateSalaryDetailRequest;
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
        $revisions = $employee->salaryDetails()
            ->withCount('salaryHistories')
            ->orderByDesc('effective_date')
            ->get();

        return ApiResponse::success(SalaryDetailResource::collection($revisions));
    }

    public function store(StoreSalaryDetailRequest $request, Employee $employee): JsonResponse
    {
        $salaryDetail = $this->salaryDetailService->create($employee, $request->validated());

        return ApiResponse::success(new SalaryDetailResource($salaryDetail), 'Salary configuration saved successfully.', 201);
    }

    public function update(UpdateSalaryDetailRequest $request, SalaryDetail $salaryDetail): JsonResponse
    {
        $salaryDetail = $this->salaryDetailService->update($salaryDetail, $request->validated());

        return ApiResponse::success(new SalaryDetailResource($salaryDetail), 'Salary configuration updated successfully.');
    }

    public function destroy(SalaryDetail $salaryDetail): JsonResponse
    {
        $this->salaryDetailService->delete($salaryDetail);

        return ApiResponse::success(message: 'Salary configuration deleted successfully.');
    }
}
