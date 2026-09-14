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

                    <!-- Activity Type (Change Event Added Here) -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Activity Type <span
                                class="req_star text-danger">*</span></label>
                        <div class="col-sm-7">
                            <select class="form-select" v-model="form.activity_type" @change="onActivityTypeChange"
                                required>
                                <option value="1">Job_Activity</option>
                                <option value="2">Salary_Activity</option>
                                <!-- <option value="20">Illegal_Activity</option> -->
                            </select>
                        </div>
                    </div>

                    <!-- Activity Date -->
                    <div class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Date <span class="req_star text-danger">*</span></label>
                        <div class="col-sm-7">
                            <input type="date" v-model="form.activity_date" class="form-control" required>
                        </div>
                    </div>

                    <!-- Dynamic Status Dropdown Based on activity_type -->
                    <!-- 1. Job Status (Job_Activity or Illegal_Activity selected) -->
                    <div v-if="form.activity_type === '1' || form.activity_type === '20'"
                        class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Job Status <span
                                class="req_star text-danger">*</span></label>
                        <div class="col-sm-7">
                            <select class="form-control" v-model="form.job_status" required>
                                <option value="">Select Job Status</option>
                                <option value="1">Active</option>
                                <option value="2">InActive</option>
                                <option value="3">Final_Exit</option>
                                <option value="4">Release</option>
                                <option value="5">Vacation</option>
                                <option value="6">Run_Away</option>
                                <option value="7">VISA_Cancel</option>
                                <option value="8">Final_Exit_and_Return_Back</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Salary Status (Salary_Activity selected) -->
                    <div v-if="form.activity_type === '10'" class="form-group row custom_form_group mb-3">
                        <label class="col-sm-3 control-label">Salary Status <span
                                class="req_star text-danger">*</span></label>
                        <div class="col-sm-7">
                            <select class="form-control" v-model="form.salary_status" required>
                                <option value="">Select Salary Status</option>
                                <option value="0">Undefined</option>
                                <option value="1">Active</option>
                                <option value="20">Salary Hold</option>
                            </select>
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
                            {{ is_saving ? 'Saving...' : 'SAVE' }}
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

import EmployeeSearchComponent from '../../../../../../../Modules/HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';

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
                activity_date: new Date().toISOString().slice(0, 10),
                job_status: '',
                salary_status: '',
                activity_remarks: '',
                activity_description: ''
            }
        }
    },

    methods: {
        handleSearchingResult(employee) {
            if (!employee) return;
            this.searched_employee = employee;
            this.form.employee_id = employee.employee_id;
            this.form.emp_auto_id = employee.emp_auto_id;
        },

        // Activity Type ড্রপডাউন চেঞ্জ হলে অটোমেটিক ফিল্ড এবং ট্যাব সুইচ করবে
        onActivityTypeChange() {
            if (this.form.activity_type === '2') {
                this.active_tab = 'salary';
                this.form.job_status = ''; // reset job status when switched to salary
            } else {
                this.active_tab = 'job';
                this.form.salary_status = ''; // reset salary status when switched to job
            }
        },



        resetForm() {
            this.form.job_status = '';
            this.form.salary_status = '';
            this.form.activity_remarks = '';
            this.form.activity_description = '';
        },

        async submitForm() {
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