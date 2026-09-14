<template>
    <div class="container-fluid">


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <label class="col-md-1 col-form-label">Project</label>
                            <div class="col-md-3">
                                <select class="form-select" v-model="searchForm.project_id">
                                    <option value="">Select Project</option>
                                    <option v-for="proj in projects" :key="proj.proj_id" :value="proj.proj_id">
                                        {{ proj.proj_name }}
                                    </option>
                                </select>
                            </div>
                            <label class="col-md-2 col-form-label">OR Emp. ID</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" v-model="searchForm.multi_emp_id"
                                    placeholder="Multiple Employee ID Here" autofocus
                                    @keyup.enter="searchProjectWiseEmployeeList()" />
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary waves-effect w-100" :disabled="isSearching"
                                    @click="searchProjectWiseEmployeeList()">
                                    <span v-if="isSearching">   <i class="fas fa-spinner fa-spin"></i> Searching... </span>
                                    <span v-else>Search</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-if="showTable">
            <div class="col-lg-12">
                <div class="card">
                    <!-- <form  novalidate @submit.prevent="submitTransfer"> -->

                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <label class="col-sm-2 col-form-label">Assign Project</label>
                                <div class="col-sm-2">
                                    <select class="form-select" v-model="transferForm.assigned_project" required>
                                        <option value="">Select Project</option>
                                        <option v-for="proj in projects" :key="proj.proj_id" :value="proj.proj_id">
                                            {{ proj.proj_name }}
                                        </option>
                                    </select>
                                </div>

                                <label class="col-sm-1 col-form-label">Date</label>
                                <div class="col-sm-2">
                                    <input type="date" class="form-control" v-model="transferForm.asign_date"
                                        required />
                                </div>

                                <label class="col-sm-1 col-form-label">Remarks</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" v-model="transferForm.remarks" />
                                </div>

                                <div class="col-sm-2 d-flex gap-2">
                                     <button type="button" @click="submitTransfer()"  class="btn btn-primary waves-effect flex-fill"
                                        :disabled="isSubmitting">
                                        <span v-if="isSubmitting"> <i class="fas fa-spinner fa-spin"></i> Transferring...</span>
                                        <span v-else>Transfer</span>
                                    </button>                                     
                                    <!-- <button @change="toggleSelectAll()"  class="btn btn-primary btn-sm">
                                        {{ allSelected ? 'Unselect All' : 'Select All' }}
                                    </button> -->
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover custom_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Iqama</th>
                                                    <th>Trade</th>
                                                    <th>Salary</th>
                                                    <th>Project</th>
                                                    <th>Status</th>
                                                    <th style="width: 120px;">
                                                       <input type="checkbox" v-model="selectAll" />  Select All
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(emp, index) in employeeList" :key="emp.emp_auto_id">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>{{ emp.employee_id }}</td>
                                                    <td>{{ emp.employee_name }}</td>
                                                    <td>{{ emp.akama_no }}</td>
                                                    <td>{{ emp.catg_name }}</td>
                                                    <td>{{ emp.hourly_employee == 1 ? 'Hourly' : 'Basic Salary' }}</td>
                                                    <td>{{ emp.proj_name }}</td>
                                                    <td>
                                                        <span
                                                            :class="emp.job_status == 1 ? 'badge bg-success' : 'badge bg-secondary'">
                                                            {{ emp.job_status == 1 ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>                                                                                                                
                                                        <input type="checkbox" v-model="selectedEmployees" :value="emp.emp_auto_id" />
                                                    </td>
                                                </tr>
                                                <tr v-if="employeeList.length === 0">
                                                    <td colspan="9" class="text-center text-muted">No Employees Found!
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- </form> -->
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { EmpTransferSearchAPI, EmpTransferSubmitAPI } from '../../routes.js';

const today = new Date().toISOString().split('T')[0];

export default {
    name: 'EmployeeTransferComponent',
    props: {
        data: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            showTable: false,
            isSearching: false,
            isSubmitting: false,

            projects: [],
            employeeList: [],


            searchForm: {
                project_id: '',
                multi_emp_id: '',
            },

            transferForm: {
                assigned_project: '',
                asign_date: today,
                remarks: '',
                selectedEmployeeIds: [],
            },
            selectedEmployees: []
        };
    },
    computed: {
        selectAll: {
            get() {
                return this.employeeList.length > 0 && 
                       this.selectedEmployees.length === this.employeeList.length;
            },
            set(value) {
                if (value) {
                    this.selectedEmployees = this.employeeList.map(emp => emp.emp_auto_id);
                } else {
                    this.selectedEmployees = [];
                }
            }
        },
        allSelected() {
            return this.employeeList.length > 0 && 
                   this.selectedEmployees.length === this.employeeList.length;
        }
    },
    mounted() {
        this.initializeProjects();
    },
    methods: {
        initializeProjects() {
            if (Array.isArray(this.data)) {
                this.projects = this.data;
            } else if (this.data && this.data.projects) {
                this.projects = this.data.projects;
            }
        },
         

        async searchProjectWiseEmployeeList() {
            
                
            if(this.searchForm.project_id == '' && this.searchForm.multi_emp_id == ''){
                toast.error('Please Select Project or\n Enter Employee IDs'+this.searchForm.project_id);
                return;
            }
            this.isSearching = true;
            const requestData = {
                'multi_emp_id': this.searchForm.multi_emp_id,
                "project_id": this.searchForm.project_id

            }
             
            const response = await axios.get(EmpTransferSearchAPI, {
                 params:requestData
            })

            if (response.data.status == 200) {
                this.employeeList = response.data.data;
                this.showTable = true;
            }else{
                toast.error('Employee not Found');
            }
            this.isSearching = false;

        },

      

        async submitTransfer() {
             
            console.log("Selected Employees for Transfer:", this.selectedEmployees);
            if (this.selectedEmployees.length === 0) {
                toast.error('Please select at least one employee to transfer!');
                return;
            }
            if (!this.transferForm.assigned_project) {
                 toast.error('Please Select Project For Transfer Employees!');
                return;
            }

            this.isSubmitting = true;

            try {

                let formData = new FormData();
                formData.append('assigned_project', this.transferForm.assigned_project);
                formData.append('assign_date', this.transferForm.asign_date);
                formData.append('remarks', this.transferForm.remarks);
                formData.append('emp_auto_id', this.selectedEmployees);
                const response = await axios.post(EmpTransferSubmitAPI, formData);

                if (response.data.success || response.data.status === 200) {                   

                    this.selectedEmployees = [];
                    this.transferForm.assigned_project = '';
                    this.transferForm.remarks = '';
                    toast.success('Employees transferred successfully!');
                  //  this.searchProjectWiseEmployeeList();
                  this.showTable = false;
                } else {
                     toast.error(response.data.message || 'Operation Failed, Please try again!');
                }

            } catch (error) {                 
                toast.error('Operation Failed, Refresh Page and Please try again.');
            } finally {
                this.isSubmitting = false;
            }
        },

        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedEmployees = [];
            } else {
                this.selectedEmployees = this.employeeList.map(emp => emp.emp_auto_id);
            }
        }

        
    },
};
</script>

<style scoped>
.col-form-label {
    font-weight: 600;
    color: #495057;
}
</style>