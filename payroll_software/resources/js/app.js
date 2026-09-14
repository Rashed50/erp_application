import "./bootstrap";


// 1. Import All CSS
// import '../../public/contents/admin/assets/css/salary-bootstrap.min.css';
// import '../../public/contents/admin/assets/css/datatables.min.css';
// import '../../public/contents/common/css/magnific-popup.css';
// import '../../public/contents/admin/assets/css/icons.css';
// import '../../public/contents/admin/assets/css/all.min.css';
// import '../../public/contents/admin/plugins/summernote/summernote-bs4.css';
// import '../../public/contents/admin/assets/css/moltran.css';
// import '../../public/contents/admin/assets/css/bootstrap-datepicker.min.css';
// import '../../public/contents/admin/assets/css/style.css';

// // 2. Import Libraries (Order Matters!)
// import '../../public/contents/admin/assets/js/jquery.min.js';
// import '../../public/contents/admin/assets/js/modernizr.min.js';
// import '../../public/contents/admin/assets/js/font_end_validation/jquery.validate.min.js';

// // 3. Import Custom Logic
// import '../../public/contents/admin/assets/js/font_end_validation/salary-details.js';
// import '../../public/contents/admin/assets/js/font_end_validation/employee-info.js';
// import '../../public/contents/admin/assets/js/font_end_validation/division.js';

// Import Vue
import { createApp } from "vue";

// Import Toaster
import "vue3-toastify/dist/index.css"; // Import Toaster CSS
import { toast } from 'vue3-toastify';

// Import Sweet Alart 2
import Swal from "sweetalert2";

// Import Multiselect
import "vue-multiselect/dist/vue-multiselect.css";

// Import UserPermission
import {useAuth } from "./components/useAuth";

// Create Vue Instance
const app = createApp({});

// Global Properties User Permission
app.provide('auth', useAuth())
app.provide('toast', toast)

//! Accounts Components
import {
    AccountModulesProductAdd,
    AccountModulesProducts,
    AccountModulesSalesAdd,
    AccountModulesSalesEdit,
    AccountModulesSalesCustomer,
    AccountModulesSalesIndex,
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
} from "../../Modules/Account/Resources/assets/js/app";

app.component("account-modules-products", AccountModulesProducts);
app.component("account-modules-product-add", AccountModulesProductAdd);
app.component("account-modules-sale-add", AccountModulesSalesAdd);
app.component("account-modules-sale-edit", AccountModulesSalesEdit);
app.component("account-modules-sale-index", AccountModulesSalesIndex);
app.component("account-modules-customers", AccountModulesSalesCustomer);
app.component("account-modules-product-units", AccountModulesSalesProductUnit);
app.component("chart-of-account-create", ChartOfAccountComponent);
app.component("chart-of-account-list", ChartOfAccountListComponent);
app.component("chart-of-account-update", ChartOfAccountUpdate);
app.component("inventory-suppliers-create", InventorySuppliersCreate);
app.component("inventory-suppliers-list", InventorySuppliersList);
app.component("inventory-suppliers-update", InventorySuppliersUpdate);
app.component("account-modules-general-ledger", GeneralLedgerReport);
app.component("account-modules-trial-balance", TrialBalanceReport);
app.component("account-modules-purchase-bill", PurchaseBill);
app.component("account-modules-purchase-list", PurchaseList);
app.component("account-modules-purchase-edit", PurchaseEdit);
app.component("account-modules-sales-report", SalesReportComponent);
app.component("account-modules-bill-payment", BillPaymentComponent);
app.component("account-modules-fund-transfer", FundTransferComponent);
app.component("account-modules-sales-payment", SalesPaymentReceived);
app.component('account-modules-report-container',ReportContainer);
app.component("daily-expense-component-manager", DailyExpenseComponentManager);






//! Advance Components
import {
    EmployeeAdvance,
    SingleAdvance,
   // MultipleAdvance,
    SearchEmployee,
    AdvanceReportPage,
    IqamaRenewalContainer,
} from "../../Modules/Advance/Resources/assets/js/app";

app.component("advance-modules-employee-advance", EmployeeAdvance);
app.component("employee-advance-single", SingleAdvance);
// app.component("employee-advance-multiple", MultipleAdvance);
app.component("employee-advance-search", SearchEmployee);
app.component("advance_report_page",AdvanceReportPage);
app.component('iqama-renewal-container',IqamaRenewalContainer);



//! Expense Components
import {
    TicketCreate,
    TicketList,
    TicketIndex,
} from "../../Modules/Expense/Resources/assets/js/app";

app.component("ticket-create", TicketCreate);
app.component("ticket-list", TicketList);
app.component("ticket-index", TicketIndex);

//! Tender Components
import { TenderCreate } from "../../Modules/Tender/Resources/assets/js/app";

app.component("tender-create", TenderCreate);

//! Employee Component
import { EmployeeCreate } from "../../Modules/Employee/Resources/assets/js/app";

app.component("employee-create-component", EmployeeCreate);

//! Subcontractor components
import {
    NewSubcontractorForm,
    EditSubcontractorForm,
    SubContractor,
    SearchSubcontractor,ReportsSubContractor,
} from "../../Modules/Subcontractor/Resources/assets/js/app";

