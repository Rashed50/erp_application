<template>
    <div>
        <!-- Employee Search Component -->
        <EmployeeSearchComponent @searching_result="handleSearchingResult" />

        <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <!-- Employee ID (Readonly) -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="control-label col-md-3">Employee ID:</label>
                        <div class="col-md-7">
                            <input readonly type="text" class="form-control" placeholder="Employee ID"
                                v-model="form.employee_id" id="emp_id">
                        </div>
                    </div>

                    <!-- Activity Type -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">
                            Activity Type <span class="req_star text-danger">*</span>
                        </label>
                        <div class="col-sm-7">
                            <select class="form-select" v-model="form.activity_type" @change="onActivityTypeChange"
                                required>
                                <option value="1">Job Activity</option>
                                <option value="2">Salary Activity</option>
                            </select>

                            <span class="invalid-feedback d-block" v-if="errors.activity_type">
                                {{ errors.activity_type_msg }}
                            </span>
                        </div>
                    </div>

                    <!-- Activity Date -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Date <span class="req_star text-danger">*</span></label>
                        <div class="col-sm-7">
                            <input type="date" v-model="form.activity_date" class="form-control" required>
                        </div>
                    </div>

                    <!-- 1. Job Status (Job_Activity or Illegal_Activity) -->
                    <div v-if="form.activity_type === '1' || form.activity_type === '20'"
                        class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">
                            Job Status <span class="req_star text-danger">*</span>
                        </label>
                        <div class="col-sm-7">
                            <select class="form-select" v-model="form.job_status">
                                <option value="">Select Job Status</option>
                                <option value="1">Active</option>
                                <option value="2">InActive</option>
                                <option value="3">Final Exit</option>
                                <option value="4">Release</option>
                                <option value="5">Vacation</option>
                                <option value="6">Run Away</option>
                                <option value="7">VISA Cancel</option>
                                <option value="8">Final Exit & Return Back</option>
                            </select>
                            <span class="invalid-feedback d-block" v-if="errors.job_status">
                                {{ errors.job_status_msg }}
                            </span>
                        </div>
                    </div>

                    <!-- 2. Salary Status (Salary_Activity) -->
                    <div v-if="form.activity_type === '2'" class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">
                            Salary Status <span class="req_star text-danger">*</span>
                        </label>
                        <div class="col-sm-7">
                            <select class="form-select" v-model="form.salary_status">
                                <option value="">Select Salary Status</option>
                                <option value="0">Undefined</option>
                                <option value="1">Active</option>
                                <option value="20">Salary Hold</option>
                            </select>

                            <span class="invalid-feedback d-block" v-if="errors.salary_status">
                                {{ errors.salary_status_msg }}
                            </span>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Remarks:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" placeholder="Remarks"
                                v-model="form.activity_remarks">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Description:</label>
                        <div class="col-sm-7">
                            <textarea v-model="form.activity_description" class="form-control"
                                placeholder="Activity description" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="card-footer card_footer_button text-center bg-light mt-4">
                        <button type="submit" class="btn btn-primary waves-effect" :disabled="is_saving">
                            <i v-if="is_saving" class="fas fa-spinner fa-spin me-2"></i>
                            {{ is_saving ? 'Updating...' : 'Update' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import {
    NewActivityInsertAPI,
    SalaryActivityUpdateAPI
} from '../../routes.js';

import EmployeeSearchComponent from './SearchComponent.vue';

export default {
    name: 'EmployeeActivityManager',
    components: { EmployeeSearchComponent },

    data() {
        return {
            searched_employee: null,
            is_saving: false,

            form: {
                emp_auto_id: '',
                employee_id: '',
                activity_type: '1',
                activity_date: new Date().toLocaleDateString('en-CA'),
                job_status: '',
                salary_status: '',
                activity_remarks: '',
                activity_description: ''
            },
            errors: {
                job_status: false,
                job_status_msg: '',
                salary_status: false,
                salary_status_msg: '',
                activity_type: false,
                activity_type_msg: '',
            },
        }
    },

    methods: {
        handleSearchingResult(employee) {
            if (!employee) return;
            this.searched_employee = employee;
            this.form.employee_id = employee.employee_id;
            this.form.emp_auto_id = employee.emp_auto_id;
        },

        onActivityTypeChange() {
            this.clearErrors();
            if (this.form.activity_type === '2') {
                this.form.job_status = '';
            } else {
                this.form.salary_status = '';
            }
        },

        clearErrors() {
            this.errors = {
                job_status: false,
                job_status_msg: '',
                salary_status: false,
                salary_status_msg: '',
                activity_type: false,
                activity_type_msg: '',
            };
        },

        validateForm() {
            let isValid = true;
            this.clearErrors();

            if (!this.form.activity_type) {
                this.errors.activity_type = true;
                this.errors.activity_type_msg = "Please select an activity type";
                isValid = false;
            }

            // Job activity type (1 or 20) check
            if ((this.form.activity_type === '1' || this.form.activity_type === '20') && !this.form.job_status) {
                this.errors.job_status = true;
                this.errors.job_status_msg = "Please select Job Status";
                isValid = false;
            }

            // Salary activity type (2) check
            if (this.form.activity_type === '2' && !this.form.salary_status) {
                this.errors.salary_status = true;
                this.errors.salary_status_msg = "Please select Salary Status";
                isValid = false;
            }

            return isValid;
        },

        resetForm() {
            this.form.job_status = '';
            this.form.salary_status = '';
            this.form.activity_remarks = '';
            this.form.activity_description = '';
            this.clearErrors();
        },

        async submitForm() {
            if (this.is_saving) return;

            if (!this.validateForm()) {
                toast.error('Please fix the validation errors before updating.');
                return;
            }

            this.is_saving = true;

            try {
                let response;

                if (this.form.activity_type === '2') {
                    const formData = new FormData();
                    formData.append('emp_auto_id', this.form.emp_auto_id || '');
                    formData.append('salary_status', this.form.salary_status || '');
                    formData.append('activity_date', this.form.activity_date || '');
                    formData.append('activity_description', this.form.activity_description || '');
                    formData.append('activity_remarks', this.form.activity_remarks || '');
                    formData.append('activity_type', this.form.activity_type || '');

                    response = await axios.post(SalaryActivityUpdateAPI, formData);

                } else {
                    const formData = new FormData();
                    formData.append('emp_auto_id', this.form.emp_auto_id || '');
                    formData.append('job_status', this.form.job_status || '');
                    formData.append('activity_date', this.form.activity_date || '');
                    formData.append('activity_description', this.form.activity_description || '');
                    formData.append('activity_remarks', this.form.activity_remarks || '');
                    formData.append('activity_type', this.form.activity_type || '');

                    response = await axios.post(NewActivityInsertAPI, formData);
                }

                if (response && response.data.status === 200) {
                    toast.success(response.data.message || 'Updated successfully!');
                    this.resetForm();
                } else {
                    toast.error('Update failed: ' + (response.data.error || response.data.message || 'Unknown error'));
                }

            } catch (error) {
                console.error(error);
                toast.error('Update failed: ' + (error.response?.data?.message || error.message));
            } finally {
                this.is_saving = false;
            }
        }
    }
}
</script>