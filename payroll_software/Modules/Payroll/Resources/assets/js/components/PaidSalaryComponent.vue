<template>
    <!-- Top Bar -->


    <!-- Searching UI Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- <form @submit.prevent="searchPaidSalaryRecords"> -->
                    <div class="row">
                        <div class="form-group row custom_form_group">
                            <label class="col-md-1 control-label">Sponsor:</label>
                            <div class="col-md-3">
                                <select class="form-select" v-model="searchForm.spons_id">
                                    <option value="">Select Sponsor</option>
                                    <option v-for="sponsor in sponsor_list" :key="sponsor.spons_id"
                                        :value="sponsor.spons_id">
                                        {{ sponsor.spons_name }}
                                    </option>
                                </select>
                            </div>
                            <label class="col-md-1 control-label">Project:</label>
                            <div class="col-md-3">
                                <select class="form-select" v-model="searchForm.proj_id">
                                    <option value="">Select Project</option>
                                    <option v-for="project in project_list" :key="project.proj_id"
                                        :value="project.proj_id">
                                        {{ project.proj_name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-1 control-label">From:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control fromDate" v-model="searchForm.fromDate" required>
                            </div>
                            <label class="col-sm-1 control-label">To:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control toDate" v-model="searchForm.toDate" required>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" placeholder="Search By Employee ID" autofocus
                                    v-model="searchForm.searchValue" name="employee_id">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" style="margin-top: 2px" class="btn btn-primary waves-effect"
                                    @click="searchPaidSalaryRecords()" :disabled="isSearching">
                                    <span v-if="isSearching"><i class="fas fa-spinner fa-spin"></i>
                                        Searching...</span>
                                    <span v-else>Search</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
            </div>

            <!-- Searching Result Table -->
            <div class="row" v-if="showTable">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-3"></div>

                            <div class="col-md-2">
                                <label class="control-label">
                                    Selected: {{ selectedRecords.length }}
                                </label>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">
                                    Total: {{ totalSelectedSalary }}
                                </label>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary waves-effect"
                                    @click="updateMultipleEmpSalaryStatusAsPaidToUnpaid()"
                                    v-if="hasPermission('monthly_salary_paid_to_unpaid_permission')"
                                    :disabled="selectedRecords.length === 0">
                                    Update as Unpaid
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Iqama</th>
                                            <th>Sponsor</th>
                                            <th>Trade</th>
                                            <th>Worked</th>
                                            <th>Month</th>
                                            <th>Method</th>
                                            <th>Paid By</th>
                                            <th>Salary</th>
                                            <th class="text-center" style="width: 120px;">
                                                <input type="checkbox" @change="toggleSelectAll"
                                                    :checked="allSelected" /> Select All
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(value, index) in records"
                                            :key="value.slh_auto_id || value.emp_auto_id || index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ value.employee_id }}</td>
                                            <td>{{ value.employee_name }}</td>
                                            <td>{{ value.akama_no }}</td>
                                            <td>{{ value.spons_name }}</td>
                                            <td>{{ value.catg_name }}</td>
                                            <td>{{ value.proj_name }}</td>
                                            <td> {{ getMonthName(value.slh_month) }}



                                                <!-- <span v-for="month in 12" :key="month">
                                                    <span v-if="value.slh_month === month">
                                                        {{ new Date(2000, month - 1).toLocaleString('en-US', {
                                                            month:
                                                                'long'
                                                        }) }}
                                                    </span>
                                                </span> -->
                                                , {{ value.slh_year }}<br>
                                                <small class="text-muted" v-if="value.slh_salary_date">Paid at: {{
                                                    value.slh_salary_date }}</small>
                                            </td>
                                            <td>
                                                <span class="badge"
                                                    :class="value.slh_paid_method ? 'bg-info' : 'bg-success'">
                                                    {{ value.slh_paid_method == null ? 'Cash Paid' : 'Bank Paid' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ value.paid_by_name }}<br>
                                                <small class="text-muted" v-if="value.updated_at">Updated: {{
                                                    value.updated_at }}</small>
                                            </td>
                                            <td>{{ value.slh_total_salary }}</td>
                                            <td class="text-center">
                                                <input type="checkbox" v-model="selectedRecords"
                                                    :value="value.slh_auto_id || value.emp_auto_id" />
                                            </td>
                                        </tr>
                                        <tr v-if="records.length === 0">
                                            <td colspan="12" class="text-center">No records found.</td>
                                        </tr>
                                    </tbody>
                                </table>
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
import { useAuth } from '../../../../../../resources/js/components/useAuth';
import { toast } from 'vue3-toastify';
import { EmployeeSalaryPaidListAPI, EmployeeSalaryPaymentUndoStatusAPI } from '../routes';
const month_names = [
    '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'
]

export default {
    props: {
        sponsor_list: {
            type: Array,
            required: true
        },
        project_list: {
            type: Array,
            required: true
        }
    },

    setup() {
        const auth = inject('auth');
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },

    data() {
        return {
            showTable: false,
            isSearching: false,
            records: [],
            selectedRecords: [],
            slh_auto_ids: '',

            searchForm: {
                spons_id: '',
                proj_id: '',
                fromDate: '',
                toDate: '',
                searchValue: ''
            }
        };
    },

    mounted() {
        const today = new Date().toISOString().split('T')[0];
        this.searchForm.fromDate = today;
        this.searchForm.toDate = today;
    },

    computed: {

        allSelected() {
            if (this.records.length === 0) return false;
            return this.records.every(record => {
                const id = record.slh_auto_id || record.emp_auto_id;
                return this.selectedRecords.includes(id);
            });
        },
        totalSelectedSalary() {
            return this.records
                .filter(record => {
                    const id = record.slh_auto_id || record.emp_auto_id;
                    return this.selectedRecords.includes(id);
                })
                .reduce((sum, record) => sum + Number(record.slh_total_salary || 0), 0);
        }
    },

    methods: {
        async searchPaidSalaryRecords() {
            this.isSearching = true;
            try {
                const response = await axios.post(EmployeeSalaryPaidListAPI, this.searchForm);
                if (response.data.status === 200 || response.data.success) {
                    this.records = response.data.data || response.data;
                    // reset selected list
                    this.selectedRecords = [];
                    this.showTable = true;
                } else {
                    toast.error(response.data.message || "Failed to fetch data");
                }
            } catch (error) {
                console.error(error);
                toast.error("Something went wrong!");
            } finally {
                this.isSearching = false;
            }
        },

        getMonthName(value) {

            return month_names[parseInt(value)];
        },

        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedRecords = [];
            } else {
                this.selectedRecords = this.records.map(record => record.slh_auto_id || record.emp_auto_id);
            }
        },

        async updateMultipleEmpSalaryStatusAsPaidToUnpaid() {
            if (this.selectedRecords.length === 0) {
                toast.error("Please select at least one record.");
                return;
            }

            try {
                const response = await axios.post(EmployeeSalaryPaymentUndoStatusAPI, {
                    slh_auto_ids: this.selectedRecords
                });
                if (response.data.status === 200 || response.data.success) {

                    toast.success("Updated  successfully");
                    this.records = [];
                    this.searchPaidSalaryRecords();
                } else {
                    toast.error(response.data.message || "Failed to fetch data");
                }

               
            } catch (error) {
                console.error(error);
                toast.error("Failed to update status");
            }
        }
    }
};
</script>