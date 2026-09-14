<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

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

// Route::prefix('admin/report')->name('admin.report.')->middleware('admin', 'auth')->group(function () {
//     Route::get('/', [ReportController::class, 'index'])->name('index');

//     // Supplier Report
//     Route::get('/suppliers', [ReportController::class, 'getSuppliers'])->name('suppliers');
//     Route::post('/supplier-report', [ReportController::class, 'supplierReport'])->name('supplier.report');
//     Route::get('/supplier-report/pdf', [ReportController::class, 'getSupplierReportPdf'])->name('supplier.report.pdf');

//     // Sales Report
//     Route::get('/customers', [ReportController::class, 'getCustomers'])->name('customers');
//     Route::post('/sales-report', [ReportController::class, 'salesReport'])->name('sales.report');
//     Route::get('/sales-report/pdf', [ReportController::class, 'getSalesReportPdf'])->name('sales.report.pdf');

//     // Cash Transaction
//     Route::get('/accounts', [ReportController::class, 'getAccounts'])->name('accounts');
//     Route::post('/cash-transaction', [ReportController::class, 'cashTransaction'])->name('cash.transaction');
//     Route::get('/cash-transaction/download', [ReportController::class, 'getCashTransactionDownload'])->name('cash.transaction.download');
//     Route::get('/sales-purchase-summary', [ReportController::class, 'processGeneralLedgerSaleAndPurchaseSummaryReport'])->name('sales-purchase-summary');







//     // expense report
//     Route::get('/expense-details-report', [ReportController::class, 'getExpenseDetailsReport'])->name('expense.details.report');



//     // Previous Three Month Reports (Used by External Comprehensive Live Dashboard)
//     Route::get('/previous-three-month-report', [ReportController::class, 'previousThreeMonthReport'])->name('previous.three.month.report');
//     Route::post('/previous-three-month-report', [ReportController::class, 'generatePreviousThreeMonthReport'])->name('previous.three.month.report.generate');

//     // Previous Three Month Subcontractor Report (Used by External Comprehensive Live Dashboard)
//     Route::get('/previous-three-month-subcontractor-report', [ReportController::class, 'previousThreeMonthSubcontractorReport'])->name('previous.three.month.subcontractor.report');
//     Route::post('/previous-three-month-subcontractor-report', [ReportController::class, 'generatePreviousThreeMonthSubcontractorReport'])->name('previous.three.month.subcontractor.report.generate');
// });
