<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Create Purchase Invoice</h4>
            </div>
            <div class="col-md-6 text-right">

            </div>
        </div>
        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="submit" enctype="multipart/form-data">

                <div class="row mb-4" id="main">

                    <div class="col-md-6" id="sub-1">

                        <div class="row mt-2">
                            <div class="col-md-3">
                             </div>
                            <div class="col-md-4">
                                <input type="radio" name="option" v-model="paymentTypeOption" value="1"> &nbsp;Cash Purchase
                            </div>
                            <div class="col-md-4">
                                 <input type="radio" name="option" v-model="paymentTypeOption" value="2">&nbsp; Credit Purchase
                            </div>
                        </div>


                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">Invoice Type <span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="purchase_type" placeholder="Select Type"
                                    :options="['product', 'service']" :searchable="true" :close-on-select="true"
                                    :show-labels="false" @change="PurchaseInvoiceType" />
                            </div>
                        </div>

                         <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">Supplier <span class="req_star">*</span> </label>

                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="suppliers" placeholder="Select suppliers"
                                    :options="selectSuppliers" :searchable="true" :close-on-select="true"
                                    :show-labels="false" />
                            </div>
                        </div>



                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">Payment Source <span class="req_star">*</span> </label>
                                 <!-- this is credit account -->
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="account_credit_id" :options="filteredCreditAccounts"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end">Account Debit <span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9 d-noe">
                                <Multiselect required v-model="account_debit_id" :options="filteredDebitAccounts"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>


                    </div>


                    <div class="col-md-6" id="sub-2">

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label  class="form-label text-end d-block">Project<span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="project_id" :options="filteredProjects"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">Invoice No <span class="req_star">*</span> </label>

                            </div>
                            <div class="col-md-9">
                                <input type="text" required class="form-control inpute_height" v-model="invoice_number"
                                    :class="{ 'is-invalid': formErrors.invoice_number && formErrors.invoice_number.length > 0 }"
                                    placeholder="Invoice Number..." @input="invoiceValidation" />
                                <div class="invalid-feedback">
                                    {{ formErrors.invoice_number && formErrors.invoice_number.length > 0 ?
                                    formErrors.invoice_number[0] : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 d-none">
                            <div class="col-md-3">
                                <label class="form-label text-end d-block"> Issue At<span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" required class="form-control inpute_height" v-model="issue_date" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">Supply At <span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" required class="form-control inpute_height"
                                    v-model="purchase_date" />
                            </div>
                        </div>

                          <div class="row mt-2">
                            <div class="col-md-3">
                              <label class="form-label text-end d-block">Remarks <span class="req_star">*</span> </label>

                            </div>
                            <div class="col-md-9">
                                <input class="form-control inpute_height" placeholder="Bill description... "
                                    v-model="bill_description" />
                            </div>
                        </div>




                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered custom_table mb-0" role="table">
                            <thead class="table-dark">
                                <tr>
                                    <th width="8%">S.L</th>
                                    <th width="35%">Item Name</th>
                                    <th width="10%">U.Rate</th>
                                    <th width="8%">Qty</th>
                                    <th width="8%">VAT(%)</th>
                                    <th width="11%">Total</th>
                                    <th width="8%">VAT</th>
                                    <th width="14%">Total+VAT</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(item, index) in items" :key="index"
                                    :class="index % 2 === 0 ? 'table-light' : ''">
                                    <td style="width: 50px">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="items.splice(index, 1)">
                                            <i class="fa fa-times"></i>
                                        </button> &nbsp;
                                        {{ index + 1 }}
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Show Multiselect for 'product' -->
                                            <Multiselect v-if="showProduct" v-model="item.product"
                                                placeholder="Select product" :options="selectProduct" :searchable="true"
                                                :close-on-select="true" :show-labels="false"
                                                @change="onProductChange(item, $event)" />
                                            <!-- Show input field for 'service' -->
                                            <input v-else v-model="item.service_name" placeholder="Enter service name"
                                                class="form-control" />
                                        </div>
                                    </td>

                                    <!-- Unit Price Input -->
                                    <td style="width: 100px">
                                        <input type="number" step="0.01" v-model="item.unit_price" class="form-control"
                                            @input="handlePositiveInput('unit_price', item)" />
                                    </td>

                                    <!-- Quantity Input -->
                                    <td style="width: 100px">
                                        <input type="number" step="0.01" v-model="item.qty" class="form-control"
                                            placeholder="Qty..." @input="handlePositiveInput('qty', item)"
                                            :readonly="!item.qty_editable" />
                                    </td>

                                    <!-- VAT Input -->
                                    <td style="width: 100px">
                                        <input type="number" step="0.01" v-model="item.vat" class="form-control"
                                            placeholder="Vat..." @input="handlePositiveInput('vat', item)" />
                                    </td>

                                    <td style="width: 120px">
                                        {{ without_vat_price(item) }}
                                    </td>
                                    <td style="width: 120px">
                                        {{ item.vat_amount }}
                                    </td>
                                    <td style="width: 150px">
                                        {{ with_vat_price(item) }}
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                        <button type="button" class="btn btn-success btn-sm mt-2" @click.prevent="addItem">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>


                    <div class="offset-sm-8 col-sm-4" style="font-size: 18px">
                        <div class="row">
                            <div class="col-md-6">
                                Total Before VAT:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalBeforeVat.toFixed(2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                VAT Amount:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalVatAmount.toFixed(2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                Total:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ totalWithVat.toFixed(2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                Discount Amount:
                            </div>
                            <div class="col-md-6 text-right">
                                <input type="number" step="0.01" v-model="discount" class="form-control"
                                    placeholder="Hold..." required style="text-align: right; font-size: 18px"
                                    @input="discountPositiveInput" />
                            </div>
                        </div>
                        <hr>


                        <div class="row">
                            <div class="col-md-6">
                                Grand Total:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ grandTotal.toFixed(2) }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <label>Note</label>
                        <textarea class="form-control" rows="2" v-model="note"
                            placeholder="Write purchase note..."></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Attachment's</label>
                        <input type="file" class="form-control" multiple @change="handleFileUpload" ref="fileInput" />

                        <h4>
                            Selected Files:
                            <span class="fw-bold" v-if="selectedFiles.length > 0">{{ selectedFiles.length }} file(s)
                                selected</span>
                        </h4>
                        <ul>
                            <li v-for="(file, index) in selectedFiles" :key="index">
                                <i class="fa fa-file me-2 fs-1 mb-2 file_icon" aria-hidden="true"></i>
                                {{ file.name }} ({{ (file.size / 1024).toFixed(2) }} KB)
                                <button class="btn ms-2 file_remove" @click="removeFile(index)" type="button">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </li>
                        </ul>
                    </div>


                </div>

                <button class="btn btn-success btn-sm mt-2"  v-if="hasPermission('accounts_purchase_invoice_create')"  type="submit" @click="force_save = 1" :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
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
import { PurchaseInvoiceStore, PurchaseInvoiceList } from "../../routes";


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

            purchase_type: 'product',
            showProduct: true,
            bill_description: '',
            suppliers: null,
            invoice_number: '',
            issue_date: today,
            purchase_date: today,
            discount: 0,
            note: '',
            account_credit_id: null,
            account_debit_id: 5, // default inventory/stock account id
            items: [{ unit_price: 0, qty: 1, vat: 15, vat_amount: 0, qty_editable: true }],
            selectedFiles: [],
            formErrors: {},
            project_id:null,
            paymentTypeOption: '1',

            // Local reactive copy of the credit accounts
            local_cr_accounts: this.data.asset_accounts,
            local_dr_accounts: this.data.dr_accounts,
        };
    },

    watch: {
        // The watch property detects changes to `selectedOption`
        paymentTypeOption(value) {

            // 1. Reset the selected account ID
            this.account_credit_id = null;
            if (value === '1') {
                // Cash Purchase -> Payment Source should be Asset Accounts (Cash, Bank)
                this.local_cr_accounts = this.data.asset_accounts;
                console.log('Switched to Cash Purchase (Asset Accounts)');
            } else if (value === '2') {
                // Credit Purchase -> Payment Source should be Liability Accounts (Creditors)
                this.local_cr_accounts = this.data.liability_accounts;
                console.log('Switched to Credit Purchase (Liability Accounts)');
            }

        },

        // Watcher for purchase_type to handle auto-selection of Account Debit
        purchase_type(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.handlePurchaseTypeChange();
            }
        }
    },

    created() {
        // Initialize with asset_accounts because paymentTypeOption defaults to '1' (Cash)
      //  this.local_cr_accounts = this.data.asset_accounts;
    },

    computed: {
        selectSuppliers() {
            return [
                { label: 'Select item', value: null },
                ...this.data.suppliers.map(d => ({
                    label: d.supplier_name,
                    value: d.supplier_id
                }))
            ];
        },

        selectProduct() {
            console.log(this.ty)
            return [
                { label: 'Select item', value: null },
                ...this.data.products.map(d => ({
                    label: d.item_deta_name,
                    value: d.item_id
                }))
            ];
        },

        filteredCreditAccounts() {
            console.log('filteredCreditAccounts called with local data:', this.local_cr_accounts);

            // 🚨 Use the local reactive property!
            if (!this.local_cr_accounts) {
                return []; // empty data
            }
            return this.local_cr_accounts
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },

        filteredDebitAccounts() {
            return this.local_dr_accounts
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },
        filteredProjects() {
            return this.data.projects
                .filter(d => d.proj_id  !== this.project_id)
                .map(d => ({ label: d.proj_name, value: d.proj_id }));
        },

        // Computed properties for totals to ensure reactivity
        totalBeforeVat() {
            return this.items.reduce((sum, item) => sum + (Number(item.unit_price || 0) * (Number(item.qty || 1))), 0);
        },

        totalVatAmount() {
            return this.items.reduce((sum, item) => sum + Number(item.vat_amount || 0), 0);
        },

        totalWithVat() {
            return this.items.reduce((sum, item) => sum + Number(item.with_vat || 0), 0);
        },

        grandTotal() {
            return this.totalWithVat - (Number(this.discount) || 0);
        },
    },

    methods: {


        PurchaseInvoiceType() {
            // This method is called on @change, but the watcher will handle the logic
            // to avoid duplication
        },

        // Handle purchase type change with auto-selection
        handlePurchaseTypeChange() {
            try {
                // Reset account debit selection
                this.account_debit_id = null;

                // Set the appropriate accounts based on purchase type
                if (this.purchase_type === 'product') {
                    this.local_dr_accounts = this.data.dr_accounts;
                    this.items = [{ unit_price: 0, qty: 1, vat: 15, vat_amount: 0, qty_editable: true }];
                } else {
                    this.local_dr_accounts = this.data.expense_accounts;
                    this.items = [{ unit_price: 0, qty: 1, vat: 15, vat_amount: 0, qty_editable: false }];
                }
                this.showProduct = this.purchase_type === 'product';

                // Auto-select Account Debit using nextTick to avoid reactivity issues
                this.$nextTick(() => {
                    try {
                        const availableOptions = this.filteredDebitAccounts;
                        if (availableOptions && availableOptions.length > 0) {
                            // Always select the first available option
                            this.account_debit_id = availableOptions[0].value;
                        }
                        // If no options, account_debit_id remains null
                    } catch (error) {
                        console.error('Error in auto-selection:', error);
                        // Reset to safe state
                        this.account_debit_id = null;
                    }
                });
            } catch (error) {
                console.error('Error in handlePurchaseTypeChange:', error);
                // Reset to safe state
                this.account_debit_id = null;
                this.local_dr_accounts = this.data.dr_accounts;
                this.showProduct = true;
            }
        },

        addItem() {
            // Add new item based on current purchase type
            const newItem = {
                qty: 1,
                qty_editable: this.purchase_type === 'product',
                unit_price: 0,
                vat: 15,
                vat_amount: 0,
                service_name: this.purchase_type === 'service' ? '' : null,
                product: this.purchase_type === 'product' ? null : null,
            };
            this.items.push(newItem);
        },

        validateItem(item) {
            if (!item.product && !item.service_name) {
                if (this.purchase_type === 'product') {
                    toast.error('Please select a product.');
                }
                else if (this.purchase_type === 'service') {
                    toast.error('Please create a service.');
                }
                return false;
            }
            if (item.unit_price < 0) {
                toast.error('Unit price cannot be smaller than 0');
                return false;
            }
            if (item.qty < 1) {
                toast.error('Quantity cannot be smaller than 1');
                return false;
            }
            if (item.vat < 0) {
                toast.error('VAT cannot be smaller than 0');
                return false;
            }
            return true;
        },

        onProductChange(item, selectedProduct) {
            const selected = this.data.products.find(product => product.id === selectedProduct);
            item.product = selected ? { label: selected.name, value: selected.id } : null;
            if (selected) {
                item.unit_price = selected.price;
                this.calculateTotals(item);
            } else {
                item.unit_price = 0;
                item.qty = 1;
                item.vat = 0;
                this.calculateTotals(item);
            }
        },

        invoiceValidation() {
            // Initialize formErrors.invoice_number as an array if undefined
            if (!this.formErrors.invoice_number) {
                this.formErrors.invoice_number = [];
            }

            // Reset the errors first
            this.formErrors.invoice_number = [];

            if (this.invoice_number.length < 3) {
                this.formErrors.invoice_number.push('Invoice number must be at least 3 characters long.');
            }
        },

        discountPositiveInput() {
            if (this.discount < 0) {
                this.discount = 0;
            }
        },

        handlePositiveInput(field, item) {
            if (item[field] < 0) {
                item[field] = 0;
            }
            this.calculateTotals(item); // Recalculate totals after input correction
        },

        // Your existing calculateTotals method
        calculateTotals(item) {
            // Calculate price without VAT
            item.price = (item.unit_price * item.qty) || 0;

            // Calculate VAT amount
            item.vat_amount = (item.price * (item.vat / 100))   || 0;


            // Calculate total with VAT
            item.with_vat = item.price + item.vat_amount;
        },

        // Return the price without VAT
        without_vat_price(item) {
            return item.price ? item.price.toFixed(2) : '0.00';
        },

        // Return the total with VAT
        with_vat_price(item) {
            return item.with_vat ? item.with_vat.toFixed(2) : '0.00';
        },

        handleFileUpload(event) {
            this.selectedFiles = Array.from(event.target.files);
            // this.selectedFiles = [];
            // const files = event.target.files;


            // for (let i = 0; i < files.length; i++) {
            //     this.selectedFiles.push(files[i]);
            // }
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            // Clear the file input and repopulate with remaining files
            this.$refs.fileInput.value = '';
            if (this.selectedFiles.length > 0) {
                // Use a more efficient approach to update the file input
                const dt = new DataTransfer();
                this.selectedFiles.forEach(file => dt.items.add(file));
                this.$refs.fileInput.files = dt.files;
            }
        },

        async submit() {
            const total_amount = this.items.reduce((sum, item) => sum + (Number(item.unit_price || 0) * (Number(item.qty || 1))), 0);
            const vat_amount = this.items.reduce((sum, item) => sum + (Number(item.vat_amount || 0)), 0);
            const net_total = this.items.reduce((sum, item) => sum + (Number(item.with_vat || 0)), 0) - (Number(this.discount) || 0);
            const total_discount = (Number(this.discount) || 0).toFixed(2);

            // Validate items
            for (const item of this.items) {
                if (!this.validateItem(item)) {
                    return;
                }
            }

            // Check if total_amount is less than 0
            if (total_amount <= 0) {
                toast.error('Total amount must be greater than 0.');
                return;
            }

            // Create FormData instance
            const formData = new FormData();
            formData.append('purchase_type', this.purchase_type);
            formData.append('description', this.bill_description);
            formData.append('supplier_id', this.suppliers || null);
            formData.append('invoice_number', this.invoice_number);
            formData.append('issue_date', this.issue_date);
            formData.append('purchase_date', this.purchase_date);
            formData.append('note', this.note);
            formData.append('project_id', this.project_id);
            formData.append('total_amount', total_amount);
            formData.append('vat_amount', vat_amount);
            formData.append('discount', total_discount);
            formData.append('net_total', net_total);
            formData.append('account_credit_id', this.account_credit_id);
            formData.append('account_debit_id', this.account_debit_id);

            // Append items array
            this.items.forEach(item => {
                formData.append('items[]', JSON.stringify({
                    product_id: item.product || null,
                    service_name: item.service_name || null,
                    unit_price: item.unit_price,
                    qty: item.qty,
                    vat: item.vat,
                    vat_amount: item.vat_amount,
                    total_with_vat: item.with_vat
                }));
            });

            // Append files
            this.selectedFiles.forEach(file => {
                formData.append('files[]', file);
            });

            try {
                const response = await axios.post(PurchaseInvoiceStore, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Handle success
                toast.success(response.data.message);
                console.log(response.data);
                // Reset form fields


                // Optionally redirect or perform another action
                setTimeout(() => {
                   // window.location = PurchaseInvoiceList;
                   //debugger;
                    //this.resetForm();
                }, 1500);

            } catch (error) {


                if (error.response && error.response.data.errors) {
                    this.formErrors = error.response.data.errors;

                    for (const [key, value] of Object.entries(this.formErrors)) {
                        toast.error(`${key}: ${value[0]}`);
                    }
                } else {
                    toast.error('An error occurred while saving the purchase.');
                }
                console.error('Error:', error);
            }
        },

        resetForm() {
            this.bill_description = '';
            this.suppliers = null;
            this.invoice_number = '';
            this.issue_date = new Date().toISOString().split('T')[0];
            this.purchase_date = new Date().toISOString().split('T')[0];
            this.note = '';
            this.items = [{ qty: 1, qty_editable: true }];
            this.selectedFiles = [];
        }

    }




}
</script>

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

.file_remove {
    outline: none;
    background: none;
    border: none;
    color: red;
    cursor: pointer;
}

.file_icon {
    color: rgb(49, 49, 209);
}

.inpute_height {
    height: 43px;
}
</style>
