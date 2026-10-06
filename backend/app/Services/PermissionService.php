<?php

namespace App\Services;

use App\Models\PermissionCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * @return Collection<int, PermissionCategory>
     */
    public function categoriesWithPermissions(): Collection
    {
        return PermissionCategory::query()
            ->with(['permissions' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();
    }

    /**
     * Permissions not yet put in any category (e.g. created outside the UI).
     *
     * @return Collection<int, Permission>
     */
    public function uncategorizedPermissions(): Collection
    {
        return Permission::query()
            ->whereNotIn('id', DB::table('permission_category_relations')->select('permission_id'))
            ->orderBy('name')
            ->get();
    }

    public function createCategory(string $name): PermissionCategory
    {
        return PermissionCategory::create(['name' => $name])->load('permissions');
    }

    public function renameCategory(PermissionCategory $category, string $name): PermissionCategory
    {
        $category->update(['name' => $name]);

        return $category->load('permissions');
    }

    public function createPermission(PermissionCategory $category, string $name): Permission
    {
        return DB::transaction(function () use ($category, $name) {
            // Explicit guard: under auth:sanctum the default guard would be "sanctum",
            // which never matches the "web"-guarded roles.
            $permission = Permission::create(['name' => $name, 'guard_name' => 'web']);

            $category->permissions()->attach($permission->id);

            return $permission;
        });
    }
}
