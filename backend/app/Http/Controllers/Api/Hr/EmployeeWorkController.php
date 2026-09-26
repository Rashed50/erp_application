<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreEmployeeWorkRequest;
use App\Http\Requests\Hr\UpdateEmployeeWorkRequest;
use App\Http\Resources\EmployeeWorkResource;
use App\Http\Responses\ApiResponse;
use App\Models\EmployeeWork;
use App\Services\EmployeeWorkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeWorkController extends Controller
{
    public function __construct(private readonly EmployeeWorkService $workService) {}

    public function index(Request $request): JsonResponse
    {
        $works = $this->workService->paginate(
            (int) $request->integer('per_page', 15),
            [
                ...$request->only(['month', 'department', 'designation', 'search']),
                'employee_id' => $request->integer('employee_id') ?: null,
            ],
        );

        return ApiResponse::success([
            'works' => EmployeeWorkResource::collection($works),
            'meta' => [
                'current_page' => $works->currentPage(),
                'per_page' => $works->perPage(),
                'total' => $works->total(),
                'last_page' => $works->lastPage(),
            ],
        ]);
    }

    /**
     * The work record for one employee and month, or null when none exists
     * yet, so the entry form can switch between creating and editing.
     */
    public function find(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => ['required', 'integer'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $work = $this->workService->findFor($request->integer('employee_id'), $request->string('month')->value());

        return ApiResponse::success([
            'work' => $work ? new EmployeeWorkResource($work) : null,
            'is_locked' => $work?->isLocked() ?? false,
            'has_salary' => $work ? $this->workService->hasLiveSalary($work) : false,
        ]);
    }

    public function store(StoreEmployeeWorkRequest $request): JsonResponse
    {
        $work = $this->workService->create($request->validated());

        return ApiResponse::success(new EmployeeWorkResource($work), 'Work record saved successfully.', 201);
    }

    public function update(UpdateEmployeeWorkRequest $request, EmployeeWork $employeeWork): JsonResponse
    {
        $work = $this->workService->update($employeeWork, $request->validated());
        $message = $this->workService->hasLiveSalary($work)
            ? 'Work record updated. Generate the salary for this month again to apply the change.'
            : 'Work record updated successfully.';

        return ApiResponse::success(new EmployeeWorkResource($work), $message);
    }

    public function destroy(EmployeeWork $employeeWork): JsonResponse
    {
        $this->workService->delete($employeeWork);

        return ApiResponse::success(message: 'Work record deleted successfully.');
    }
}
