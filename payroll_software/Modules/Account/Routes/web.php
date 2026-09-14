<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\{
    AccountReportsController,
    AccountSettingController,
    InventorySuppliersController,
    SaleCustomerController,
    SalesController,
    SalesProductController,
    SalesPaymentReceived,
    PurchaseController,
    PaymentController,
    AccountingExpenseController,
    Report\SalesReportController,
    Report\ReportController,
};


Route::prefix('admin/accounting')->name('admin.accounting.')->middleware('admin', 'auth')->group(function () {

    Route::get('customers', [SaleCustomerController::class, 'index'])->name('customer');
    Route::post('customer', [SaleCustomerController::class, 'store'])->name('customer.save');
    Route::post('customer/update', [SaleCustomerController::class, 'update'])->name('customer.update');
    Route::post('customer/delete', [SaleCustomerController::class, 'delete'])->name('customer.delete');

    Route::get('product/list', [SalesProductController::class, 'index'])->name('product.list');
    Route::post('product/add', [SalesProductController::class, 'store'])->name('product.store');
    Route::post('product/update', [SalesProductController::class, 'update'])->name('product.update');
    Route::get('product/units', [SalesProductController::class, 'units'])->name('units');
    Route::delete('product/{product}', [SalesProductController::class, 'delete'])->name('product.delete');

    // Sales
    Route::get('sale/list', [SalesController::class, 'index'])->name('sale.list');
    Route::get('sale/create', [SalesController::class, 'create'])->name('sale.create');
    Route::post('sale', [SalesController::class, 'store'])->name('sale.store');
    Route::get('sale/{id}', [SalesController::class, 'show']);
    Route::get('sale/edit/{id}', [SalesController::class, 'edit'])->name('sale.edit');
    Route::post('sale/update/{id}', [SalesController::class, 'update'])->name('sale.update');
    Route::get('sale/pdf/{id}', [SalesController::class, 'salesPDF'])->name('sale.pdf');

    // sales payment received
    Route::get('sales/payment-received/create', [SalesPaymentReceived::class, 'create'])->name('sales.payment.received.create');
    Route::post('sales/payment-received', [SalesPaymentReceived::class, 'store'])->name('sales.payment.received.store');
    Route::get('api/sales/payment-received/search', [SalesPaymentReceived::class, 'search'])->name('sales.payment.received.search');


    Route::get('/company/chart-of-account/records', [AccountSettingController::class, 'index'])->name('company.chart.of.account');
    Route::post('/company/chart-of-account/records-store', [AccountSettingController::class, 'storeChartOfAccountInfos'])->name('company.chart.of.account.info.store');
    Route::get('/company/chart-of-account/list', [AccountSettingController::class, 'listChartOfAccountInfos'])->name('company.chart.of.account.list');

    Route::get('/company/chart-of-mother-account/list-by-acct-type-id', [AccountSettingController::class, 'getChartOfAccountsMotherAccountlistForDropdown'])->name('company.chart.of.account.list.by.acct.type.id');
    // Route::get('api/chart-of-accounts', [AccountSettingController::class, 'listChartOfAccountInfosAPI']);
    Route::get('api/chart-of-accounts', [AccountSettingController::class, 'listChartOfAccountInfosAPI']);
    Route::get('api/chart-of-accounts/{id}', [AccountSettingController::class, 'closeSingleChartOfAccount']);
    Route::get('/company/chart-of-account/record-edit/{id}', [AccountSettingController::class, 'ChartOfAccountEditGet']);
    Route::post('/company/chart-of-account/record-update/{id}', [AccountSettingController::class, 'updateChartOfAccountInfos']);

    // Accounting API method
    Route::get('api/chart-of-accounts/get-unique-account-number/{id}', [AccountSettingController::class, 'getUniqueAccountNumberBySelectedLedgerAccount']);

    // Suppliers
    Route::get('/suppliers/index', [InventorySuppliersController::class, 'index'])->name('inventory.suppliers.list');
    Route::get('/suppliers/list-api', [InventorySuppliersController::class, 'listAPI']);
    Route::get('/suppliers/create', [InventorySuppliersController::class, 'create'])->name('inventory.suppliers.create');
    Route::post('/suppliers/store', [InventorySuppliersController::class, 'store']);
    Route::delete('/suppliers/delete/{id}', [InventorySuppliersController::class, 'destroy']);
    Route::get('/suppliers/edit/{id}', [InventorySuppliersController::class, 'edit']);
    Route::post('/suppliers/update/{id}', [InventorySuppliersController::class, 'updateAPI']);



    // Purchase
    Route::get('/purchase/bill', [PurchaseController::class, 'billCreate'])->name('purchase.bill.create');
    Route::post('/purchase/bill', [PurchaseController::class, 'storePurchase'])->name('purchase.bill.store');
    Route::get('/purchase/index', [PurchaseController::class, 'index'])->name('purchase.bill.list');
    Route::get('/api/purchase-invoice-list', [PurchaseController::class, 'listAPI'])->name('purchase.bill.api');
    Route::get('/purchase/details/{id}', [PurchaseController::class, 'show'])->name('purchase.bill.details');
    Route::get('/purchase/edit/{id}', [PurchaseController::class, 'edit'])->name('purchase.bill.edit');
    Route::post('/purchase/edit/{id}', [PurchaseController::class, 'updatePurchaseAPI'])->name('purchase.bill.edit');
    Route::get('/purchase/pdf/{id}', [PurchaseController::class, 'generatePurchasePdf'])->name('purchase.pdf');

    //! Purchase Payment routes
    Route::get('/purchase/payment/index', [PaymentController::class, 'billPaymentView'])->name('purchase.bill.payment');
    Route::post('/purchase/payment/store', [PaymentController::class, 'billPaymentSave'])->name('purchase.bill.payment.store');


    // Daily Expense Routes
    Route::get('/daily-expense', [AccountingExpenseController::class, 'index'])->name('daily.expense.index');
    Route::post('/daily-expense/store', [AccountingExpenseController::class, 'store'])->name('daily.expense.store');
    Route::get('/daily-expense-search', [AccountingExpenseController::class, 'search'])->name('daily.expense.search');
    // Route::put('/daily-expense/{id}', [AccountingExpenseController::class, 'update'])->name('daily.expense.update');
    // Route::delete('/daily-expense/{id}', [AccountingExpenseController::class, 'destroy'])->name('daily.expense.delete');


    // reports


    Route::get('/reports/report-ui', [SalesReportController::class, 'index'])->name('reports');

    Route::get('/reports/general-ledger', [AccountReportsController::class, 'generalLedger'])->name('reports.general.ledger');
    Route::post('/reports/general-ledger', [AccountReportsController::class, 'generalLedgerPdf'])->name('reports.general.ledger');
    Route::get('/reports/trial-balance', [AccountReportsController::class, 'trialBalance'])->name('reports.trial.balance');
    Route::post('/reports/trial-balance', [AccountReportsController::class, 'trialBalancePdf'])->name('reports.trial.balance');
    Route::get('/reports/profit-and-loss', [AccountReportsController::class, 'calculateProfitLoss'])->name('reports.profit.loss');
    Route::get('/reports/profit-and-loss/download', [AccountReportsController::class, 'downloadProfitLossReport'])->name('reports.profit.loss.download');
    Route::get('/reports/sales-report', [SalesReportController::class, 'salesReport'])->name('reports.sales.report');
    Route::get('/reports/sales-report/list', [SalesReportController::class, 'salesReportListAPI'])->name('reports.sales.report.api');
    Route::get('/reports/sales-report/pdf', [SalesReportController::class, 'downloadPDF'])->name('reports.sales.report.pdf');
    Route::get('/reports/expense/pdf', [SalesReportController::class, 'expensePDF'])->name('reports.expense.report.pdf');

    Route::get('/reports/blance-sheet', [AccountReportsController::class, 'generateBalanceSheet'])->name('reports.balance.sheet');
    Route::get('/reports/balance-sheet/download', [AccountReportsController::class, 'downloadBalanceSheet'])->name('reports.balance.sheet.download');


    // internal fund transfer
    Route::get('/internal-fund-transfer', [PaymentController::class, 'fundTransferView'])->name('internal.fund.transfer');
    Route::post('/internal-fund-transfer-store', [PaymentController::class, 'fundTransferSave'])->name('internal.fund.transfer.store');
});


