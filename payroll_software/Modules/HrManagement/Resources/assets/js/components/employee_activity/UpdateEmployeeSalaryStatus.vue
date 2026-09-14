<template>
    <div>
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

        <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">


            <div>
                <h5>Update Employee Salary Status</h5>
                <form @submit.prevent="submitForm">

                    <br>

                    <div class="form-group row custom_form_group mb-3">
                        <label class="control-label col-md-3">Employee ID:</label>
                        <div class="col-md-7">
                            <input readonly type="text" class="form-control" placeholder="Employee ID"
                                v-model="form.emp_id" id="emp_id">
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label "> Activity Type </label>
                        <div class="col-sm-7 ">
                            <select class="form-select" v-model="form.activity_type">
                                <option value="10">Salary_Activity</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Date<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="date" v-model="form.activity_date" value="{{ date('Y-m-d') }}"
                                class="form-control">

                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Salary Status<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <select class="form-control" v-model="form.salary_status" required>
                                <option value="">Select Salary Status</option>
                                <option value="0"> Undefined </option>
                                <option value="1"> Active </option>
                                <option value="20"> Salary Hold </option>



                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Remarks:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" placeholder="Remarks "
                                v-model="form.activity_remarks">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Description:</label>
                        <div class="col-sm-7">
                            <textarea v-model="form.activity_description" class="form-control"
                                placeholder="Activity description"> </textarea>
                        </div>
                    </div>

                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i v-if="is_saving" class="fas fa-spinner fa-spin me-2"></i>
                            {{ is_saving ? 'Saving...' : 'SAVE' }}</button>
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
    SalaryActivityUpdateAPI

} from '../../routes.js';

import EmployeeSearchComponent from '../../../../../../../Modules/HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';

export default {
    components: { EmployeeSearchComponent },

    data() {
        return {
            searched_employee: null,
            is_saving: false,

            form: {
                emp_id: '',
                emp_auto_id: '',
                salary_status: '',
                activity_remarks: '',
                activity_date: '',
                activity_description: '',
                activity_type: '10',

            }



        }
    },

    methods: {
        handleSearchingResult(employee) {
            if (employee == null)
                return;
            this.searched_employee = employee;
            this.form.emp_id = employee.employee_id;
            this.form.emp_auto_id = employee.emp_auto_id;



        },

        async submitForm() {
            try {
                // if (!this.validateForm()) {
                //     return;
                // }

                this.is_saving = true;
                const formData = new FormData();
                formData.append('emp_auto_id', this.form.emp_auto_id || '');
                formData.append('salary_status', this.form.job_status || '');
                formData.append('activity_date', this.form.activity_date || '');
                formData.append('activity_description', this.form.activity_description || '');
                formData.append('activity_remarks', this.form.activity_remarks || '');
                formData.append('activity_type', this.form.activity_type || '');

                console.log(formData);
                const response = await axios.post(SalaryActivityUpdateAPI, formData)

                if (response.data.status === 200) {
                    toast.success(response.data.message || 'Updated successfully!');
                } else {
                    toast.error('Update failed: ' + response.data.message);
                }

            } catch (error) {
                console.error(error);
                toast.error('Update failed: ' + error.message);
            }
        }
    }





}
</script>