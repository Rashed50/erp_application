<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\Api\ProductController;
use Modules\Account\Http\Controllers\Api\UnitController;
use Modules\Account\Http\Controllers\SalesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('/admin/accounting')->name('admin.accounting.')->middleware('admin', 'auth')->group(function () {
    /* ================ Unit controller ================ */
    Route::resource('unit', UnitController::class);

    /* ================ Product controller ================ */
    Route::resource('product', ProductController::class);


    /* ================ Sales controller ================ */
    Route::get('sale/list', 'SalesController@index');
    Route::get('sales/customer/unpaid-records/{id}', [SalesController::class, 'getACustomerUnpiadSalesRecordsAPI']);

    /* ================ Sales controller ================ */
    // Route::get('sale/list', 'SalesController@index')
    // Route::get('sale/create', 'SalesController@create');


});
