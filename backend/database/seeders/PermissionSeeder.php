<?php

namespace Database\Seeders;

use App\Models\PermissionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * The baseline permission catalog for the application, grouped by the
     * category it is shown under on the role screens.
     *
     * @var array<string, array<int, string>>
     */
    protected array $permissions = [
        'Users' => [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ],
        'Roles & Permissions' => [
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.update',
        ],
        'Settings' => [
            'settings.update',
        ],
        'Customers' => [
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'customers.approve',
            'customer-transactions.view',
            'customer-transactions.create',
            'customer-transactions.delete',
        ],
        'Work Orders' => [
            'work-orders.view',
            'work-orders.create',
            'work-orders.update',
            'work-orders.delete',
            'work-orders.approve',
        ],
        'Products' => [
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
        ],
        'Sales' => [
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.delete',
            'sales.approve',
            'sale-payments.create',
        ],
        'Suppliers' => [
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',
            'suppliers.approve',
            'supplier-transactions.view',
            'supplier-transactions.create',
            'supplier-transactions.delete',
        ],
        'Purchases' => [
            'purchases.view',
            'purchases.create',
            'purchases.update',
            'purchases.delete',
            'purchases.approve',
            'purchase-payments.create',
        ],
        'Supplier Payments' => [
            'supplier-payments.view',
            'supplier-payments.create',
            'supplier-payments.delete',
            'supplier-payments.approve',
        ],
        'Fund Transfers' => [
            'fund-transfers.view',
            'fund-transfers.create',
            'fund-transfers.delete',
            'fund-transfers.approve',
        ],
        'Ledger Accounts' => [
            'ledger-accounts.view',
            'ledger-accounts.create',
            'ledger-accounts.update',
            'ledger-accounts.delete',
            'ledger-accounts.approve',
        ],
        'Income & Expenses' => [
            'income-expenses.view',
            'income-expenses.create',
            'income-expenses.update',
            'income-expenses.delete',
            'income-expenses.approve',
        ],
        'Account Reports' => [
            'account-reports.view',
        ],
        'Employees' => [
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
            'salary-configs.view',
            'salary-configs.create',
            'salary-configs.update',
            'salary-configs.delete',
        ],
        'HR Setup' => [
            'departments.view',
            'departments.create',
            'departments.update',
            'designations.view',
            'designations.create',
            'designations.update',
            'locations.create',
        ],
        'Employee Works' => [
            'employee-works.view',
            'employee-works.create',
            'employee-works.update',
            'employee-works.delete',
        ],
        'Payroll' => [
            'payroll.view',
            'payroll.generate',
            'payroll.approve',
            'payroll.pay',
            'payroll.cancel',
        ],
        'HR Reports' => [
            'hr-reports.view',
        ],
        'Asset Setup' => [
            'item-categories.view',
            'item-categories.create',
            'item-categories.update',
            'item-sub-categories.view',
            'item-sub-categories.create',
            'item-sub-categories.update',
            'item-names.view',
            'item-names.create',
            'item-names.update',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * A permission that already has a category keeps it, so re-seeding never
     * undoes a change made from the permissions screen.
     */
    public function run(): void
    {
        foreach ($this->permissions as $categoryName => $permissionNames) {
            $category = PermissionCategory::firstOrCreate(['name' => $categoryName]);

            foreach ($permissionNames as $permissionName) {
                $permission = Permission::findOrCreate($permissionName, 'web');

                if (DB::table('permission_category_relations')->where('permission_id', $permission->id)->doesntExist()) {
                    $category->permissions()->attach($permission->id);
                }
            }
        }
    }
}
