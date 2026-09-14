<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center fw-bold" style="color: blue">Report List</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search reports...">
                </div>
                <div class="col-md-4">
                    <select v-model="filterType" class="form-control">
                        <option value="">All Types</option>
                        <option value="employee">Employee Report</option>
                        <option value="salary">Salary Report</option>
                        <option value="attendance">Attendance Report</option>
                        <option value="expense">Expense Report</option>
                        <option value="project">Project Report</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary" @click="searchReports">
                        <i class="fa fa-search me-2"></i> Search
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Report Type</th>
                            <th>Project</th>
                            <th>Date Range</th>
                            <th>Generated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="reports.length === 0">
                            <td colspan="6" class="text-center">No reports found</td>
                        </tr>
                        <tr v-for="(report, index) in reports" :key="report.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ report.type }}</td>
                            <td>{{ report.project_name }}</td>
                            <td>{{ report.start_date }} - {{ report.end_date }}</td>
                            <td>{{ report.created_at }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" @click="viewReport(report.id)">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" @click="downloadReport(report.id)">
                                    <i class="fa fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteReport(report.id)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    name: "ReportList",
    data() {
        return {
            reports: [],
            searchQuery: "",
            filterType: "",
            loading: false,
        };
    },
    mounted() {
        this.fetchReports();
    },
    methods: {
        async fetchReports() {
            this.loading = true;
            try {
                // Add API call to fetch reports
                // const response = await axios.get("/admin/report/list");
                // this.reports = response.data.reports;
            } catch (error) {
                toast.error("Failed to fetch reports");
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        searchReports() {
            this.fetchReports();
        },
        viewReport(id) {
            // Implement view logic
            window.open(`/admin/report/view/${id}`, "_blank");
        },
        downloadReport(id) {
            // Implement download logic
            window.open(`/admin/report/download/${id}`, "_blank");
        },
        async deleteReport(id) {
            if (confirm("Are you sure you want to delete this report?")) {
                try {
                    await axios.delete(`/admin/report/delete/${id}`);
                    toast.success("Report deleted successfully");
                    this.fetchReports();
                } catch (error) {
                    toast.error("Failed to delete report");
                    console.error(error);
                }
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
</style>

