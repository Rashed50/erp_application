<template>
    <div class="col-md-12">
        <div class="card">
            <div class="row pt-2 form-group">
                <label class="col-md-3 control-label text-right">Employee Searching by</label>
                <div class="col-md-3">
                    <select v-model="searchBy" class="form-select">
                        <option value="employee_id">Employee ID</option>
                        <option value="akama_no">Iqama Number</option>
                        <option value="passfort_no">Passport</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <form @submit.prevent="searchEmployee" class="d-flex">
                        <input type="text" v-model="searchInpute" ref="input_employee_id"
                            placeholder="Enter ID/Iqama/Passport No" class="form-control" required
                            @keyup.enter="searchEmployee" :disabled="this.is_active_dynamic_search" />
                        <button type="submit" class="btn btn-primary ms-2">SEARCH</button>
                    </form>
                </div>
            </div>

            <!-- searching data -->
            <div v-if="this.is_searching == 1" class="loading-overlay">
                <div class="spinner-border text-primary"></div>
            </div>

            <!-- Employee Information -->
            <div class="row" v-else-if="this.is_searching == 2">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <div style="max-height: 300px !important;overflow-y: auto;">
                            <table id="employeeinfo" class="table table-bordered table-hover custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-center bg-primary text-white py-2">Employee Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="emp fw-bold">Emp. ID: </span>
                                            <span>{{ employeeDetails.employee_id }}</span>
                                        </td>
                                        <td>
                                            <a v-if="employeeDetails.profile_photo"
                                                :href="getAttachmentUrl(employeeDetails.profile_photo)" target="_blank">
                                                <i class="fas fa-eye fa-lg view_icon"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            <span>{{ employeeDetails.employee_name }}</span>
                                        </td>
                                        <td>
                                            <a v-if="employeeDetails.akama_photo"
                                                :href="getAttachmentUrl(employeeDetails.akama_photo)" target="_blank">
                                                <i class="fas fa-eye fa-lg view_icon"></i>
                                            </a>
                                            <span class="emp fw-bold">&nbsp; Iqama: </span>
                                            <span>{{ employeeDetails.akama_no }}, {{ employeeDetails.akama_expire_date
                                                }} </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp fw-bold">Job Status: </span>
                                            <span style="color:red;font-size: 14px;font: bold;">{{
                                                employeeDetails.job_status == 1 ? 'Active' : 'Inactive' }}, {{
                                                    employeeDetails.isNightShift == 0 ? "Day shift" : "Night Shift"
                                                }}</span>
                                        </td>

                                        <td>
                                            <span class="emp fw-bold">Project: </span>
                                            <span>{{ employeeDetails.proj_name }}</span>
                                        </td>

                                        <td>
                                            <span class="emp fw-bold">Home Contact: </span>
                                            <span>{{ employeeDetails.country_phone_no }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a v-if="employeeDetails.akama_photo"
                                                :href="getAttachmentUrl(employeeDetails.pasfort_photo)" target="_blank">
                                                <i class="fas fa-eye fa-lg view_icon"></i>
                                            </a>
                                            <span class="emp fw-bold">&nbsp;Passport: </span>
                                            <span>{{ employeeDetails.passfort_no }}</span>
                                        </td>
                                        <td>
                                            <span class="emp fw-bold">Trade: </span>
                                            <span>{{ employeeDetails.catg_name }}</span>
                                        </td>
                                        <td>
                                            <span class="emp fw-bold">Mobile: </span>
                                            <span>{{ employeeDetails.mobile_no }}</span>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="emp fw-bold">Agency: </span>
                                            <span>{{ employeeDetails.agc_title }}</span>
                                        </td>
                                        <td>
                                            <span class="emp fw-bold">Sponsor: </span>
                                            <span>{{ employeeDetails.spons_name }}</span>
                                        </td>
                                        <td>
                                            <span class="emp fw-bold">Joined: </span>
                                            <span>{{ employeeDetails.joining_date }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="emp fw-bold">Salary: </span>
                                            <span style="font-weight: bold; color: red; font-size: 18px;">
                                                {{ employeeDetails.hourly_employee == 1 ? 'Hourly' : 'Basic' }}
                                            </span>&nbsp;&nbsp;
                                            <span v-if="hasPermission('employee_salary_show_permission')"
                                                style="font-weight: bold; color: red; font-size: 18px;">
                                                {{ employeeDetails.hourly_employee == 1 ? employeeDetails.hourly_rent :
                                                    employeeDetails.basic_amount }}
                                            </span>
                                        </td>
                                        <td>
                                            AJEER:
                                            <a v-if="employeeDetails.ajeer_file"
                                                :href="getAttachmentUrl(employeeDetails.ajeer_file)" target="_blank"> <i
                                                    class="fas fa-eye fa-lg view_icon"></i>
                                            </a>

                                            <span class="emp fw-bold">&nbsp;,Gosi No: </span>
                                            <span>{{ employeeDetails.gosi_number != null ? employeeDetails.gosi_number :
                                                '--' }}</span>
                                        </td>
                                        <td>

                                            <span>Blood Group: {{ employeeDetails.blood_group != null ?
                                                employeeDetails.blood_group : '--' }}</span>
                                            <span class="emp fw-bold">&nbsp;,Email: {{ employeeDetails.email != null ?
                                                employeeDetails.email : '--' }}, </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <span class="emp fw-bold">Address: </span>
                                            {{ employeeDetails.country_name }} , {{ employeeDetails.division_name }} ,
                                            {{ employeeDetails.district_name }} ,{{ employeeDetails.details }}
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
</template>


<script>
import axios from 'axios';
import { inject } from 'vue';
import { toast } from 'vue3-toastify';
import { EmployeeSearchAPI } from "../../routes.js";
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')

const SingleAdvance = '';
export default {
    setup() {
        const { hasPermission } = useAuth();
        return {
            hasPermission,
        };
    },
    // props: {
    //     searched_employee_id: {
    //         type: Number,
    //         default: null
    //     }
    // },
    mounted() {
        this.searched_employee_id = 2879
        console.log('SearchComponent mounted with data:', this.searched_employee_id);
        if (this.searched_employee_id) {
            this.searchBy = "employee_id";
            this.searchInpute = this.searched_employee_id;
            this.searchEmployee();
        }
    },

    data() {
        return {
            is_active_dynamic_search: false,
            searchBy: "employee_id",
            searchInpute: "",
            is_searching: 0, // not performing anything, 1 = searching , 2= completed
            employeeDetails: {
                empId: '',
                akamaNo: '',
                name: '',
                jobStatus: '',
                category: '',
                passportNo: '',
                mobileNo: '',
                agencyName: '',
                sponsorName: '',
                projectName: ''
            },
            errors: {}, // For form validation errors
        }
    },

    computed: {

    },
    mounted() {
        // Focus when component is mounted
        this.$refs.input_employee_id.focus();
    },

    methods: {
        async searchEmployee() {

            // 1. Validation Checks
            if (this.searchInpute == '') {
                toast.error("Employee ID/Iqama/Passport is required!");
                return;
            }

            if (this.searchInpute.length <= 2) {
                toast.error("Invalid Input!");
                return;
            }
     

            const requestData = {
                search_by: this.searchBy,
                employee_searching_value: this.searchInpute
            };

            this.is_searching = 1;

            try {
                const response = await axios.post(EmployeeSearchAPI, requestData);

                // 2. Handle API-level "Not Found" (where status is 200 but success is false)
                if (response.data.success === false) {
                    this.is_searching = 0; // initial state
                    toast.error(response.data.message || "Employee not found");
                    this.$emit('searching_result', null);
                    return; // ⛔ IMPORTANT: Stop here!
                }
                // 2. Handle Success
                this.is_searching = 2;
                const data = response.data.findEmployee[0];
                this.employeeDetails = data;
                this.$emit('searching_result', data);

            } catch (error) {
                // 4. Handle Network/Server Errors (404, 500, etc.)
                this.is_searching = 0;
                toast.error(error.response.data.message || "Operation Failed");
                console.error("Error:", error);
            }
        },

        getAttachmentUrl(path) {
            return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
        },

    },


}
</script>

<style>
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
