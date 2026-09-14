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
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    name: "ReportGenerate",
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
                report_type: "",
                project_id: "",
                start_date: "",
                end_date: "",
            },
            projects: [],
        };
    },
    mounted() {
        if (this.data_for_form && this.data_for_form.projects) {
            this.projects = this.data_for_form.projects;
        }
    },
    methods: {
        async generateReport() {
            this.loading = true;
            try {
                const response = await axios.post("/admin/report/generate", this.form);
                if (response.data.success) {
                    toast.success("Report generated successfully!");
                }
            } catch (error) {
                toast.error("Failed to generate report. Please try again.");
                console.error(error);
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
</style>
<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center fw-bold" style="color: blue">Generate Report</h4>
        </div>
        <div class="card-body">
            <form @submit.prevent="generateReport">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="report_type">Report Type</label>
                            <select v-model="form.report_type" class="form-control" id="report_type" required>
                                <option value="">Select Report Type</option>
                                <option value="employee">Employee Report</option>
                                <option value="salary">Salary Report</option>
                                <option value="attendance">Attendance Report</option>
                                <option value="expense">Expense Report</option>
                                <option value="project">Project Report</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="project_id">Project</label>
                            <select v-model="form.project_id" class="form-control" id="project_id">
                                <option value="">All Projects</option>
                                <option v-for="project in projects" :key="project.id" :value="project.id">
                                    {{ project.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" v-model="form.start_date" class="form-control" id="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" v-model="form.end_date" class="form-control" id="end_date" required>
                        </div>
                    </div>

