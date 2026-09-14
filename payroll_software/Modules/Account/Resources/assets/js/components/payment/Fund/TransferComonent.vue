<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Internal Fund Transfer</h4>
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
                                   Sender(CR)<span class="req_star">*</span>

                                </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="fundTransferForm.credit_account_id"
                                    :options="billPaymentOptions.CreditAccount"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>



                        <!-- Amount -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Amount <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" required class="form-control inpute_height"
                                    v-model="fundTransferForm.bill_amount" placeholder="Transfer Amount..."
                                    @input="calculateTotal" />
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
                                    v-model="fundTransferForm.bank_charge" placeholder="Bank Charge..."
                                    @input="calculateTotal" />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                         <!-- VAT  -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    VAT <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" required class="form-control inpute_height"
                                    v-model="fundTransferForm.vat" placeholder="VAT..."
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
                                    v-model="fundTransferForm.total" placeholder="Total..." />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>




                    </div>

                    <div class="col-md-6" id="sub-2">
                        <!-- Debit Account -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label  class="form-label text-end d-block">
                                    Receiver (DR)<span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="fundTransferForm.debit_account_id"
                                    :options="billPaymentOptions.DebitAccount"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>

                        <!-- Invoice Number -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">
                                    Receipt No.
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" required class="form-control inpute_height"
                                    v-model="fundTransferForm.invoice_number" placeholder="Receipt No..."
                                    @input="invoiceValidation" />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Invoice date -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">
                                    Trans. Date <span class="req_star">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" required class="form-control inpute_height"
                                    v-model="fundTransferForm.payment_date" :max="today" />
                            </div>
                        </div>

                         <!-- Remarks -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block">Remarks</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" required class="form-control inpute_height"
                                    v-model="fundTransferForm.remarks" placeholder="Remarks..."/>
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

                <button class="btn btn-success btn-sm mt-2"    v-if="hasPermission('accounts_internal_transfer_create')" type="submit" style="float: right;"
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
import { InternalFundTransferStoreAPI } from "../../../routes";


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
            fundTransferForm: {
                credit_account_id: null,
                debit_account_id: null,
                payment_date: today,
                invoice_number: '',
                bill_amount: '',
                bank_charge: 0,
                vat: 0,
                total: 0,
                attachment: null,
                remarks: '',
            },
            billPaymentOptions: {
                CreditAccount: [],
                DebitAccount: [],
            },
        };
    },

    created() {
        this.initializeOptions();
    },

    methods: {
        initializeOptions() {
            // Chart of Accounts Process (Credit Account)
            if (this.data.cr_accounts && Array.isArray(this.data.cr_accounts)) {
                this.billPaymentOptions.CreditAccount = this.data.cr_accounts.map(account => ({
                    value: account.chart_of_acct_id,
                    label: `${account.chart_of_acct_number} - ${account.chart_of_acct_name}`
                }));
            }

            // Chart of Accounts Process (Debit Account)
            if (this.data.dr_accounts && Array.isArray(this.data.dr_accounts)) {
                this.billPaymentOptions.DebitAccount = this.data.dr_accounts.map(account => ({
                    value: account.chart_of_acct_id,
                    label: `${account.chart_of_acct_number} - ${account.chart_of_acct_name}`
                }));
            }

        },

        calculateTotal() {
            const billAmount = parseFloat(this.fundTransferForm.bill_amount) || 0;
            const bankCharge = parseFloat(this.fundTransferForm.bank_charge) || 0;
            const vat = parseFloat(this.fundTransferForm.vat) || 0;
            this.fundTransferForm.total = billAmount + bankCharge + vat;
        },

        invoiceValidation() {

        },

        handleFileUpload(field, event) {
            this.fundTransferForm[field] = event.target.files[0];
        },
        resetForm() {
            this.fundTransferForm = {
                credit_account_id: null,
                debit_account_id: null,
                payment_date: this.today,
                invoice_number: '',
                bill_amount: '',
                bank_charge: 0,
                vat: 0,
                total: 0,
                attachment: null,
                remarks: '',
            };
        },

        async paymentCreate() {
            this.saving = true;
            try {

                const response = await axios.post(InternalFundTransferStoreAPI, this.fundTransferForm, {
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
