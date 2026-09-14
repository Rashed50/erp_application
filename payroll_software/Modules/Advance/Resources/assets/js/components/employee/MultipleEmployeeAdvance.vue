<template>
    <div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <h5 class="card-title">Multiple Employees Advance Form</h5><br><br>
                        <!-- <label class="col-md-1 control-label" hidden>Project:</label> -->
                        <div class="col-md-3">
                            <select class="form-select" id="proj_name" v-model="proj_id" @change="searchProjectWiseEmployeeList"  >
                                <option value="">Select Project</option>
                                <option v-for="project in data.projects" :key="project.proj_id"
                                    :value="project.proj_id">
                                    {{ project.proj_name }}
                                </option>
                            </select>
                        </div>

                        <label for="orlabel" class="col-md-1 control-label">OR</label>
                        <div class="col-md-3 d-flex">
                            <input type="text" v-model="multi_emp_id" ref="multiEmpIdInput" class="form-control"  placeholder="Input Multiple Employee ID (e.g 1231,1101)"  @keyup.enter="searchProjectWiseEmployeeID" autofocus>
                        </div>

                        <label class="col-md-1 control-label text-end">Adv. Date:</label>
                        <div class="col-md-2">
                            <input type="date" v-model="search_adv_date" class="form-control" required />
                        </div>
                        <div class="col-md-2">

                            <button type="submit" @click.prevent="searchProjectWiseEmployeeID" class="btn text-white px-4 py-2"
                                style="background-color: #2c3e50; border-radius: 4px; font-weight: 500;">
                                <i v-if="is_data_loading" class="fas fa-spinner fa-spin me-2"></i>
                                {{ is_data_loading ? 'Searching...' : 'Search' }}
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitMultipleEmployeeAdvanceForm" class="form-horizontal" enctype="multipart/form-data"
                v-if="projectEmployeeList.length">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-header">
                                <div class="row">
                                    <input type="hidden" class="form-control" name="adv_project_id"
                                        id="adv_project_id" readonly>

                                    <label class="col-md-1 control-label"> Purpose</label>
                                    <div class="col-md-3">
                                        <select v-model="adv_purpose_id" class="form-select"
                                            name="adv_purpose_id" autofocus required>
                                            <option value="">Select Purpose</option>
                                            <option v-for="p in data.purpose" :key="p.id" :value="p.id">
                                                {{  p.purpose }}</option>
                                        </select>
                                    </div>
                                    <label class="col-md-1 control-label text-right"> Adv.Date</label>
                                    <div class="col-md-2">
                                        <input v-model="adv_date" type="date" class="form-control"
                                            name="adv_date" value="" required :disabled="true">
                                    </div>
                                    <label class="col-md-1 control-label">Remarks:</label>
                                    <input v-model="adv_remarks" type="text" class="form-control col-md-2"
                                                    placeholder="Remarks" name="adv_remarks" value="" required>
                                    <div class="col-md-12 d-flex justify-content-end">
                                            <button type="submit" id="multi_adv_save_btn" v-if="updating == false" class="btn btn-primary waves-effect">Save Advance</button>
                                    </div>
                                </div>
                                <!-- file upload section -->
                                <div class="row">
                                    <label class="col-md-1 control-label text-right">Advance Paper</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <span class="btn btn-default btn-file btnu_browse">
                                                    Browse… <input type="file" @change="handleFileUpload"  accept="image/*,.pdf">
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" :value="fileName" readonly>
                                        </div>

                                    </div>
                                    <div class="col-md-4 d-flex text-end">
                                        <h4><strong>Selected:</strong> {{ totalPersons }},&nbsp;&nbsp;</h4>&nbsp;
                                        <h4><strong>Total Amount:</strong> {{ totalAmount }}</h4>
                                    </div>
                                </div>
                                <!-- file preview section -->
                                <div class="form-group row custom_form_group">

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
                                            <p>Selected File: <strong>Preview is not possible,  {{ fileName }}</strong></p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">

                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Emp.ID</th>
                                        <th>Name</th>
                                        <th>Iqama No</th>
                                        <th>Designation</th>
                                        <th>Salary</th>
                                        <th>Amount</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(employee, index) in projectEmployeeList"
                                        :key="employee.emp_auto_id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ employee.employee_id }}</td>
                                        <td>{{ employee.employee_name }}</td>
                                        <td>{{ employee.akama_no }}</td>
                                        <td>{{ employee.catg_name }}</td>
                                        <td>{{ employee.hourly_employee == 1 ? 'Hourly':'Basic'  }}</td>
                                        <td>
                                            <input type="text" v-model="employee.advance_amount"
                                                placeholder="Advance amount" style="width: 120px;" :disabled="employee.operation_lock" />
                                            <br>    {{ employee.operation_lock ? employee.operation_message:'' }}
                                        </td>
                                        <td><input type="checkbox" v-model="employee.selected" :disabled="employee.operation_lock"  /></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </form>

            <h2 class="text-danger fs-bold text-center"
                v-if="!projectEmployeeList.length && !this.is_data_found">Data Not Found!</h2>
        </div>
    </div>
</template>

<script>
import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { EmpListProjectWise, MultipleEmpAdvance } from "../../routes.js";
// import MultipleEmployeeAdvance from '../employee/MultipleEmployeeAdvance.vue';

import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')

