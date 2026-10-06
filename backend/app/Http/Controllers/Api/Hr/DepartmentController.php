<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreDepartmentRequest;
use App\Http\Requests\Hr\UpdateDepartmentRequest;
use App\Http\Requests\Hr\UpdateSetupStatusRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Responses\ApiResponse;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(private readonly DepartmentService $departmentService) {}

    public function index(Request $request): JsonResponse
    {
        $departments = $this->departmentService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('status') ? $request->boolean('status') : null,
        );

        return ApiResponse::success([
            'departments' => DepartmentResource::collection($departments),
            'meta' => [
                'current_page' => $departments->currentPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
                'last_page' => $departments->lastPage(),
            ],
        ]);
    }

    public function show(Department $department): JsonResponse
    {
        return ApiResponse::success(new DepartmentResource($department));
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = $this->departmentService->create($request->validated());

        return ApiResponse::success(new DepartmentResource($department), __('Department created successfully.'), 201);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        $department = $this->departmentService->update($department, $request->validated());

        return ApiResponse::success(new DepartmentResource($department), __('Department updated successfully.'));
    }

    public function updateStatus(UpdateSetupStatusRequest $request, Department $department): JsonResponse
    {
        $department = $this->departmentService->setStatus($department, $request->boolean('status'));

        return ApiResponse::success(
            new DepartmentResource($department),
            $department->status ? __('Department activated successfully.') : __('Department deactivated successfully.'),
        );
    }
}
