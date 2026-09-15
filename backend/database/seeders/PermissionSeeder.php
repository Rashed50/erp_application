<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * The baseline permission catalog for the application.
     *
     * @var array<int, string>
     */
    protected array $permissions = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'customers.view',
        'customers.create',
        'customers.update',
        'customers.delete',
        'customer-transactions.view',
        'customer-transactions.create',
        'customer-transactions.delete',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
