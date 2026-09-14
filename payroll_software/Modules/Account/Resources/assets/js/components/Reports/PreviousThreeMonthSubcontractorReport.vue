<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center fw-bold" style="color: blue">Previous Three Month Subcontractor Report</h4>
        </div>
        <div class="card-body">
            <form @submit.prevent="generateReport">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="selected_date">Select Date</label>
                            <input
                                type="date"
                                v-model="form.selected_date"
                                class="form-control"
                                id="selected_date"
                                required
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                <span v-if="loading">
                                    <i class="fa fa-spinner fa-spin"></i> Generating...
                                </span>
                                <span v-else>
                                    <i class="fa fa-file-text-o me-2"></i> Generate Report
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Report Results Table -->
            <div class="table-responsive mt-4" v-if="reportData.length > 0">
                <h5 class="text-center mb-3">Subcontractor Report for Previous 3 Months from {{ selectedDateFormatted }}</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" class="align-middle">Subcontractor Name</th>
                            <th v-for="month in monthHeaders" :key="month" colspan="2" class="text-center">
                                {{ month }}
                            </th>
                        </tr>
                        <tr>
                            <template v-for="month in monthHeaders" :key="month + '_sub'">
                                <th class="text-center">Invoice Amount</th>
                                <th class="text-center">Payment Amount</th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(subcontractor, index) in reportData" :key="index">
                            <td><strong>{{ subcontractor.subcon_name }}</strong></td>
                            <template v-for="(month, monthIndex) in subcontractor.months" :key="monthIndex">
                                <td class="text-end">{{ formatCurrency(month.invoice_amount) }}</td>
                                <td class="text-end">{{ formatCurrency(month.payment_amount) }}</td>
                            </template>
                        </tr>
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th>Total:</th>
                            <template v-for="(month, monthIndex) in monthHeaders" :key="'total_' + monthIndex">
                                <th class="text-end">{{ formatCurrency(getTotalInvoiceForMonth(monthIndex)) }}</th>
                                <th class="text-end">{{ formatCurrency(getTotalPaymentForMonth(monthIndex)) }}</th>
                            </template>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- No Data Message -->
            <div v-if="showNoData" class="alert alert-info text-center mt-4">
                <i class="fa fa-info-circle me-2"></i>
                No subcontractor data found for the selected period.
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    name: "PreviousThreeMonthSubcontractorReport",
    data() {
        return {
            loading: false,
            showNoData: false,
            form: {
                selected_date: new Date().toISOString().slice(0, 10),
            },
            reportData: [],
            selectedDate: '',
            monthHeaders: [],
        };
    },
    computed: {
        selectedDateFormatted() {
            if (!this.selectedDate) return '';
            const date = new Date(this.selectedDate);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        },
    },
    methods: {
        formatCurrency(amount) {
            return parseFloat(amount || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        getTotalInvoiceForMonth(monthIndex) {
            return this.reportData.reduce((sum, subcontractor) => {
                return sum + parseFloat(subcontractor.months[monthIndex]?.invoice_amount || 0);
            }, 0);
        },
        getTotalPaymentForMonth(monthIndex) {
            return this.reportData.reduce((sum, subcontractor) => {
                return sum + parseFloat(subcontractor.months[monthIndex]?.payment_amount || 0);
            }, 0);
        },
        async generateReport() {
            this.loading = true;
            this.showNoData = false;

            try {
                const response = await axios.post("/admin/report/previous-three-month-subcontractor-report", this.form);

                if (response.data.success) {
                    this.reportData = response.data.reportData || [];
                    this.selectedDate = response.data.selectedDate;

                    // Extract month headers from the first subcontractor if data exists
                    if (this.reportData.length > 0 && this.reportData[0].months) {
                        this.monthHeaders = this.reportData[0].months.map(month => month.month_year);
                    }

                    if (this.reportData.length === 0) {
                        this.showNoData = true;
                        toast.info("No data found for the selected period");
                    } else {
                        toast.success("Report generated successfully!");
                    }
                } else {
                    toast.error(response.data.message || "Failed to generate report");
                    this.reportData = [];
                    this.monthHeaders = [];
                    this.showNoData = false;
                }
            } catch (error) {
                console.error("Failed to generate report", error);
                const errorMessage = error.response?.data?.message || "An error occurred while generating the report";
                toast.error(errorMessage);
                this.reportData = [];
                this.monthHeaders = [];
                this.showNoData = false;
            } finally {
                this.loading = false;
            }
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
.table th {
    background-color: #f8f9fa;
    font-weight: bold;
    vertical-align: middle;
}
.align-middle {
    vertical-align: middle !important;
}
</style>
