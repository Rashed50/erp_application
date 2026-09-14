<template>


    <!-- Searching UI Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- <form @submit.prevent="searchPendingRecords"> -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Project:</label>
                                <div class="col-sm-9">
                                    <Multiselect v-model="searchForm.proj_id" mode="multiple" :options="projectOptions"
                                        value-prop="value" track-by="label" label="label" placeholder="Select Projects "
                                        :searchable="true" :close-on-select="false" :clear-on-select="false"
                                        :create-option="false" :hide-selected="false" :caret="true"
                                        class="multiselect-blue">
                                        <template #tag="{ option, handleTagRemove }">
                                            <div class="multiselect-tag is-user">
                                                {{ option.label }}
                                                <span class="multiselect-tag-remove"
                                                    @click="handleTagRemove(option, $event)">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </div>
                                        </template>
                                        <template #multiplelabel="{ values }">
                                            <div class="multiselect-multiple-label">
                                                {{ values.length }} Projects Selected
                                            </div>
                                        </template>


                                    </Multiselect>


                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Sponsor:</label>
                                <div class="col-sm-8">
                                    <!-- <select class="form-control" v-model="searchForm.SponsId" multiple>
                                            <option v-for="sponsor in sponsor_list" :key="sponsor.spons_id"
                                                :value="sponsor.spons_id">
                                                {{ sponsor.spons_name }}
                                            </option>
                                        </select> -->

                                    <Multiselect v-model="searchForm.SponsId" mode="multiple" :options="sponsorOptions"
                                        value-prop="value" track-by="label" label="label" placeholder="Select Sponsors "
                                        :searchable="true" :close-on-select="false" :clear-on-select="false"
                                        :create-option="false" :hide-selected="false" :caret="true"
                                        class="multiselect-blue">
                                        <template #tag="{ option, handleTagRemove }">
                                            <div class="multiselect-tag is-user">
                                                {{ option.label }}
                                                <span class="multiselect-tag-remove"
                                                    @click="handleTagRemove(option, $event)">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </div>
                                        </template>
                                        <template #multiplelabel="{ values }">
                                            <div class="multiselect-multiple-label">
                                                {{ values.length }} Sponsors Selected
                                            </div>
                                        </template>


                                    </Multiselect>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Emp. Type:</label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select" v-model="searchForm.emp_type">
                                        <option value="-1">Select One</option>
                                        <option value="0">Direct Basic Employee</option>
                                        <option value="1">Hourly Employee</option>
                                        <option value="2">Indirect Employee</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-group row custom_form_group">
                                <label class="col-md-1 control-label">Month:<span class="req_star">*</span></label>
                                <div class="col-md-3">
                                    <input type="month" class="form-control" v-model="searchForm.fromDate" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control"
                                        placeholder="Search By Employee ID (Comma separated)"
                                        v-model="searchForm.employee_id">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" :disabled="isSearching" @click="searchPendingRecords()"
                                        class="btn btn-primary waves-effect">
                                        {{ isSearching ? 'SEARCHING...' : 'SEARCH' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- //  </form> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Pending Result Table -->
    <div class="row" v-if="showTable">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">

                        <div class="col-md-3">
                            <label class="control-label">Selected Count: <b>{{ selectedRecords.length }}</b></label>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">Total Salary: <b>{{ totalSelectedSalary.toFixed(2)
                                    }}</b></label>
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary waves-effect"
                                @click="updateMultipleEmpSalaryStatusAsUnPaidTopaid"
                                :disabled="selectedRecords.length === 0">
                                Update as paid
                            </button>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover custom_table mb-0">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Iqama</th>
                                    <th>Sponsor</th>
                                    <th>Project</th>
                                    <th>Period</th>
                                    <th>Net Amnt</th>
                                    <th class="text-center" style="width: 120px;">
                                        <input type="checkbox" @change="toggleSelectAll" :checked="allSelected" />
                                        Select
                                        All
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in records" :key="item.slh_auto_id">

                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.employee_id }}</td>
                                    <td>{{ item.employee_name }}</td>
                                    <td>{{ item.akama_no }}</td>
                                    <td>{{ item.spons_name }}</td>
                                    <td>{{ item.proj_name }}</td>


                                    <td>{{ getMonthName(item.slh_month) }},


                                        {{ String(item.slh_year).substring(2) }}</td>
                                    <td>{{ Math.round(item.slh_total_salary) }}</td>
                                    <td class="text-center">

                                        <input type="checkbox" :value="item.slh_auto_id" v-model="selectedRecords"> ||

                                        <button @click="openEditModal(item)"
                                            v-if="hasPermission('employee_salary_record_edit')"
                                            style="background: none; border: none; padding: 0; cursor: pointer; color: #007bff; margin-right: 8px;">
                                            <i class="fa fa-pencil-square fa-lg edit_icon"></i>
                                        </button>||
                                        <button @click="deleteRecord(item.slh_auto_id)"
                                            v-if="hasPermission('employee_salary_record_delete')"
                                            style="background: none; border: none; padding: 0; cursor: pointer; color: #dc3545;">
                                            <i class="fas fa-trash fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="records.length === 0">
                                    <td colspan="10" class="text-center text-danger">No Records Found!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Salary Update Modal -->
    <div class="modal fade show d-block" v-if="showEditModal" tabindex="-1" role="dialog"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Update Employee Salary
                        <span class="text-danger" v-if="editForm.errorData">{{ editForm.errorData }}</span>
                    </h5>
                    <button type="button" class="btn-close" @click="closeEditModal()"></button>
                </div>

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                    <!-- ID, Name & Iqama -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-4 control-label">ID, Name & Iqama:</label>
                        <div class="col-sm-8">
                            <span style="color:red; font-weight:bold;">
                                {{ editForm.employee_id }}, {{ editForm.employee_name }}, {{ editForm.akama_no }},
                                Basic/Hourly: {{ editForm.basic_amount }}/{{ editForm.hourly_rent }}
                            </span>
                        </div>
                    </div>

                    <!-- Month / Year -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Month</label>
                        <div class="col-sm-3">
                            <input type="text" v-model="editForm.salary_month" class="form-control" readonly>
                        </div>
                        <label class="col-sm-3 control-label">Year</label>
                        <div class="col-sm-3">
                            <input type="text" v-model="editForm.salary_year" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Working Days</label>
                        <div class="col-sm-3">
                            <input type="text" v-model="editForm.working_days" class="form-control" readonly>
                        </div>
                        <label class="col-sm-3 control-label">Total Hours</label>
                        <div class="col-sm-3">
                            <input type="text" v-model="editForm.total_hours" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Total(Excl. Food)</label>
                        <div class="col-sm-3">
                            <input type="number" v-model="editForm.total_amount" class="form-control" readonly>
                        </div>
                        <label class="col-sm-3 control-label">Food</label>
                        <div class="col-sm-3">
                            <input type="number" v-model="editForm.food_amount" class="form-control" readonly>
                        </div>
                    </div>

                    <!-- Mobile / Medical -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Mobile</label>
                        <div class="col-sm-3">
                            <input type="number" v-model="editForm.mobile_allowance" class="form-control" readonly>
                        </div>
                        <label class="col-sm-3 control-label">Medical</label>
                        <div class="col-sm-3">
                            <input type="number" v-model="editForm.medical_allowance" class="form-control" readonly>
                        </div>
                    </div>

                    <!-- Total (All Inc.) -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Total(All Inc.)</label>
                        <div class="col-sm-9">
                            <input type="number" v-model="editForm.grand_total_salary" class="form-control"
                                style="background:#eef2f7;" readonly>
                        </div>
                    </div>

                    <!-- Saudi TAX / Iqama Deduc. -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Saudi TAX</label>
                        <div class="col-sm-3">
                            <input type="number" v-model.number="editForm.saudi_tax" @input="calculateReceivable"
                                class="form-control" min="0" max="300" required>
                        </div>
                        <label class="col-sm-3 control-label">Iqama Deduc.</label>
                        <div class="col-sm-3">
                            <input type="number" v-model.number="editForm.new_iqama_advance"
                                @input="calculateReceivable" class="form-control" min="0" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Catering</label>
                        <div class="col-sm-3">
                            <input type="number" v-model.number="editForm.catering_amount" @input="calculateReceivable"
                                class="form-control" min="0">
                        </div>
                        <label class="col-sm-3 control-label">Food</label>
                        <div class="col-sm-3">
                            <input type="number" v-model.number="editForm.new_food_amount" @input="calculateReceivable"
                                class="form-control" min="0" max="2000" required>
                            <small class="text-danger fw-bold d-block mt-1">
                                New Total: {{ editForm.new_total_preview }}
                            </small>
                        </div>
                    </div>

                    <!-- Other Deduc. / Partial Paid -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label">Other Deduc.</label>
                        <div class="col-sm-3">
                            <input type="number" v-model.number="editForm.new_other_advance"
                                @input="calculateReceivable" class="form-control" min="0" required>
                        </div>
                        <label class="col-sm-3 control-label">Partial Paid</label>
                        <div class="col-sm-3">
                            <input type="number" v-model="editForm.partial_paid" class="form-control" min="0" disabled>
                        </div>
                    </div>

                    <!-- Receivable / Salary Paid -->
                    <div class="form-group row custom_form_group mb-2">
                        <label class="col-sm-3 control-label"><b style="color:red">Receivable</b></label>
                        <div class="col-sm-4">
                            <input type="number" v-model="editForm.new_receivable_total_salary" class="form-control"
                                style="font-weight:bold; color:red" readonly>
                        </div>
                        <div class="col-sm-5">
                            <input type="checkbox" v-model="editForm.salary_paid__status" :true-value="1"
                                :false-value="0">&nbsp; Salary Paid
                            <select v-model="editForm.paytment_method" class="form-select d-inline-block w-auto ms-2">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeEditModal()">Close</button>
                    <button type="button" @click="submitSalaryUpdate" class="btn btn-success" :disabled="isUpdating">
                        {{ isUpdating ? 'Updating--' : 'Salary Update' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { inject } from 'vue';
import { useAuth } from '../../../../../../resources/js/components/useAuth';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Multiselect from '@vueform/multiselect';

import {
    EmployeeSalaryPeindingListAPI,
    EmployeeSalaryPeindingUpdateAPI,
    EmployeeSalaryPeindingDeleteAPI,
    EmployeeSalaryPeindingGetRecordAPI,
    EmployeeSalaryPaymentPaidStatusAPI
} from '../routes';
const month_names = [
    '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'
]
export default {
    components: { Multiselect },


    props: {
        sponsor_list: { type: Array, required: true, default: () => [] },
        project_list: { type: Array, required: true, default: () => [] }
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
            isUpdating: false,
            records: [],
            selectedRecords: [],
            showEditModal: false,

            searchForm: {
                SponsId: [],
                proj_id: [],
                emp_type: "1",
                fromDate: new Date().toISOString().slice(0, 7)
            },

            editForm: {
                emp_auto_id: null,
                slh_auto_id: null,
                employee_id: '',
                employee_name: '',
                akama_no: '',
                basic_amount: 0,
                hourly_rent: 0,

                salary_month: '',
                salary_year: '',
                working_days: 0,
                total_hours: 0,

                total_amount: 0,
                food_amount: 0,
                mobile_allowance: 0,
                medical_allowance: 0,
                grand_total_salary: 0,

                saudi_tax: 0,
                new_iqama_advance: 0,
                catering_amount: 0,
                new_food_amount: 0,
                new_other_advance: 0,
                partial_paid: 0,

                new_total_preview: 0,
                new_receivable_total_salary: 0,

                salary_paid__status: 0,
                paytment_method: 1,
                errorData: ''
            }
        };
    },

    computed: {
        allSelected() {
            if (this.records.length === 0) return false;
            return this.selectedRecords.length === this.records.length;
        },
        totalSelectedSalary() {
            return this.records
                .filter(record => this.selectedRecords.includes(record.slh_auto_id))
                .reduce((sum, record) => sum + Number(record.slh_total_salary || 0), 0);
        },

        projectOptions() {
            return (this.project_list).map(p => ({
                value: p.proj_id,
                label: p.proj_name
            }));
        },
        sponsorOptions() {
            return (this.sponsor_list).map(s => ({
                value: s.spons_id,
                label: s.spons_name
            }));
        },
    },

    methods: {
        async searchPendingRecords() {
            if (!this.searchForm.fromDate) {
                toast.error('Please Select Month and Year');
                return;
            }

            this.isSearching = true;
            try {
                const response = await axios.post(EmployeeSalaryPeindingListAPI, this.searchForm);
                if (response.data.status === 200) {
                    this.records = response.data.pendingSalary || [];
                    this.selectedRecords = [];
                    this.showTable = true;
                } else {
                    this.records = [];
                    toast.error(response.data.message || "Operation Failed");
                }
            } catch (error) {
                console.error(error);
                toast.error("Operation Failed");
            } finally {
                this.isSearching = false;
            }
        },

        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedRecords = [];
            } else {
                this.selectedRecords = this.records.map(r => r.slh_auto_id);
            }
        },

        getMonthName(value) {
            return month_names[parseInt(value)];
        },

        async updateMultipleEmpSalaryStatusAsUnPaidTopaid() {
            if (this.selectedRecords.length === 0) {
                toast.error("Please select at least one record.");
                return;
            }
            try {
                const response = await axios.post(EmployeeSalaryPaymentPaidStatusAPI, {
                    slh_auto_ids: this.selectedRecords
                });
                if (response.data.status === 200 || response.data.success) {
                    toast.success(response.data.message || 'Updated Successfully');
                    this.selectedRecords = [];
                    this.searchPendingRecords();
                } else {
                    toast.error(response.data.message || "Failed to update status");
                }
            } catch (error) {
                console.error(error);
                toast.error("Failed to update status");
            }
        },
        async openEditModal(item) {
            this.editForm.errorData = '';
            this.showEditModal = true;

            try {
                const response = await axios.post(EmployeeSalaryPeindingGetRecordAPI, {
                    slh_auto_id: item.slh_auto_id
                });

                if (response.data.status === 200) {
                    const arecord = response.data.arecord;

                    let slh_all_include_amount = parseFloat(arecord.slh_all_include_amount) || 0;
                    if (slh_all_include_amount <= 0) {
                        slh_all_include_amount =
                            (parseFloat(arecord.slh_total_salary) || 0) +
                            (parseFloat(arecord.slh_iqama_advance) || 0) +
                            (parseFloat(arecord.slh_other_advance) || 0) +
                            (parseFloat(arecord.slh_saudi_tax) || 0) +
                            (parseFloat(arecord.slh_food_deduction) || 0);
                    }

                    const total_working_amount = slh_all_include_amount - (
                        (parseFloat(arecord.food_allowance) || 0) +
                        (parseFloat(arecord.mobile_allowance) || 0) +
                        (parseFloat(arecord.medical_allowance) || 0)
                    );

                    this.editForm = {
                        emp_auto_id: arecord.emp_auto_id,
                        slh_auto_id: arecord.slh_auto_id,
                        employee_id: arecord.employee_id,
                        employee_name: arecord.employee_name,
                        akama_no: arecord.akama_no,
                        basic_amount: arecord.basic_amount,
                        hourly_rent: arecord.hourly_rent,

                        salary_month: arecord.slh_month,
                        salary_year: arecord.slh_year,
                        working_days: arecord.slh_total_working_days,
                        total_hours: arecord.slh_total_hours,

                        total_amount: total_working_amount,
                        food_amount: parseFloat(arecord.food_allowance) || 0,
                        mobile_allowance: parseFloat(arecord.mobile_allowance) || 0,
                        medical_allowance: parseFloat(arecord.medical_allowance) || 0,
                        grand_total_salary: Math.round(slh_all_include_amount),

                        saudi_tax: parseFloat(arecord.slh_saudi_tax) || 0,
                        new_iqama_advance: parseFloat(arecord.slh_iqama_advance) || 0,
                        catering_amount: parseFloat(arecord.slh_food_deduction) || 0,
                        new_food_amount: parseFloat(arecord.food_allowance) || 0,
                        new_other_advance: parseFloat(arecord.slh_other_advance) || 0,
                        partial_paid: parseFloat(arecord.partial_paid_amount) || 0,

                        new_total_preview: 0,
                        new_receivable_total_salary: Math.round(parseFloat(arecord.slh_total_salary) || 0),

                        salary_paid__status: 0,
                        paytment_method: 1,
                        errorData: ''
                    };

                    this.calculateReceivable();
                } else {
                    this.editForm.errorData = response.data.error || 'Failed to load record';
                }
            } catch (error) {
                console.error(error);
                toast.error('Operation Failed');
                this.showEditModal = false;
            }
        },


        calculateReceivable() {
            this.editForm.catering_amount = this.editForm.catering_amount == '' ? 0 : this.editForm.catering_amount;
            this.editForm.saudi_tax = this.editForm.saudi_tax == '' ? 0 : this.editForm.saudi_tax;
            this.editForm.new_food_amount = this.editForm.new_food_amount == '' ? 0 : this.editForm.new_food_amount;
            this.editForm.new_other_advance = this.editForm.new_other_advance == '' ? 0 : this.editForm.new_other_advance;
            this.editForm.new_iqama_advance = this.editForm.new_iqama_advance == '' ? 0 : this.editForm.new_iqama_advance;

            const total_amount = parseInt(this.editForm.total_amount) || 0;
            const new_food_amount = parseInt(this.editForm.new_food_amount) || 0;


            this.editForm.new_total_preview = total_amount + new_food_amount;
            const saudi_tax = parseInt(this.editForm.saudi_tax) || 0;
            const catering_amount = parseInt(this.editForm.catering_amount);
            const partial_paid = parseInt(this.editForm.partial_paid) || 0;
            const new_other_advance = parseInt(this.editForm.new_other_advance) || 0;
            const new_iqama_advance = parseInt(this.editForm.new_iqama_advance) || 0;

            const total = (total_amount + new_food_amount) -
                (new_other_advance + saudi_tax + new_iqama_advance + catering_amount + partial_paid);

            this.editForm.new_receivable_total_salary = Math.round(total);
        },

        closeEditModal() {
            this.showEditModal = false;
        },

        // Exact port of Blade's sumbitSalaryCorrectionData() payload
        async submitSalaryUpdate() {
            this.isUpdating = true;
            try {
                const formData = new FormData();
                formData.append('working_days', this.editForm.working_days ?? '');
                formData.append('total_amount', this.editForm.total_amount ?? '');
                formData.append('new_food_amount', this.editForm.new_food_amount ?? '');
                formData.append('saudi_tax', this.editForm.saudi_tax ?? '');
                formData.append('new_other_advance', this.editForm.new_other_advance ?? '');
                formData.append('new_iqama_advance', this.editForm.new_iqama_advance ?? '');
                formData.append('new_receivable_total_salary', this.editForm.new_receivable_total_salary ?? '');
                formData.append('salary_month', this.editForm.salary_month ?? '');
                formData.append('salary_year', this.editForm.salary_year ?? '');
                formData.append('emp_auto_id', this.editForm.emp_auto_id ?? '');
                formData.append('slh_auto_id', this.editForm.slh_auto_id ?? '');
                formData.append('salary_paid__status', this.editForm.salary_paid__status ?? '');
                formData.append('catering_amount', this.editForm.catering_amount ?? '');
                formData.append('paytment_method', this.editForm.paytment_method ?? '');

                const response = await axios.post(EmployeeSalaryPeindingUpdateAPI, formData);

                if (response.data.status === 200) {
                    toast.success('Update Operation Successfully Completed');
                    this.closeEditModal();
                    this.searchPendingRecords();
                } else {
                    toast.error('Update Operation Failed');
                }
            } catch (error) {
                console.error(error);
                toast.error('Update Operation Failed');
            } finally {
                this.isUpdating = false;
            }
        },

        async deleteRecord(slh_auto_id) {
            try {
                const response = await axios.delete(`${EmployeeSalaryPeindingDeleteAPI}/${slh_auto_id}`);
                if (response.data.status === 200) {
                    this.searchPendingRecords();
                } else {
                    toast.error('failed to delete record');
                }
            } catch (error) {
                console.error(error);
                toast.error('Error occurred while deleting');
            }
        }
    }
};
</script>