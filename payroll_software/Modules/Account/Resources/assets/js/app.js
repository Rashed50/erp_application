/* import components */
import AccountModulesProducts from './components/Products/Index.vue';
import AccountModulesProductAdd from './components/Products/Add.vue';

/* ==================== sale components ==================== */
import AccountModulesSalesAdd from './components/sales/Add.vue';
import AccountModulesSalesEdit from './components/sales/Edit.vue';
import AccountModulesSalesIndex from './components/sales/Index.vue';

/* ==================== sale payment received ==================== */
import SalesPaymentReceived from './components/sales/SalesPaymentReceived.vue';

/* ============= Customer Component ===============*/
import AccountModulesSalesCustomer from './components/customers/index.vue';
import AccountModulesSalesProductUnit from './components/products/units.vue';
import ChartOfAccountComponent from './components/Account/ChartOfAccountComponent.vue';
import ChartOfAccountListComponent from './components/Account/ChartOfAccountListComponent.vue';
import ChartOfAccountUpdate from './components/Account/ChartOfAccountUpdate.vue';

/* ============= Inventory Suppliers Component ===============*/
import InventorySuppliersCreate from './components/Suppliers/CreateComponent.vue';
import InventorySuppliersList from './components/Suppliers/ListComponent.vue';
import InventorySuppliersUpdate from './components/Suppliers/UpdateComponent.vue';

/* ============= Inventory Suppliers Component ===============*/
import GeneralLedgerReport from './components/Reports/GeneralLedgerReport.vue';
import TrialBalanceReport from './components/Reports/TrialBalanceReport.vue';

/* ==================== sale components ==================== */
import PurchaseBill from './components/Purchase/Bill.vue';
import PurchaseList from './components/Purchase/List.vue';
import PurchaseEdit from './components/Purchase/Edit.vue';


import DailyExpenseComponentManager from './components/Purchase/DailyExpenseManagerComponent.vue';

// ====================== Reports ======================
import SalesReportComponent from './components/Reports/SalesReport.vue';



// ====================== Payments ======================
import BillPaymentComponent from './components/payment/Bill/PaymentComponent.vue';
import FundTransferComponent from './components/payment/Fund/TransferComonent.vue';
import ReportContainer from './components/Reports/ReportContainer.vue';
import 'vue3-toastify/dist/index.css';


/* export components */
export {
    AccountModulesProducts,
    AccountModulesProductAdd,
    AccountModulesSalesAdd,
    AccountModulesSalesEdit,
    AccountModulesSalesIndex,
    AccountModulesSalesCustomer,
    AccountModulesSalesProductUnit,
    ChartOfAccountComponent,
    ChartOfAccountListComponent,
    ChartOfAccountUpdate,
    InventorySuppliersCreate,
    InventorySuppliersList,
    InventorySuppliersUpdate,
    GeneralLedgerReport,
    TrialBalanceReport,
    PurchaseBill,
    PurchaseList,
    PurchaseEdit,
    SalesReportComponent,
    BillPaymentComponent,
    FundTransferComponent,
    SalesPaymentReceived,
    DailyExpenseComponentManager,
    ReportContainer
}
