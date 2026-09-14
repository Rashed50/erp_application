<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Purchase Invoice Update</h4>
            </div>
            <div class="col-md-6 text-right">

            </div>
        </div>
        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="submit" enctype="multipart/form-data">

                <div class="row mb-4" id="main">
                    <div class="col-md-5" id="sub-1">
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Invoice Type <span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="purchase_type" placeholder="Select Type"
                                    :options="['product', 'service']" :searchable="true" :close-on-select="true"
                                    :show-labels="false" @click="PurchaseInvoiceType" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Bill Description </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control inpute_height" placeholder="Bill description... "
                                    v-model="bill_description" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Account (Credit)<span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="account_credit_id" :options="filteredCreditAccounts"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Account (Debit) <span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="account_debit_id" :options="filteredDebitAccounts"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1"></div>

                    <div class="col-md-5" id="sub-2">
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Invoice Number<span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" required class="form-control inpute_height" v-model="invoice_number"
                                    placeholder="Invoice Number..."
                                    :class="{ 'is-invalid': formErrors.invoice_number }" />
                                <div class="invalid-feedback">
                                    {{ formErrors.invoice_number ? formErrors.invoice_number[0] : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Issue Date<span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" required class="form-control inpute_height" v-model="issue_date" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Purchase Date<span class="req_star">*</span> </label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" required class="form-control inpute_height"
                                    v-model="purchase_date" />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Vendor/Suppliers <span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="suppliers" placeholder="Select suppliers"
                                    :options="selectSuppliers" :searchable="true" :close-on-select="true"
                                    :show-labels="false" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-12">
                        <table id="" class="table table-bordered custom_table mb-0 no-footer" role="grid"
                            aria-describedby="alltableinfo_info">
                            <thead>
                                <tr role="row">
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="5%">S.L</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="40%">Name</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="10%">Un.Rate</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="10%">Qty</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="10%">Vat (%)</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="8%">Price</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="8%">Vat Amount</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" width="14%">Price + Vat</th>
                                </tr>
                            </thead>

                            <tbody id="item_details_cart_table_content_view">
                                <tr v-for="(item, index) in items" :key="index"
                                    :class="{odd: index % 2, even: index % 2 == 0}">
                                    <td style="width: 80px">
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
                                {{ items.reduce((sum, item) => sum + (Number(item.unit_price || 0) * (Number(item.qty ||
                                1))), 0).toFixed(2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                VAT Amount:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ items.reduce((sum, item) => sum + Number(item.vat_amount || 0), 0).toFixed(2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                Total:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ items.reduce((sum, item) => sum + Number(item.with_vat || 0), 0).toFixed(2) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                Discount Amount:
                            </div>
                            <div class="col-md-6 text-right">
                                <input type="number" step="any" v-model="discount" class="form-control"
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
                                {{ (items.reduce((sum, item) => sum + Number(item.with_vat || 0), 0) - (Number(discount)
                                || 0)).toFixed(2) }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <label>Note</label>
                        <textarea class="form-control" rows="2" v-model="note"
                            placeholder="Write purchase note..."></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Attachments</label>
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

                        <h4 class="mt-3">Attachments:</h4>
                        <ul>
                            <li v-for="(attachment, index) in attachments" :key="index">
                                <a :href="attachment.full_url" target="_blank">
                                    <i class="fa fa-file me-2 fs-1 mb-2 file_icon" aria-hidden="true"></i>
                                    {{ attachment.file_path.split('/').pop() }}
                                </a>
                                <span class="text-muted"> (View)</span>
                            </li>
                        </ul>
                    </div>

                </div>

                <button class="btn btn-success btn-sm mt-2" type="submit"   v-if="hasPermission('accounts_purchase_invoice_update')" @click="force_save = 1" :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp;
                    Update
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
import {PurchaseInvoiceEdit, PurchaseInvoiceList} from "../../routes";

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
            account_debit_id: null,
            items: [{
                unit_price: 0,
                qty: 1,
                vat: 0,
                qty_editable: true,
                purchase_invoice_details_id: null,
            }],

            baseURL: window.location.origin,
            selectedFiles: [], // This will hold selected files for upload
            attachments: [],    // To hold the fetched attachments

            purchase_invoice_id:null,
            purchase_invoice_details_id:null,
            formErrors: {},
        };
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
            return [
                { label: 'Select item', value: null },
                ...this.data.products.map(d => ({
                    label: d.item_deta_name,
                    value: d.item_id
                }))
            ];
        },

        filteredCreditAccounts() {
            // return [{ label: 'Select item', value: null }, ...this.data.cr_accounts.map(d => ({label: d.chart_of_acct_name, value: d.chart_of_acct_id}))];
            return this.data.cr_accounts
                .filter(d => d.chart_of_acct_id !== this.account_debit_id)
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },

        filteredDebitAccounts() {
            // return [{ label: 'Select item', value: null }, ...this.data.dr_accounts.map(d => ({label: d.chart_of_acct_name, value: d.chart_of_acct_id}))];
            return this.data.dr_accounts
                .filter(d => d.chart_of_acct_id !== this.account_credit_id)
                .map(d => ({ label: d.chart_of_acct_name, value: d.chart_of_acct_id }));
        },
    },

    mounted() {

    },

    methods: {
        PurchaseInvoiceType() {
            // Reset items and set default based on purchase type
            this.items = this.purchase_type === 'product'
                ? [{ unit_price:0, qty: 1, vat:0, qty_editable: true }]
                : [{ unit_price:0, qty: 1, vat:0, qty_editable: false }];
            this.showProduct = this.purchase_type === 'product';
        },

        addItem() {
            // Add new item based on current purchase type
            const newItem = {
                qty: 1,
                qty_editable: this.purchase_type === 'product',
                unit_price: 0,
                vat: 0,
                vat_amount: 0,
                service_name: this.purchase_type === 'service' ? '' : null,
                product: this.purchase_type === 'product' ? null : null,
                purchase_invoice_details_id: null,
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
            }else{
                item.unit_price = 0;
                item.qty = 1;
                item.vat = 0;
                this.calculateTotals(item);
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

        // This method calculates the price, VAT amount, and the total amount with VAT
        calculateTotals(item) {
            // Calculate price without VAT
            item.price = (item.unit_price * item.qty) || 0;

            // Calculate VAT amount
            item.vat_amount = (item.price * (item.vat / 100)) || 0;

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
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            this.$refs.fileInput.value = '';

            if (this.selectedFiles.length > 0) {
                const dataTransfer = new DataTransfer();
                this.selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                this.$refs.fileInput.files = dataTransfer.files;
            }
        },

        async submit() {
            const total_amount = this.items.reduce((sum, item) => sum + (Number(item.unit_price || 0) * (Number(item.qty || 1))), 0);
            const vat_amount = this.items.reduce((sum, item) => sum + (Number(item.vat_amount || 0)), 0);
            const net_total  = this.items.reduce((sum, item) => sum + (Number(item.with_vat || 0)), 0) - (Number(this.discount) || 0);
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
            formData.append('purchase_invoice_id', this.purchase_invoice_id);
            formData.append('purchase_type', this.purchase_type);
            formData.append('description', this.bill_description);
            formData.append('supplier_id', this.suppliers || null);
            formData.append('invoice_number', this.invoice_number);
            formData.append('issue_date', this.issue_date);
            formData.append('purchase_date', this.purchase_date);
            formData.append('note', this.note);
            formData.append('total_amount', total_amount);
            formData.append('vat_amount', vat_amount);
            formData.append('discount', total_discount);
            formData.append('net_total', net_total);
            formData.append('account_credit_id', this.account_credit_id);
            formData.append('account_debit_id', this.account_debit_id);

            // Append items array
            this.items.forEach(item => {
                formData.append('items[]', JSON.stringify({
                    purchase_invoice_details_id: item.purchase_invoice_details_id || null,
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

            // Log the FormData content
            // for (let [key, value] of formData.entries()) {
            //     console.log(`${key}:`, value);
            // }

            try {
                const response = await axios.post(`${PurchaseInvoiceEdit}/${this.purchase_invoice_id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                // console.log(response.data.message)
                toast.success(response.data.message);
                setTimeout(() => {
                    window.location = PurchaseInvoiceList;
                }, 1500);

            } catch (error) {
                // if (error.response && error.response.data.errors) {
                //     const errors = error.response.data.errors;
                //     for (const [key, value] of Object.entries(errors)) {
                //         toast.error(`${key}: ${value[0]}`);
                //     }
                // } else {
                //     toast.error('An error occurred while saving the purchase.');
                // }
                // console.error('Error:', error);

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
    },


    beforeMount() {
        if (this.data.purchase) {
            const dateObject = new Date(this.data.purchase.issue_date.replace(" ", "T"));

            this.purchase_invoice_id = this.data.purchase.pur_id;
            this.purchase_type = this.data.purchase.purchase_type;
            this.suppliers = this.data.purchase.supplier_id;
            this.bill_description = this.data.purchase.description;
            this.invoice_number = this.data.purchase.invoice_number;
            this.issue_date = dateObject.toISOString().split('T')[0];
            this.purchase_date = this.data.purchase.purchase_date;
            this.note = this.data.purchase.notes;
            this.discount = this.data.purchase.discount_amount;
            this.account_credit_id = this.data.purchase.account_credit_id;
            this.account_debit_id = this.data.purchase.account_debit_id;

            // Check if there are details
            if (this.data.purchase.details) {
                this.items = this.data.purchase.details.map(item => {

                    if (this.purchase_type === 'service') {
                        this.showProduct = false; // Set showProduct to false for service
                        return {
                            purchase_invoice_details_id: item.pur_det_id,
                            service_name: item.service_name,
                            unit_price: parseFloat(item.unit_price),
                            qty: parseFloat(item.qty),
                            vat: parseFloat(item.vat),
                            qty_editable: false, // Service items are not editable
                        };
                    } else if (this.purchase_type === 'product') {
                        this.showProduct = true; // Set showProduct to true for product
                        const product = item.product
                            ? this.data.products.find(p => p.item_id === item.product.item_id)
                            : null;

                            console.log("Product =", product)

                        return {
                            purchase_invoice_details_id: item.pur_det_id,
                            product: product ? product.item_id : null,
                            unit_price: parseFloat(item.unit_price),
                            qty: parseFloat(item.qty),
                            vat: parseFloat(item.vat),
                            qty_editable: true, // Product items are editable
                        };
                    }
                });
            }

            // Check if there are attachments
            if (this.data.purchase.attachments) {
                this.attachments = this.data.purchase.attachments.map(attachment => {
                    return {
                        ...attachment,
                        full_url: `${this.baseURL}/storage/${attachment.file_path.replace('//', '/')}`
                    };
                });
            }

        }

        // Calculate totals for each item after they are loaded
        this.items.forEach(item => {
            this.calculateTotals(item);
        });
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
.inpute_height{
    height: 43px;
}
</style>
