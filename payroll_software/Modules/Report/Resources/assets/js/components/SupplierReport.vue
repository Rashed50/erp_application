<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center fw-bold" style="color: blue">Supplier Report</h4>
        </div>
        <div class="card-body">
            <form @submit.prevent="generateReport">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="supplier_id">Supplier</label>
                            <Multiselect
                                v-model="form.supplier_ids"
                                mode="multiple"
                                :options="supplierOptions"
                                value-prop="value"
                                track-by="label"
                                label="label"
                                placeholder="Select Suppliers"
                                :searchable="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :create-option="false"
                                :hide-selected="false"
                                :caret="true"
                                class="multiselect-blue"
                            >
                                <template #tag="{ option, handleTagRemove }">
                                    <div class="multiselect-tag is-user">
                                        {{ option.label }}
                                        <span
                                            class="multiselect-tag-remove"
                                            @click="handleTagRemove(option, $event)"
                                        >
                                            <i class="fa fa-times"></i>
                                        </span>
                                    </div>
                                </template>

                                <template #multiplelabel="{ values }">
                                    <div class="multiselect-multiple-label">
                                        {{ values.length }} Suppliers Selected
                                    </div>
                                </template>

                                <template #nooptions>
                                    <span class="text-muted">No suppliers available</span>
                                </template>
                            </Multiselect>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" v-model="form.start_date" class="form-control" id="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" v-model="form.end_date" class="form-control" id="end_date" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="payment_type">Type</label>
                            <select v-model="form.payment_type" class="form-select" id="payment_type">
                                <option value="">Select Type</option>
                                <option value="1">Bill Payment</option>
                                <option value="2">Purchase</option>
                                <option value="3">Current Balance</option>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary" :disabled="loading">
                            <span v-if="loading">
                                <i class="fa fa-spinner fa-spin"></i> Generating...
                            </span>
                            <span v-else>
                                <i class="fa fa-file-pdf-o me-2"></i> Generate Report
                            </span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Report Results Table -->
            <div class="table-responsive mt-4" v-if="reportData.length > 0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier Name</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in reportData" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.supplier_name }}</td>
                            <td>{{ item.invoice_no }}</td>
                            <td>{{ item.date }}</td>
                            <td>{{ item.amount }}</td>
                            <td>{{ item.paid }}</td>
                            <td>{{ item.due }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th>{{ totalAmount }}</th>
                            <th>{{ totalPaid }}</th>
                            <th>{{ totalDue }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import Multiselect from "@vueform/multiselect";
var today = new Date().toISOString().slice(0, 10);

export default {
    name: "SupplierReport",
    components: {
        Multiselect,
    },
    props: {
        data_for_form: {
            type: Object,
            required: false,
            default: () => ({}),
        },
    },
    data() {
        return {
            loading: false,
            form: {
                supplier_ids: [],
                start_date: today,
                end_date: today,
                payment_type: "",
            },
            suppliers: [],
            reportData: [],
        };
    },
    computed: {
        supplierOptions() {
            if (!this.suppliers || !Array.isArray(this.suppliers)) return [];
            return this.suppliers.map((supplier) => ({
                label: supplier.supplier_name,
                value: supplier.supplier_id,
            }));
        },
        totalAmount() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0).toFixed(2);
        },
        totalPaid() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.paid || 0), 0).toFixed(2);
        },
        totalDue() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.due || 0), 0).toFixed(2);
        },
    },
    mounted() {
        this.fetchSuppliers();
    },
    methods: {
        async fetchSuppliers() {
            try {
                const response = await axios.get("/admin/report/suppliers");
                this.suppliers = response.data.suppliers || [];
            } catch (error) {
                console.error("Failed to fetch suppliers", error);
            }
        },
        async generateReport() {
            // Construct the PDF URL with form parameters
            const params = new URLSearchParams();

            // Handle multiple supplier IDs
            if (this.form.supplier_ids && this.form.supplier_ids.length > 0) {
                this.form.supplier_ids.forEach(id => {
                    params.append('supplier_id[]', id);
                });
            }

            if (this.form.start_date) {
                params.append('start_date', this.form.start_date);
            }
            if (this.form.end_date) {
                params.append('end_date', this.form.end_date);
            }
            if (this.form.payment_type) {
                params.append('payment_type', this.form.payment_type);
            }

            const pdfUrl = `/admin/report/supplier-report/pdf?${params.toString()}`;
            // Open the PDF in a new tab
            window.open(pdfUrl, '_blank');
        },
    },
};
</script>

<style scoped>
.card {
    margin: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.card-header {
    background-color: #f8f9fa;
}

.multiselect-blue {
    --ms-tag-bg: #4361ee;
    --ms-tag-color: #fff;
    --ms-tag-radius: 6px;
    --ms-ring-color: #4361ee40;
}

.multiselect-tag {
    background: #4361ee !important;
    color: white !important;
    padding: 4px 8px !important;
    border-radius: 6px !important;
    margin: 2px !important;
    font-size: 0.85rem;
}

.multiselect-tag-remove {
    margin-left: 6px !important;
    cursor: pointer;
}

.multiselect-multiple-label {
    padding: 8px 12px;
    font-weight: 500;
    color: #4361ee;
}
</style>
