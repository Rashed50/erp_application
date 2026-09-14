<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                     <!-- Search Form -->
                    <div class="row mb-1">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">Search Payslip Files</h6>
                                    <form @submit.prevent="searchSheets">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Employee ID</label>
                                                    <input
                                                        type="number"
                                                        v-model="searchParams.employee_id"
                                                        class="form-control"
                                                        placeholder="Optional"
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Month</label>
                                                    <select class="form-select" v-model="searchParams.month">
                                                        <option value="">Select Month</option>
                                                        <option v-for="item in months" :value="item.month_id">
                                                            {{ item.month_name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Year</label>
                                                    <select class="form-select" v-model="searchParams.year">
                                                        <option value="">Select Year</option>
                                                        <option v-for="year in years" :value="year">
                                                            {{ year }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input
                                                        type="date"
                                                        v-model="searchParams.date"
                                                        class="form-control"
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button
                                                        type="submit"
                                                        class="btn btn-primary w-100"
                                                        :disabled="loading"
                                                    >
                                                        <i class="fas fa-search"></i>
                                                        {{ loading ? 'Searching...' : 'Search' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover custom_table mb-0">
                                    <thead style="background-color: black; color: white;">
                                        <tr>
                                            <th style="width: 5%">S.N</th>
                                            <th style="width: 15%">Employee ID</th>
                                            <th style="width: 20%">Month & Year</th>
                                            <th style="width: 15%">Salary Date</th>
                                            <th style="width: 15%">Uploaded By</th>
                                            <th style="width: 25%">Remarks</th>
                                            <th style="width: 20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="loading && salarySheets.length === 0">
                                            <td colspan="6" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-else-if="!loading && salarySheets.length === 0">
                                            <td colspan="6" class="text-center">No records found</td>
                                        </tr>
                                        <tr v-for="(sheet, index) in salarySheets" :key="sheet.id">
                                            <td> {{ index +1 }}</td>
                                            <td>{{ sheet.no_of_emp || 'N/A' }}</td>
                                            <td>{{ getMonthName(sheet.month) }} {{ sheet.year }}</td>
                                            <td>{{ formatDate(sheet.salary_date) }}</td>
                                            <td>{{ sheet.uploader_name ? sheet.uploader_name : 'N/A' }}</td>
                                            <td>{{ sheet.remarks || 'No remarks' }}</td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-info me-1"
                                                    @click="viewFile(sheet.file_path)"
                                                    title="View" >
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-danger"
                                                    @click="deleteSheet(sheet.ss_auto_id)"
                                                    title="Delete"
                                                    :disabled="deleting"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3" v-if="lastPage > 1">
                                <nav>
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                            <button class="page-link" @click="changePage(currentPage - 1)">Previous</button>
                                        </li>
                                        <li class="page-item" v-for="page in pages" :key="page"
                                            :class="{ active: page === currentPage }">
                                            <button class="page-link" @click="changePage(page)">{{ page }}</button>
                                        </li>
                                        <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                                            <button class="page-link" @click="changePage(currentPage + 1)">Next</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { inject } from 'vue';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
import {PayslipSearchAPI,PayslipDeleteAPI} from '../../routes.js';
const today = new Date();
const currentYear = today.getFullYear();
const currentMonth = today.getMonth() + 1; // Months are zero-indexed

export default {
    name: 'SearchSalarySheet',
    props: {
        // months: {
        //     type: Array,
        //     required: true
        // }
    },
    data() {
        return {
            searchParams: {
                employee_id: '',
                month: '',
                year: '',
                date: ''
            },
            salarySheets: [],
            loading: false,
            deleting: false,
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            total: 0,
            years: [],
            months: [
                { month_id: 1, month_name: 'January' },
                { month_id: 2, month_name: 'February' },
                { month_id: 3, month_name: 'March' },
                { month_id: 4, month_name: 'April' },
                { month_id: 5, month_name: 'May' },
                { month_id: 6, month_name: 'June' },
                { month_id: 7, month_name: 'July' },
                { month_id: 8, month_name: 'August' },
                { month_id: 9, month_name: 'September' },
                { month_id: 10, month_name: 'October' },
                { month_id: 11, month_name: 'November' },
                { month_id: 12, month_name: 'December' }
            ],
        }
    },
    computed: {
        pages() {
            const pages = [];
            for (let i = 1; i <= this.lastPage; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    mounted() {
        this.generateYears();
        this.searchSheets();
    },
    methods: {
        generateYears() {
            const currentYear = new Date().getFullYear();
            const startYear = 2022; // system started from 2022
            for (let i = currentYear; i >= startYear; i--) {
                this.years.push(i);
            }
        },
        async searchSheets() {
            this.loading = true;
            this.currentPage = 1;

            try {
                const params = {
                    page: this.currentPage,
                    ...this.searchParams
                };

                const response = await axios.get(PayslipSearchAPI, { params });

                if (response.data.status == 200) {
                    this.salarySheets = response.data.data;
                    this.currentPage = parseInt(response.data.current_page);
                    this.lastPage = parseInt(response.data.last_page);
                    this.total = response.data.total;
                }else {
                    this.salarySheets = [];
                     this.total = 0;
                    toast.error(response.data.message || 'Records not found');
                }
            } catch (error) {
                console.error('Error searching salary sheets:', error);
                toast.error('Failed to search salary sheets');
            } finally {
                this.loading = false;
            }
        },
        async changePage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) {
                return;
            }

            this.currentPage = page;
            this.loading = true;

            try {
                const params = {
                    page: this.currentPage,
                    ...this.searchParams
                };

                const response = await axios.get(PayslipSearchAPI, { params });

                if (response.data.status == 200) {

                    this.salarySheets = response.data;
                    this.currentPage = parseInt(response.data.current_page);
                    this.lastPage = parseInt(response.data.last_page);
                }
            } catch (error) {
                console.error('Error changing page:', error);
                toast.error('Failed to load page');
            } finally {
                this.loading = false;
            }
        },
        viewFile(filePath) {


            filePath = (import.meta.env.VITE_AWS_S3_ENDPOINT + filePath);
            if (filePath) {
                window.open(filePath, '_blank');
            } else {
                toast.error('File not found');
            }
        },
        async deleteSheet(id) {
            if (!confirm('Are you sure you want to delete this salary sheet?')) {
                return;
            }

            this.deleting = true;

            try {
                const response = await axios.delete(`${PayslipDeleteAPI}/${id}`);

                if (response.data.status == 200) {
                    toast.success('Salary sheet deleted successfully');
                    this.searchSheets();
                } else {
                    toast.error(response.data.message || 'Failed to delete');
                }
            } catch (error) {
                console.error('Error deleting salary sheet:', error);
                toast.error('Failed to delete salary sheet');
            } finally {
                this.deleting = false;
            }
        },
        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString();
        },
        resetSearch() {
            this.searchParams = {
                employee_id: '',
                month: '',
                year: '',
                date: ''
            };
            this.searchSheets();
        },
        getMonthName(monthNumber) {
            const date = new Date();
            date.setMonth(monthNumber - 1); // JavaScript months are 0-indexed
            return date.toLocaleString('default', { month: 'short' });
        }
    }
}
</script>

<style scoped>
.custom_table {
    font-size: 14px;
}
</style>
