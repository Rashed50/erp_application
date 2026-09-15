<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerTransactionController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierTransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRoleController;
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
});
