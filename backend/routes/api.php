<?php

use App\Http\Controllers\Api\AccountTypeController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChartOfAccountController;
use App\Http\Controllers\Api\CompanySettingController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerTransactionController;
use App\Http\Controllers\Api\FundTransferController;
use App\Http\Controllers\Api\Hr\EmployeeController;
use App\Http\Controllers\Api\Hr\EmployeeFileController;
use App\Http\Controllers\Api\Hr\EmployeeWorkController;
use App\Http\Controllers\Api\Hr\HrReportController;
use App\Http\Controllers\Api\Hr\PayrollController;
use App\Http\Controllers\Api\Hr\SalaryDetailController;
use App\Http\Controllers\Api\IncomeExpenseTransactionController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\PurchasePaymentController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SalePaymentController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierPaymentController;
use App\Http\Controllers\Api\SupplierTransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/user', function (Request $request) {
        return ApiResponse::success(new UserResource($request->user()->load('roles')));
    });

    // Every signed-in user needs the company branding (sidebar logo); only editing is gated.
    Route::get('/settings', [CompanySettingController::class, 'show'])->name('settings.show');
    Route::post('/settings', [CompanySettingController::class, 'update'])
        ->name('settings.update')
        ->middleware('permission:settings.update');

    Route::apiResource('users', UserController::class)
        ->middlewareFor(['index', 'show'], 'permission:users.view')
        ->middlewareFor('store', 'permission:users.create')
        ->middlewareFor('update', 'permission:users.update')
        ->middlewareFor('destroy', 'permission:users.delete');

    Route::post('/users/{user}/roles', [UserRoleController::class, 'store'])
        ->name('users.roles.store')
        ->middleware('permission:users.update');
    Route::delete('/users/{user}/roles/{role}', [UserRoleController::class, 'destroy'])
        ->name('users.roles.destroy')
        ->middleware('permission:users.update');

    Route::apiResource('roles', RoleController::class)
        ->middlewareFor(['index', 'show'], 'permission:roles.view')
        ->middlewareFor('store', 'permission:roles.create')
        ->middlewareFor('update', 'permission:roles.update')
        ->middlewareFor('destroy', 'permission:roles.delete');

    Route::get('/permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:roles.view');

    Route::apiResource('customers', CustomerController::class)
        ->middlewareFor(['index', 'show'], 'permission:customers.view')
        ->middlewareFor('store', 'permission:customers.create')
        ->middlewareFor('update', 'permission:customers.update')
        ->middlewareFor('destroy', 'permission:customers.delete');

    Route::apiResource('work-orders', WorkOrderController::class)
        ->middlewareFor(['index', 'show'], 'permission:work-orders.view')
        ->middlewareFor('store', 'permission:work-orders.create')
        ->middlewareFor('update', 'permission:work-orders.update')
        ->middlewareFor('destroy', 'permission:work-orders.delete');

    // The sale form needs a customer's work orders even without work-orders.view.
    Route::get('/customers/{customer}/work-orders', [WorkOrderController::class, 'forCustomer'])
        ->name('customers.work-orders.index')
        ->middleware('permission:work-orders.view|sales.create|sales.update');

    Route::apiResource('products', ProductController::class)
        ->middlewareFor(['index', 'show'], 'permission:products.view')
        ->middlewareFor('store', 'permission:products.create')
        ->middlewareFor('update', 'permission:products.update')
        ->middlewareFor('destroy', 'permission:products.delete');

    Route::get('/customers/{customer}/transactions', [CustomerTransactionController::class, 'index'])
        ->name('customers.transactions.index')
        ->middleware('permission:customer-transactions.view');
    Route::post('/customers/{customer}/transactions', [CustomerTransactionController::class, 'store'])
        ->name('customers.transactions.store')
        ->middleware('permission:customer-transactions.create');
    Route::delete('/customers/{customer}/transactions/{transaction}', [CustomerTransactionController::class, 'destroy'])
        ->name('customers.transactions.destroy')
        ->middleware('permission:customer-transactions.delete');

    Route::apiResource('suppliers', SupplierController::class)
        ->middlewareFor(['index', 'show'], 'permission:suppliers.view')
        ->middlewareFor('store', 'permission:suppliers.create')
        ->middlewareFor('update', 'permission:suppliers.update')
        ->middlewareFor('destroy', 'permission:suppliers.delete');

    Route::get('/suppliers/{supplier}/transactions', [SupplierTransactionController::class, 'index'])
        ->name('suppliers.transactions.index')
        ->middleware('permission:supplier-transactions.view');
    Route::post('/suppliers/{supplier}/transactions', [SupplierTransactionController::class, 'store'])
        ->name('suppliers.transactions.store')
        ->middleware('permission:supplier-transactions.create');
    Route::delete('/suppliers/{supplier}/transactions/{transaction}', [SupplierTransactionController::class, 'destroy'])
        ->name('suppliers.transactions.destroy')
        ->middleware('permission:supplier-transactions.delete');

    Route::apiResource('purchases', PurchaseController::class)
        ->middlewareFor(['index', 'show'], 'permission:purchases.view')
        ->middlewareFor('store', 'permission:purchases.create')
        ->middlewareFor('update', 'permission:purchases.update')
        ->middlewareFor('destroy', 'permission:purchases.delete');

    // Supplier (purchase bill) payments, at the same URLs as the payroll_software
    // Account module's admin/accounting/purchase/payment/* pages.
    Route::prefix('/accounting/purchase/payment')->name('supplier-payments.')->group(function () {
        Route::get('/list', [SupplierPaymentController::class, 'index'])
            ->name('index')
            ->middleware('permission:supplier-payments.view');
        Route::post('/store', [SupplierPaymentController::class, 'store'])
            ->name('store')
            ->middleware('permission:supplier-payments.create');
        Route::get('/{supplier_payment}', [SupplierPaymentController::class, 'show'])
            ->whereNumber('supplier_payment')
            ->name('show')
            ->middleware('permission:supplier-payments.view');
        Route::delete('/{supplier_payment}', [SupplierPaymentController::class, 'destroy'])
            ->whereNumber('supplier_payment')
            ->name('destroy')
            ->middleware('permission:supplier-payments.delete');
    });

    // Internal fund transfers, at the same URLs as the payroll_software Account
    // module's admin/accounting/internal-fund-transfer* pages.
    Route::prefix('/accounting')->name('fund-transfers.')->group(function () {
        Route::get('/internal-fund-transfer/list', [FundTransferController::class, 'index'])
            ->name('index')
            ->middleware('permission:fund-transfers.view');
        Route::post('/internal-fund-transfer-store', [FundTransferController::class, 'store'])
            ->name('store')
            ->middleware('permission:fund-transfers.create');
        Route::get('/internal-fund-transfer/{fund_transfer}', [FundTransferController::class, 'show'])
            ->whereNumber('fund_transfer')
            ->name('show')
            ->middleware('permission:fund-transfers.view');
        Route::delete('/internal-fund-transfer/{fund_transfer}', [FundTransferController::class, 'destroy'])
            ->whereNumber('fund_transfer')
            ->name('destroy')
            ->middleware('permission:fund-transfers.delete');
    });

    Route::post('/purchases/{purchase}/payments', [PurchasePaymentController::class, 'store'])
        ->name('purchases.payments.store')
        ->middleware('permission:purchase-payments.create');

    Route::apiResource('sales', SaleController::class)
        ->middlewareFor(['index', 'show'], 'permission:sales.view')
        ->middlewareFor('store', 'permission:sales.create')
        ->middlewareFor('update', 'permission:sales.update')
        ->middlewareFor('destroy', 'permission:sales.delete');

    Route::post('/sales/{sale}/payments', [SalePaymentController::class, 'store'])
        ->name('sales.payments.store')
        ->middleware('permission:sale-payments.create');

    Route::get('/account-types', [AccountTypeController::class, 'index'])
        ->name('account-types.index')
        ->middleware('permission:ledger-accounts.view');

    Route::get('/ledger-accounts/{ledger_account}/next-account-number', [ChartOfAccountController::class, 'nextAccountNumber'])
        ->name('ledger-accounts.next-account-number')
        ->middleware('permission:ledger-accounts.create|ledger-accounts.update');

    Route::apiResource('ledger-accounts', ChartOfAccountController::class)
        ->middlewareFor(['index', 'show'], 'permission:ledger-accounts.view')
        ->middlewareFor('store', 'permission:ledger-accounts.create')
        ->middlewareFor('update', 'permission:ledger-accounts.update')
        ->middlewareFor('destroy', 'permission:ledger-accounts.delete');

    Route::apiResource('income-expenses', IncomeExpenseTransactionController::class)
        ->middlewareFor(['index', 'show'], 'permission:income-expenses.view')
        ->middlewareFor('store', 'permission:income-expenses.create')
        ->middlewareFor('update', 'permission:income-expenses.update')
        ->middlewareFor('destroy', 'permission:income-expenses.delete');
    Route::post('/customers/{customer}/approve', [ApprovalController::class, 'customer'])
        ->name('customers.approve')
        ->middleware('permission:customers.approve');

    Route::post('/work-orders/{work_order}/approve', [ApprovalController::class, 'workOrder'])
        ->name('work-orders.approve')
        ->middleware('permission:work-orders.approve');

    Route::post('/suppliers/{supplier}/approve', [ApprovalController::class, 'supplier'])
        ->name('suppliers.approve')
        ->middleware('permission:suppliers.approve');

    Route::post('/purchases/{purchase}/approve', [ApprovalController::class, 'purchase'])
        ->name('purchases.approve')
        ->middleware('permission:purchases.approve');

    Route::post('/sales/{sale}/approve', [ApprovalController::class, 'sale'])
        ->name('sales.approve')
        ->middleware('permission:sales.approve');

    Route::post('/accounting/internal-fund-transfer/{fund_transfer}/approve', [ApprovalController::class, 'fundTransfer'])
        ->name('fund-transfers.approve')
        ->middleware('permission:fund-transfers.approve');

    Route::post('/accounting/purchase/payment/{supplier_payment}/approve', [ApprovalController::class, 'supplierPayment'])
        ->name('supplier-payments.approve')
        ->middleware('permission:supplier-payments.approve');

    Route::post('/ledger-accounts/{ledger_account}/approve', [ApprovalController::class, 'ledgerAccount'])
        ->name('ledger-accounts.approve')
        ->middleware('permission:ledger-accounts.approve');

    Route::post('/income-expenses/{income_expense}/approve', [ApprovalController::class, 'incomeExpense'])
        ->name('income-expenses.approve')
        ->middleware('permission:income-expenses.approve');

    /* ====================== HR & Payroll ====================== */
    Route::prefix('/hr')->name('hr.')->group(function () {
        Route::get('/dashboard', [HrReportController::class, 'dashboard'])
            ->name('dashboard')
            ->middleware('permission:employees.view|payroll.view');

        Route::get('/employees/options', [EmployeeController::class, 'options'])
            ->name('employees.options')
            ->middleware('permission:employees.view|employee-works.view|payroll.view|hr-reports.view');

        // Employee pickers on the work, payroll and report screens use the list too.
        Route::get('/employees', [EmployeeController::class, 'index'])
            ->name('employees.index')
            ->middleware('permission:employees.view|employee-works.view|hr-reports.view');
        Route::apiResource('employees', EmployeeController::class)
            ->except('index')
            ->middlewareFor('show', 'permission:employees.view')
            ->middlewareFor('store', 'permission:employees.create')
            ->middlewareFor('update', 'permission:employees.update')
            ->middlewareFor('destroy', 'permission:employees.delete');

        Route::post('/employees/{employee}/files', [EmployeeFileController::class, 'store'])
            ->name('employees.files.store')
            ->middleware('permission:employees.update');
        Route::get('/employee-files/{employee_file}/download', [EmployeeFileController::class, 'download'])
            ->name('employee-files.download')
            ->middleware('permission:employees.view');
        Route::delete('/employee-files/{employee_file}', [EmployeeFileController::class, 'destroy'])
            ->name('employee-files.destroy')
            ->middleware('permission:employees.update');

        Route::get('/employees/{employee}/salary-details', [SalaryDetailController::class, 'index'])
            ->name('employees.salary-details.index')
            ->middleware('permission:salary-configs.view');
        Route::post('/employees/{employee}/salary-details', [SalaryDetailController::class, 'store'])
            ->name('employees.salary-details.store')
            ->middleware('permission:salary-configs.create');
        Route::put('/salary-details/{salary_detail}', [SalaryDetailController::class, 'update'])
            ->name('salary-details.update')
            ->middleware('permission:salary-configs.update');
        Route::delete('/salary-details/{salary_detail}', [SalaryDetailController::class, 'destroy'])
            ->name('salary-details.destroy')
            ->middleware('permission:salary-configs.delete');

        Route::get('/employee-works/find', [EmployeeWorkController::class, 'find'])
            ->name('employee-works.find')
            ->middleware('permission:employee-works.view');
        Route::get('/employee-works', [EmployeeWorkController::class, 'index'])
            ->name('employee-works.index')
            ->middleware('permission:employee-works.view|hr-reports.view');
        Route::apiResource('employee-works', EmployeeWorkController::class)
            ->only(['store', 'update', 'destroy'])
            ->middlewareFor('store', 'permission:employee-works.create')
            ->middlewareFor('update', 'permission:employee-works.update')
            ->middlewareFor('destroy', 'permission:employee-works.delete');

        Route::get('/payroll/preview', [PayrollController::class, 'preview'])
            ->name('payroll.preview')
            ->middleware('permission:payroll.generate');
        Route::post('/payroll/generate', [PayrollController::class, 'generate'])
            ->name('payroll.generate')
            ->middleware('permission:payroll.generate');
        Route::get('/salary-histories', [PayrollController::class, 'index'])
            ->name('salary-histories.index')
            ->middleware('permission:payroll.view|hr-reports.view');
        Route::post('/salary-histories/approve', [PayrollController::class, 'approve'])
            ->name('salary-histories.approve')
            ->middleware('permission:payroll.approve');
        Route::post('/salary-histories/pay', [PayrollController::class, 'pay'])
            ->name('salary-histories.pay')
            ->middleware('permission:payroll.pay');
        Route::get('/salary-histories/{salary_history}', [PayrollController::class, 'show'])
            ->whereNumber('salary_history')
            ->name('salary-histories.show')
            ->middleware('permission:payroll.view');
        Route::post('/salary-histories/{salary_history}/cancel', [PayrollController::class, 'cancel'])
            ->whereNumber('salary_history')
            ->name('salary-histories.cancel')
            ->middleware('permission:payroll.cancel');

        Route::get('/reports/department-wise', [HrReportController::class, 'departmentWise'])
            ->name('reports.department-wise')
            ->middleware('permission:hr-reports.view');
        Route::get('/reports/salary-summary', [HrReportController::class, 'salarySummary'])
            ->name('reports.salary-summary')
            ->middleware('permission:hr-reports.view');
    });
});