// move  report module to accounting module
 

Route::prefix('admin/report')->name('admin.report.')->middleware('admin', 'auth')->group(function () {
    // Route::get('/', [ReportController::class, 'index'])->name('index');

    // Supplier Report
    Route::get('/suppliers', [ReportController::class, 'getSuppliers'])->name('suppliers');
    Route::post('/supplier-report', [ReportController::class, 'supplierReport'])->name('supplier.report');
    Route::get('/supplier-report/pdf', [ReportController::class, 'getSupplierReportPdf'])->name('supplier.report.pdf');

    // Sales Report
    Route::get('/customers', [ReportController::class, 'getCustomers'])->name('customers');
    Route::post('/sales-report', [ReportController::class, 'salesReport'])->name('sales.report');
    Route::get('/sales-report/pdf', [ReportController::class, 'getSalesReportPdf'])->name('sales.report.pdf');

    // Cash Transaction
    Route::get('/accounts', [ReportController::class, 'getAccounts'])->name('accounts');
    Route::post('/cash-transaction', [ReportController::class, 'cashTransaction'])->name('cash.transaction');
    Route::get('/cash-transaction/download', [ReportController::class, 'getCashTransactionDownload'])->name('cash.transaction.download');
    Route::get('/sales-purchase-summary', [ReportController::class, 'processGeneralLedgerSaleAndPurchaseSummaryReport'])->name('sales-purchase-summary');







    // expense report
    Route::get('/expense-details-report', [ReportController::class, 'getExpenseDetailsReport'])->name('expense.details.report');



    // Previous Three Month Reports (Used by External Comprehensive Live Dashboard)
    Route::get('/previous-three-month-report', [ReportController::class, 'previousThreeMonthReport'])->name('previous.three.month.report');
    Route::post('/previous-three-month-report', [ReportController::class, 'generatePreviousThreeMonthReport'])->name('previous.three.month.report.generate');

    // Previous Three Month Subcontractor Report (Used by External Comprehensive Live Dashboard)
    Route::get('/previous-three-month-subcontractor-report', [ReportController::class, 'previousThreeMonthSubcontractorReport'])->name('previous.three.month.subcontractor.report');
    Route::post('/previous-three-month-subcontractor-report', [ReportController::class, 'generatePreviousThreeMonthSubcontractorReport'])->name('previous.three.month.subcontractor.report.generate');
});
