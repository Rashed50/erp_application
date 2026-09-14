export const ProductList = '/admin/accounting/product/list';
export const ProductAdd = '/admin/accounting/product/add';

export const SalesCreateAPI = '/admin/accounting/sale';
export const SalesEditAPI = '/admin/accounting/sale/update';
export const SalesList = '/admin/accounting/sale/list';
export const SalesDetails = '/admin/accounting/sale';
export const SalesEdit = '/admin/accounting/sale/edit';

// Sales Payment
export const SalesPaymentReceivedCreate = '/admin/accounting/sales/payment-received/create'; //sales/payment-received/create
export const SalesUnpaidRecordAPI = '/api/admin/accounting/sales/customer/unpaid-records'
export const SalesPaymentReceived ='/admin/accounting/sales/payment-received';
export const SalesPaymentRecordSearchAPI = '/admin/accounting/api/sales/payment-received/search'

export const ChartOfMotherAccountListByAcctypeId = "/admin/accounting/company/chart-of-mother-account/list-by-acct-type-id";
export const ChartOfAccountList = "/admin/accounting/company/chart-of-account/list";
export const ChartOfAccountListAPI = "/admin/accounting/api/chart-of-accounts";
export const ChartOfAccountCreateGet = "/admin/accounting/company/chart-of-account/records"
export const ChartOfAccountCreate = "/admin/accounting/company/chart-of-account/records-store"
export const closeSingleChartOfAccount = '/admin/accounting/api/chart-of-accounts';
export const ChartOfAccountEditGet = '/admin/accounting/company/chart-of-account/record-edit';
export const ChartOfAccountEditPost = '/admin/accounting/company/chart-of-account/record-update';
export const ChartOfAccountUniqueAccountNumber = '/admin/accounting/api/chart-of-accounts/get-unique-account-number';


export const InventorySuppliersList = '/admin/accounting/suppliers/index';
export const InventorySuppliersListAPI = '/admin/accounting/suppliers/list-api';
export const InventorySuppliersCreate = '/admin/accounting/suppliers/create';
export const InventorySuppliersStore = '/admin/accounting/suppliers/store';
export const InventorySuppliersDelete = '/admin/accounting/suppliers/delete';
export const InventorySuppliersEdit = '/admin/accounting/suppliers/edit';
export const InventorySuppliersUpdate = '/admin/accounting/suppliers/update';

export const PurchaseInvoiceCreate = '/admin/accounting/purchase/bill';
export const PurchaseInvoiceStore = '/admin/accounting/purchase/bill';
export const PurchaseInvoiceList = '/admin/accounting/purchase/index';
export const PurchaseInvoiceListAPI = '/admin/accounting/api/purchase-invoice-list';
export const PurchaseInvoiceDetails = '/admin/accounting/purchase/details';
export const PurchaseInvoiceEdit = '/admin/accounting/purchase/edit';
export const PurchasePaymentStoreAPI = '/admin/accounting/purchase/payment/store';

export const DailyExpenseStoreAPII = '/admin/accounting/daily-expense/store';
export const DailyExpenseSearchAPI = '/admin/accounting/daily-expense-search';
export const DailyExpenseUpdateAPI = '/admin/accounting/daily-expense/update';
export const DailyExpenseDeleteAPI = '/admin/accounting/daily-expense/delete';

    // Route::get('/purchase/payment/index', [PaymentController::class, 'billPaymentView'])->name('purchasebill.payment');
    // Route::post('/purchase/payment/store', [PaymentController::class, 'billPaymentSave'])->name('purchase.bill.payment.store');

// internal fund transfer
export const InternalFundTransferStoreAPI = '/admin/accounting/internal-fund-transfer-store';


