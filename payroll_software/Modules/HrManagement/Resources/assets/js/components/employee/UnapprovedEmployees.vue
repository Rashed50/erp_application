<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <button
                                type="button"
                                @click="toggleSelectAll"
                                class="btn btn-primary waves-effect"
                                v-if="canApprove"
                            >
                                Check/UnCheck
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label" id="lbl_total_selected_counter">
                                Total Selected: {{ selectedCount }}
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label" id="lbl_total_selected_salary"></label>
                        </div>
                        <div class="col-md-2">
                            <button
                                type="button"
                                class="btn btn-primary waves-effect"
                                @click="updateMultipleAsApproved"
                                :disabled="selectedCount === 0 || loading"
                                v-if="canApprove"
                            >
                                <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                                Update as Approved
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>ID</th>
                                            <th>Emp. Name</th>
                                            <th>Designation</th>
                                            <th>Sponsor</th>
                                            <th>Addr.</th>
                                            <th>Type</th>
                                            <th>Basic</th>
                                            <th>B.Hours</th>
                                            <th>Hourly</th>
                                            <th>Food</th>
                                            <th>S.T</th>
                                            <th>Photo</th>
                                            <th colspan="2">Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeePendingList">
                                        <tr v-for="(employee, index) in employees" :key="employee.emp_auto_id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ employee.employee_id }}</td>
                                            <td>{{ employee.employee_name }}</td>
                                            <td>{{ employee.category?.catg_name || 'N/A' }}</td>
                                            <td>{{ employee.sponser?.spons_name || 'N/A' }}</td>
                                            <td>{{ employee.country?.country_name ? truncateText(employee.country.country_name, 5) : 'Not-Set' }}</td>
                                            <td>{{ employee.employeeType?.name ? truncateText(employee.employeeType.name, 4) : 'Not Set' }}</td>
                                            <td>{{ employee.basic_amount }}</td>
                                            <td>{{ employee.basic_hours }}</td>
                                            <td>{{ employee.hourly_rent }}</td>
                                            <td>{{ employee.food_allowance }}</td>
                                            <td>{{ employee.saudi_tax }}</td>
                                            <td>
                                                <img :src="employee.profile_photo" alt="Not Found" width="80">
                                            </td>
                                            <td style="background-color: #fff; color:#fff; padding: 0px;">
                                                {{ employee.emp_auto_id }}
                                            </td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    v-if="canApprove"
                                                    :checked="isSelected(employee.emp_auto_id)"
                                                    @change="toggleSelection(employee.emp_auto_id)"
                                                >
                                                ||
                                                <a href="#" @click.prevent="openEditModal(employee)" title="Edit">
                                                    <i class="fa fa-pencil-square fa-lg edit_icon"></i>
                                                </a> ||
                                                <a href="#" @click.prevent="openViewModal(employee)">
                                                    <i class="fas fa-eye fa-lg view_icon"></i>
                                                </a>
                                                <button @click="deleteEmployee(employee.emp_auto_id)"
                                                    style="background: none; border: none; padding: 0; cursor: pointer; color: #dc3545;"
                                                    title="Delete">
                                                    <i class="fas fa-trash fa-lg"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="card-title text-danger">{{ viewEmployee?.employee_name }} Details Informations</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="viewEmployee">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            Please Download
                                            <a v-if="viewEmployee.pasfort_photo" target="_blank" class="download_button"
                                                :href="getAttachmentUrl(viewEmployee.pasfort_photo)">Passport</a>
                                            &nbsp; &nbsp;
                                            <a v-if="viewEmployee.akama_photo" target="_blank" class="download_button"
                                                :href="getAttachmentUrl(viewEmployee.akama_photo)">Iqama</a>
                                            &nbsp; &nbsp;
                                            <a v-if="viewEmployee.employee_appoint_latter" target="_blank" class="download_button"
                                                :href="getAttachmentUrl(viewEmployee.employee_appoint_latter)">Offer Letter</a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item list-group-item-primary">
                                                Employee Id No : <span class="badge badge-primary badge-pill">{{ viewEmployee.employee_id }}</span>
                                            </li>
                                            <li class="list-group-item list-group-item-success">
                                                Name : <span class="badge badge-primary badge-pill">{{ viewEmployee.employee_name }}</span>
                                            </li>
                                            <li class="list-group-item">Akama No : {{ viewEmployee.akama_no }}</li>
                                            <li class="list-group-item">Sponsor Name : <b>{{ viewEmployee.sponser?.spons_name }}</b></li>
                                            <li class="list-group-item">Agency Name : <b>{{ viewEmployee.agc_title }}</b></li>
                                            <li class="list-group-item">Job Status : <b>Approval Pending</b></li>
                                            <li class="list-group-item">Employee Designation : <b>{{ viewEmployee.category?.catg_name }}</b></li>
                                            <li class="list-group-item">Akama Expire Date : {{ formatDate(viewEmployee.akama_expire) }}</li>
                                            <li class="list-group-item">Passport No : <b>{{ viewEmployee.passfort_no }}</b></li>
                                            <li class="list-group-item">Passport Expire Date : {{ formatDate(viewEmployee.passfort_expire_date) }}</li>
                                            <li class="list-group-item">Employee Type : <b>{{ viewEmployee.employeeType?.name }}</b></li>
                                            <li class="list-group-item">Date of Birth : {{ formatDate(viewEmployee.date_of_birth) }}</li>
                                            <li class="list-group-item">Mobile Number : {{ viewEmployee.mobile_no || 'No Number ...' }}</li>
                                            <li class="list-group-item">Phone Number : {{ viewEmployee.phone_no }}</li>
                                            <li class="list-group-item">Email Address : {{ viewEmployee.email }}</li>
                                            <li class="list-group-item">Gender : {{ viewEmployee.gender == 1 ? 'Male' : 'Female' }}</li>
                                            <li class="list-group-item">Maritus Status : {{ viewEmployee.maritus_status == 1 ? 'Unmarried' : 'Married' }}</li>
                                            <li class="list-group-item">Religion : {{ getReligionName(viewEmployee.religion) }}</li>
                                            <li class="list-group-item">Joining Date : {{ formatDate(viewEmployee.joining_date) }}</li>
                                            <li class="list-group-item">Confirmation Date : {{ formatDate(viewEmployee.confirmation_date) }}</li>
                                            <li class="list-group-item">Appointment Date : {{ formatDate(viewEmployee.appointment_date) }}</li>
                                            <li class="list-group-item">Job Location : {{ viewEmployee.job_location }}</li>
                                            <li class="list-group-item">Employee Insert Date : {{ formatDate(viewEmployee.emp_insert_date) }}</li>
                                            <li class="list-group-item">Present Address : {{ viewEmployee.present_address }}</li>
                                            <li class="list-group-item">Permanent Address : {{ viewEmployee.country?.country_name }},
                                                {{ viewEmployee.division?.division_name }},
                                                {{ viewEmployee.district?.district_name }},
                                                <span>post code : {{ viewEmployee.post_code }}</span>
                                            </li>
                                            <li class="list-group-item">Permanent Address Details : {{ viewEmployee.details }}</li>
                                        </ul>

                                        <div class="card-header bg-primary text-white">
                                            {{ viewEmployee.employee_name }} Salary Details Informations
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Basic Amount : <b>{{ viewEmployee.basic_amount }}</b></li>
                                            <li class="list-group-item">Basic Hour : {{ viewEmployee.basic_hours }}</li>
                                            <li class="list-group-item">Hourly Rate : <b>{{ viewEmployee.hourly_rent }}</b></li>
                                            <li class="list-group-item">Mobile Allowance : {{ viewEmployee.mobile_allowance }}</li>
                                            <li class="list-group-item">Food Allowance : <b>{{ viewEmployee.food_allowance }}</b></li>
                                            <li class="list-group-item">Saudi Tax : {{ viewEmployee.saudi_tax }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="card-title">Employee Salary Details Update</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="updateEmployeeSalary">
                            <div class="card">
                                <div class="EmpIDName text-center">
                                    <h5 class="card-title">Employee Id : <span class="req_star">{{ editForm.employee_id }}</span></h5>
                                    <br>
                                    <h5 class="card-title">Employee Name : <span class="req_star">{{ editForm.employee_name }}</span></h5>
                                </div>
                                <div class="card-body card_form">
                                    <input type="hidden" v-model="editForm.emp_auto_id">

                                    <div class="row custom_form_group">
                                        <label class="col-sm-4 control-label">Employee Type:<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-select" v-model="editForm.emp_type_id">
                                                <option v-for="type in employeeTypes" :key="type.id" :value="type.id">
                                                    {{ type.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="hourlyEmployeeFeild" class="d-block">
                                        <div class="row custom_form_group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-6" style="margin-left: 10px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" :checked="editForm.hourly_employee"
                                                        v-model="editForm.hourly_employee" id="flexCheckDefault">
                                                    <label class="form-check-label" for="flexCheckDefault"
                                                        style="font-size:13px; font-weight:400">
                                                    </label>
                                                    <label class="control-label">Hourly Employee:<span class="req_star">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label" for="basic_amount">Basic Salary:<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Input Amount" class="form-control"
                                                v-model="editForm.basic_amount" min="0">
                                            <span v-if="errors.basic_amount" class="invalid-feedback d-block">
                                                <strong>{{ errors.basic_amount[0] }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label" for="basic_hours">Basic Hours:<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Input Basic Hours" class="form-control"
                                                v-model="editForm.basic_hours" min="1">
                                            <span v-if="errors.basic_hours" class="invalid-feedback d-block">
                                                <strong>{{ errors.basic_hours[0] }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Mobile Allowance:</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Input Amount" class="form-control"
                                                v-model="editForm.mobile_allowance">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Medical Allowance:</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Input Amount" class="form-control"
                                                v-model="editForm.medical_allowance">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Saudi Tax:</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Input Amount" class="form-control"
                                                v-model="editForm.saudi_tax">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Food Allowance:<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" v-model="editForm.food_allowance" min="0">
                                            <span v-if="errors.food_allowance" class="invalid-feedback d-block">
                                                <strong>{{ errors.food_allowance[0] }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label" for="hourly_rate">Hourly Rate:<span class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Input Hours Rate" class="form-control"
                                                v-model="editForm.hourly_rate" min="0">
                                            <span v-if="errors.hourly_rate" class="invalid-feedback d-block">
                                                <strong>{{ errors.hourly_rate[0] }}</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary" :disabled="loadingEdit">
                                            <span v-if="loadingEdit" class="spinner-border spinner-border-sm mr-1"></span>
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { UnapprovedEmployeesAPI,ApprovalOfNewEmployeesAPI,UnapprovedEmpSalaryUpdateAPI ,UnapprovedEmployeesDeleteAPI} from '../../routes.js';

// export const UnapprovedEmployeesAPI = "/admin/hrmanagement/unapproved-employees";
// export const ApprovedEmployeesAPI = "/admin/hrmanagement/new-employee-job-status-approved";
// export const UnapprovedEmpSalaryUpdateAPI = "/admin/hrmanagement/employee-salary-update-at-approval";
// export const UnapprovedEmployeesDeleteAPI = "/admin/hrmanagement/employee";



export default {
    name: 'UnapprovedEmployees',

    props: {
        employeeTypes: {
            type: Array,
            default: () => [{'id':1, "name":"Direct Employee"},{"id":2, "name":"Indirect Employee"}]
        },
        canApprove: {
            type: Boolean,
            default: true
        },
        apiBaseUrl: {
            type: String,
            default: '/admin/hrmanagement'
        }
    },

    data() {
        return {
            employees: [],
            selectedEmployees: [],
            loading: false,
            loadingEdit: false,
            viewEmployee: null,
            editForm: {
                emp_auto_id: '',
                employee_id: '',
                employee_name: '',
                emp_type_id: '',
                hourly_employee: 0,
                basic_amount: '',
                basic_hours: '',
                mobile_allowance: '',
                medical_allowance: '',
                saudi_tax: '',
                food_allowance: '',
                hourly_rate: ''
            },
            errors: {},
            awsS3BucketUrl: 'https://your-s3-bucket-url.s3.amazonaws.com/'
        }
    },

    computed: {
        selectedCount() {
            return this.selectedEmployees.length;
        }
    },

    mounted() {
        this.loadEmployees();
        // Set S3 bucket URL from config
        if (window.config && window.config.aws_s3_bucket_url) {
            this.awsS3BucketUrl = window.config.aws_s3_bucket_url;
        }
    },

    methods: {
        async loadEmployees() {
            this.loading = true;
            try {
                const response = await axios.get(UnapprovedEmployeesAPI);
                this.employees = response.data.data;
                console.log(this.employees);
               // toast.success('Employees loaded successfully');
            } catch (error) {
                console.error('Error loading employees:', error);
                toast.error('Failed to load employees');
            } finally {
                this.loading = false;
            }
        },

        isSelected(empId) {
            return this.selectedEmployees.includes(empId);
        },

        toggleSelection(empId) {
            const index = this.selectedEmployees.indexOf(empId);
            if (index > -1) {
                this.selectedEmployees.splice(index, 1);
            } else {
                this.selectedEmployees.push(empId);
            }
        },

        toggleSelectAll() {
            if (this.selectedEmployees.length === this.employees.length) {
                this.selectedEmployees = [];
            } else {
                this.selectedEmployees = this.employees.map(emp => emp.emp_auto_id);
            }
        },

        async updateMultipleAsApproved() {
            if (this.selectedEmployees.length === 0) {
                this.showSweetAlert('Please select at least one record to update', 'error');
                return;
            }

            this.loading = true;
            console.log(this.selectedEmployees);
            try {
                const response = await axios.post(`${ApprovalOfNewEmployeesAPI}`, {
                    emp_auto_ids: this.selectedEmployees
                });

                if (response.data.status === 200) {
                    this.showSweetAlert('Employee Updated Successfully', 'success');
                    this.selectedEmployees = [];
                    await this.loadEmployees();
                } else {
                    this.showSweetAlert('Update Failed', 'error');
                }
            } catch (error) {
                console.error('Error updating employees:', error);
                this.showSweetAlert('Update Failed', 'error');
            } finally {
                this.loading = false;
            }
        },

        openViewModal(employee) {
            this.viewEmployee = employee;
            $('#viewModal').modal('show');
        },

        openEditModal(employee) {
            this.errors = {};

            // Using Object.assign to trigger reactivity
            this.editForm = Object.assign({}, {
                emp_auto_id: employee.emp_auto_id || null,
                employee_id: employee.employee_id || '',
                employee_name: employee.employee_name || '',
                emp_type_id: employee.emp_type_id || '',
                hourly_employee: employee.hourly_employee,
                basic_amount: employee.basic_amount || 0,
                basic_hours: employee.basic_hours || 0,
                mobile_allowance: employee.mobile_allowance || 0,
                medical_allowance: employee.medical_allowance || 0,
                saudi_tax: employee.saudi_tax || 0,
                food_allowance: employee.food_allowance || 0,
                hourly_rate: employee.hourly_rent || 0
            });

            // Force Vue to re-render
           // this.$forceUpdate();

            $('#editModal').modal('show');
             // Show modal after data is set
            // this.$nextTick(() => {
            //     $('#editModal').modal('show');
            //     console.log('Modal shown, form data:', this.editForm);
            // });
             console.log('Employee object:', this.editForm);
            console.log('Basic amount:', employee.basic_amount);
        },

        async updateEmployeeSalary() {
            this.loadingEdit = true;
            this.errors = {};

            try {
                const response = await axios.post(`${UnapprovedEmpSalaryUpdateAPI}`, this.editForm);

                if (response.data.status == 200) {
                    $('#editModal').modal('hide');
                    this.showSweetAlert('Employee salary updated successfully', 'success');
                    await this.loadEmployees();
                } else {
                    this.showSweetAlert('Update failed', 'error');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    toast.error('Please check the form for errors');
                } else {
                    console.error('Error updating employee salary:', error);
                    this.showSweetAlert('Update failed', 'error');
                }
            } finally {
                this.loadingEdit = false;
            }
        },

        async deleteEmployee(empId) {
            if (!confirm('Are you sure?')) return;

            try {
                const response = await axios.delete(`${UnapprovedEmployeesDeleteAPI}/${empId}`);
                if (response.data.status === 200) {
                    this.showSweetAlert('Employee deleted successfully', 'success');
                    await this.loadEmployees();
                }
            } catch (error) {
                console.error('Error deleting employee:', error);
                this.showSweetAlert('Delete failed', 'error');
            }
        },

        getAttachmentUrl(path) {
            return this.awsS3BucketUrl + path;
        },

        truncateText(text, words) {
            if (!text) return '';
            const parts = text.split(' ');
            if (parts.length <= words) return text;
            return parts.slice(0, words).join(' ') + '...';
        },

        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('en-US', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },

        getReligionName(religionId) {
            const religions = {
                1: 'Muslim',
                2: 'Christianity',
                3: 'Hinduism'
            };
            return religions[religionId] || 'N/A';
        },

        showSweetAlert(message, type) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: type,
                title: message
            });
        }
    }
}
</script>

<style scoped>
.approve_button {
    background: #2B4049;
    color: #fff;
    font-size: 12px;
    padding: 3px 6px;
    border-radius: 5px;
}
.approve_button:hover {
    color: #fff;
}
.req_star {
    color: red;
}
</style>
