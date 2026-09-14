{{-- resources/js/components/payroll/PartialSalaryExcelUpload.vue --}}

<template>
    <div class="card mt-4">
        <div class="card-header">
            <h4>Multiple Employees Partial Salary Excel File Upload</h4>
        </div>
        <div class="card-body">
            <form @submit.prevent="handlePreview">
                <div class="row">
                    <div class="col-md-3 d-none">
                        <div class="form-group">
                            <label>Project *</label>
                            <select
                                v-model="form.project_id"
                                class="form-select"
                                :class="{ 'is-invalid': errors.project_id }">
                                <option value="">Select Project</option>
                                <option v-for="project in projects" :key="project.proj_id" :value="project.proj_id">
                                    {{ project.proj_name }}
                                </option>
                            </select>
                            <div v-if="errors.project_id" class="invalid-feedback">
                                {{ errors.project_id[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Month *</label>
                            <select
                                v-model="form.month"
                                class="form-select"
                                :class="{ 'is-invalid': errors.month }"
                                required
                            >
                                <option value="">Select Month</option>
                                <option v-for="(name, num) in months" :key="num" :value="num">
                                    {{ name }}
                                </option>
                            </select>
                            <div v-if="errors.month" class="invalid-feedback">
                                {{ errors.month[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Year *</label>
                            <select
                                v-model="form.year"
                                class="form-select"
                                :class="{ 'is-invalid': errors.year }"
                                required
                            >
                                <option value="">Select Year</option>
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                            <div v-if="errors.year" class="invalid-feedback">
                                {{ errors.year[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Paid Date *</label>
                            <input
                                type="date"
                                v-model="form.paid_at"
                                class="form-control"
                                :class="{ 'is-invalid': errors.paid_at }"
                                required
                            >
                            <div v-if="errors.paid_at" class="invalid-feedback">
                                {{ errors.paid_at[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button
                                type="button"
                                class="btn btn-info btn-block"
                                @click="downloadSample"
                            >
                                <i class="fas fa-download"></i> Download Sample
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Excel File (xlsx, xls, csv)*</label>
                            <div class="custom-file">
                                <input
                                    type="file"
                                    class="custom-file-input"
                                    id="excelFile"
                                    ref="fileInput"
                                    @change="handleFileChange"
                                    accept=".xlsx,.xls,.csv"
                                >
                                <label class="custom-file-label" for="excelFile">
                                    {{ fileName || 'Choose file...' }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Required columns: employee_id, amount
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="!file || loading"
                        >
                            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                            Preview Records
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary ml-2"
                            @click="resetForm"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </form>

            <!-- Preview Section -->
            <div v-if="previewData" class="mt-4">
                <div class="alert alert-info">
                    <strong>Summary:</strong>
                    Total Rows: {{ previewData.total_rows }} |
                    Valid Rows: {{ previewData.valid_rows }} |
                    Invalid Rows: {{ previewData.invalid_rows }}
                </div>

                <div v-if="previewData.errors.length > 0" class="alert alert-warning">
                    <!-- <strong>Validation Errors:</strong>
                    <ul>
                        <li v-for="error in previewData.errors" :key="error">{{ error }}</li>
                    </ul> -->
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Row #</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Iqama No</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Error Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in previewData.preview_data" :key="row.row_number">
                                <td>{{ index + 1 }}</td>
                                <td>{{ row.row_number }}</td>
                                <td>{{ row.employee_id }}</td>
                                <td>{{ row.employee_name }}</td>
                                <td>{{ row.akama_no }}</td>
                                <td>{{ formatAmount(row.amount) }}</td>
                                <td>
                                    <span v-if="row.is_valid" class="badge badge-success">Valid</span>
                                    <span v-else class="badge badge-danger">Invalid</span>
                                </td>
                                <td>{{ row.error_message || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button
                        @click="submitImport"
                        class="btn btn-success"
                        :disabled="importing || previewData.valid_rows === 0"
                    >
                        <i v-if="importing" class="fas fa-spinner fa-spin"></i>
                        Import Valid Records ({{ previewData.valid_rows }})
                    </button>
                    <button
                        @click="cancelImport"
                        class="btn btn-danger ml-2"
                        :disabled="importing"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Import Result -->
            <div v-if="importResult" class="mt-4">
                <div :class="['alert', importResult.success_count > 0 ? 'alert-success' : 'alert-danger']">
                    <strong>{{ importResult.message }}</strong>
                    <div v-if="importResult.errors && importResult.errors.length > 0" class="mt-2">
                        <strong>Errors:</strong>
                        <ul>
                            <li v-for="error in importResult.errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import * as XLSX from 'xlsx';

const today = new Date();
export default {
    name: 'PartialSalaryExcelUpload',

    props: {
        projects: {
            type: Array,
            required: true
        },
        months: {
            type: Object,
            required: true
        },
        years: {
            type: Array,
            required: true
        }
    },

    data() {
        return {
            form: {
                project_id: "",
                month: '',
                year: '',
                paid_at: today.toISOString().substr(0, 10), // Format as YYYY-MM-DD,
            },
            file: null,
            fileName: '',
            errors: {},
            loading: false,
            importing: false,
            previewData: null,
            importResult: null
        }
    },

    methods: {
        handleFileChange(event) {
            this.file = event.target.files[0];
            this.fileName = this.file ? this.file.name : '';
            this.previewData = null;
            this.importResult = null;
        },

        async handlePreview() {
            if (!this.validateForm()) {
                return;
            }

            if (!this.file) {
                toast.error('Please select a file');
                return;
            }

            this.loading = true;
            this.errors = {};

            const formData = new FormData();
            formData.append('project_id', this.form.project_id);
            formData.append('month', this.form.month);
            formData.append('year', this.form.year);
            formData.append('paid_at', this.form.paid_at);
            formData.append('file', this.file);

            try {

                const response = await axios.post('/admin/payroll/salary/partial-salary-histories/preview-excel', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.previewData = response.data;
                this.importResult = null;

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else if (error.response && error.response.data.error) {
                    toast.error(error.response.data.error);
                } else {
                    toast.error('Error previewing file. Please check the file format.');
                }
            } finally {
                this.loading = false;
            }
        },

        async submitImport() {
            if (!confirm(`Are you sure you want to import ${this.previewData.valid_rows} records?`)) {
                return;
            }

            this.importing = true;

            const formData = new FormData();
            formData.append('project_id', this.form.project_id);
            formData.append('month', this.form.month);
            formData.append('year', this.form.year);
            formData.append('paid_at', this.form.paid_at);
            formData.append('file', this.file);

            try {
                const response = await axios.post('/admin/payroll/salary/partial-salary-histories/import-excel', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.importResult = response.data;

                if (response.data.success_count > 0) {
                    // called back function (import-completed) to refresh the list in parent component
                    this.$emit('import-completed');
                    debugger;
                    setTimeout(() => {
                        this.resetForm();
                    }, 3000);
                }

            } catch (error) {
                debugger;
                if (error.response && error.response.data.error) {
                    toast.error(error.response.data.error);
                } else {
                    toast.error('Error importing file');
                }
            } finally {
                 debugger;
                this.importing = false;
            }
        },

        validateForm() {
            // if (!this.form.project_id) {
            //     toast.error('Please select a project');
            //     return false;
            // }
            if (!this.form.month) {
                toast.error('Please select a month');
                return false;
            }
            if (!this.form.year) {
                toast.error('Please select a year');
                return false;
            }
            if (!this.form.paid_at) {
                toast.error('Please select paid date');
                return false;
            }
            return true;
        },

        resetForm() {
            this.form = {
                project_id: '',
                month: '',
                year: '',
                paid_at: ''
            };
            this.file = null;
            this.fileName = '';
            this.previewData = null;
            this.importResult = null;
            this.errors = {};

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        cancelImport() {
            this.previewData = null;
            this.importResult = null;
        },

        async downloadSample() {
            try {
                this.generateSampleExcel();
            } catch (error) {
                toast.error('Error downloading sample file');
            }
        },

        formatAmount(amount) {
            if (!amount && amount !== 0) return 'N/A';
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'SAR'
            }).format(amount);
        },

        generateSampleExcel() {
            const sampleData = [
                { employee_id: '1020', amount: 1500 },
                { employee_id: '1021', amount: 2000 },
                { employee_id: '1022', amount: 1800 }
            ];

            const worksheet = XLSX.utils.json_to_sheet(sampleData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sample Data');

            const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
            const blob = new Blob([excelBuffer], { type: 'application/octet-stream' });
            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'partial_salary_sample.xlsx');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }



    }
}
</script>

<style scoped>
.custom-file-label::after {
    content: "Browse";
}
</style>
