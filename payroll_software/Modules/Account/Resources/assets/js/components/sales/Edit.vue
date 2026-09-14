<template>
    <div>

        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="submit">

                <div class="row">

                    <div class="row" id="main">
                        <h5>Update Sales Information</h5>
                        <div class="col-md-5" id="sub-1">
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Sales To <span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="customer" placeholder="Select item"
                                        :options="selectCustomer" />
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Invoice No<span class="req_star">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" required class="form-control inpute_height" v-model="invoice_no"
                                        :class="{ 'is-invalid': formErrors.invoice_no && formErrors.invoice_no.length > 0 }"
                                        placeholder="Invoice No..." @input="invoiceValidation" />
                                    <div class="invalid-feedback">
                                        {{ formErrors.invoice_no && formErrors.invoice_no.length > 0 ?
                                        formErrors.invoice_no[0] : '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Remarks </label>
                                </div>
                                <div class="col-md-9">
                                    <input class="form-control" placeholder="Invoice desc... " v-model="invoice_desc" />
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Pay Terms </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="payment_term"
                                        :options="['20 Days', '30 Days', '40 Days', '50 Days', '60 Days']"
                                        :searchable="true" />
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Project<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="project_id" :options="filteredProjects"
                                        placeholder="Select item" :searchable="true" />
                                </div>
                            </div>



                        </div>

                        <div class="col-md-1"></div>

                        <div class="col-md-5" id="sub-2">
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Acc.(Credit)<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="account_credit_id" :options="filteredCreditAccounts"
                                        placeholder="Select item" :searchable="true" />
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Acc.(Debit) <span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <Multiselect required v-model="account_debit_id" :options="filteredDebitAccounts"
                                        placeholder="Select item" :searchable="true" />
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Issued<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" required class="form-control" v-model="issue_date" />
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Due<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" required class="form-control" v-model="due_date" />
                                </div>
                            </div>

                              <div class="row mt-2 mb-3">
                                <div class="col-md-3">
                                    <label  class="form-label text-end d-block">Supplied<span class="req_star">*</span> </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" required class="form-control" v-model="supply_date" />
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-12">
                        <table id="" class="table table-bordered custom_table mb-0 no-footer" role="grid"
                            aria-describedby="alltableinfo_info">
                            <thead>
                                <tr role="row">
                                    <th class="sorting_disabled" rowspan="1" colspan="1">S.L</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Product Name</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Un.Rate</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Qty</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Vat (%)</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Price</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Vat Amount</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1">Price + Vat</th>
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
                                            <div style="width: 100%;">
                                                <div v-if="!item.showInput">
                                                    <Multiselect v-model="item.product"
                                                        :options="data.products.map(product => ({ value: product.spi_auto_id, label: product.spi_name_en }))"
                                                        :searchable="true" :close-on-select="true" :show-labels="false"
                                                        @change="onProductChange(item, $event)" />

                                                </div>

                                                <div v-else>
                                                    <input v-model="item.newProduct" type="text"
                                                        class="form-control input_fild"
                                                        placeholder="Enter product name" />
                                                </div>
                                            </div>

                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm toggle_btn"
                                                @click="toggleInputField(item)">
                                                <span v-if="!item.showInput">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    <!-- Create -->
                                                </span>
                                                <span v-else>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                    <!-- Select -->
                                                </span>
                                            </button>
                                        </div>
                                    </td>

                                    <td style="width: 100px">
                                        <div v-if="!item.showInput">
                                            <input type="number" step="0.01" v-model="item.unit_price"
                                                class="form-control" placeholder="Price..." required
                                                @input="PositiveInput('unit_price', item)">
                                        </div>
                                        <div v-else>
                                            <input type="number" step="0.01" v-model="item.unit_price"
                                                class="form-control" placeholder="Price..." required
                                                @input="PositiveInput('unit_price', item)" />
                                        </div>
                                    </td>

                                    <td style="width: 100px">
                                        <input type="number" step="0.01" v-model="item.qty" class="form-control"
                                            placeholder="Qty..." required @input="PositiveInput('qty', item)" />
                                    </td>

                                    <td style="width: 100px">
                                        <input type="number" step="any" v-model="item.vat" class="form-control"
                                            placeholder="Vat..." required @input="PositiveInput('vat', item)" />
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
                        <button type="button" class="btn btn-success btn-sm mt-2" @click.prevent="items.push({})">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4"></div>


                    <div class="col-sm-4" style="font-size: 18px">
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
                                Retention:
                            </div>
                            <div class="col-md-6 text-right">
                                <input type="number" step="any" v-model="retention" class="form-control"
                                    placeholder="Hold..." required style="text-align: right; font-size: 18px"
                                    @input="retentionPositiveInput" />
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                Grand Total:
                            </div>
                            <div class="col-md-6 text-right">
                                {{ (items.reduce((sum, item) => sum + Number(item.with_vat || 0), 0) -
                                (Number(retention) || 0)).toFixed(2) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="mt-2">
                            <div class="mb-2">
                                <label>Notes</label>
                                <textarea class="form-control" v-model="notes" rows="2"></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="attachments">Attachments</label>
                                <div>
                                    <input id="attachments" type="file" class="form-control" multiple
                                        placeholder="Attachments..." @change="fileSelected" />
                                </div>
                                <div>
                                    <div class="mt-1" v-for="(attachment, index) in attachments" :key="index">
                                        {{ attachment.name }} - {{ getSizeInFormatted(attachment.size) }}
                                        <a href="#" class="text-danger" @click.prevent="attachments.splice(index, 1)"><i
                                                class="fa fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div v-if="error" class="alert alert-danger mt-2">
                    {{ error }}
                </div>
                <div v-if="success" class="alert alert-success mt-2">
                    {{ success }}
                </div> -->
                <button class="btn btn-success btn-sm mt-2" type="button"  v-if="hasPermission('accounts_sales_invoice_update')" @click.prevent="save" :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp;
                    Update
                </button>

                &nbsp;
                <button class="btn btn-dark btn-sm mt-2" type="button"  v-if="hasPermission('accounts_sales_invoice_update')" @click.prevent="save_as_draft"
                    :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-save"></i>
                    &nbsp;
                    Save as Draft
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>
    .input_fild{
        height: 42px !important;
    }
    .btn_wid{
        min-width: 80px;
    }
</style>


<script>
import { toast } from 'vue3-toastify';
import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css"
import {SalesEditAPI, SalesList} from "../../routes";

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
        return {
            items: [{}],
            attachments: [],
            notes: '',
            payment_term: '20 Days',
            invoice_no: '',
            invoice_desc: '',
            customer: null,
            issue_date: '',
            due_date: '',
            supply_date: '',
            account_credit_id: null,
            account_debit_id: null,
            retention: 0,
            error: '',
            success: '',
            uploading: false,
            is_draft: false,
            force_save: false,
            manual_price: false,
            formErrors: {},
            project_id:null,
        };
    },

    computed: {
        selectCustomer(){
            return [{ label: 'Select item', value: null }, ...this.data.customers.map(d => ({label: d.cus_name, value: d.cus_auto_id}))];
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
            // return [{ label: 'Select item', value: null }, ...this.data.dr_accounts.map(d => ({label: d.chart_of_acct_name, value: d.chart_of_acct_id}))];
            return this.data.projects
                .filter(d => d.proj_id  !== this.project_id)
                .map(d => ({ label: d.proj_name, value: d.proj_id }));
        },

    },

    methods: {
        toggleInputField(item) {
            item.showInput = !item.showInput;

            if (item.showInput) {
                item.product = null;
                item.product_data = null;
                item.unit_price = 0;
                item.qty = 1;
                item.vat = 0;
                item.price = 0;
                item.manual_price = true; // Set to true when manually entering price
            } else {
                item.manual_price = false; // Set to false when using selected product price
            }
        },

        validateItem(item) {
            if (!item.product && !item.newProduct) {
                toast.error('Please select or create a product.');
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

        onProductChange(item, selectedProductId) {
            item.product_data = this.data.products.find(product => product.id == selectedProductId);

            if (item.product_data) {
                item.unit_price = item.product_data.price;
                item.manual_price = false; // Reset flag when selecting a product

                if (!item.qty) item.qty = 1;
                if (!item.vat) item.vat = 0;

                this.calculateTotals(item);
            } else {
                console.error("Selected Product not found");
            }
        },

        PositiveInput(field, item) {
            if (item[field] < 0) {
                item[field] = 0;
            }
        },

        retentionPositiveInput() {
            if (this.retention < 0) {
                this.retention = 0;
            }
        },

        calculateTotals(item) {
            // Calculate price without VAT
            item.price = Number((item.unit_price || 0) * (item.qty || 1)).toFixed(2) * 1;
            // Calculate VAT amount
            item.vat_amount = Number((item.price * ((item.vat || 0) / 100)) || 0).toFixed(2) * 1;
            // Calculate total with VAT
            item.with_vat = Number(item.price + item.vat_amount).toFixed(2) * 1;
            return {
                price: item.price,
                with_vat: item.with_vat
            };
        },

        without_vat_price(item) {
            return this.calculateTotals(item).price;
        },

        with_vat_price(item) {
            return this.calculateTotals(item).with_vat;
        },

        save() {
            // console.log("Save Click")
            this.force_save = true;
            this.is_draft = false;
            this.submit();
        },

        save_as_draft() {
            // console.log("Draft Click")
            this.force_save = false;
            this.is_draft = true;
            this.submit();
        },


        async submit() {
            this.error = null;
            if (this.customer === null) {
                this.error = 'Please select a customer';
                return;
            }

            // console.log("force_save =", this.force_save);
            // console.log("is_draft =", this.is_draft);

            // Validate items
            for (const item of this.items) {
                if (!this.validateItem(item)) {
                    return;
                }
            }

            // Calculate total amount
            const total_amount = this.items.reduce((sum, item) => sum + Number(item.unit_price || 0) * (Number(item.qty || 1)), 0);

            // Check if total_amount is less than 0
            if (total_amount <= 0) {
                toast.error('Total amount must be greater than 0.');
                return;
            }

            this.uploading = true;
            let formData = new FormData();
            formData.append('items', JSON.stringify(this.items));
            formData.append('total_amount', this.items.reduce((sum, item) => sum + Number(item.unit_price || 0) * (Number(item.qty || 1)), 0));
            formData.append('vat_amount', this.items.reduce((sum, item) => sum + Number(item.vat_amount || 0), 0));
            formData.append('total', this.items.reduce((sum, item) => sum + Number(item.with_vat), 0) - (Number(this.retention) || 0));
            formData.append('retention', (Number(this.retention) || 0).toFixed(2));
            formData.append('customer', this.customer);
            this.attachments.forEach((attachment) => {
                formData.append('attachments[]', attachment);
            });
            formData.append('notes', this.notes);
            formData.append('payment_term', this.payment_term);
            formData.append('invoice_no', this.invoice_no);
            formData.append('invoice_desc', this.invoice_desc);
            formData.append('customer', this.customer);
            formData.append('issue_date', this.issue_date);
            formData.append('due_date', this.due_date);
            formData.append('supply_date', this.supply_date);
            formData.append('account_credit_id', this.account_credit_id);
            formData.append('account_debit_id', this.account_debit_id);
            formData.append('project_id',this.project_id);
            formData.append('is_draft', this.is_draft);
            formData.append('force_save', this.force_save);

            // Log the formData contents
            for (let pair of formData.entries()) {
                console.log(`${pair[0]}: ${pair[1]}`);
            }
            // console.log("Sale Id =", this.data.sale.sr_auto_id)

            try {
                await axios.post(`${SalesEditAPI}/${this.data.sale.sr_auto_id}`, formData);
                if (this.is_draft && this.force_save == 0) {
                    toast.success("Successfully saved as draft");
                    setTimeout(() => {
                        window.location = SalesList
                    }, 1500);
                } else {
                    toast.success("Successfully created sale");
                    setTimeout(() => {
                        window.location = SalesList
                    }, 1500);
                }
            } catch (e) {
                // this.error = e.response.data.message;
                toast.error(e.response?.data?.message || 'An error occurred');
            }
            this.uploading = false;
            this.is_draft = !!this.data.sale;
        }
    },

    beforeMount() {
        // console.log("Data =", this.data.sale)
        // console.log("Retention Amount =", this.data.sale.retention_amount)
        if(this.data.sale) {
            this.items = this.data.sale.items.map(item => {
                let product = item.srd_product_id
                    ? this.data.products.find(p => p.id === item.srd_product_id)
                    : null;

                // console.log("Product =", product);

                return {
                    ...item,
                    product: product ? product.id : null,
                    newProduct: !product ? item.product_name : '',
                    showInput: !product,
                    unit_price: product ? product.price : item.srd_unit_price,
                    qty: item.srd_qty,
                    vat: item.srd_vat_percent,
                    price: item.srd_total_amount,
                };
            });


            // Set other fields as before
            this.attachments = this.data.sale.attachments || [];
            this.notes = this.data.sale.notes;
            this.payment_term = this.data.sale.sr_payment_terms;
            this.invoice_no = this.data.sale.sr_invoice_no;
            this.invoice_desc = this.data.sale.sr_invoice_description;
            this.customer = this.data.sale.cus_auto_id;
            this.issue_date = this.data.sale.sr_issue_date;
            this.due_date = this.data.sale.sr_due_date;
            this.supply_date = this.data.sale.sr_supply_date;
            this.account_credit_id = this.data.sale.account_credit_id;
            this.account_debit_id = this.data.sale.account_debit_id;
            this.retention = this.data.sale.retention_amount;
            this.project_id = this.data.sale.project_id;
            this.is_draft = false;
            this.force_save = false;
        }
    }

}
</script>


