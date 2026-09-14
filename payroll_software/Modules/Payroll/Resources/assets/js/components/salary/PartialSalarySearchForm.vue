<template>
    <div>
        <div class="card mb-2">
            <div class="card-body">
                <form @submit.prevent="search">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Employee</label>
                                 <input
                                type="text"
                                v-model="filters.employee_id"
                                class="form-control"
                                placeholder="Search by ID"
                             >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Project</label>
                                <select v-model="filters.project_id" class="form-select">
                                    <option value="">Select Project</option>
                                    <option v-for="project in projects" :key="project.proj_id" :value="project.proj_id">
                                        {{ project.proj_name }}
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Month</label>
                                <select v-model="filters.month" class="form-select">
                                    <option value="">Select Month</option>
                                    <option v-for="(name, num) in months" :key="num" :value="num">
                                        {{ name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year</label>
                                <select v-model="filters.year" class="form-select">
                                    <option value="">Select Years</option>
                                    <option v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                        </div>

                         <div class="col-md-2">
                            <div class="form-group">
                                <label>Search Type</label>
                                <select v-model="filters.operation_type" class="form-select">
                                    <option value="search">Search</option>
                                    <option value="excel">Export Excel</option>
                                    <option value="pdf">Export PDF</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                    <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                                    Search
                                </button>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" :disabled="this.results.length == 0" @click="download()">
                                    Excel Download
                                </button>
                            </div>
                        </div>

                        <!-- New: Summary Section -->
                        <div class="col-md-6" v-if="results.length > 0">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="summary-info" style="display: flex; gap: 20px; padding-top: 5px;">
                                    <span><strong>Total Employees:</strong> {{ totalEmployees }}</span>
                                    <span><strong>Total Amount:</strong> {{ formatAmount(totalAmount) }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div v-if="results.length > 0" class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ID</th>
                        <th>Emp. Name</th>
                        <th>Iqama</th>
                        <th>Passport</th>
                        <th>Salary</th>
                        <th>Project</th>
                        <th>Salary For</th>
                        <th>Paid At</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(record, index) in results" :key="record.psh_auto_id">
                        <td>{{ index+1 }}</td>
                        <td>{{ record.employee?.employee_id || 'N/A' }}</td>
                        <td>{{ record.employee?.employee_name || 'N/A' }}</td>
                        <td>{{ record.employee?.akama_no || 'N/A' }}</td>
                        <td>{{ record.employee?.passfort_no || 'N/A' }}</td>
                        <td>{{ record.employee?.hourly_employee == 1 ? 'Hourly' : 'Basic'  }}</td>
                        <td>{{ record.project?.proj_name || 'N/A' }}</td>
                        <td>{{ getMonthName(record.month) }},{{ record.year }}</td>
                        <td>{{ formatDate(record.paid_at) }}</td>
                        <td>{{ formatAmount(record.amount) }}</td>
                        <td>
                            <button @click="editRecord(record)" :hidden="!hasPermission('partial_salary_update')" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button  @click="deleteRecord(record.psh_auto_id)" :hidden="!hasPermission('partial_salary_delete')" class="btn btn-sm btn-danger" title="Delete">
                                 <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <!-- Add total row in table -->
                <tfoot v-if="results.length > 0">
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="9" style="text-align: right;">Total:</td>
                        <td>{{ formatAmount(totalAmount) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div v-else-if="searched && results.length === 0" class="alert alert-info">
            No records found.
        </div>
    </div>
</template>

<script>
import { inject } from 'vue';
import { toast } from 'vue3-toastify';
import * as XLSX from 'xlsx';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
import {PartialSalarySearchAPI,PartialSalaryDeleteAPI,PartialSalaryDownloadAPI} from '../../routes.js';
const today = new Date();
const currentYear = today.getFullYear();
const currentMonth = today.getMonth() + 1; // Months are zero-indexed


export default {
    setup() {
        const auth = inject('auth');
        const { hasPermission } = useAuth();

        return {
            hasPermission,
            auth,
        };
    },

    props: {
        projects: Array,
        authUserId: Number,
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
            filters: {
                project_id: '',
                employee_id: '',
                month: currentMonth,
                year: currentYear,
                operation_type: 'search' // default to search, can be changed to 'excel' or 'pdf' for export functionality
            },
            results: [],
            loading: false,
            searched: false,
        }
    },
    computed: {
        totalEmployees() {
            return this.results.length;
        },
        totalAmount() {
            return this.results.reduce((sum, record) => sum + parseFloat(record.amount || 0), 0);
        }
    },
    methods: {
        async search() {
            this.loading = true;
            this.searched = true;
            if(this.filters.employee_id == '' && this.filters.project_id == '' && this.filters.month == '' ){
                this.loading = false;
                toast.error('Please Input An Employee ID or Select Project Name')
                return;
            }
            try {
                const response = await axios.post(PartialSalarySearchAPI, this.filters);
                this.results = response.data;
            } catch (error) {
               // alert('Error searching records');
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        download() {
            try {
                var sampleData = [];

                // Use push instead of appendChild
                this.results.forEach((element, index) => {
                    sampleData.push({
                        'S.N': index + 1,
                        'Employee ID': element.employee?.employee_id || 'N/A',
                        'Employee Name': element.employee?.employee_name || 'Not Found',
                        'Iqama No': element.employee?.akama_no || 'N/A',
                        'Passport No': element.employee?.passfort_no || 'N/A',
                        'Salary Type': element.employee?.hourly_employee == 1 ? 'Hourly' : 'Basic',
                        'Project Name': element.project?.proj_name || 'N/A',
                        'Month': this.getMonthName(element.month),
                        'Year': element.year,
                        'Paid At': element.paid_at || 'N/A',
                        'Amount (SAR)': element.amount || 0
                    });
                });

                // Add Total row at the end
                sampleData.push({
                    'S.N': '',
                    'Employee ID': '',
                    'Employee Name': '',
                    'Iqama No': '',
                    'Passport No': '',
                    'Salary Type': '',
                    'Project Name': '',
                    'Month': '',
                    'Year': '',
                    'Paid At': '',
                    'Amount (SAR)': this.totalAmount
                });

                // Add Summary row with Total Employees
                sampleData.push({
                    'S.N': '',
                    'Employee ID': '',
                    'Employee Name': '',
                    'Iqama No': '',
                    'Passport No': '',
                    'Salary Type': '',
                    'Project Name': '',
                    'Month': '',
                    'Year': '',
                    'Paid At': 'Total Employees:',
                    'Amount (SAR)': this.totalEmployees
                });

                const worksheet = XLSX.utils.json_to_sheet(sampleData);

                // Set column widths for better readability
                const colWidths = [
                    { wch: 10 }, // S.N
                    { wch: 15 }, // Employee ID
                    { wch: 25 }, // Employee Name
                    { wch: 15 }, // Iqama No
                    { wch: 15 }, // Passport No
                    { wch: 15 }, // Salary Type
                    { wch: 25 }, // Project Name
                    { wch: 15 }, // Month
                    { wch: 10 }, // Year
                    { wch: 15 }, // Paid At
                    { wch: 20 }, // Amount (SAR)
                ];
                worksheet['!cols'] = colWidths;

                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Partial Salary');

                // Generate Excel file
                const excelBuffer = XLSX.write(workbook, {
                    bookType: 'xlsx',
                    type: 'array',
                    bookSST: false
                });

                const blob = new Blob([excelBuffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });

                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `partial_salary_${new Date().toISOString().slice(0,10)}.xlsx`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);

                toast.success('Excel file downloaded successfully');

            } catch (error) {
                console.log(error);
                toast.error('Error downloading Excel file');
            } finally {
                this.loading = false;
            }
        },

        editRecord(record) {
            this.$emit('edit', record);
        },

        async deleteRecord(id) {
            if (!confirm('Are you sure you want to delete this record?')) {
                return;
            }

            try {
                await axios.delete(PartialSalaryDeleteAPI.replace(':id', id));
                this.search(); // Refresh the list
                toast.success('Record deleted successfully');
            } catch (error) {
                toast.error('Error deleting record');
                console.error(error);
            }
        },

        getMonthName(month) {
            return this.months[month] || 'N/A';
        },

        formatAmount(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'SAR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0);
        },

        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString();
        }
    }
}
</script>

<style scoped>
.summary-info {
    font-size: 14px;
    background-color: #f8f9fa;
    padding: 5px 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
</style>
