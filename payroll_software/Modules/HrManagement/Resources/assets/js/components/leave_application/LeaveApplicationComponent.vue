<template>
    <div>
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

        <div id="emp_leave_form" v-if="this.searched_employee">
            <form class="form-horizontal" @submit.prevent="submitForm" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-body card_form">
                        <h4 style="text-align: center; padding-top:5px;">Leave Application Form</h4>

                        <!-- Hidden Employee ID -->
                        <input type="text" class="form-control" v-model="form.app_employee_id" hidden required>

                        <!-- Mobile & Home Contact -->
                        <div class="form-group row custom_form_group">
                            <label for="" class="col-sm-2 control-label">Mobile No <span class="req_star">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="form.mobile_no" disabled placeholder="Enter Mobile Number">
                            </div>
                            <label for="" class="col-sm-2 control-label">Home Contact No<span class="req_star">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="form.home_mobile_no" disabled placeholder="Enter Home Contact Number">
                            </div>
                        </div>

                        <!-- Leave Start & Leave Reason -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Leave Start</label>
                            <div class="col-sm-4">
                                <input type="date" v-model="form.start_date" class="form-control" required>
                            </div>
                            <label class="col-sm-2 control-label">Leave For</label>
                            <div class="col-sm-4">
                                <select class="form-select" v-model="form.leave_reason_id" required>
                                    <option value="">Select Leave Reason</option>
                                    <option v-for="reason in this.leaveReasons" :key="reason.lev_reas_id" :value="reason.lev_reas_id">
                                        {{ reason.lev_reas_name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Leave End & Application Date -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Leave End</label>
                            <div class="col-sm-4">
                                <input type="date" v-model="form.end_date" class="form-control" required>
                            </div>
                            <label class="col-sm-2 control-label text-right">Application Date</label>
                            <div class="col-sm-4">
                                <input type="date" v-model="form.app_date" class="form-control" required>
                            </div>
                        </div>

                        <!-- Reference By & Leave Contract -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label text-right">Reference By</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control typeahead" placeholder="Reference By" v-model="form.reference_by">
                            </div>
                            <label class="col-sm-2 control-label text-right">Leave Contract</label>
                            <div class="col-sm-4">
                                <select class="form-select" v-model="form.leave_contract" required>
                                    <option value="">Please Select Leave Contract</option>
                                    <option value="1">1 Year</option>
                                    <option value="2">2 Years</option>
                                    <option value="3">3 Years</option>
                                </select>
                                <select class="form-control" v-model="form.application_status" hidden>
                                    <option v-for="status in applicationStatuses" :key="status.leav_sta_auto_id" :value="status.leav_sta_auto_id">
                                        {{ status.status_title }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label text-right">Remarks</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="2" placeholder="Remarks Here" v-model="form.remarks"></textarea>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Application File</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" @change="handleFileUpload" ref="fileInput" required>
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" :value="fileName" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <img id='img-upload4' class="upload_image" :src="imagePreview" v-if="imagePreview" style="max-height: 100px;" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="card-footer card_footer_button text-center">
                             <div class="col-md-6">  </div>
                             <div class="col-md-3">
                                <button type="submit" class="btn btn-primary waves-effect" :disabled="loading">
                                <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                                {{ loading ? 'Submitting...' : 'SAVE' }}
                                </button>
                             </div>
                             <div class="col-md-3">
                                <button type="button" class="btn btn-primary waves-effect" @click="printLeaveApplication" >
                                    <i class="fa fa-print fa-print"></i> &nbsp;Print
                                </button>
                             </div>


                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import EmployeeSearchComponent from '../employee/SearchComponent.vue';
import {LeaveApplicationSubmitAPI,LeaveApplicationFormPrintAPI} from '../../routes.js'

export default {
    name: 'LeaveApplicationForm',
    components: { EmployeeSearchComponent },

    props: {
        employeeId: {
            type: [String, Number],
            default: ''
        },
        mobileNo: {
            type: String,
            default: ''
        },
        homeMobileNo: {
            type: String,
            default: ''
        },
        leaveReasons: {
            type: Array,
            required: true
        },
        applicationStatuses: {
            type: Array,
            required: true
        },
        submitUrl: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            form: {
                app_employee_id: this.employeeId || '',
                emp_auto_id:'',
                mobile_no: this.mobileNo || '',
                home_mobile_no: this.homeMobileNo || '',
                start_date: new Date().toISOString().split('T')[0],
                leave_reason_id: '',
                end_date: this.getTomorrowDate(),
                app_date: new Date().toISOString().split('T')[0],
                reference_by: '',
                leave_contract: '',
                application_status: 1, // Default status
                remarks: ''
            },
            file: null,
            fileName: '',
            imagePreview: null,
            loading: false,
            errors: {},
            searched_employee:null,
        }
    },

    watch: {
        employeeId(newVal) {
            this.form.app_employee_id = newVal;
        },
        mobileNo(newVal) {
            this.form.mobile_no = newVal;
        },
        homeMobileNo(newVal) {
            this.form.home_mobile_no = newVal;
        }
    },

    mounted() {
        // Set default dates
        this.form.start_date = new Date().toISOString().split('T')[0];
        this.form.end_date = this.getTomorrowDate();
        this.form.app_date = new Date().toISOString().split('T')[0];

        // Set default application status if available
        if (this.applicationStatuses && this.applicationStatuses.length > 0) {
            this.form.application_status = this.applicationStatuses[0].leav_sta_auto_id;
        }

    },

    methods: {
        handleSearchingResult(employee) {
            if (employee == null) return; // employee not found
            this.searched_employee = employee;
            this.form.app_employee_id = employee.emp_auto_id;
            this.form.emp_auto_id = employee.emp_auto_id;
            this.form.home_mobile_no = employee.country_phone_no;
            this.form.mobile_no = employee.mobile_no;

            console.log('Data Received Successfully', employee);
        },
        getTomorrowDate() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            return tomorrow.toISOString().split('T')[0];
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.file = file;
                this.fileName = file.name;

                // Preview image
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async submitForm() {
            // Validate
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.errors = {};

            const formData = new FormData();

            // Append all form fields
            Object.keys(this.form).forEach(key => {
                formData.append(key, this.form[key] || '');
            });

            // Append file if exists
            if (this.file) {
                formData.append('leave_paper', this.file);
            }

            try {
                const response = await axios.post(LeaveApplicationSubmitAPI, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.status === 200) {
                    toast.success('Leave application submitted successfully!');
                    this.resetForm();
                   // this.$emit('submitted', response.data);
                } else {
                    toast.error(response.data.message || 'Failed to submit leave application');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    // Show first validation error
                    const firstError = Object.values(this.errors)[0];
                    if (firstError && firstError[0]) {
                        toast.error(firstError[0]);
                    }
                } else {
                    toast.error(error.response?.data?.message || 'An error occurred. Please try again.');
                }
                console.error('Submit error:', error);
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            // Check required fields
            if (!this.form.start_date) {
                toast.error('Please select leave start date');
                return false;
            }
            if (!this.form.leave_reason_id) {
                toast.error('Please select leave reason');
                return false;
            }
            if (!this.form.end_date) {
                toast.error('Please select leave end date');
                return false;
            }
            if (!this.form.app_date) {
                toast.error('Please select application date');
                return false;
            }
            if (!this.form.leave_contract) {
                toast.error('Please select leave contract');
                return false;
            }
            if (!this.file) {
                toast.error('Please upload application file');
                return false;
            }

            // Validate dates
            if (this.form.end_date < this.form.start_date) {
                toast.error('Leave end date must be after start date');
                return false;
            }

            return true;
        },

        resetForm() {
            this.form = {
                app_employee_id: this.employeeId || '',
                mobile_no: this.mobileNo || '',
                home_mobile_no: this.homeMobileNo || '',
                start_date: new Date().toISOString().split('T')[0],
                leave_reason_id: '',
                end_date: this.getTomorrowDate(),
                app_date: new Date().toISOString().split('T')[0],
                reference_by: '',
                leave_contract: '',
                application_status: this.applicationStatuses[0]?.leav_sta_auto_id || 1,
                remarks: ''
            };
            this.file = null;
            this.fileName = '';
            this.imagePreview = null;
            this.errors = {};

            // Clear file input
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },
        printLeaveApplication(){

                // Build query string
                const queryString = new URLSearchParams({
                    searchType: 'employee_id',
                    searchValue: this.searched_employee.employee_id,
                }).toString();

                const url = `${LeaveApplicationFormPrintAPI}?${queryString}`;
                // Open in new tab
                window.open(url, '_blank');
        }

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
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
.upload_image {
    max-height: 100px;
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 4px;
}
</style>
