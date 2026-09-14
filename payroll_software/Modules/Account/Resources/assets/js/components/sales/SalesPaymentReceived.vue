<template>
    <!-- <div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <button class="btn btn-success btn-sm mt-2" type="button" data-toggle="modal" data-target="#payment_received_modal" >
                                <i class="fa fa-plus"></i>
                                Payment Received
                            </button>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <button class="btn btn-success btn-sm mt-2" type="button" data-toggle="modal" data-target="#payment_received_modal" >
                                <i class="fa fa-plus"></i>
                                Payment Received
                            </button></div>


                </div>

        </div>
    </div> -->

    <div class="row" >
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" type="button" data-toggle="modal"
                data-target="#payment_received_modal" v-if="hasPermission('accounts_sales_payment_create')">
                <i class="fa fa-plus"></i>
                Payment Received
            </button>
            <!-- <button class="btn btn-primary m-2" @click="createNew" v-if="hasPermission('add_new_subcontractor')" >New</button> -->
            <button class="btn btn-primary m-2" @click="search" v-if="hasPermission('accounts_sales_payment_create')">  Search </button>
        </div>
        <hr />
    </div>
    <div class="row">
        <!-- <div v-if="showForm == 1">
            <subcontractor-create
                :data_for_form="data_for_subcontractor_form"
            ></subcontractor-create>
        </div>

        <div v-else-if="showForm == 2">
            <subcontractor_search
                @edit-subcontractor="editPage"
            ></subcontractor_search>
        </div>

         <div v-else-if="showForm == 4">
            <subcontractor-edit
                :subcontractor-id="currentEditId"
                :data_for_form="data_for_subcontractor_form"
            >
            </subcontractor-edit>
        </div> -->


    </div>


    <div v-if="showForm == 2">
        <!-- breadcrumb -->
          <div class="row mb-2">
                <label class="form-label text-end d-block col-md-1">Paid By </label>
                <div class="col-md-3">
                    <Multiselect required v-model="paid_customer_id" placeholder="Select item"
                        :options="filterPaidCustomer" :multiple="false" :searchable="true" />
                </div>
                 <label class="form-label text-end d-block col-md-1">From <span class="req_star">*</span> </label>
                <div class="col-md-2">
                      <input type="date" required class="form-control" v-model="from_date" />
                </div>
                 <label class="form-label text-end d-block col-md-1">To <span class="req_star">*</span> </label>
                <div class="col-md-2">
                        <input type="date" required class="form-control" v-model="to_date" />
                </div>
                <div class="col-md-2">
                     <button class="btn btn-primary m-2" @click="searchPaymentRecords" v-if="hasPermission('search_subcontractor')" :disabled="is_loading">
                        <i v-if="is_searching" class="fa fa-spinner fa-spin"></i>
                        <i v-else class="fa fa-search"></i> &nbsp; {{ is_searching ? 'Searching...' : 'Search' }}
                    </button>
                </div>

        </div>
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Search and Page Set -->
                        <!-- <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length" id="alltableinfo_length">
                                    <label><strong>Show </strong>
                                        <select v-model="perPage" @change="fetchTableData"
                                            class="custom-select custom-select-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <strong> entries</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div id="alltableinfo_filter" class="dataTables_filter">
                                    <label><strong>Search:</strong>
                                        <input v-model="search" @input="fetchTableData" type="search"
                                            :class="{ 'sky-border': search.length > 0 }"
                                            class="form-control form-control-lg" placeholder="Search...">
                                    </label>
                                </div>
                            </div>
                        </div> -->

                        <table class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Inv. No.</th>
                                    <th>Received From</th>
                                    <th>Paid At</th>
                                    <th>Remarks</th>
                                    <th>Created By</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in this.payment_received_records" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.invoice_no }}</td>
                                    <td>{{ item.customer_name }}</td>
                                    <!-- <td>{{ item.account_no }}</td> -->
                                    <td>{{ item.transaction_date }}</td>
                                    <td>{{ item.notes }}</td>
                                    <td>{{ item.created_by_name }}</td>
                                    <td>{{ item.credit }}</td>

                                    <td>
                                        <div class="flex-container">
                                            <a class="view_btn" v-if="hasPermission('accounts_sales_invoice_view')"  @click="viewPage(item.sr_auto_id)">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            <!-- <a v-if="  hasPermission('accounts_sales_invoice_edit') " class="edit_btn"
                                                @click="editPage(item.sr_auto_id)">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a> -->
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Payment Received Modal -->
    <div class="modal fade" id="payment_received_modal" tabindex="-1" aria-labelledby="example2ModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center">
                            Payment received
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" @submit.prevent="submitPaymentReceived">

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block">Payment From <span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="paid_customer_id" placeholder="Select item"
                                        :options="filterPaidCustomer" :multiple="false" :searchable="true" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block">Account(DR)<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="paid_account_debit_id" :options="paymentReceivedDebitAccount"
                                        placeholder="Select Item" searchable="true" />
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block"> Account (CR)<span class="req_star">*</span> </label>
                                    <!-- Credit Account --> :
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="paid_account_credit_id" :options="paymentReceivedCreditAccounts"
                                        placeholder="Select item" :searchable="true" />
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block">Invoice No<span class="req_star">*</span></label>
                                </div>
                                <div class="col-md-9">
                                     <!-- <select class="form-select" required v-model="paid_invoice_no">
                                        <option value="">Select One</option>
                                         <option
                                            v-for="item in this.data.unpaid_sales_records"
                                            :key="item.sr_auto_id"
                                            :value="item.sr_auto_id"
                                        >
                                            {{ item.sr_invoice_no }}
                                        </option>
                                    </select> -->
                                    <Multiselect required v-model="paid_invoice_no" :options="filteredUnpaidInvoices"
                                        placeholder="Select item" :searchable="true" />
                                </div>

                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block">Amount<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" step="1" required class="form-control" v-model="paid_amount" />
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label text-end d-block">Paid At<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" required class="form-control" v-model="paid_date" />
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Remarks </label>
                                </div>
                                <div class="col-md-9">
                                    <input class="form-control" placeholder="Invoice desc... " v-model="paid_remarks" />
                                </div>
                            </div>

                            <div class="mt-2 text-center">
                                <button type="submit" v-if="hasPermission('accounts_sales_payment_create')" class="btn btn-primary" :disabled="is_saving">Save</button>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div> -->
                </div>
            </div>
    </div>

