<template>
     <EmployeeSearchComponent ref="employeeSearchComponent" @searching_result="handleSearchingResult" ></EmployeeSearchComponent>
<div class="card"  v-if="searched_employee">
    <div class="card-header">
        <h4>Employee Salary Details</h4>
    </div>

    <div class="card-body">
        <form @submit.prevent="submitForm">

            <div class="row g-3">

                <!-- Employee Type -->
                <div class="col-md-4">
                    <label class="form-label">Employee Type *</label>
                    <select v-model="form.emp_type_id" class="form-select" required>
                        <option value="">Select Employee Type</option>
                        <option value="1">Direct Employee</option>
                        <option value="2">Indirect Employee</option>
                        <!-- <option v-for="type in empTypes" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option> -->
                    </select>
                </div>
                <!-- Checkboxes -->
                <div class="col-md-4">
                    <label class="form-label d-block">Options</label>
                    <input type="checkbox" v-model="form.hourly_employee" @change="toggleHourlyFields" /> Hourly Employee
                    <br>
                    <input type="checkbox" v-model="form.staff_employee" @change="toggleHourlyFields" /> Staff Employee

                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">Payment Method</label>
                    <input type="radio" value="Cash" v-model="form.payment_method">
                    CASH
                    <br>
                    <input type="radio" value="Bank" v-model="form.payment_method">
                    Bank
                </div>

                <!-- Basic Hours -->
                <div class="col-md-4">
                    <label class="form-label">Basic Hours</label>
                    <input type="number" v-model="form.basic_hours"  @input="calculateHourlyRate" min="0" required  class="form-control">
                </div>

                <!-- Basic Amount -->
                <div class="col-md-4">
                    <label class="form-label">Basic Amount</label>
                    <input type="number" v-model="form.basic_amount" @input="calculateHourlyRate" :disabled="this.hourly_employee" min="0" required class="form-control">
                </div>

                <!-- Hourly Rate -->
                <div class="col-md-4">
                    <label class="form-label">Hourly Rate</label>
                    <input type="number"  v-model="form.hourly_rent" :step="this.hourly_employee? 0.5:0.01" min="0" required   class="form-control">
                </div>

                <!-- Allowances -->
                <div class="col-md-4">
                    <label class="form-label">Saudi TAX</label>
                    <input type="number" v-model="form.saudi_tax" min="0" required   class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Food</label>
                    <input type="number" v-model="form.food_allowance"  min="0" required class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mobile</label>
                    <input type="number" v-model="form.mobile_allowance"   min="0" required  class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">House Allowance</label>
                    <input type="number" v-model="form.house_rent"    min="0" required  class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Medical Allowance</label>
                    <input type="number" v-model="form.medical_allowance"  min="0" required   class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Travel Allowance</label>
                    <input type="number" v-model="form.local_travel_allowance"  min="0" required class="form-control">
                </div>

                <div class="col-md-4" hidden>
                    <label class="form-label">Conveyance Conveyance</label>
                    <input type="number" v-model="form.conveyance_allowance"   min="0" required class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Increment Amount</label>
                    <input type="number" v-model="form.increment_amount"  min="0" required  class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">CPF Contribution</label>
                    <input type="number" v-model="form.contribution_amoun"  min="0" required class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Others</label>
                    <input type="number" v-model="form.others"  min="0" required   class="form-control">
                </div>

            </div>

            <!-- Submit -->
            <div class="text-end mt-4">
                <button type="submit" :disabled="this.is_saving" class="btn btn-primary">
                   <i v-if="this.is_saving" class="fas fa-spinner fa-spin"></i> Update
                </button>
            </div>

        </form>
    </div>
</div>
</template>

<script>
import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import EmployeeSearchComponent from './SearchComponent.vue';
import { SalaryUpdateAPI } from '../../routes.js';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

