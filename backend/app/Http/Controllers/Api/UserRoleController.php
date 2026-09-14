<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AssignRolesRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * Replace the user's roles with the given set of role names.
     */
    public function store(AssignRolesRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->syncRoles($user, $request->validated('roles'));

        return ApiResponse::success(new UserResource($user), 'Roles assigned successfully.');
    }

    /**
     * Revoke a single role from the user.
     */
    public function destroy(User $user, Role $role): JsonResponse
    {
        $user = $this->userService->revokeRole($user, $role);

        return ApiResponse::success(new UserResource($user), 'Role revoked successfully.');
    }
}