app.component("subcontractor-create", NewSubcontractorForm);
app.component("subcontractor-edit", EditSubcontractorForm);
app.component("subcontractor-menu", SubContractor);
app.component("subcontractor_search", SearchSubcontractor);
app.component("subcontractor_all_reports", ReportsSubContractor);

//! subcontractor services
import {
    Sc_SerivceMenu,
    Sc_NewService,
    Sc_EditService,
    Sc_SearchService,
    Sc_Invoice,
} from "../../Modules/Subcontractor/Resources/assets/js/app";

app.component("sc_service_menu", Sc_SerivceMenu);
app.component("sc_new_service", Sc_NewService);
app.component("sc_edit_service", Sc_EditService);
app.component("sc_search_service", Sc_SearchService);
app.component("sc_invoice", Sc_Invoice);

//! subcontractor payment
import {
    Sc_PaymentMenu,
    Sc_NewPayment,
    Sc_EditPayment,
    Sc_SearchPayment,
} from "../../Modules/Subcontractor/Resources/assets/js/app";

app.component("sc_payment_menu", Sc_PaymentMenu);
app.component("sc_new_payment", Sc_NewPayment);
app.component("sc_edit_payment", Sc_EditPayment);
app.component("sc_search_payment", Sc_SearchPayment);

//! transportation  maintanance
import {
    ServicingMenu,
    VehicleServicing,
    SearchServicing,
    VehicleServiceName,
    EditVehicleService,
    VehicleMaintenanceReports,
} from "../../Modules/Transportation/Resources/assets/js/app";

app.component("vehicle_servicing_menu", ServicingMenu);
app.component("vehicle_servicing", VehicleServicing);
app.component("search_servicing", SearchServicing);
app.component("vehicle_service_name", VehicleServiceName);
app.component("vehicle_service_edit", EditVehicleService);
app.component("vehicle_maintenance_report",VehicleMaintenanceReports);



//! Hr Management Components
import {
    DailyActivityComponent,
    EmployeeUpdateContainer,
    EmployeeSearchComponent,
    LeaveAppManagerComponent,
    NewEmployeeContainer,
    EmployeeTransferContainer,
    EmployeeActivityContainer,


} from "../../Modules/HrManagement/Resources/assets/js/app";

app.component("hr-daily-activity", DailyActivityComponent);
app.component("employee_update_container", EmployeeUpdateContainer);
app.component("new_employee_container", NewEmployeeContainer);
app.component("employee_transfer_container", EmployeeTransferContainer);
app.component("employee_activity_container", EmployeeActivityContainer);
app.component("employee_search_component", EmployeeSearchComponent);
app.component("leave_appliation_manager", LeaveAppManagerComponent);



//! Payroll Components
import {
    EmployeeSalaryComponent,
    SalaryProcessComponent,
    AttendanceProcessComponent,
    AttendanceReportComponent,
    AttendanceINOUTComponent,
    OvertimeComponent,
    PartialSalaryHistoryManager,
    PayslipUploadManager,
    SalaryUpdateContainer,
    EmployeeBonusContainer,
    AttendanceBioComponent
} from "../../Modules/Payroll/Resources/assets/js/app";

app.component("payroll-employee-salary", EmployeeSalaryComponent);
app.component("employee_bonus", EmployeeBonusContainer);
app.component("salary_update", SalaryUpdateContainer);
app.component("salary_process",SalaryProcessComponent)
app.component("attendance_process",AttendanceProcessComponent)
app.component('attendance_report',AttendanceReportComponent)
app.component('attendance_in_out',AttendanceINOUTComponent)
app.component('overtime_component',OvertimeComponent)
app.component('attendence_bio_component',AttendanceBioComponent)

// Import your main component
//import PartialSalaryHistoryManager from '../../Modules/Payroll/Resources/assets/js/components/salary/PartialSalaryHistoryManager.vue';
// import SalarySheetManager from '../../Modules/Payroll/Resources/assets/js/components/payslip_upload/SalarySheetManager.vue';

// Register only the parent component globally
app.component('partial-salary-history-manager', PartialSalaryHistoryManager);
app.component('payslip-upload-manager', PayslipUploadManager);




//! Dashboard Components
import { DashboardContainer,TestView } from "../../Modules/Dashboard/Resources/assets/js/app";

app.component("live-dashboard", DashboardContainer);
// app.component("attendance-view", AttendanceView);
// app.component("comprehensive-view", ComprehensiveView);
// app.component("salary-view", SalaryView);
app.component("testview", TestView);

//DashboardContainer, AttendanceView, ComprehensiveView, SalaryView, TestView

//! Report Components
import {
    ReportMenu,
    SupplierReport,
    SalesReport,
    CashTransaction,
    PreviousThreeMonthReport,
    PreviousThreeMonthSubcontractorReport
} from "../../Modules/Report/Resources/assets/js/app";

app.component("report-menu", ReportMenu);
app.component("supplier-report", SupplierReport);
app.component("sales-report", SalesReport);
app.component("cash-transaction", CashTransaction);
app.component("previous-three-month-report", PreviousThreeMonthReport);
app.component("previous-three-month-subcontractor-report", PreviousThreeMonthSubcontractorReport);
 

// mounted components
app.mount("#app");