export default {

    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    data() {
        return {
            // loading: false,
            flashMessage: null,
            currentSection: null,
            searchBy: "employee_id",
            employeeInfo: "",
            showEmployeeDetails: false,
            employeeDetails: {},
            advanceAmount: "",
            purpose: "",
            purposes: [], // Load from API or props
            advanceDate: new Date().toISOString().slice(0, 10),
            remarks: "",

            // Multiple Employees Advance Section
            multi_emp_id: "",
            proj_id: "",

            projectEmployeeList: [],
            adv_purpose_id: '',
            search_adv_date: '',
            adv_date: '',
            adv_remarks: '',
            advance_paper: null,
            fileType: '',
            fileName: '',
            is_data_found: true,
            updating: false,
            is_data_loading: false,
        };
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },

    computed: {
        totalPersons() {
            return this.projectEmployeeList.filter(emp => emp.selected).length;
        },
        totalAmount() {
            return this.projectEmployeeList.reduce((sum, emp) => {
                return emp.selected ? sum + (parseFloat(emp.advance_amount) || 0) : sum;
            }, 0);
        }

    },

    methods: {
        // openSection(section) {
        //     this.currentSection = section;
        // },
        // handleButtonClick(section) {
        //     this.openSection(section);
        //     this.$nextTick(() => {
        //         if (section === 'multiple') {
        //             this.$refs.multiEmpIdInput.focus();
        //         }
        //     });
        // },



        searchProjectWiseEmployeeList() {

            const employeeId = this.multi_emp_id;
            const projectId = this.proj_id;

            console.log("Searching with Employee ID:", employeeId, "date ", this.search_adv_date);
            if ( this.search_adv_date == null || this.search_adv_date == '') {
                toast.error("Please Select Advance Date.");
                return;
            }
            else if ( !employeeId && !projectId) {

                toast.error("Please Select Project or Input Employee ID.");
                return;
            }


            const requestData = {
                project_id: projectId,
                multi_emp_id: employeeId,
                adv_date: this.search_adv_date,
            };
            this.projectEmployeeList = [];
            this.is_data_loading = true;

            axios.post(EmpListProjectWise, requestData)
                .then(response => {
                    if (response.data.success) {
                        this.projectEmployeeList = response.data.data;
                        this.is_data_found = true;
                        this.adv_date = this.search_adv_date;
                    } else {
                        this.projectEmployeeList = [];
                        // console.log("No data found:", response.data.error);
                        // this.loading = false;
                         this.is_data_found = false;
                    }
                    this.is_data_loading = false;
                })
                .catch(error => {
                    console.error("Error:", error.response ? error.response.data : error.message);
                    // this.loading = false;
                    this.is_data_loading = false;
                });
        },

        searchProjectWiseEmployeeID(){
            if ( this.search_adv_date == null || this.search_adv_date == '') {
                toast.error("Please Select Advance Date.");
                return;
            }
            else if ( !this.proj_id || this.multi_emp_id.length >2) {
                this.searchProjectWiseEmployeeList()

            }
        },

        handleFileUpload(event) {
             this.advance_paper = event.target.files[0];
             if (!this.advance_paper) {
                 this.uploadedFilePreview = '';
                 this.fileType = '';
                 this.fileName = '';
                 return;
             }


                this.fileName = this.advance_paper.name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploadedFilePreview = e.target.result;
                    if (this.advance_paper.type.startsWith('image/')) {
                        this.fileType = 'image';
                    }
                    else if (this.advance_paper.type === 'application/pdf') {
                        this.fileType = 'pdf';
                    }
                    else {
                        this.fileType = 'other';
                    }
                };

                reader.readAsDataURL(this.advance_paper);
        },
        resetForm() {
            this.adv_purpose_id = '';
            this.adv_date = new Date().toISOString().slice(0, 10);
            this.adv_remarks = '';
            this.advance_paper = null;
            this.projectEmployeeList.forEach(emp => {
                emp.selected = false;
                emp.advance_amount = '';
            });
            this.multi_emp_id = '';
            this.proj_id = '';
            this.uploadedFilePreview = '';
            this.fileType = '';
            this.fileName = '';
            this.updating = false;

        },

       async submitMultipleEmployeeAdvanceForm() {
            const selectedEmployees = this.projectEmployeeList.filter(employee => employee.selected);
            if (selectedEmployees.length === 0) {
                toast.error("At least one employee must be selected.");
                return;
            }else if(!this.adv_date){
                toast.error("Please fill all required fields.");
                return;
            }
            this.updating = true;

            const formData = new FormData();
            formData.append('adv_purpose_id', this.adv_purpose_id);
            formData.append('adv_date', this.adv_date);
            formData.append('adv_remarks', this.adv_remarks);

            if (this.advance_paper) {
                formData.append('advance_paper', this.advance_paper);
            }
            this.projectEmployeeList.forEach((employee, index) => {
                if (employee.selected) {
                    formData.append(`emp_auto_id[${index}]`, employee.emp_auto_id);
                    formData.append(`adv_checkbox-${employee.emp_auto_id}`, employee.selected);
                    formData.append(`adv_amount-${employee.emp_auto_id}`, employee.advance_amount);
                }
            });

            axios.post(MultipleEmpAdvance, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        toast.success(response.data.message);
                        this.projectEmployeeList = [];
                        this.resetForm(); // Reset the form after successful submission
                    } else if (response.data && !response.data.success) {
                        toast.error(response.data.message);
                        this.updating = false;
                    } else {
                        toast.error("Unexpected error occurred.");
                        this.updating = false;
                    }

                })
                .catch(error => {
                    console.error("Error:", error);
                    toast.error("Failed to submit the form. Please try again.");
                    this.updating = false;
                });
        }
    },

    watch: {
        flashMessage(newValue) {
            if (newValue && newValue.type === "error") {
                toast.error(newValue.message);
            }
        }
    }
};
</script>



<style scoped>
/* Add any specific styles here */
/* Loading Screen Styles */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
