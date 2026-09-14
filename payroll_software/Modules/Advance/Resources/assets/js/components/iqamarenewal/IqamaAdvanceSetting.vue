<template>
    <div>
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

        <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

            <!-- form -->
            <form action="">

                <div class="row">
                    <div class="form-group row custom_form_group" style="margin:0px;padding:0px; color:red">
                        <label class="col-sm-3 control-label" id="emp_id"> </label>
                        <label class="col-sm-3 control-label" id="emp_name"> </label>
                        <label class="col-sm-3 control-label" id="emp_salary"> </label>
                    </div>

                    <div class="form-group row row custom_form_group" style="margin:0px;padding:0px;color:red">
                        <label class="col-sm-3 control-label" id="iqama_no"> </label>
                        <label class="col-sm-3 control-label" id="passport_no"> </label>
                    </div>


                    <div class="col-md-6">
                        <div class="form-wrap">
                            <input type="hidden" name="id" id="adv_pay_id" value="">
                            <input type="hidden" name="operation_type" id="operation_type" value="1">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Total Iqama:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="totalIqama" class="form-control" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Installes Amount:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="installIqama" class="form-control" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Total Paid:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="payIqama" class="form-control" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Next Pay:</label>
                                <div class="col-sm-7">
                                    <input type="number" id="nextPayIqama" name="nextPayIqama" class="form-control"
                                        value="">
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-6">
                        <div class="form-wrap">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Total Others:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="totalOthers" class="form-control" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Installes Amount:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="installOthers" class="form-control" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Total Paid:</label>
                                <div class="col-sm-7">
                                    <input type="text" id="payOthers" class="form-control" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Next Pay:</label>
                                <div class="col-sm-7">
                                    <input type="number" id="nextPayOthers" name="nextPayOthers" class="form-control"
                                        value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm waves-effect"
                        style="display:inline-block; margin: 0 auto;">SAVE</button>
                </div>

            </form>

            <div class="d-none card_footer_button text-center" style="padding-top: 20px;">

                <form action="{{ route('update.advance-installAmount') }}" method="post">
                    <div class="row">
                        <!-- <div class="form-group row custom_form_group" style="margin-top:10px;color:red">-->
                        <!--    <label class="col-sm-2 control-label" id ="emp_id">   </label>-->
                        <!--    <label class="col-sm-3 control-label" id ="emp_name">   </label>-->
                        <!--    <label class="col-sm-2 control-label" id ="emp_iqama">   </label> -->
                        <!--    <label class="col-sm-2 control-label" id ="emp_salary">   </label>               -->
                        <!--</div>-->


                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

import { SingleEmployeeAdvanceSettingDataFetchAPI } from '../../routes.js';
import EmployeeSearchComponent from '../../../../../../../Modules/HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';

export default {
    components: { EmployeeSearchComponent },

    data() {
        return {
            searched_employee: null,
            is_saving: false,

        }
    },

    methods: {
        handleSearchingResult(employee) {
            if (employee == null)
                return;
            this.searched_employee = employee;
            this.fetchEmployeeAdvanceCurrentData(employee.employee_id);
        },

        async fetchEmployeeAdvanceCurrentData(employee_id) {

            const requestData = {
                'emp_id': employee_id,
            }

            console.log(requestData);
            const response = await axios.get(SingleEmployeeAdvanceSettingDataFetchAPI,{
                'emp_id': employee_id,
            })
            console.log(response.data);
        }
    }



}
</script>