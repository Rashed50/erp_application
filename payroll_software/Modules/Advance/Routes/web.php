<?php

use Illuminate\Support\Facades\Route;
use Modules\Advance\Http\Controllers\Employee\EmployeeAdvanceController;
use Modules\Advance\Http\Controllers\Employee\IqamaRenewalController;
use App\Http\Controllers\Admin\AdvancePayController;
use Modules\Advance\Http\Controllers\Employee\CashRececivedController;

Route::prefix('admin/advance')->name('admin.advance.')->middleware('admin', 'auth')->group(function () {

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeAdvanceController::class, 'index'])->name('employee.advance');
        Route::post('payment/project-wise-emp-list',  [EmployeeAdvanceController::class, 'getEmployeeListForMultipleEmployeeAdvancePayment'])->name('employee.advance.getEmployeeListForMultipleEmployeeAdvancePayment');
        Route::post('multiple-payment', [EmployeeAdvanceController::class, 'multipleEmployeeAdvanceInsertRequest'])->name('employee.advance.multipleEmployeeAdvanceInsertRequest');
        Route::get('report-process', [EmployeeAdvanceController::class, 'processAdvanceReport'])->name('advance.report.process');
        Route::post('single-employee-insert', [AdvancePayController::class, 'insert'])->name('single.employee.insert');
        Route::post('advance-information-update', [AdvancePayController::class, 'updateEmployeeAdvanceInformation'])->name('update.employee.advance.information');
        Route::post('advance-search', [AdvancePayController::class, 'employeeAdvanceListSearch'])->name('employee.advance.list.search');
        Route::get('advance-delete/{adv_pay_id}', [AdvancePayController::class, 'delete'])->name('delete-advance.pay');
        Route::post('advance-processing', [EmployeeAdvanceController::class, 'employeeAdvanceProcessingRequest'])->name('employee.advance.processing');


        //Iqama Renewal
        Route::get('/iqama-renewal', [IqamaRenewalController::class, 'iqamarenewal'])->name('iqama.renewal');
        // Route::post('/iqama-renewal/search', [IqamaRenewalController::class, 'searchAnEmployeeIqamaRenewalExpenseRecords'])->name('iqama.renewal.search');
        Route::post('/iqama-renewal/search', [IqamaRenewalController::class, 'searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest'])->name('iqama.renewal.search');
        Route::delete('delete/iqama-anual/fee/{IqamaRenewId}', [IqamaRenewalController::class, 'deleteAnEmployeeIqamaAnualExpenseBeforeApproval'])->name('anemp.iqama.renewal.expense.delete.before.approval');

        // new iqama renewal add
        Route::post('/new-iqama-renewal-store', [IqamaRenewalController::class, 'insert']);
        // update 
        Route::post('update/iqama-anual/fee/for-employee', [IqamaRenewalController::class, 'update'])->name('update-iqamarenewal-fee');
        Route::post('update/iqama-anual/fee/approve-of-multi-employeee', [IqamaRenewalController::class, 'approveOfMultiEmployeeeIqamaRenewalExpenseRecord'])->name('update-iqamarenewal-fee');




        Route::get('/advance-deduction-setting', [IqamaRenewalController::class, 'findanEmployeeWithAdvanceSetting']);






        Route::post('/payment-received-store', [CashRececivedController::class, 'advancePaymentReceivedFromEmployee']);
        Route::post('/search-payment-received', [CashRececivedController::class, 'searchCashReceivedRecord']);
        Route::delete('payment/receive-delete/{id}', [CashRececivedController::class, 'deleteCashDepositAdvancePayment'])->name('delete-employee-advance-payment-cash-receive');
    });
});
