<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center fw-bold" style="color: blue">Previous Three Month Report</h4>
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
                <h5 class="text-center mb-3">Report for Previous 3 Months from {{ selectedDateFormatted }}</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Month / Year</th>
                            <th class="text-end">Total Salary</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Unpaid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in reportData" :key="index">
                            <td><strong>{{ item.month_year }}</strong></td>
                            <td class="text-end">{{ formatCurrency(item.total_salary) }}</td>
                            <td class="text-end text-success">{{ formatCurrency(item.paid) }}</td>
                            <td class="text-end text-danger">{{ formatCurrency(item.unpaid) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th>Total:</th>
                            <th class="text-end">{{ formatCurrency(totalSalary) }}</th>
                            <th class="text-end text-success">{{ formatCurrency(totalPaid) }}</th>
                            <th class="text-end text-danger">{{ formatCurrency(totalUnpaid) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- No Data Message -->
            <div v-if="showNoData" class="alert alert-info text-center mt-4">
                <i class="fa fa-info-circle me-2"></i>
                No salary data found for the selected period.
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    name: "PreviousThreeMonthReport",
    data() {
        return {
            loading: false,
            showNoData: false,
            form: {
                selected_date: new Date().toISOString().slice(0, 10),
            },
            reportData: [],
            selectedDate: '',
        };
    },
    computed: {
        selectedDateFormatted() {
            if (!this.selectedDate) return '';
            const date = new Date(this.selectedDate);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        },
        totalSalary() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.total_salary || 0), 0);
        },
        totalPaid() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.paid || 0), 0);
        },
        totalUnpaid() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.unpaid || 0), 0);
        },
    },
    methods: {
        formatCurrency(amount) {
            return parseFloat(amount || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        async generateReport() {
            this.loading = true;
            this.showNoData = false;

            try {
                const response = await axios.post("/admin/report/previous-three-month-report", this.form);

                if (response.data.success) {
                    this.reportData = response.data.reportData || [];
                    this.selectedDate = response.data.selectedDate;

                    if (this.reportData.length === 0) {
                        this.showNoData = true;
                        toast.info("No data found for the selected period");
                    } else {
                        toast.success("Report generated successfully!");
                    }
                } else {
                    toast.error(response.data.message || "Failed to generate report");
                    this.reportData = [];
                    this.showNoData = false;
                }
            } catch (error) {
                console.error("Failed to generate report", error);
                const errorMessage = error.response?.data?.message || "An error occurred while generating the report";
                toast.error(errorMessage);
                this.reportData = [];
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
}
.text-success {
    color: #28a745 !important;
}
.text-danger {
    color: #dc3545 !important;
}
</style>
