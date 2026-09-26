<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreEmployeeRequest;
use App\Http\Requests\Hr\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Responses\ApiResponse;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    public function index(Request $request): JsonResponse
    {
        $employees = $this->employeeService->paginate(
            (int) $request->integer('per_page', 15),
            $request->only(['search', 'department', 'designation', 'status', 'employment_type']),
        );

        return ApiResponse::success([
            'employees' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
                'last_page' => $employees->lastPage(),
            ],
        ]);
    }

    /**
     * Departments, designations and fixed choice lists for forms and filters.
     */
    public function options(): JsonResponse
    {
        return ApiResponse::success([
            ...$this->employeeService->options(),
            'statuses' => Employee::STATUSES,
            'employment_types' => Employee::EMPLOYMENT_TYPES,
            'genders' => Employee::GENDERS,
            'next_employee_code' => $this->employeeService->nextEmployeeCode(),
        ]);
    }

    public function show(Employee $employee): JsonResponse
    {
        return ApiResponse::success(new EmployeeResource($this->employeeService->find($employee)));
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->employeeService->create($request->validated());

        return ApiResponse::success(new EmployeeResource($employee), 'Employee created successfully.', 201);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $employee = $this->employeeService->update($employee, $request->validated());

        return ApiResponse::success(new EmployeeResource($employee), 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): JsonResponse
    {
        if ($employee->salaryHistories()->exists()) {
            return ApiResponse::error('This employee has salary history and cannot be deleted. Set the status to Resigned or Inactive instead.', 422);
        }

        $this->employeeService->delete($employee);

        return ApiResponse::success(message: 'Employee deleted successfully.');
    }
}
