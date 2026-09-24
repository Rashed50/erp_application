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
        'suppliers.view',
        'suppliers.create',
        'suppliers.update',
        'suppliers.delete',
        'supplier-transactions.view',
        'supplier-transactions.create',
        'supplier-transactions.delete',
        'purchases.view',
        'purchases.create',
        'purchases.update',
        'purchases.delete',
        'purchase-payments.create',
        'supplier-payments.view',
        'supplier-payments.create',
        'supplier-payments.delete',
        'fund-transfers.view',
        'fund-transfers.create',
        'fund-transfers.delete',
        'sales.view',
        'sales.create',
        'sales.update',
        'sales.delete',
        'sale-payments.create',
        'ledger-accounts.view',
        'ledger-accounts.create',
        'ledger-accounts.update',
        'ledger-accounts.delete',
        'income-expenses.view',
        'income-expenses.create',
        'income-expenses.update',
        'income-expenses.delete',
        'customers.approve',
        'suppliers.approve',
        'purchases.approve',
        'sales.approve',
        'ledger-accounts.approve',
        'income-expenses.approve',
        'supplier-payments.approve',
        'fund-transfers.approve',
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