export default {
    setup() {
        const auth = inject('auth');
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    components: {
        EmployeeSearchComponent,
    },
    emits: ['updateSalaryCompletedCallBack'],
    props: {
        data: {
                type: Object,
                required: true
            },
        searched_employee_id: {
                type: Number,
                default: null
            }
    },
    mounted() {
        this.form.employee_id = this.searched_employee_id;
        console.log('SalaryUpdateComponent mounted with data:', this.searched_employee_id);
        if(this.searched_employee_id) {
            this.$nextTick(() => {
                this.$refs.employeeSearchComponent.searchBy = "employee_id";
                this.$refs.employeeSearchComponent.searchInpute = this.searched_employee_id;
                this.$refs.employeeSearchComponent.searchEmployee();
                this.$refs.employeeSearchComponent.is_active_dynamic_search = true; // Disable the input field after search
            });

        }
    },


    data() {
        return {
            empTypes: [ {'id':1 , 'name':'Direct Employee'},{'id':2 , 'name':'Indirect Employee'}],
            searched_employee:null,
            is_saving:false,
            form: {
                emp_auto_id: '',
                employee_id: this.data.employee_id,
                emp_type_id: '',
                hourly_employee : false,
                staff_employee: false,
                basic_hours: 0,
                basic_amount: 0,
                hourly_rent: 0,
                house_rent: 0,
                mobile_allowance: 0,
                food_allowance: 0,
                contribution_amoun: 0,
                saudi_tax: 0,
                medical_allowance: 0,
                local_travel_allowance: 0,
                conveyance_allowance: 0,
                increment_amount: 0,
                others: 0,
                payment_method: 'Cash', // or 'cash' default
            },

        }
    },

    methods: {
        handleSearchingResult(employee) {
            if(employee == null){return;}

            console.log('receiving searching data ',employee);
            this.searched_employee = employee;
         //   console.log(this.searched_employee);
            this.form.emp_auto_id = this.searched_employee.emp_auto_id;
            this.form.employee_id = this.searched_employee.employee_id;
            this.form.emp_type_id = this.searched_employee.emp_type_id;
            this.form.payment_method = this.searched_employee.payment_method;
            this.form.hourly_employee   = this.searched_employee.hourly_employee == 1 ? true:false;
            this.form.staff_employee = this.searched_employee.staff_employee  == 1 ? true:false;
           this.form.basic_amount = this.searched_employee.basic_amount;
           this.form.basic_hours =  this.searched_employee.basic_hours;
           this.form.hourly_rent =  this.searched_employee.hourly_rent;
           this.form.house_rent =  this.searched_employee.house_rent;
           this.form.mobile_allowance =  this.searched_employee.mobile_allowance;
           this.form.food_allowance =  this.searched_employee.food_allowance;
           this.form.contribution_amoun =  this.searched_employee.cpf_contribution;
           this.form.saudi_tax =  this.searched_employee.saudi_tax;
           this.form.medical_allowance =  this.searched_employee.medical_allowance;
           this.form.local_travel_allowance =  this.searched_employee.local_travel_allowance;
           this.form.conveyance_allowance =  this.searched_employee.conveyance_allowance;
           this.form.others =  this.searched_employee.others1;
        },

        calculateHourlyRate() {
            if (this.form.hourly_employee != 1) {
                this.form.hourly_rent = (
                    this.form.basic_amount / this.form.basic_hours
                ).toFixed(2);
            }
        },


        async submitForm() {
            try {

                this.is_saving = true;

                const response = await axios.post(SalaryUpdateAPI, {
                    ...this.form,
                });

                if(response.data.status === 200){
                    toast.success(response.data.message);
                    this.$emit('updateSalaryCompletedCallBack',true);

                }else {
                    toast.error('Update failed: ' + response.data.message);
                }


            } catch (error) {
                console.error(error);
                   toast.error('Update failed: ' +  error.message);
            }finally{
                 this.is_saving = false;
            }
        },
        toggleHourlyFields() {

        },

    }
}
</script>
