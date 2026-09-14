<template>
    <div>
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

        <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">
            <div>
                <div class="card p-4 shadow-sm mb-4 border border-light">
                    <!-- Form Title -->
                    <h5 class="fw-bold mb-4">
                        Employee Bonus Insertion Form
                        <span class="text-danger" id="errorData"></span>
                    </h5>

                    <!-- Row 1: Bonus Type & Month -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <label class="fw-bold me-2 text-nowrap" style="min-width: 100px; text-align: right;">
                                    Bonus Type:
                                </label>
                                <select class="form-select" :class="{ 'is-invalid': errors.bonus_type }"
                                    v-model="form.bonus_type" @change="clearValidationError('bonus_type')" required>
                                    <option value="" disabled>Select Bonus Type</option>
                                    <option value="5">Annual Bonus</option>
                                    <option value="10">Performance Bonus</option>
                                    <option value="15">Festival Bonus</option>
                                    <option value="30">Single Air Ticket</option>
                                    <option value="35">Return Air Ticket</option>
                                    <option value="37">Round Air Ticket</option>
                                    <option value="40">One Month Salary</option>
                                    <option value="45">Leave Salary</option>
                                    <option value="50">Bonus and Air Ticket</option>
                                </select>
                            </div>
                            <div class="text-end" v-if="errors.bonus_type">
                                <span class="invalid-feedback d-block me-1">
                                    {{ errors.bonus_type_msg }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <label class="fw-bold me-2 text-nowrap" style="min-width: 100px; text-align: right;">
                                Month:
                            </label>
                            <select class="form-select" v-model="form.month" required>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Year & Bonus Amount -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
                            <label class="fw-bold me-2 text-nowrap" style="min-width: 100px; text-align: right;">
                                Year:
                            </label>
                            <select class="form-select" v-model="form.year" required>
                                <option v-for="y in yearsList" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <label class="fw-bold me-2 text-nowrap" style="min-width: 100px; text-align: right;">
                                    Bonus Amount:
                                </label>
                                <input type="number" class="form-control" :class="{ 'is-invalid': errors.bonus_amount }"
                                    v-model.number="form.bonus_amount" min="0" step="1" required
                                    @focus="handleFocus('bonus_amount')" @blur="handleNumericInput('bonus_amount')" />
                            </div>
                            <div class="text-end" v-if="errors.bonus_amount">
                                <span class="invalid-feedback d-block me-1">
                                    {{ errors.bonus_amount_msg }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Remarks & Submit Button -->
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
                            <label class="fw-bold me-2 text-nowrap" style="min-width: 100px; text-align: right;">
                                Remarks:
                            </label>
                            <input type="text" class="form-control" v-model="form.remarks" />
                        </div>

                        <div class="col-md-6 text-end">
                            <button type="submit" :disabled="is_saving" @click="submitEmployeeBonus"
                                class="btn text-white fw-bold px-4" style="background-color: #2b3e4e;">
                                <i class="fas fa-spinner fa-spin me-1" v-if="is_saving"></i>
                                {{ is_saving ? 'Updating...' : 'Update' }}
                            </button>
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

import EmployeeSearchComponent from '../../../../../../HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';
import { EmployeeBonusStoreAPI } from '../../routes.js';

export default {
    components: { EmployeeSearchComponent },

    data() {
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1; // 1 to 12

        return {
            searched_employee: null,
            is_saving: false,
            yearsList: [currentYear - 1, currentYear, currentYear + 1],

            form: {
                emp_auto_id: '',
                month: currentMonth,
                year: currentYear,
                bonus_amount: 0,
                bonus_type: '',
                remarks: '',
            },

            errors: {
                bonus_type: false,
                bonus_type_msg: '',
                bonus_amount: false,
                bonus_amount_msg: '',
            }
        }
    },

    methods: {
        handleSearchingResult(employee) {
            if (!employee) return;
            this.searched_employee = employee;
            this.form.emp_auto_id = employee.emp_auto_id;
        },

        handleFocus(field) {
            if (this.form[field] === 0) {
                this.form[field] = '';
            }
            this.clearValidationError(field);
        },

        handleNumericInput(field) {
            if (this.form[field] === '' || this.form[field] === null || isNaN(this.form[field])) {
                this.form[field] = 0;
            }
        },

        clearValidationError(field) {
            if (this.errors[field]) {
                this.errors[field] = false;
                this.errors[`${field}_msg`] = '';
            }
        },

        validateForm() {
            let isValid = true;

            // Clear previous errors
            this.errors.bonus_type = false;
            this.errors.bonus_type_msg = '';


            // Bonus Type Validation
            if (!this.form.bonus_type) {
                this.errors.bonus_type = true;
                this.errors.bonus_type_msg = 'Bonus Type is Required';
                isValid = false;
            }



            return isValid;
        },

        async submitEmployeeBonus() {
            // Run Validation Check
            if (!this.validateForm()) {
                toast.error('Please correct the highlighted errors.');
                return;
            }

            this.is_saving = true;
            try {
                const formData = new FormData();
                formData.append('emp_auto_id', this.form.emp_auto_id);
                formData.append('month', this.form.month);
                formData.append('year', this.form.year);
                formData.append('bonus_amount', this.form.bonus_amount);
                formData.append('bonus_type', this.form.bonus_type);
                formData.append('remarks', this.form.remarks ?? '');

                const response = await axios.post(EmployeeBonusStoreAPI, formData);

                if (response.data.status === 200) {
                    toast.success('Saved Successfully');
                    this.resetForm();
                } else {
                    toast.error(response.data.message || 'Operation Failed');
                }

            } catch (error) {
                console.error(error);
                toast.error('Error occurred, Please Try Again: ' + error.message);
            } finally {
                this.is_saving = false;
            }
        },

        resetForm() {
            this.form.bonus_amount = 0;
            this.form.bonus_type = '';
            this.form.remarks = '';
            this.errors.bonus_type = false;
            this.errors.bonus_amount = false;
        }
    }
}
</script>