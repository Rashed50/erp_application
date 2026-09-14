<template>
    <div>
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

        <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">


            <div>
                <form @submit.prevent="submitIqamaRenewal" class="form-horizontal">
                    <div class="card">
                        <div class="card-body card_form">
                            <div
                                class="form-group row custom_form_group{{ $errors->has('emp_auto_id') ? ' has-error' : '' }}">
                                <div class="col-sm-5"></div>
                                <div class="col-sm-5">
                                    <span class="req_star">Employee ID <span id="show_employee_id"
                                            class="req_star">Required</span>
                                    </span>
                                    <input type="hidden" class="form-control" id="emp_auto_id"
                                        v-model="form.emp_auto_id" value="" required>

                                </div>
                                <div class="col-sm-2"></div>

                            </div>
                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label  text-right">Jawazat Fee:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="Jawazat Fee" class="form-control"
                                            id="jawazat_fee" v-model="form.jawazat_fee" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('jawazat_fee')" value="0" required>

                                    </div>
                                </div>
                                <div class="form-group row col-md-6">

                                    <label class="col-sm-4 control-label text-right">Maktab Al Amal Fee:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="Maktab Al Amal Fee" class="form-control"
                                            v-model="form.maktab_alamal_fee" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('maktab_alamal_fee')" value="0" required>

                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">VISA Amount:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="BD Amount" class="form-control"
                                            v-model="form.bd_amount" value="0" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('bd_amount')" required>

                                    </div>
                                </div>
                                <div class="form-group row col-md-6">

                                    <label class="col-sm-4 control-label text-right">Medical Inssurance:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="Maktab Al Amal Fee" class="form-control"
                                            v-model="form.medical_insurance" value="0" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('medical_insurance')" required>

                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Jawazat Fee Penalty:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="" class="form-control"
                                            v-model="form.jawazat_penalty" value="0" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('jawazat_penalty')" required>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">

                                    <label class="col-sm-4 control-label text-right text-right">Others:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="" class="form-control" id="others_fee"
                                            v-model="form.others_fee" value="0" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('others_fee')" required>

                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Emp Tranfer Fee<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="number" placeholder="" class="form-control" id="transfer_fee"
                                            v-model="form.transfer_fee" value="0" @input="calculateTotalAmount()"
                                            @blur="handleNumericInput('transfer_fee')" required>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="col-sm-2  text-right"> <span class="req_star"
                                        style="font-size: 18px; font-weight:bold; color:red"><b> Total Expense</b>
                                    </span> </div>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control typeahead" placeholder=""
                                        v-model="form.total_amount" value="0" required readonly
                                        style="font-size: 18px; font-weight:bold; color:red"
                                        @blur="handleNumericInput('total_amount')">
                                </div>
                                <div class="col-sm-2"> </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">
                                        Renewal Duration:<span class="req_star">*</span>
                                    </label>
                                    <div class="col-sm-6">
                                        <select class="form-select" v-model="form.duration" id="duration" required>
                                            <option value="" disabled>Select Duration</option>

                                            <!-- Vue.js v-for directive -->
                                            <option v-for="i in 72" :key="i" :value="i">
                                                {{ i }} Month{{ i > 1 ? 's' : '' }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 text-right">
                                    <label class="col-sm-4 control-label">Renewal Date:</label>
                                    <div class="col-sm-6">
                                        <input type="date" v-model="form.renewal_date" class="form-control"
                                            max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Payment Number:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="Payment Number" class="form-control"
                                            id="payment_number" v-model="form.payment_number">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Payment Date:</label>
                                    <div class="col-sm-6">
                                        <input type="date" v-model="form.payment_date" class="form-control"
                                            max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Renewal Status -->
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">
                                        Renewal Status:<span class="req_star">*</span>
                                    </label>
                                    <div class="col-sm-6">
                                        <select class="form-select" v-model="form.renewal_status">
                                            <option value="" disabled>-- Select Status --</option>
                                            <option value="1">Initial Step</option>
                                            <option value="2">Payment Initialize</option>
                                            <option value="3">Payment Completed</option>
                                            <option value="4">Renewal Pending</option>
                                            <option value="5">Renewal Completed</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Expense Paid By -->
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">
                                        Expense Paid By:<span class="req_star">*</span>
                                    </label>
                                    <div class="col-sm-6">
                                        <select class="form-select" v-model="form.expense_paid_by">
                                            <option value="" disabled>Select Expense Paid By</option>
                                            <option value="1">Self</option>
                                            <option value="2">Company</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Reference Employee:
                                    </label>
                                    <div class="col-sm-7">
                                        <input type="number" class="form-control typeahead"
                                            placeholder="Input Employee ID" v-model="form.reference_emp_id"
                                            onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()">
                                        <div id="showEmpId"></div>

                                    </div>
                                </div>
                                <div class="form-group row col-md-6">

                                    <label class="col-sm-4 control-label text-right">Iqama Expire:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="date" v-model="form.iqama_expire_date" class="form-control"
                                            id="iqama_expire_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                            required>
                                    </div>


                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Purpose:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <select class="form-select" v-model="form.payment_purpose_id"
                                            id="payment_purpose_id" required>
                                            <option value="">Select Purpose</option>
                                            <option value="1"> Iqama Renewal</option>
                                            <option value="2"> Medical Insurance</option>
                                            <option value="3"> Exit-Re-Entry</option>
                                            <option value="4"> Family Iqama</option>
                                            <option value="5"> Family Medical Insurance</option>
                                            <!--<option value="6">Traffic Violation</option>-->

                                        </select>

                                    </div>

                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-sm-4 control-label text-right">Remarks:</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="Remarks Here" class="form-control" id="remarks"
                                            v-model="form.remarks">
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

import { NewIqamaRenewalSubmitAPI } from '../../routes.js';
import EmployeeSearchComponent from '../../../../../../../Modules/HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';

export default {
    components: { EmployeeSearchComponent },

    data() {
        return {
            searched_employee: null,
            is_saving: false,

            form: {
                emp_auto_id: '1111',
                jawazat_fee: 0,
                maktab_alamal_fee: 0,
                bd_amount: 0,
                medical_insurance: 0,
                jawazat_penalty: 0,
                others_fee: 0,
                transfer_fee: 0,
                total_amount: '',
                duration: '',
                renewal_date: new Date().toISOString().split('T')[0],
                payment_number: '',
                payment_date: new Date().toISOString().split('T')[0],
                expense_paid_by: 2,
                reference_emp_id: '',
                iqama_expire_date: '',
                payment_purpose_id: '',
                remarks: '',
                renewal_status: '',
            }

        }
    },

    methods: {
        handleSearchingResult(employee) {
            if (employee == null)
                return;
            this.searched_employee = employee;
            this.form.emp_auto_id = employee.emp_auto_id;
            this.form.expense_paid_by = employee.hourly_employee;


        },


        async submitIqamaRenewal() {

            try {
                if (!this.form.emp_auto_id) {
                    toast.error("Please Search an Employee Try to Again")
                }

                this.is_saving = true;
                const total = parseFloat(this.form.total_amount) || 0;
                if (total <= 0) {
                    toast.error("Total Expense must be greater than 0!");
                    return;
                }
                const response = await axios.post(NewIqamaRenewalSubmitAPI, this.form)
                //  console.log(response.data);


                if (response.data.status === 200) {
                    toast.success(response.data.message || ' Iqama Renewal Add sucessfully')
                } else {
                    toast.error('Failed' + response.data.message);
                }
            } catch (error) {
                console.error(error);
                toast.error('Error occurred , Please Try Again: ' + error.message);
            } finally {
                this.is_saving = false;
            }




        },

        handleNumericInput(field) {
            if (this.form[field] === '' || this.form[field] === null || isNaN(this.form[field])) {
                this.form[field] = 0;
            }
            this.calculateTotalAmount();
        },

        calculateTotalAmount() {
            const value1 = parseFloat(this.form.jawazat_fee) || 0;
            const value2 = parseFloat(this.form.maktab_alamal_fee) || 0;
            const value3 = parseFloat(this.form.bd_amount) || 0;
            const value4 = parseFloat(this.form.medical_insurance) || 0;
            const value5 = parseFloat(this.form.others_fee) || 0;
            const jawazat_penalty = parseFloat(this.form.jawazat_penalty) || 0;
            const transfer_fee = parseFloat(this.form.transfer_fee) || 0;

            this.form.total_amount = value1 + value2 + value3 + value4 + value5 + jawazat_penalty + transfer_fee;


        }


    }





}
</script>