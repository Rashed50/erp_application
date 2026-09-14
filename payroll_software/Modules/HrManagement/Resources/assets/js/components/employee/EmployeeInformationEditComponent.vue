<template>
    <div class="row">
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>
        <div class="col-lg-12" v-if="this.employeeData">
             <!-- v-if="this.is_searching == 2"> -->
            <form class="form-horizontal" @submit.prevent="submitForm">
                <div class="card">
                    <h5> &nbsp; &nbsp; &nbsp;<i class="fab fa-gg-circle"></i> Employee Information Update</h5>

                    <div class="card-body card_form">
                        <input type="hidden" name="emp_auto_id" v-model="form.emp_auto_id" required>

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Employee ID (Disabled) -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Employee ID:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" v-model="form.employee_id" disabled
                                            placeholder="Employee Id">
                                    </div>
                                </div>

                                <!-- Employee Name -->
                                <div class="form-group row" :class="{ 'has-error': errors.emp_name }">
                                    <label class="col-sm-4 control-label  text-right">Name:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Employee Name Here" class="form-control"
                                            v-model="form.emp_name" required>
                                        <span class="invalid-feedback" role="alert" v-if="errors.emp_name">
                                            <strong>{{ errors.emp_name[0] }}</strong>
                                        </span>
                                    </div>
                                </div>



                                <!-- Passport No -->
                                <div class="form-group row" :class="{ 'has-error': errors.passfort_no }">
                                    <label class="col-sm-4 control-label  text-right">Passport No:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Passport Number Here" class="form-control"
                                            v-model="form.passfort_no" disabled required>
                                        <span class="invalid-feedback" role="alert" v-if="errors.passfort_no">
                                            <strong>{{ errors.passfort_no[0] }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Passport Expire Date -->
                                <div class="form-group row" :class="{ 'has-error': errors.passport_expire_date }">
                                    <label class="col-sm-4 control-label  text-right">Passport Expire:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" v-model="form.passport_expire_date"
                                            required>
                                        <span class="invalid-feedback" role="alert" v-if="errors.passport_expire_date">
                                            <strong>{{ errors.passport_expire_date[0] }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Iqama No -->
                                <div class="form-group row" :class="{ 'has-error': errors.akama_no }">
                                    <label class="col-sm-4 control-label  text-right">Iqama No:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Iqama Number Here" class="form-control"
                                            v-model="form.akama_no" disabled required>
                                        <span class="invalid-feedback" role="alert" v-if="errors.akama_no">
                                            <strong>{{ errors.akama_no[0] }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Iqama Expire Date -->
                                <div class="form-group row" :class="{ 'has-error': errors.akama_expire }">
                                    <label class="col-sm-4 control-label  text-right">Iqama Expire:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" v-model="form.akama_expire" required>
                                        <span class="invalid-feedback" role="alert" v-if="errors.akama_expire">
                                            <strong>{{ errors.akama_expire[0] }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Mobile No -->
                                <div class="form-group row" :class="{ 'has-error': errors.mobile_no }">
                                    <label class="col-sm-4 control-label  text-right">Abshar Mobile No:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Mobile Number" class="form-control"
                                            v-model="form.mobile_no" required>
                                        <span class="invalid-feedback" role="alert" v-if="errors.mobile_no">
                                            <strong>{{ errors.mobile_no[0] }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Phone No 2 -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Mobile Number2:</label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Phone Number" class="form-control"
                                            v-model="form.phone_no">
                                    </div>
                                </div>

                                <!-- Home Country Contact No -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label text-right">Home Contact No:</label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Home Country Contact Number"
                                            class="form-control" v-model="form.country_phone_no">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Email:</label>
                                    <div class="col-sm-8">
                                        <input type="email" placeholder="Input Email Address" class="form-control"
                                            v-model="form.email" :class="{ 'is-invalid': emailError }"
                                            @blur="validateEmail">
                                        <div v-if="emailError" class="invalid-feedback d-block">
                                            {{ emailError }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Date of Birth -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Date Of Birth:</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" v-model="form.date_of_birth"
                                            :max="maxBirthDate">
                                    </div>
                                </div>
                                <!-- Present Address -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Present Address:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" v-model="form.present_address"
                                            placeholder="Input Present Address Here" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">



                                <!-- Designation -->
                                <div class="form-group row" :class="{ 'has-error': errors.designation_id }">
                                    <label class="col-sm-4 control-label  text-right">Designation:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.designation_id" disabled required>
                                            <option value="">Select Designation</option>
                                            <option v-for="designation in designations" :key="designation.catg_id"
                                                :value="designation.catg_id">
                                                {{ designation.catg_name }}
                                            </option>
                                        </select>
                                        <span class="invalid-feedback" role="alert" v-if="errors.designation_id">
                                            <strong>{{ errors.designation_id[0] }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <!-- Project -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Project:</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.project_id" disabled required>
                                            <option value="">Select Here</option>
                                            <option v-for="project in projects" :key="project.proj_id"
                                                :value="project.proj_id">
                                                {{ project.proj_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sponsor Name -->
                                <div class="form-group row" :class="{ 'has-error': errors.sponsor_id }">
                                    <label class="col-sm-4 control-label  text-right">Sponsor Name:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.sponsor_id" disabled  required>
                                            <option value="">Select Sponsor Name</option>
                                            <option v-for="sponsor in sponsors" :key="sponsor.spons_id"
                                                :value="sponsor.spons_id">
                                                {{ sponsor.spons_name }}
                                            </option>
                                        </select>
                                        <span class="invalid-feedback" role="alert" v-if="errors.sponsor_id">
                                            <strong>{{ errors.sponsor_id[0] }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Agency Name -->
                                <div class="form-group row" :class="{ 'has-error': errors.agency_id }">
                                    <label class="col-sm-4 control-label  text-right">Agency Name:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.agency_id" disabled required>
                                            <option value="">Select Agency Name</option>
                                            <option v-for="agency in agencies" :key="agency.agc_info_auto_id"
                                                :value="agency.agc_info_auto_id">
                                                {{ agency.agc_title }}
                                            </option>
                                        </select>
                                        <span class="invalid-feedback" role="alert" v-if="errors.agency_id">
                                            <strong>{{ errors.agency_id[0] }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <!-- Department -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Department:</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.department_id" required>
                                            <option value="">Select Department</option>
                                            <option v-for="department in departments" :key="department.dep_id"
                                                :value="department.dep_id">
                                                {{ department.dep_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Blood Group -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Blood Group:</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.blood_group" required>
                                            <option value="">Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Marital Status -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Marital Status:</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.maritus_status" >
                                            <option value="1">Married</option>
                                            <option value="2">Unmarried</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Gender:</label>
                                    <div class="col-sm-8">
                                        <div class="radio-inline">
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <label class="mr-3">
                                                <input type="radio" value="1" v-model="form.gender"> Male
                                            </label> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <label>
                                                <input type="radio" value="2" v-model="form.gender"> Female
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Religion -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Religion:</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" v-model="form.religion">
                                            <option value="">Select Religion</option>
                                            <option v-for="religion in religions" :key="religion.relig_id"
                                                :value="religion.relig_id">
                                                {{ religion.relig_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Reference Employee -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Reference Employee:</label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Reference Name or Employee ID"
                                            class="form-control" v-model="form.ref_employee_id">
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <div class="form-group row">
                                    <label class="col-sm-4 control-label  text-right">Remarks:</label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Input Remarks Here" class="form-control"
                                            v-model="form.remarks">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permanent Address Section (Full Width) -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Permanent Address</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select class="form-select" v-model="form.country_id"
                                                        name="country_id" @change="onCountryChange" required>
                                                        <option value="">Select Country</option>
                                                        <option v-for="country in countries" :key="country.id"
                                                            :value="country.id">
                                                            {{ country.country_name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Division</label>
                                                    <select class="form-select" v-model="form.division_id"
                                                        name="division_id" @change="onDivisionChange" required>
                                                        <option value="">Select Division</option>
                                                        <option v-for="division in divisions"
                                                            :key="division.division_id" :value="division.division_id">
                                                            {{ division.division_name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>District</label>
                                                    <select class="form-select" v-model="form.district_id" required>
                                                        <option value="">Select District</option>
                                                        <option v-for="district in districts"
                                                            :key="district.district_id" :value="district.district_id">
                                                            {{ district.district_name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Post Code</label>
                                                    <input type="text" class="form-control" v-model="form.post_code"
                                                        placeholder="Input Post Code">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address Details</label>
                                                    <textarea class="form-control" v-model="form.details"
                                                        placeholder="Input Address Details" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment & Joining Dates Row -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group" :class="{ 'has-error': errors.appointment_date }">
                                    <label>Appointment Date:<span class="req_star">*</span></label>
                                    <input type="date" v-model="form.appointment_date" class="form-control">
                                    <span class="invalid-feedback" role="alert" v-if="errors.appointment_date">
                                        <strong>{{ errors.appointment_date[0] }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Joining Date:</label>
                                    <input type="date" v-model="form.joining_date" class="form-control"
                                        :max="currentDate">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Confirmation Date:</label>
                                    <input type="date" v-model="form.confirmation_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary waves-effect" :disabled="is_updating">
                            <span v-if="this.is_updating" class="spinner-border spinner-border-sm mr-1"></span>
                                    {{ this.is_updating ? 'Updating...' : 'Update' }}
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

import axios from 'axios';
import { inject } from 'vue';
import { toast } from 'vue3-toastify';
import { EmployeeSearchAPIForInfoEdit, EmployeeAllInfoUpdateAPI } from "../../routes.js";
import EmployeeSearchComponent from './SearchComponent.vue';

export default {
    name: 'EmployeeInformationUpdate',
    components: { EmployeeSearchComponent },

    props: {
        data: {
                type: Object,
                required: true
            },
    },
    mounted() {
       // console.log('Emp Update page Props data:', this.data);
    },
    data() {
        return {
            searchBy: 'employee_id',
            searchInpute: '1020',
            employeeData: null,
            is_updating: false,
            is_searching: 0,// 0 = initial, 1 = searching running , 2 = searching done
            emailError: '',
            agencies: this.data.agencies || [],
            sponsors: this.data.sponsors || [],
            countries: this.data.country || [],
            departments: this.data.departments || [],
            empTypes: this.data.empTypes || [],
            designations: this.data.designations,
            projects: this.data.projects || [],
            villas: this.data.accommodations || [],
            divisions: [],
            districts: [],
            religions: [{ 'relig_id': 1, 'relig_name': 'Muslim' }, { 'relig_id': 2, 'relig_name': 'Hinduism' }, { 'relig_id': 3, 'relig_name': 'Christianity' }],

            form: {
                emp_auto_id: '',
                employee_id: '',
                emp_name: '',
                agency_id: '',
                sponsor_id: '',
                passfort_no: '',
                passport_expire_date: '',
                akama_no: '',
                akama_expire: '',
                mobile_no: '',
                phone_no: '',
                country_phone_no: '',
                email: '',
                present_address: '',
                country_id: '',
                division_id: '',
                district_id: '',
                post_code: '',
                details: '',
                emp_type_id: '',
                designation_id: '',
                project_id: '',
                department_id: '',
                date_of_birth: '',
                blood_group: '',
                maritus_status: '1',
                gender: '1',
                religion: '',
                ref_employee_id: '',
                remarks: '',
                appointment_date: '',
                joining_date: '',
                confirmation_date: ''
            },
            errors: {},

            currentDate: new Date().toISOString().split('T')[0],
            minDate: new Date().toISOString().split('T')[0]
        }
    },
    watch: {

        'form.email': function (newEmail) {
            if (newEmail && !this.isValidEmail(newEmail)) {
                this.emailError = 'Please enter a valid email address';
            } else {
                this.emailError = '';
            }
        },
        'form.country_id': function (newCountryId) {

            if (newCountryId) {
                this.loadDivisions(newCountryId);
            } else {
                this.divisions = [];
                this.form.division_id = '';
                this.districts = [];
                this.form.district_id = '';
            }
        },

        'form.division_id': function (newDivisionId) {
            if (newDivisionId) {
                this.loadDistricts(newDivisionId);
            } else {
                this.districts = [];
                this.form.district_id = '';
            }
        }
    },

    computed: {
        maxBirthDate() {
            const date = new Date();
            date.setFullYear(date.getFullYear() - 18);
            return date.toISOString().split('T')[0];
        },

    },

    mounted() {
    },

    methods: {

        // Binds searching result employee data on search success
        handleSearchingResult(employee) {
            if (employee == null) return;
            this.employeeData = employee; 
          //  console.log('Employee Data found Successfully', employee);
            this.initializeForm();
        },

        initializeForm() {

            if (this.employeeData) {
                this.form = {
                    emp_auto_id: this.employeeData.emp_auto_id || '',
                    employee_id: this.employeeData.employee_id || '',
                    emp_name: this.employeeData.employee_name || '',
                    agency_id: this.employeeData.agc_info_auto_id || '',
                    sponsor_id: this.employeeData.sponsor_id || '',
                    passfort_no: this.employeeData.passfort_no || '',
                    passport_expire_date: this.employeeData.passfort_expire_date || '',
                    akama_no: this.employeeData.akama_no || '',
                    akama_expire: this.employeeData.akama_expire_date || '',
                    mobile_no: this.employeeData.mobile_no || '',
                    phone_no: this.employeeData.phone_no || '',
                    country_phone_no: this.employeeData.country_phone_no || '',
                    email: this.employeeData.email || '',
                    present_address: this.employeeData.present_address || '',
                    country_id: this.employeeData.country_id || '',
                    division_id: this.employeeData.division_id || '',
                    district_id: this.employeeData.district_id || '',
                    post_code: this.employeeData.post_code || '',
                    details: this.employeeData.details || '',
                    emp_type_id: this.employeeData.emp_type_id || '',
                    designation_id: this.employeeData.designation_id || '',
                    project_id: this.employeeData.project_id || '',
                    department_id: this.employeeData.department_id || '',
                    date_of_birth: this.employeeData.date_of_birth || '',
                    blood_group: this.employeeData.blood_group || '',
                    maritus_status: this.employeeData.is_married ? this.employeeData.is_married.toString() : '1',
                    gender: this.employeeData.gender === 'F' ? '2' : '1',
                    religion: this.employeeData.religion_id || '',
                    ref_employee_id: this.employeeData.ref_employee_id || '',
                    remarks: this.employeeData.remarks || '',
                    appointment_date: this.employeeData.appointment_date || '',
                    joining_date: this.employeeData.joining_date || '',
                    confirmation_date: this.employeeData.confirmation_date || '',

                };
            }
        },

        async submitForm() {
            this.is_updating = true;
            this.errors = {};

            try {
                // this.form.gender  = this.form.gender  ==  1 ? "M":"F";
                const response = await axios.post(EmployeeAllInfoUpdateAPI, this.form);
                if (response.data.status === 200) {
                    toast.success('Employee information updated successfully!');

                } else {
                    toast.error(response.data.message || 'Failed to update employee information');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    toast.error('Please check the form for errors');
                } else if (error.response && error.response.data) {
                    toast.error(error.response.data.message || 'An error occurred');
                } else {
                    toast.error('Network error. Please try again.');
                }
                console.error('Update error:', error);
            } finally {
                this.is_updating = false;
            }
        },

        resetForm() {
            this.initializeForm();
            this.errors = {};

        },


        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
            return emailRegex.test(email);
        },

        // Load divisions based on selected country
        async loadDivisions(countryId, selectedDivisionId = null) {
            this.divisionsLoading = true;
            this.divisions = [];
            this.districts = [];
            // API URLs
            const divisionApiUrl = '/admin/division/ajax';
            try {
                const response = await axios.get(`${divisionApiUrl}/${countryId}`);
                const data = response.data;

                if (data && data.length > 0) {
                    this.divisions = data;

                    // If there's a selected division ID, set it after loading
                    if (selectedDivisionId) {
                        // Check if selected division exists in the loaded divisions
                        const divisionExists = this.divisions.some(d => d.division_id == selectedDivisionId);
                        this.form.division_id = selectedDivisionId;
                        if (divisionExists) {
                            // Load districts for this division
                            await this.loadDistricts(selectedDivisionId, this.form.district_id);
                        }
                    }
                } else {
                    this.divisions = [];
                    toast.info('No divisions found for selected country');
                }
            } catch (error) {
                console.error('Error loading divisions:', error);
                toast.error('Failed to load divisions');
                this.divisions = [];
            } finally {
                this.divisionsLoading = false;
            }
        },

        // Load districts based on selected division
        async loadDistricts(divisionId, selectedDistrictId = null) {
            this.districtsLoading = true;
            this.districts = [];
            const districtApiUrl = '/admin/district/ajax';

            try {
                const response = await axios.get(`${districtApiUrl}/${divisionId}`);
                const data = response.data;

                if (data && data.length > 0) {
                    this.districts = data;

                    // If there's a selected district ID, set it after loading
                    if (selectedDistrictId) {
                        const districtExists = this.districts.some(d => d.district_id == selectedDistrictId);
                        if (districtExists) {
                            this.form.district_id = selectedDistrictId;
                        }
                    }
                } else {
                    this.districts = [];
                    toast.info('No districts found for selected division');
                }
            } catch (error) {
                console.error('Error loading districts:', error);
                toast.error('Failed to load districts');
                this.districts = [];
            } finally {
                this.districtsLoading = false;
            }
        },

        // Event handler for country change
        onCountryChange() {
            // Watch is already handling this, but we can add additional logic if needed
            console.log('Country changed to:', this.form.country_id);
        },

        // Event handler for division change
        onDivisionChange() {
            // Watch is already handling this
            console.log('Division changed to:', this.form.division_id);
        },
    }
}
</script>

<style scoped>
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.control-label {
    font-weight: 600;
    padding-top: 8px;
}

.req_star {
    color: red;
    margin-left: 3px;
}

.has-error .form-control {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
}

.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}

.radio-inline label {
    margin-right: 15px;
    cursor: pointer;
}

.radio-inline input {
    margin-right: 5px;
}

.mt-3 {
    margin-top: 1rem;
}

.ml-2 {
    margin-left: 0.5rem;
}

.mr-3 {
    margin-right: 1rem;
}

.card-outline-primary {
    border: 1px solid #007bff;
}
</style>
