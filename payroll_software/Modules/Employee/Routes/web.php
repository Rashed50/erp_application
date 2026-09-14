<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\EmployeeController;

Route::prefix('admin/employee')->name('admin.employee.')->middleware('admin', 'auth')->group(function () {

    Route::prefix('/')->group(function () {
        Route::get('create/', [EmployeeController::class, 'create'])->name('employee.create');
        Route::get('payslip/', [EmployeeController::class, 'payslip'])->name('employee.payslip');
    });
});
