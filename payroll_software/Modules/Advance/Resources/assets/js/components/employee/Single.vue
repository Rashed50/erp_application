<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header mt-2">
                <h5 class="card-title">Single Employee Advance</h5>
            </div>
            <div class="row form-group">
                <div class="col-md-2"></div>
                <label class="col-md-2 control-label">Employee Searching by</label>
                <div class="col-md-3">
                    <select v-model="searchBy" class="form-select">
                        <option value="employee_id">Employee ID</option>
                        <option value="akama_no">Iqama Number</option>
                        <option value="passport_no">Passport</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <form @submit.prevent="searchEmployee" class="d-flex">
                        <input type="text" v-model="searchInpute" ref="searchInput"
                            placeholder="Enter ID/Iqama/Passport No" class="form-control" required
                            @keyup.enter="searchEmployee" />
                        <button type="submit" class="btn btn-primary ms-2">SEARCH</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Employee Information and Advance Insert Form -->
        <div v-if="showEmployeeDetails" class="card">
            <div class="row">

                <!-- Employee Information -->
                <div class="col-md-6">
                    <div class="emp_id_show text-center">
                        <span class="req_star" style="font-size: 20px;">Employee ID {{ employeeDetails.empId }}
                        </span>
                    </div>

                    <table class="table table-bordered table-striped table-hover w-100" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center bg-primary text-white py-2">Employee Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="emp fw-bold">Iqama: </span>
                                    <span>{{ employeeDetails.akamaNo }}</span>
                                </td>
                                <td>
                                    <span class="emp fw-bold">Name: </span>
                                    <span>{{ employeeDetails.name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="emp fw-bold">Emp. Status: </span>
                                    <span>{{ employeeDetails.jobStatus}}</span>
                                </td>
                                <td>
                                    <span class="emp fw-bold">Trade: </span>
                                    <span>{{ employeeDetails.category }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="emp fw-bold">Passport: </span>
                                    <span>{{ employeeDetails.passportNo}}</span>
                                </td>
                                <td>
                                    <span class="emp fw-bold">Mobile: </span>
                                    <span>{{ employeeDetails.mobileNo }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="emp fw-bold">Agency: </span>
                                    <span>{{ employeeDetails.agencyName}}</span>
                                </td>
                                <td>
                                    <span class="emp fw-bold">Sponsor: </span>
                                    <span>{{ employeeDetails.sponsorName}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="emp fw-bold">Salary: </span>
                                    <span style="font-weight: bold; color: red; font-size: 18px;">
                                        {{ employeeDetails.hourly_employee == 1 ? 'Hourly' : 'Basic Salary' }}
                                    </span>&nbsp;&nbsp;
                                    <span v-if="hasPermission('employee_salary_show_permission')"
                                        style="font-weight: bold; color: red; font-size: 18px;">
                                        {{ employeeDetails.hourly_employee == 1 ? employeeDetails.hourly_rent :
                                        employeeDetails.basic_amount }}
                                    </span>
                                </td>
                                <td>
                                    <span class="emp fw-bold">Working at: </span>
                                    <span>{{ employeeDetails.projectName }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <!-- Advance Insert User Interface -->
                <div class="col-md-6" style="border-left: 2px solid rgb(75, 64, 64);">
                    <form @submit.prevent="submitForm" class="form-horizontal" enctype="multipart/form-data">
                        <div class="card-body card_form row">
                            <input type="hidden" v-model="employeeDetails.empId" class="form-control" name="emp_id"
                                required />

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label"> Working Project:</label>
                                <div class="col-sm-7">
                                    <select v-model="projectId" class="form-select" required>
                                        <option value="">Select Working Project</option>
                                        <option v-for="project in projects" :key="project.proj_id"
                                            :value="project.proj_id">{{ project.proj_name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label"> Purpose:</label>
                                <div class="col-sm-7">
                                    <select v-model="advPurposeId" class="form-select" required>
                                        <option value="">Select Purpose</option>
                                        <option v-for="p in data.purpose" :key="p.id" :value="p.id">{{
                                            p.purpose }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Amount: <span class="req_star">*</span></label>
                                <div class="col-sm-4">
                                    <input type="number" v-model="advAmount" class="form-control"
                                        placeholder="Input Amount" step="1" min="1" required />
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Advance Date:</label>
                                <div class="col-sm-7">
                                    <input type="date" v-model="advDate" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Remarks:</label>
                                <div class="col-sm-7">
                                    <input type="text" v-model="remarks" class="form-control" placeholder="Remarks" />
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Upload:</label>
                                <div class="col-sm-7">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                <!-- Browse… <input type="file" @change="handleFileUpload" ref="fileInput" /> -->
                                                Browse… <input type="file" @change="handleFileUpload" ref="fileInput" accept="image/*,.pdf">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" :value="fileName" readonly />
                                    </div>
                                </div>

                            </div>


                        </div>

                        <div style="text-align:center; padding-bottom:10px">
                            <button type="submit" id="submit_btn"  class="btn btn-primary waves-effect">SAVE</button>
                        </div>
                    </form>
                    <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label" v-if="uploadedFilePreview != '' " >Preview:</label>
                                <!-- <div class="col-sm-9">
                                     <img :src="uploadedFilePreview" class="upload_image" />
                                </div> -->
                                <div class="col-sm-9">
                                    <!-- Image Preview -->
                                    <img v-if="fileType === 'image'"
                                        :src="uploadedFilePreview"
                                        class="upload_image"
                                        style="width:100%; max-height:300px; object-fit:contain;" />
                                    <!-- PDF Preview -->
                                    <embed v-if="fileType === 'pdf'"
                                        :src="uploadedFilePreview"
                                        type="application/pdf"
                                        width="100%"
                                        height="300px" />

                                    <!-- Other File -->
                                    <div v-if="fileType === 'other'" class="text-center">
                                        <p>Selected File: <strong>{{ fileName }}</strong></p>
                                    </div>

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
import { SearchEmployee, SingleAdvance } from "../../routes.js";
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();
        return {
            hasPermission,
        };
    },
    props: {
        data: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            searchBy: "employee_id",
            searchInpute: "",
            showEmployeeDetails: false,

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

            projectId: '',
            advPurposeId: '',
            advAmount: '',
            installesMonth: 1,
            advDate: new Date().toISOString().split('T')[0], // Default current date
            remarks: '',
            uploadedFilePreview: '',
            fileType: '',
            fileName: '',

            // Props Data set
            projects: this.data.projects || [],  // Initialize from props
            purposes: this.data.purpose || [],   // Initialize from props
            errors: {}, // For form validation errors
        }
    },

    computed: {

    },

    methods: {
        async searchEmployee() {

            if (this.searchInpute == '') {
                toast.error("Employee ID/Iqama/Passport is required!");
            } else if (this.searchInpute.length <= 2) {
                toast.error("Invalid Inpute!")
            }

            const requestData = {
                search_by: this.searchBy,
                employee_searching_value: this.searchInpute
            }

            // console.log("Paylode =", requestData);

            await axios.post(SearchEmployee, requestData)
                .then(response => {


                    if (response.data.success == false) {
                        this.showEmployeeDetails = false;
                        this.resetForm();
                        toast.error(response.data.message || "Failed to delete record");
                        return;
                    }

                        const data = response.data.findEmployee[0];

                        this.employeeDetails = {
                            empId: data.employee_id,
                            name: data.employee_name,
                            akamaNo: data.akama_no,
                            jobStatus: data.title,
                            category: data.catg_name,
                            passportNo: data.passfort_no,
                            mobileNo: data.mobile_no,
                            agencyName: data.agc_title,
                            sponsorName: data.spons_name,

                            hourly_employee: data.hourly_employee,
                            hourly_rent: data.hourly_rent,
                            basic_amount: data.basic_amount,

                            projectName: data.proj_name,
                        }

                        this.showEmployeeDetails = true;

                })
                .catch(error => {
                    console.error("Error:", error.response ? error.response.data : error.message);
                    // this.loading = false;
                });
        },

        async submitForm() {
            try {
                // Validate required fields
                if (!this.employeeDetails.empId || !this.projectId || !this.advPurposeId ||
                    !this.advAmount || !this.installesMonth || !this.advDate) {
                    toast.error("Please fill all required fields");
                    return;
                }
                 submit_btn.disabled = true;

                // Prepare form data according to API requirements
                const formData = new FormData();
                formData.append('emp_id', this.employeeDetails.empId);
                formData.append('project_id', this.projectId);
                formData.append('adv_purpose_id', this.advPurposeId);
                formData.append('adv_amount', this.advAmount);
                formData.append('installes_month', this.installesMonth);
                formData.append('adv_date', this.advDate);
                formData.append('adv_remarks', this.remarks);

                // Append file with the correct field name that API expects
                if (this.$refs.fileInput && this.$refs.fileInput.files[0]) {
                    formData.append('advance_paper', this.$refs.fileInput.files[0]);
                }

                // Send POST request
                const response = await axios.post(SingleAdvance, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    toast.success(response.data.message || "Advance payment submitted successfully!");
                    this.resetForm();
                } else {
                    toast.error(response.data.message || "Failed to submit advance payment");
                }
                submit_btn.disabled = false;


            } catch (error) {
                console.error("Error submitting form:", error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    "An error occurred while submitting";
                toast.error(errorMessage);
            }
        },

        resetForm() {
            this.projectId = '';
            this.purpose = '';
            this.advAmount = '';
            this.installesMonth = 1; // Reset to default value
            this.advDate = new Date().toISOString().slice(0, 10);
            this.remarks = '';
            this.uploadedFilePreview = '';
            this.fileType = '';
            this.fileName = '';
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        handleFileUpload(event) {
             const file = event.target.files[0];
             if (!file) {
                 this.uploadedFilePreview = '';
                 this.fileType = '';
                 this.fileName = '';
                 return;
             }

                this.fileName = file.name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploadedFilePreview = e.target.result;
                    if (file.type.startsWith('image/')) {
                        this.fileType = 'image';
                    }
                    else if (file.type === 'application/pdf') {
                        this.fileType = 'pdf';
                    }
                    else {
                        this.fileType = 'other';
                    }
                };

                reader.readAsDataURL(file);
        }
    },

    mounted() {
        this.$nextTick(() => {
            this.$refs.searchInput.focus();
        });
    },
}
</script>

<style></style>