</template>

<style scoped>
/* .toggle_btn{
        background: red;
    } */
.input_fild {
    height: 42px !important;
}

.btn_wid {
    min-width: 80px;
}
</style>

<script>
import { toast } from 'vue3-toastify';
import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css"
import { SalesPaymentRecordSearchAPI,SalesUnpaidRecordAPI ,SalesPaymentReceived} from "../../routes";
// import { config } from 'vue/types/umd';

// role and permission composable
import { inject } from 'vue';
import {useAuth} from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    components: {
        Multiselect,
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },

    data() {
        const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD

        return {
            showForm: 0,
            invoice_no: '',
            invoice_desc: '',
            issue_date: today,
            error: '',
            success: '',
            payment_received_records:[],
            from_date:today,
            to_date:today,
            is_saving: false,
            is_loading: false,
            // Payment Received Section
            paid_amount:0,
            paid_customer_id:"",
            paid_invoice_no:'',
            paid_date:today,
            paid_account_credit_id: null,
            paid_account_debit_id: null,
            paid_remarks:'',
            unpaid_sales_records:[],

        };
    },

    computed: {
        selectCustomer() {
            return [{ label: 'Select item', value: null }, ...this.data.customers.map(d => ({ label: d.customer_name, value: d.customer_id }))];
        },

        filteredCreditAccounts() {
            return this.data.cr_accounts
                .filter(d => d.chart_of_acct_id !== this.account_debit_id)
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },
        filteredDebitAccounts() {
            return this.data.dr_accounts
                .filter(d => d.chart_of_acct_id !== this.account_credit_id)
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },

        filteredProjects() {
            return this.data.projects
                .filter(d => d.proj_id  !== this.project_id)
                .map(d => ({ label: d.proj_name, value: d.proj_id }));
        },

        // Payment Received Section
        filterPaidCustomer() {

            if(this.paid_customer_id){
                this.paid_invoice_no = '';
                this.getACustomerUnpaidInvoiceRecords(this.paid_customer_id);
            }
            return this.data.customers
                .filter(d => d.customer_id !== this.paid_customer_id)
                .map(d => ({ label: d.customer_name, value: d.customer_id }));
        },
        paymentReceivedDebitAccount() {
             return this.data.payment_received_dr_accounts
                .filter(d => d.chart_of_acct_id !== this.account_credit_id)
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },
        paymentReceivedCreditAccounts() {
            return this.data.dr_accounts
                .filter(d => d.chart_of_acct_id !== this.account_credit_id)
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },
        filteredUnpaidInvoices() {
            return this.unpaid_sales_records
                .filter(d => d.sr_invoice_no !== this.paid_invoice_no)
                .map(d => ( { label: d.sr_invoice_no+' => '+d.sr_grand_total_amount, value: d.sr_auto_id }
                 ));
        },


    },


    methods: {

        //  createNew() {
        //     this.showForm = 1;
        // },

        search() {
            this.showForm = 2;
        },
        showReport() {
            this.showForm = 3;
        },

        editPage(id) {
            this.currentEditId = id;
            this.showForm = 4;
        },

        invoiceValidation() {
            // Initialize formErrors.invoice_number as an array if undefined
            if (!this.formErrors.invoice_no) {
                this.formErrors.invoice_no = [];
            }

            // Reset the errors first
            this.formErrors.invoice_no = [];

            if (this.invoice_no.length < 3) {
                this.formErrors.invoice_no.push('Invoice number must be at least 3 characters long.');
            }
        },

         paidInvoiceValidation() {
            // Initialize formErrors.invoice_number as an array if undefined
            if (!this.formErrors.paid_invoice_no) {
                this.formErrors.paid_invoice_no = [];
            }

            // Reset the errors first
            this.formErrors.paid_invoice_no = [];

            if (this.paid_invoice_no.length < 3) {
                this.formErrors.paid_invoice_no.push('Invoice number must be at least 3 characters long.');
            }
        },







        // sales payment
        async getACustomerUnpaidInvoiceRecords(cus_auto_id){
            try{

                const response = await axios.get(SalesUnpaidRecordAPI+'/'+cus_auto_id);
                console.log(response.data)
                if(response.status == 200){
                    this.unpaid_sales_records = response.data.data;
                    this.filteredUnpaidInvoices();
                }

            }catch(e){

            }

        },
         async submitPaymentReceived() {

            if (this.is_saving) return;

            try {
                // 2. Set saving to true
                this.is_saving = true;

                let formData = new FormData();
                 formData.append('cus_auto_id', this.paid_customer_id);
                formData.append('account_credit_id', this.paid_account_credit_id);
                formData.append('account_debit_id', this.paid_account_debit_id);
                formData.append('sr_auto_id',this.paid_invoice_no)
                formData.append('paid_date', this.paid_date);
                formData.append('paid_amount',this.paid_amount);
                formData.append('remarks', this.paid_remarks);
                const response = await axios.post(SalesPaymentReceived, formData);

                console.log(response.data);
                if(response.data.status == 200){

                     toast.success("Successfully Updated");
                     $("#payment_received_modal").modal('hide');
                }
                this.is_saving = false; // Reset saving state after operation

            } catch (e) {
                toast.error(e.response?.data?.message || 'An error occurred');
                this.is_saving = false; // Reset saving state after operation
             }

        },
        async searchPaymentRecords(){

            if (this.is_loading) return;
            try{

                    // if(this.paid_customer_id == null){
                    //     toast.error("Please select a Payment Source to search");
                    //     return;
                    // }else if(this.paid_account_debit_id == null){
                    //     toast.error("Please select Deposit account to search");
                    //     return;
                    // }
                        this.is_loading = true;

                    const response = await axios.get(SalesPaymentRecordSearchAPI, {
                                params: {
                                    customer_id: this.paid_customer_id ?? null,
                                    from_date: this.from_date,
                                    to_date: this.to_date
                                }
                            });
                    console.log(response.data)
                    if(response.status == 200){
                        this.payment_received_records = response.data.data;
                    }
                    this.is_loading = false;

            }catch(e){
                    toast.error("Operation Failed "+ e.response?.data?.message || 'An error occurred');
                    this.is_loading = false;
            }

        },




    },

    beforeMount() {
        if (this.data.sale) {
            this.items = this.data.sale.items
            this.attachments = this.data.sale.attachments || []
            this.notes = this.data.sale.notes
            this.payment_term = this.data.sale.sr_payment_terms
            this.invoice_no = this.data.sale.sr_invoice_no
            this.invoice_desc = this.data.sale.sr_invoice_description
            this.customer = this.data.sale.cus_auto_id
            this.issue_date = this.data.sale.sr_issue_date
            this.due_date = this.data.sale.sr_due_date
            this.supply_date = this.data.sale.sr_supply_date
            this.account_credit_id = this.data.sale.account_credit_id
            this.account_debit_id = this.data.sale.account_debit_id
            this.retention = this.data.sale.retention
            this.is_draft = true
            this.force_save = false
        }
    }
}
</script>
