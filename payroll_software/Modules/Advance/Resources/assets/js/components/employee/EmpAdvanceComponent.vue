<template>
    <div>
        <!-- Flash Messages -->
        <div v-if="flashMessage" class="row">
            <div class="col-md-8 offset-md-2">
                <div :class="`alert alert-${flashMessage.type === 'error' ? 'danger' : flashMessage.type}`"
                    role="alert">
                    <strong>{{ flashMessage.message }}</strong>
                    <button type="button" class="close" aria-label="Close" @click="closeFlashMessage">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>



        <!-- Advance Menu Section -->
        <div class="row mb-1">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-sm-1">
                                <button @click.prevent="openSection('single')" class="btn btn-primary" v-if="hasPermission('employee_advance_insert')">Single</button>
                            </div>

                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                                <button @click.prevent="handleButtonClick('multiple')" v-if="hasPermission('mult_employee_advance_insert')"
                                    class="btn btn-primary">Multiple</button>
                            </div>

                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                                <button @click.prevent="openSection('search')" class="btn btn-primary" v-if="hasPermission('advance_paper_upload_search')">Search</button>
                            </div>
                            <div class="col-sm-1">
                                 <button @click.prevent="openSection('process')" class="btn btn-primary" v-if="hasPermission('employee-advance-processing')">Process</button></div>
                            <div class="col-sm-1">
                                <button @click.prevent="openSection('report')" class="btn btn-primary" v-if="hasPermission('emp_advance_paid_report')" >Report</button>
                            </div>

                            <!--
                            <div class="col-sm-2">
                                <button @click.prevent="openSection('paperUpload')" class="btn btn-primary">Paper Upload</button>
                            </div>

                            <div class="col-sm-2">
                                <button @click.prevent="openCashReceivedModal" class="btn btn-primary">Cash Received Form</button>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Single Employee Advance Insert Section -->
        <div v-if="currentSection === 'single'" class="row">
            <employee-advance-single :data="data"></employee-advance-single>
        </div>


        <!-- Multiple Employees Advance Section -->
        <div v-if=" currentSection==='multiple'" class=" row  mt-1">
            <multiple-employee-advance :data="data"></multiple-employee-advance>
        </div>


        <!-- Employee Advance Search Section -->
        <div v-if="currentSection === 'search'" class="row">
            <employee-advance-search :data="data"></employee-advance-search>
        </div>

        <div v-if="currentSection === 'process'" class="row">
            <advance-process-component :data="data"></advance-process-component>
        </div>



        <!-- Advance Report Section -->
        <div v-if="currentSection === 'report'" class="row">
            <advance_report_page :data="data"></advance_report_page>
        </div>


    </div>
</template>

<script>
import { inject } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { EmpListProjectWise, MultipleEmpAdvance } from "../../routes.js";
import AdvanceProcessComponent from '../employee/AdvanceProcessComponent.vue';
import MultipleEmployeeAdvance from '../employee/MultipleEmployeeAdvance.vue';

import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')

export default {
    components: {
        'advance-process-component': AdvanceProcessComponent,
        'multiple-employee-advance': MultipleEmployeeAdvance,
    },
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
            adv_date: '',
            adv_remarks: '',
            advance_paper: null,
            fileType: '',
            fileName: '',
            is_data_found: true,
            updating: false,
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
        openSection(section) {
            this.currentSection = section;
        },
        handleButtonClick(section) {
            this.openSection(section);
            
        },

        openCashReceivedModal() {
            // Handle modal logic here
        },

        searchProjectWiseEmployeeList() {

            const employeeId = this.multi_emp_id;
            const projectId = this.proj_id;

            if (!employeeId && !projectId) {
                this.flashMessage = {
                    message: "Please Input Multiple Employee ID Or Select Project Name",
                    type: "error"
                };

                setTimeout(() => {
                    this.flashMessage = null;
                }, 3000);
                return;
            }

            this.flashMessage = null;

            const requestData = {
                project_id: projectId,
                multi_emp_id: employeeId
            };

            axios.post(EmpListProjectWise, requestData)
                .then(response => {
                    if (response.data.success) {
                        this.projectEmployeeList = response.data.empList;
                        // console.log("Received employee list:", this.projectEmployeeList);
                        // this.loading = false;
                        this.is_data_found = true;
                    } else {
                        this.projectEmployeeList = [];
                        // console.log("No data found:", response.data.error);
                        // this.loading = false;
                         this.is_data_found = false;
                    }
                })
                .catch(error => {
                    console.error("Error:", error.response ? error.response.data : error.message);
                    // this.loading = false;
                });
        },

        searchProjectWiseEmployeeID(){
            if (this.multi_emp_id.length >2){
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
