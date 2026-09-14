<!-- resources/js/components/daily-expense/DailyExpenseForm.vue -->
<template>
    <div>
         <!-- <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent> -->
        <div class="row" >
             <!-- v-if="searched_employee" -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 style="color: green; text-align:center">Daily Expense Accounting Form</h5>
                        <hr>
                        <form class="form-horizontal" @submit.prevent="submitForm" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Left Column - All Form Fields -->
                                <div class="col-md-6">
                                    <!-- Employee ID -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Employee ID</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" v-model="form.employee_id" autofocus @keyup="checkEmployeeIDIsValid">
                                        </div>
                                        <div class="col-sm-4">
                                             <p style="color:blue" id="emp_search_result" >{{ this.search_result_lbl }}</p>
                                        </div>

                                    </div>

                                    <!-- Transaction Type -->
                                    <!-- <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Tran.Type <span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-select" v-model="form.credit_account_id" required>
                                                <option value="">Please Select One</option>
                                                <option value="205">Cash Flow Transaction</option>
                                                <option value="200">Other Transaction</option>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="form-group row custom_form_group" :class="{ 'has-error': errors.cr_accounts }">
                                        <label class="col-sm-4 control-label">Payment Source <span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-select" v-model="form.credit_account_id" required>
                                                <option value="">Please Select One</option>
                                                <option v-for="cr_acct in cr_accounts" :key="cr_acct.chart_of_acct_id" :value="cr_acct.chart_of_acct_id">
                                                    {{ cr_acct.chart_of_acct_name }}
                                                </option>
                                            </select>
                                            <span v-if="errors.cr_accounts" class="invalid-feedback d-block">
                                                <strong>{{ errors.cr_accounts[0] }}</strong>
                                            </span>
                                        </div>
                                    </div>



                                    <!-- <div class="row mt-2">
                            <div class="col-md-3">
                                 <label class="form-label text-end d-block">Payment Source <span class="req_star">*</span> </label>
                                  this is credit account
                            </div>
                            <div class="col-md-9">
                                <Multiselect required v-model="account_credit_id" :options="filteredCreditAccounts"
                                    placeholder="Select item" :searchable="true" />
                            </div>
                        </div> -->

                                    <!-- Date -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Date<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="date" v-model="form.expense_date" class="form-control" required>

                                        </div>
                                    </div>

                                    <!-- Expense Type -->
                                    <div class="form-group row custom_form_group" :class="{ 'has-error': errors.expense_type }">
                                        <label class="col-sm-4 control-label">Expense Type <span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-select" v-model="form.expense_type" required>
                                                <option value="">Please Select One</option>
                                                <option v-for="type in expenseTypes" :key="type.cost_type_id" :value="type.cost_type_id">
                                                    {{ type.cost_type_name }}
                                                </option>
                                            </select>
                                            <span v-if="errors.expense_type" class="invalid-feedback d-block">
                                                <strong>{{ errors.expense_type[0] }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Project -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Project</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" v-model="form.project_id">
                                                <option value="">Select Project Name</option>
                                                <option v-for="project in projects" :key="project.proj_id" :value="project.proj_id">
                                                    {{ project.proj_name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Payment Method</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" v-model="form.expense_method" required>
                                                <option value="">Select One</option>
                                                <option value="CASH">CASH</option>
                                                <option value="BANK">BANK</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Amount -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" v-model="form.amount" placeholder="Enter Amount Here..." min="0" required>
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Remarks</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" rows="3" v-model="form.remarks" style="resize:none"></textarea>
                                        </div>
                                    </div>
                                    <!-- Submit Button - left side of form -->
                                    <div class="row mt-3">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary waves-effect" :disabled="loading" style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">
                                                <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                                                <i v-else class="fa fa-save"></i>
                                                {{ loading ? 'Saving...' : 'Save' }}
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <strong>File Attachments</strong>
                                        </div>
                                        <div class="card-body">
                                            <!-- File Upload Area -->
                                            <div class="form-group">
                                                <div class="file-upload-area" @dragover.prevent @drop.prevent="handleDrop" @click="triggerFileUpload">
                                                    <input type="file" ref="fileInput" accept="image/*,.pdf" @change="handleFileUpload" multiple style="display: none;">
                                                    <div class="upload-placeholder text-center">
                                                        <i class="fa fa-cloud-upload" style="font-size: 48px; color: #ccc;"></i>
                                                        <p>Drag & drop files here or click to browse</p>
                                                        <p class="text-muted small">Supported: Images, PDF (Max 10MB)</p>
                                                        <button type="button" class="btn btn-sm btn-primary" @click.stop="triggerFileUpload">
                                                            <i class="fa fa-folder-open"></i> Browse Files
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Selected Files Info -->
                                            <div v-if="files.length > 0" class="mt-3">
                                                <strong>Selected Files ({{ files.length }}):</strong>
                                                <ul class="list-group mt-2">
                                                    <li v-for="(file, index) in files" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <i :class="getFileIcon(file)" class="mr-2"></i>
                                                            {{ file.name }}
                                                            <small class="text-muted">({{ formatFileSize(file.size) }})</small>
                                                        </span>
                                                        <button type="button" class="btn btn-sm btn-danger" @click="removeFile(index)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- File Preview Gallery -->
                                            <div v-if="previewUrls.length <=2" class="mt-4">
                                                <strong>Preview:</strong>
                                                <div class="row mt-2">
                                                    <div v-for="(url, index) in previewUrls" :key="index" class="col-md-6 mb-2">
                                                        <div class="preview-item">
                                                            <img v-if="isImage(files[index])" :src="url" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;">
                                                            <div v-else class="pdf-preview text-center border rounded p-3">
                                                                <i class="fa fa-file-pdf-o" style="font-size: 48px; color: #f00;"></i>
                                                                <p class="small mt-2">{{ files[index].name.substring(0, 20) }}...</p>
                                                                <a :href="url" target="_blank" class="btn btn-sm btn-info mt-1">
                                                                    <i class="fa fa-eye"></i> View PDF
                                                                </a>
                                                            </div>
                                                            <button type="button" class="btn btn-danger btn-sm mt-1 w-100" @click="removeFile(index)">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import {DailyExpenseStoreAPII} from '../../routes.js';
import EmployeeSearchComponent from '../../../../../../HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';
import { ref } from 'vue';
export const EmployeeSearchAPI ="/admin/employee/search/with/multitype-parameter/employee-details";


export default {
    components: { EmployeeSearchComponent },
    // name: 'DailyExpenseComponent',
    props: {
        expenseTypes: {
            type: Array,
            required: true
        },
        projects: {
            type: [Array, Object],  // Accept both
            default: () => []
        },
        cr_accounts: {
            type: [Array, Object],  // Accept both
            default: () => []
        }
    },
    data() {
        return {
            form: {
                credit_account_id: '',
                expense_date: new Date().toISOString().split('T')[0],
                expense_type: '',
                emp_auto_id: '',
                project_id: '',
                expense_method: '',
                amount: 0,
                remarks: '',
                dr_vou_auto_id: ''
            },
            searched_employee:null,
            files: [],
            previewUrls: [],
            errors: {},
            loading: false,
            search_result_lbl:'',
        }
    },
    mounted() {

        console.log('called new form');
    },
    watch:{

    },
    computed: {
        fileNames() {
            return this.files.length > 0 ? this.files.map(f => f.name).join(', ') : '';
        }
    },
    methods: {

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {

                const formData = new FormData();
                formData.append('credit_account_id', this.form.credit_account_id);
                formData.append('expense_date', this.form.expense_date);
                formData.append('expense_type_id', this.form.expense_type);
                formData.append('emp_auto_id', this.form.emp_auto_id);
                formData.append('project_id', this.form.project_id);
                formData.append('expense_method', this.form.expense_method);
                formData.append('amount', this.form.amount);
                formData.append('remarks', this.form.remarks);
                formData.append('dr_vou_auto_id', this.form.dr_vou_auto_id);

                this.files.forEach(file => {
                    formData.append('dr_invoice_path[]', file);
                });


                const response = await axios.post(DailyExpenseStoreAPII, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (response.data.status === 201) {
                    toast.success('Expense saved successfully!');
                    this.resetForm();
                 //   this.$emit('saved', response.data);
                } else {
                    toast.error(response.data.message || 'Failed to save expense');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    toast.error('Please check the form for errors');
                } else {
                    toast.error('An error occurred. Please try again.');
                }
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        resetForm() {
            this.searched_employee = null;
            this.form = {
                credit_account_id: '',
                expense_date: new Date().toISOString().split('T')[0],
                expense_type: '',
                employee_id: '',
                emp_auto_id:'',
                project_id: '',
                expense_method: '',
                amount: '',
                remarks: '',
                dr_vou_auto_id: ''
            };
            this.files = [];
            this.previewUrls = [];
            this.errors = {};
        },

        handleSearchingResult(employee) {
                if (employee == null) return;

                this.searched_employee = employee;
                this.form.emp_auto_id = employee.emp_auto_id;
                this.form.employee_id = employee.employee_id; // Assuming employee_id is the field you want to display
                console.log('Data Received Successfully', employee);

        },
        async checkEmployeeIDIsValid() {


            // 1. Validation Checks
            if (this.form.employee_id == '' || this.form.employee_id.length <= 2  || this.form.employee_id.length > 5) {
                this.form.emp_auto_id = '';
                this.search_result_lbl = '';
                return;
            }
            const requestData = {
                search_by: "employee_id",
                employee_searching_value: this.form.employee_id
            };

            try {
                const response = await axios.post(EmployeeSearchAPI, requestData);

                // 2. Handle API-level "Not Found" (where status is 200 but success is false)
                if (response.data.status != 200) {
                    this.form.emp_auto_id = '';
                    this.search_result_lbl = 'Employee Not Found';
                    return
                }
              //  debugger;

                const data = response.data.findEmployee[0];
                //console.log(data);
                this.form.emp_auto_id = data.emp_auto_id;
                this.search_result_lbl = data.employee_name+', '+data.akama_no+", "+data.catg_name;
               // toast.success(data.employee_name);

            } catch (error) {
                this.search_result_lbl = 'Operation Failed,Please Try Again';
                this.form.emp_auto_id = '';
            }
        },
        triggerFileUpload() {
            this.$refs.fileInput.click();
        },

        handleDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.addFiles(files);
        },

        handleFileUpload(event) {
            const files = Array.from(event.target.files);
            this.addFiles(files);
            // Clear the input value so same file can be uploaded again
            event.target.value = '';
        },

        addFiles(newFiles) {
            // Filter valid files
            const validFiles = newFiles.filter(file => {
                const isValid = file.type === 'application/pdf' || file.type.startsWith('image/');
                const isSizeValid = file.size <= 10 * 1024 * 1024; // 10MB
                if (!isValid) toast.error(`${file.name}: Invalid file type`);
                if (!isSizeValid) toast.error(`${file.name}: File too large (max 10MB)`);
                return isValid && isSizeValid;
            });

            this.files = [...this.files, ...validFiles];

            // Create preview URLs
            validFiles.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewUrls.push(e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.previewUrls.push(null);
                }
            });
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.previewUrls.splice(index, 1);
        },

        isImage(file) {
            return file && file.type.startsWith('image/');
        },

        getFileIcon(file) {
            if (file.type.startsWith('image/')) return 'fa fa-file-image-o';
            if (file.type === 'application/pdf') return 'fa fa-file-pdf-o';
            return 'fa fa-file-o';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

    }
}
</script>

<style scoped>
.custom_form_group {
    margin-bottom: 1rem;
}
.req_star {
    color: red;
}
.has-error .form-control,
.has-error .form-select {
    border-color: #dc3545;
}
.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
}
.img-thumbnail {
    object-fit: cover;
    width: 100%;
    height: 100px;
}
.pdf-preview {
    text-align: center;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
}
</style>
