<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Supplier Payment</h4>
            </div>
            <div class="col-md-6 text-right">

            </div>
        </div>
        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="paymentCreate" enctype="multipart/form-data">
                <div class="row mb-4" id="main">

                    <div class="col-md-6" id="sub-2">
                        <!-- Credit Account -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label  class="form-label text-end d-block">
                                    Credit Account<span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="billPaymentForm.credit_account_id"
                                    :options="billPaymentOptions.CreditAccount"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>

                        <!-- payment date -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Payment Date <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" required class="form-control inpute_height"
                                    v-model="billPaymentForm.payment_date" :max="today" />
                            </div>
                        </div>

                        <!-- Bill Amount -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Bill Amount <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" required class="form-control inpute_height"
                                    v-model="billPaymentForm.bill_amount" placeholder="Bill Amount..."
                                    @input="calculateTotal" />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Total <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" required disabled class="form-control inpute_height"
                                    v-model="billPaymentForm.total" placeholder="Total..." />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">Remarks</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" required class="form-control inpute_height"
                                    v-model="billPaymentForm.remarks" placeholder="Remarks..."/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="sub-2">
                        <!-- Supplier -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Supplier <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="billPaymentForm.supplier_id"
                                    :options="billPaymentOptions.Supplier"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>

                        <!-- Invoice Number -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">
                                    Invoice No <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" required class="form-control inpute_height"
                                    v-model="billPaymentForm.invoice_number" placeholder="Invoice Number..."
                                    @input="invoiceValidation" />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Bank Charge -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Bank Charge <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" required class="form-control inpute_height"
                                    v-model="billPaymentForm.bank_charge" placeholder="Bank Charge..."
                                    @input="calculateTotal" />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Attachment -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Attachment
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="file" @change="handleFileUpload('attachment', $event)"
                                    class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-success btn-sm mt-2" type="submit"    v-if="hasPermission('accounts_purchase_payment_create')"  style="float: right;"
                    :disabled="saving">
                    <i v-if="saving" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp;
                    Save
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css"
import {  PurchasePaymentStoreAPI } from "../../../routes";



// role and permission composable
import { inject } from 'vue';
import {useAuth} from "../../../../../../../../resources/js/components/useAuth.js";
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
        const today = new Date().toISOString().split('T')[0];
        return {
            today,
            page_loading: false,
            saving: false,
            billPaymentForm: {
                credit_account_id: null,
                payment_date: today,
                supplier_id: null,
                invoice_number: '',
                bill_amount: '',
                bank_charge: 0,
                total: 0,
                attachment: null,
                remarks: '',
            },
            billPaymentOptions: {
                CreditAccount: [],
                Supplier: [],
            },
        };
    },

    created() {
        this.initializeOptions();
    },

    methods: {
        initializeOptions() {
            // Chart of Accounts Process
            if (this.data.cr_accounts && Array.isArray(this.data.cr_accounts)) {
                this.billPaymentOptions.CreditAccount = this.data.cr_accounts.map(account => ({
                    value: account.chart_of_acct_id,
                    label: `${account.chart_of_acct_number} - ${account.chart_of_acct_name}`
                }));
            }

            // Suppliers Process
            if (this.data.suppliers && Array.isArray(this.data.suppliers)) {
                this.billPaymentOptions.Supplier = this.data.suppliers.map(supplier => ({
                    value: supplier.supplier_id,
                    label: supplier.supplier_name
                }));
            }
        },

        calculateTotal() {
            const billAmount = parseFloat(this.billPaymentForm.bill_amount) || 0;
            const bankCharge = parseFloat(this.billPaymentForm.bank_charge) || 0;
            this.billPaymentForm.total = billAmount + bankCharge;
        },

        invoiceValidation() {

        },

        handleFileUpload(field, event) {
            this.billPaymentForm[field] = event.target.files[0];
        },
        resetForm() {
            this.billPaymentForm = {
                credit_account_id: null,
                payment_date: this.today,
                supplier_id: null,
                invoice_number: '',
                bill_amount: '',
                bank_charge: 0,
                total: 0,
                attachment: null,
                remarks: '',
            };
        },

        async paymentCreate() {
            this.saving = true;

            try {
                const response = await axios.post(PurchasePaymentStoreAPI, this.billPaymentForm, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Handle success
                toast.success(response.data.message);

                // Reset form fields
                 this.resetForm();

                // Optionally redirect or perform another action
                // setTimeout(() => {
                //     window.location = PurchaseInvoiceList;
                // }, 1500);

            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.formErrors = error.response.data.errors;

                    for (const [key, value] of Object.entries(this.formErrors)) {
                        toast.error(`${key}: ${value[0]}`);
                    }
                } else {
                    toast.error('An error occurred while saving.');
                }
                console.error('Error:', error);
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>

<style scoped>
.inpute_height {
    height: 43px;
}
</style>
