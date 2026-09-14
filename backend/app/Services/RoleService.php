<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->latest()
            ->paginate($perPage);
    }

    public function find(Role $role): Role
    {
        return $role->load('permissions');
    }

    /**
     * @param  array{name: string, permissions?: array<int, string>}  $data
     */
    public function create(array $data): Role
    {
        // Explicit guard_name avoids Role::create() falling back to the
        // "sanctum" guard that auth:sanctum makes the request's default guard,
        // which would never match the "web"-guarded permissions from the seeder.
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role->load('permissions');
    }

    /**
     * @param  array{name?: string, permissions?: array<int, string>}  $data
     */
    public function update(Role $role, array $data): Role
    {
        if (isset($data['name'])) {
            $role->update(['name' => $data['name']]);
        }

        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions']);
        }

        return $role->load('permissions');
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
