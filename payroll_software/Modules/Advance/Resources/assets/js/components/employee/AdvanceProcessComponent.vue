<template>
    <div>
        <!-- Loading Overlay -->
        <div class="overlay" v-if="loading"></div>

        <!-- Main Processing Card -->
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Employee Advance Processing</h4>
                    </div>
                    <div class="card-body card_form">
                        <!-- Processing Mode Selection -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Processing Mode:<span class="req_star">*</span></label>
                            <div class="col-sm-7">
                                <select class="form-select" v-model="processingMode" required>
                                    <option value="" disabled>Select Processing Mode</option>
                                    <option value="1">All Employees</option>
                                    <!-- <option value="2">Asloob Sponsor Basic Employee</option> -->
                                    <option value="3">Multiple Employees Processing</option>
                                    <!-- <option value="4">Multiple Employees Direct Assignment</option>
                                    <option value="5">Subcon Sponsor Direct Assignment</option> -->
                                </select>
                            </div>
                        </div>

                        <!-- Multiple/Single Employee ID Input -->
                        <div class="form-group row custom_form_group" v-if="processingMode == '3' || processingMode === '4' ">
                            <label class="col-sm-3 control-label">
                                'Employee IDs:'
                                <span class="req_star">*</span>
                            </label>
                            <div class="col-sm-7">
                                <textarea
                                    class="form-control"
                                    v-model="employeeIdsInput"
                                    placeholder="Enter multiple employee IDs (comma separated)"
                                    rows="3"
                                ></textarea>
                                <small class="text-muted" >
                                    Separate IDs by comma (,)
                                </small>
                            </div>
                        </div>

                        <!-- Month Selection -->
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Salary Month:<span class="req_star">*</span></label>
                            <div class="col-sm-7">
                                <select class="form-select" v-model="selectedMonth">
                                    <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                        {{ month }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Year Selection -->
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Salary Year:<span class="req_star">*</span></label>
                            <div class="col-sm-7">
                                <select class="form-select" v-model="selectedYear">
                                    <option v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Iqama Deduction Amount -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Iqama Deduction Amount:</label>
                            <div class="col-sm-7">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="iqamaAmount"
                                    min="0"
                                    placeholder="Input Iqama Deduction Amount"
                                />
                            </div>
                        </div>

                        <!-- Other Deduction Amount -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Other Deduction Amount:</label>
                            <div class="col-sm-7">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="otherAmount"
                                    min="0"
                                    placeholder="Input Other Deduction Amount"
                                />
                            </div>

                        </div>
                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button
                            @click="processAdvance"
                            class="btn btn-primary btn-sm"
                            :disabled="isProcessing"
                        >
                            <span v-if="isProcessing" class="spinner-border spinner-border-sm me-1"></span>
                            {{ isProcessing ? 'Processing...' : 'Advance Process' }}
                        </button>
                        <button
                            v-if="processingMode !== 'project'"
                            @click="clearEmployeeIds"
                            class="btn btn-secondary btn-sm ms-2"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
</template>

<script>

import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { EmployeeAdvanceProcessingAPI } from "../../routes.js";


export default {
    name: 'EmployeeAdvanceProcessing',
    props: {
        projects: {
            type: Array,
            required: true,
            default: () => []
        },
        initialIqamaAmount: {
            type: Number,
            default: 1000
        },
        initialOtherAmount: {
            type: Number,
            default: 300
        },
        currentYear: {
            type: Number,
            default: () => new Date().getFullYear()
        },
        currentMonth: {
            type: Number,
            default: () => new Date().getMonth() + 1
        }
    },

    data() {
        return {
            // Processing mode
            processingMode: '', // '1' = Asloob Sponsor Hourly, '2' = Asloob Sponsor Basic, '3' = Multiple Employees, '4' = Multiple Direct, '5' = Other Sponsor
            employeeIdsInput: '',
            selectedMonth: this.currentMonth,
            selectedYear: this.currentYear,
            iqamaAmount: this.initialIqamaAmount,
            otherAmount: this.initialOtherAmount,

            // UI state
            loading: false,
            isProcessing: false,
            // Months data
            months: [],
        }
    },

    computed: {
        years() {
            const currentYear = new Date().getFullYear();
            return [currentYear, currentYear - 1];
        },

        resultAlertClass() {
            return {
                'alert-success': this.resultType === 'success',
                'alert-danger': this.resultType === 'error',
                'alert-warning': this.resultType === 'warning'
            };
        },

        parsedEmployeeIds() {
            if (!this.employeeIdsInput.trim()) return [];

            // For multi_employee mode - split by comma,
            const ids = this.employeeIdsInput
                .split(/[,\s\n]+/)
                .map(id => id.trim())
                .filter(id => id !== '');

            return [...new Set(ids)]; // Remove duplicates
        },

        isValid() {
            if (this.processingMode === null || this.processingMode === '') {
                return false;
            }
            else if (this.processingMode === 3 || this.processingMode === 4) {
                return this.parsedEmployeeIds.length > 0;
            }
            else if(this.selectedMonth === null || this.selectedMonth === '' || this.selectedYear === null || this.selectedYear === '') {
                return false;
            }
            return true;
        }
    },

    watch: {
        processingMode(newMode) {
            // Clear inputs when switching modes
            this.employeeIdsInput = '';
            this.processingResult = false;
        }
    },

    created() {
        this.initializeMonths();
    },

    methods: {
        initializeMonths() {
            const months = [];
            for (let m = 1; m <= 12; m++) {
                months.push(new Date(2000, m - 1, 1).toLocaleString('default', { month: 'long' }));
            }
            this.months = months;
        },

        clearEmployeeIds() {
            this.employeeIdsInput = '';
        },

        async processAdvance() {
            if (!this.isValid) {
                toast.error('Please fill in all required fields');
                return;
            }

            this.isProcessing = true;
            this.loading = true;


            try {
                const requestData = this.buildRequestData();
                const response = await axios.post(EmployeeAdvanceProcessingAPI, requestData);

                if (response.data.status === 200) {
                    toast.success(response.data.message || 'Advance processing completed successfully');
                } else {
                    toast.error(response.data.error || 'Operation failed');
                }
            } catch (error) {
                console.error('Processing error:', error);
                let errorMessage = 'An error occurred while processing';
                if (error.response && error.response.data) {
                    errorMessage = error.response.data.error || error.response.data.message || errorMessage;
                }
                toast.error(errorMessage);
            } finally {
                this.isProcessing = false;
                this.loading = false;
            }
        },

        buildRequestData() {
              return {
                    month: this.selectedMonth,
                    year: this.selectedYear,
                    iqama_amount: this.iqamaAmount,
                    other_amount: this.otherAmount,
                    mul_emp_id: this.parsedEmployeeIds.join(','),
                    processing_mode: this.processingMode,
                };
        },


    }
}
</script>

<style scoped>
.overlay {
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 999;

}

.overlay[style*="display: block"],
.overlay[v-if="loading"] {
    display: block !important;
}

/* Prevent body scroll when loading */
body.loading {
    overflow: hidden;
}

.custom_form_group {
    margin-bottom: 1rem;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}

@media (max-width: 768px) {
    .custom_form_group .col-sm-3,
    .custom_form_group .col-sm-7 {
        margin-bottom: 0.5rem;
    }
}
</style>
