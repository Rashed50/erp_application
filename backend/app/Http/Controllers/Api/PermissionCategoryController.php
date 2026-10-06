<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionCategoryRequest;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionCategoryRequest;
use App\Http\Resources\PermissionCategoryResource;
use App\Http\Resources\PermissionResource;
use App\Http\Responses\ApiResponse;
use App\Models\PermissionCategory;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;

class PermissionCategoryController extends Controller
{
    public function __construct(private readonly PermissionService $permissionService) {}

    /**
     * Every category with its permissions, plus any permission not yet categorized.
     * Used by the role add/edit screens and the permission management screen.
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success([
            'categories' => PermissionCategoryResource::collection($this->permissionService->categoriesWithPermissions()),
            'uncategorized' => PermissionResource::collection($this->permissionService->uncategorizedPermissions()),
        ]);
    }

    public function store(StorePermissionCategoryRequest $request): JsonResponse
    {
        $category = $this->permissionService->createCategory($request->validated('name'));

        return ApiResponse::success(new PermissionCategoryResource($category), __('Permission category created successfully.'), 201);
    }

    public function update(UpdatePermissionCategoryRequest $request, PermissionCategory $permissionCategory): JsonResponse
    {
        $category = $this->permissionService->renameCategory($permissionCategory, $request->validated('name'));

        return ApiResponse::success(new PermissionCategoryResource($category), __('Permission category updated successfully.'));
    }

    public function storePermission(StorePermissionRequest $request, PermissionCategory $permissionCategory): JsonResponse
    {
        $permission = $this->permissionService->createPermission($permissionCategory, $request->validated('name'));

        return ApiResponse::success(new PermissionResource($permission), __('Permission created successfully.'), 201);
    }
}
