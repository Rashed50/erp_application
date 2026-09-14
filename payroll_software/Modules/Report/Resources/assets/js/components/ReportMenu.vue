<template>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="showSupplierReport" v-if="hasPermission('supplier_report')">
                <i class="fa fa-truck me-2" aria-hidden="true"></i>
                Supplier Report
            </button>
            <button class="btn btn-success m-2" @click="showSalesReport" v-if="hasPermission('sales_report')">
                <i class="fa fa-shopping-cart me-2" aria-hidden="true"></i>
                Sales Report
            </button>
            <button class="btn btn-warning m-2" @click="showCashTransaction" v-if="hasPermission('cash_transaction')">
                <i class="fa fa-money me-2" aria-hidden="true"></i>
                Cash Transaction
            </button>
             <button class="btn btn-warning m-2" @click="showExpense" v-if="hasPermission('cash_transaction')">
                <i class="fa fa-money me-2" aria-hidden="true"></i>
                Expenses
            </button>
            <button class="btn btn-warning m-2" @click="showExpense" v-if="hasPermission('cash_transaction')">
                <i class="fa fa-money me-2" aria-hidden="true"></i>
                Ledger    
            </button>
        </div>
        <hr />
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <supplier-report :data_for_form="data_for_report_form"></supplier-report>
        </div>

        <div v-else-if="showForm == 2">
            <sales-report :data_for_form="data_for_report_form"></sales-report>
        </div>

        <div v-else-if="showForm == 3">
            <cash-transaction :data_for_form="data_for_report_form"></cash-transaction>
        </div>
        <div v-else-if="showForm == 4">
            <expense_component :data_for_form="data_for_report_form"></expense_component>
        </div>
        <div v-else-if="showForm == 5">
            <general_leder_report :data_for_form="data_for_report_form"></general_leder_report>
        </div>
    </div>
</template>

<script>
import SupplierReport from "./SupplierReport.vue";
import SalesReport from "./SalesReport.vue";
import CashTransaction from "./CashTransaction.vue";
import ExpensesComponent from "./ExpensesComponent.vue";
import GeneralLedgerComponent from "./GeneralLedgerComponent.vue";

export default {
    name: "ReportMenu",
    components: {
        "supplier-report": SupplierReport,
        "sales-report": SalesReport,
        "cash-transaction": CashTransaction,
        "expense_component":ExpensesComponent,
        'general_leder_report':GeneralLedgerComponent,
    },
    props: {
        data_for_report_form: {
            type: Object,
            required: false,
            default: () => ({}),
        },
    },
    data() {
        return {
            showForm: 1,
        };
    },
    methods: {
        hasPermission(permission) {
            // Check if user has the required permission
            // You can implement proper permission checking here
            return true;
        },
        showSupplierReport() {
            this.showForm = 1;
        },
        showSalesReport() {
            this.showForm = 2;
        },
        showCashTransaction() {
            this.showForm = 3;
        },
        showExpense(){
            this.showForm = 4;
        }
    },
};
</script>

<style scoped>
.btn {
    min-width: 150px;
}
</style>
