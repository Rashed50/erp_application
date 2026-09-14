<?php

use Illuminate\Support\Facades\Route;
use Modules\Subcontractor\Http\Controllers\SubcontractorController;
use Modules\Subcontractor\Http\Controllers\SubcontractorServiceController;
use Modules\Subcontractor\Http\Controllers\SubcontractorsPaymentController;
use Modules\Subcontractor\Http\Controllers\SubconReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::prefix('subcontractor')->group(function() {
//     Route::get('/', 'SubcontractorController@index');
// });



Route::prefix('admin/subcontractor')->name('admin.subcontractor.')->middleware('admin', 'auth')->group(function () {

    Route::get('divisions/{countryId}', [SubcontractorController::class, 'getDivisionsByCountry']);

    Route::prefix('info')->group(function () {
        //Route::get('/', 'SubcontractorController@index');
        Route::get('index/', [SubcontractorController::class, 'index'])->name('subcontractor.index');
        Route::post('store/', [SubcontractorController::class, 'store'])->name('subcontractor.store');
        Route::get('search/',  [SubcontractorController::class, 'searchSubContractor'])->name('subcontractor.search');
        Route::get('details/{id}', [SubcontractorController::class, 'show'])->name('subcontractor.details');
        Route::put('update/{id}', [SubcontractorController::class, 'update'])->name('subcontractor.update');
        Route::delete('delete/{id}', [SubcontractorController::class, 'destroy'])->name('subcontractor.destroy');
        Route::put('update-file/{id}', [SubcontractorController::class, 'updateFiles'])->name('subcontractor.file_update');
    });

    Route::prefix('service')->group(function () {
        Route::get('index/', [SubcontractorServiceController::class, 'index'])->name('service.index');
        Route::post('store/', [SubcontractorServiceController::class, 'store'])->name('service.store');
        Route::get('details/{id}', [SubcontractorServiceController::class, 'showServiceAPI'])->name('service.details');
        Route::put('update/{id}', [SubcontractorServiceController::class, 'updateServiceAPI'])->name('service.update');
        Route::delete('delete/{id}', [SubcontractorServiceController::class, 'destroyService'])->name('service.destroy');

        Route::post('work_salary_process/',  [SubcontractorServiceController::class, 'processSubcontractorWorkSalary'])->name('service.workd_salary_process');
        Route::post('search/', [SubcontractorServiceController::class, 'searchServicesForListView'])->name('service.search');
    });

    Route::prefix('payment')->group(function () {
        Route::get('index/', [SubcontractorsPaymentController::class, 'index'])->name('payment.index');
        Route::post('store/', [SubcontractorsPaymentController::class, 'store'])->name('payment.store');
        Route::post('invoice/', [SubcontractorsPaymentController::class, 'generateInvoice'])->name('payment.invoice');
        Route::get('api/summary/{id}',  [SubcontractorsPaymentController::class, 'processASubcontractorOverAllInvoiceAndPaymentSummary'])->name('payment.summary');
        Route::post('search/', [SubcontractorsPaymentController::class, 'searchPaymentForListView'])->name('payment.search');
        Route::get('details/{id}', [SubcontractorsPaymentController::class, 'showPaymentAPI'])->name('payment.details');
        Route::put('update/{id}', [SubcontractorsPaymentController::class, 'updatePaymentAPI'])->name('payment.update');
        Route::delete('delete/{id}', [SubcontractorsPaymentController::class, 'destroyPaymentAPI'])->name('payment.destroy');
    });
    Route::prefix('report')->group(function () {
        Route::get('reports-generation-page', [SubconReportController::class, 'index'])->name('reports.generation.index');
        Route::get('/single-month-service-payment/pdf', [SubconReportController::class, 'SingleMonthServiceAndPaymentDownloadPDF'])->name('single.month.serviceandpayment.pdf');
        Route::get('/report-pdf', [SubconReportController::class, 'report_pdf'])->name('sales.report.pdf');
    });
});
