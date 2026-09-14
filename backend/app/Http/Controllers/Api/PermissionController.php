<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * List every permission available to assign to a role.
     *
     * Read-only: permissions are a fixed catalog seeded by the application
     * (see database/seeders/PermissionSeeder.php), not a user-managed entity.
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success(
            PermissionResource::collection(Permission::orderBy('name')->get())
        );
    }
}
