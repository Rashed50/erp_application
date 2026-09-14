<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                                <div class="form-group mb-3">
                                    <label class="form-label">Employee ID <span class="text-muted">(Optional)</span></label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model="form.no_of_emp"
                                        placeholder="Enter Employee ID"
                                    >
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Salary Month <span class="text-danger">*</span></label>
                                    <select class="form-select" v-model="form.month" required>
                                        <option value="">Select Month</option>
                                        <option v-for="item in months" :value="item.month_id" :selected="item.month_id == currentMonth">
                                            {{ item.month_name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Salary Year <span class="text-danger">*</span></label>
                                    <select class="form-select" v-model="form.year" required>
                                        <option value="">Select Year</option>
                                        <option v-for="year in years" :value="year">
                                            {{ year }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Salary Date <span class="text-danger">*</span></label>
                                    <input
                                        type="date"
                                        v-model="form.salary_date"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea
                                        class="form-control"
                                        v-model="form.remarks"
                                        rows="3"
                                        placeholder="Enter any remarks"
                                    ></textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label">Upload File <span class="text-danger">*</span></label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        @change="handleFileUpload"
                                        ref="fileInput"
                                        required
                                        accept=".jpg,.jpeg,.png,.gif,.pdf"
                                    >
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" :disabled="loading">
                                        <i class="fas fa-upload"></i>
                                        {{ loading ? 'Uploading...' : 'Submit' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                         <!-- Quick Info Section -->
                        <div class="col-lg-6">
                            <div class="alert alert-info">
                                <h6 class="alert-heading mb-2">
                                    <i class="fas fa-info-circle"></i> Instructions
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Select the appropriate month and year</li>
                                    <li>Set the salary payment date</li>
                                    <li>Upload the salary file in image or PDF format</li>
                                    <li>Optional: Add employee ID or remarks</li>
                                    <li>Click Submit to process the file</li>
                                </ul>
                            </div>
                            <div id="preview" class="mt-3" v-if="previewUrl">
                                <h6>File Preview:</h6>
                                <img v-if="isImage" :src="previewUrl" class="img-fluid" style="max-height: 200px">
                                <iframe v-else-if="isPdf" :src="previewUrl" width="100%" height="400px"></iframe>
                                <div v-else>
                                    <p>File uploaded: {{ fileName }}</p>
                                </div>
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
import {PayslipUploadAPI } from '../../routes.js';
const today = new Date();
const currentYear = today.getFullYear();
const currentMonth = today.getMonth() + 1; // Months are zero-indexed


// export const PayslipUploadAPI = '/admin/payroll/salary-sheet/store';
// export const PayslipSearchAPI = '/admin/payroll/salary-sheets/search';
// export const PayslipUpdateAPI = '/admin/payroll/salary-sheet/update/:id';
// export const PayslipDeleteAPI = '/admin/payroll/salary-sheet/delete/:id';

export default {
    name: 'AddSalarySheet',
    // props: {
    //     months: {
    //         type: Array,
    //         required: true
    //     }
    // },
    data() {
        return {
            form: {
                no_of_emp: null,
                month: '',
                year: '',
                salary_date: new Date().toISOString().split('T')[0],
                remarks: '',
                file_name: null
            },
            loading: false,
            previewUrl: null,
            fileName: '',
            fileType: '',
            currentMonth: new Date().getMonth() + 1,
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
            years: []
        }
    },
    computed: {
        isImage() {
            return this.fileType.startsWith('image/');
        },
        isPdf() {
            return this.fileType === 'application/pdf';
        }
    },
    mounted() {
        this.generateYears();
    },
    methods: {
        generateYears() {
            const currentYear = new Date().getFullYear();
             const startYear = 2022; // system started from 2022
            for (let i = currentYear; i >= startYear; i--) {
                this.years.push(i);
            }
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) {
                this.previewUrl = null;
                this.form.file_name = null;
                return;
            }

            this.form.file_name = file;
            this.fileName = file.name;
            this.fileType = file.type;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        async submitForm() {
            if (!this.form.month) {
                toast.error('Please select salary month');
                return;
            }
            if (!this.form.year) {
                toast.error('Please select salary year');
                return;
            }
            if (!this.form.file_name) {
                toast.error('Please upload a file');
                return;
            }

            this.loading = true;

            const formData = new FormData();
            formData.append('no_of_emp', this.form.no_of_emp || '');
            formData.append('month', this.form.month);
            formData.append('year', this.form.year);
            formData.append('salary_date', this.form.salary_date);
            formData.append('remarks', this.form.remarks);
            formData.append('file_name', this.form.file_name);

            try {
                const response = await axios.post(PayslipUploadAPI, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    toast.success('Salary sheet uploaded successfully');
                    this.resetForm();
                } else {
                    toast.error(response.data.message || 'Failed to upload salary sheet');
                }
            } catch (error) {
                console.error('Error:', error);
                toast.error(error.response?.data?.message || 'An error occurred');
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
                no_of_emp: null,
                month: '',
                year: '',
                salary_date: new Date().toISOString().split('T')[0],
                remarks: '',
                file_name: null
            };
            this.previewUrl = null;
            this.fileName = '';
            this.$refs.fileInput.value = '';
        }
    }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
