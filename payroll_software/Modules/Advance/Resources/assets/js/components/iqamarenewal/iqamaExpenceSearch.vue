<template>
    <div class="row d-block">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body card_form" style="padding: 0;">
                    <div class="form-group row custom_form_group">
                        <div class="col-md-2 text-right mt-2">Employee Search</div>
                        <div class="col-md-4 mt-2">
                            <input type="text" placeholder="Enter Employee ID or Iqama Number" class="form-control"
                                v-model="searchForm.searchValue"
                                @keyup.enter="searchingEmployeeIqamaRenewalExpenseRecords()">
                        </div>


                        <!-- New Approval Status Checkbox -->
                        <div class="col-md-2 mt-2 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="approvalStatusCheck"
                                    v-model="searchForm.approval_status" :true-value="1" :false-value="0"
                                    @change="searchingEmployeeIqamaRenewalExpenseRecords()">
                                <label class="form-check-label ms-1" for="approvalStatusCheck">
                                    {{ searchForm.approval_status === 1 ? 'Approved' : 'Pending' }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <button type="button" @click="searchingEmployeeIqamaRenewalExpenseRecords()"
                                class="btn btn-primary waves-effect">
                                <span v-if="isSearching"> <i class="fas fa-spinner fa-spin"></i> Searching...</span>
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
                <div class="card-body">
                    <div class="row">

                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" @click="toggleSelectAll()"
                                        class="btn btn-primary waves-effect" :disabled="records.length == 0">

                                        {{ allSelected ? 'Uncheck All' : 'Check All' }}

                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">
                                        Total Selected: {{ selectedEmployees.length }}

                                    </label>
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" @click="multiApproveIqamaRenewal"
                                        class="btn btn-primary waves-effect"
                                        :disabled="selectedEmployees.length === 0 || isMultiUpdating">

                                        <span v-if="isMultiUpdating">
                                            <i class="fas fa-spinner fa-spin"></i> Updating...
                                        </span>
                                        <span v-else>Update</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Jawazat</th>
                                            <th>Mak.Amal</th>
                                            <th>VISA</th>
                                            <th>Medical</th>
                                            <th>Others</th>
                                            <th>Penalty</th>
                                            <th>Total</th>
                                            <th>Duration</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Paid By</th>
                                            <th>Remarks</th>
                                            <th style="width: 120px;">
                                                <input type="checkbox" @change="toggleSelectAll"
                                                    :checked="allSelected" />
                                                Select
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(emp, index) in records" :key="emp.emp_auto_id || index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ emp.employee_id }}</td>
                                            <td>{{ emp.employee_name }}</td>
                                            <td>{{ emp.jawazat_fee }}</td>
                                            <td>{{ emp.maktab_alamal_fee }}</td>
                                            <td>{{ emp.bd_amount }}</td>
                                            <td>{{ emp.medical_insurance }}</td>
                                            <td>{{ emp.others_fee }}</td>
                                            <td>{{ emp.jawazat_penalty }}</td>
                                            <td>{{ emp.total_amount }}</td>
                                            <td>{{ emp.duration }} Mon.</td>
                                            <td>{{ emp.renewal_date }}</td>
                                            <td>
                                                <span style="background-color: lightgreen; padding: 5px;"> {{
                                                    emp.approved_status == 1 ? "Approved" : "Pending" }}</span>
                                            </td>
                                            <td> <span style="background-color: lightblue; padding: 5px;"> {{
                                                emp.expense_paid_by == 1 ? 'Self' : 'Company' }} </span></td>
                                            <td>{{ emp.remarks }}</td>
                                            <td>
                                                <input type="checkbox" v-model="selectedEmployees"
                                                    :value="emp.iqama_renew_id || emp.IqamaRenewId || emp.id" /> ||
                                                <button @click="openUpdateModal(emp)"
                                                    style="background: none; border: none; padding: 0; cursor: pointer; color: #dc3545;">
                                                    <i class="fa fa-pencil-square fa-lg edit_icon"></i> ||
                                                </button>
                                                <button
                                                    @click="deleteIqamaFee(emp.IqamaRenewId || emp.iqama_renew_id || emp.id, index)"
                                                    v-if="hasPermission('iqama_renewal_expense_delete')"
                                                    style="background: none; border: none; padding: 0; cursor: pointer; color: #dc3545;">
                                                    <i class="fas fa-trash fa-lg"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="records.length === 0">
                                            <td colspan="16" class="text-center">Not found</td>
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

    <!-- Complete Update Modal -->
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);"
        v-if="showUpdateModal">
        <div class="modal-dialog modal-lg modal-dialog-scrollable renewal-modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Iqama Renewal Expense</h5>
                    <button type="button" class="btn-close" @click="closeUpdateModal()"></button>
                </div>
                <div class="modal-body">
                    <form id="iqamaRenewalUpdateForm" @submit.prevent="updateIqamaRenewal">
                        <div class="row">
                            <!-- Jawazat Fee -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jawazat Fee <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control"
                                    v-model.number="editForm.jawazat_fee" @input="calculateTotalAmount"
                                    @blur="handleNumericInput('jawazat_fee')" required>
                            </div>

                            <!-- Maktab Al Amal Fee -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maktab Al Amal Fee <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control"
                                    v-model.number="editForm.maktab_alamal_fee" @input="calculateTotalAmount"
                                    @blur="handleNumericInput('maktab_alamal_fee')" required>
                            </div>

                            <!-- VISA Amount (BD Amount) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">VISA Amount <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control" v-model.number="editForm.bd_amount"
                                    @input="calculateTotalAmount" @blur="handleNumericInput('bd_amount')" required>
                            </div>

                            <!-- Medical Insurance -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Medical Insurance <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control"
                                    v-model.number="editForm.medical_insurance" @input="calculateTotalAmount"
                                    @blur="handleNumericInput('medical_insurance')" required>
                            </div>

                            <!-- Jawazat Penalty -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jawazat Fee Penalty <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control"
                                    v-model.number="editForm.jawazat_penalty" @input="calculateTotalAmount"
                                    @blur="handleNumericInput('jawazat_penalty')" required>
                            </div>

                            <!-- Others Fee -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Others <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control"
                                    v-model.number="editForm.others_fee" @input="calculateTotalAmount"
                                    @blur="handleNumericInput('others_fee')" required>
                            </div>

                            <!-- Total Amount (Readonly) -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><strong>Total Amount</strong></label>
                                <input type="text" class="form-control fw-bold" v-model="editForm.total_amount" readonly
                                    required>
                            </div>

                            <!-- Renewal Duration -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Renewal Duration <span class="text-danger">*</span></label>
                                <select class="form-select" v-model="editForm.duration">
                                    <option v-for="i in 72" :key="i" :value="i">
                                        {{ i }} Month{{ i > 1 ? 's' : '' }}
                                    </option>
                                </select>
                            </div>

                            <!-- Renewal Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Renewal Date</label>
                                <input type="date" class="form-control" v-model="editForm.renewal_date">
                            </div>

                            <!-- Payment Number -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Payment Number"
                                    v-model="editForm.payment_number">
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" class="form-control" v-model="editForm.payment_date">
                            </div>

                            <!-- Renewal Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Renewal Status <span class="text-danger">*</span></label>
                                <select class="form-select" v-model="editForm.renewal_status">
                                    <option value="1">Initial Step</option>
                                    <option value="2">Payment Initialize</option>
                                    <option value="3">Payment Completed</option>
                                    <option value="4">Renewal Pending</option>
                                    <option value="5">Renewal Completed</option>
                                </select>
                            </div>

                            <!-- Expense Paid By -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expense Paid By</label>
                                <select class="form-select" v-model="editForm.expense_paid_by">
                                    <option value="1">Self</option>
                                    <option value="2">Company</option>
                                </select>
                            </div>

                            <!-- Reference Employee ID -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reference Employee ID <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="Input Employee ID"
                                    v-model="editForm.reference_emp_id">
                            </div>

                            <!-- Iqama Expire Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Iqama Expire at <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" v-model="editForm.iqama_expire_date">
                            </div>

                            <!-- Payment Purpose -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purpose <span class="text-danger">*</span></label>
                                <select class="form-select" v-model="editForm.payment_purpose_id" required>
                                    <option value="1">Iqama Renewal</option>
                                    <option value="2">Medical Insurance</option>
                                    <option value="3">Exit-Re-Entry</option>
                                    <option value="4">Family Iqama</option>
                                    <option value="5">Family Medical Insurance</option>
                                    <option value="6">Traffic Violation</option>
                                </select>
                            </div>

                            <!-- Remarks -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" placeholder="Remarks Type Here"
                                    v-model="editForm.remarks">
                            </div>

                            <!-- Update & Approved Checkbox -->
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        v-model="editForm.update_approved_chk" true-value="1" false-value="0">
                                    <label class="form-check-label" for="update_approved_chk">
                                        Update & Approved
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeUpdateModal()">Cancel</button>
                    <button type="submit" form="iqamaRenewalUpdateForm" class="btn btn-primary" :disabled="isUpdating">
                        {{ isUpdating ? 'Updating...' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { inject } from 'vue';
import { useAuth } from '../../../../../../../resources/js/components/useAuth.js';

import { toast } from 'vue3-toastify';
import {
    IqamaRenewalExpencesSearchAPI,
    IqamaRenewalfeedeleteAPI,
    IqamaRenewalUpdateAPI,
    MultiEmployeeApproveIqamaRenewalAPI
} from '../../routes.js';

export default {

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
            isMultiUpdating: false,

            showUpdateModal: false,

            records: [],
            selectedEmployees: [],
            searchForm: {
                searchValue: '',
                searchType: 'employee_id',
                approval_status: 0,
            },
            editForm: {
                id: null,
                jawazat_fee: 0,
                maktab_alamal_fee: 0,
                bd_amount: 0,
                medical_insurance: 0,
                jawazat_penalty: 0,
                others_fee: 0,
                total_amount: 0,
                duration: 1,
                renewal_date: '',
                payment_number: '',
                payment_date: '',
                renewal_status: 1,
                expense_paid_by: 1,
                reference_emp_id: '',
                iqama_expire_date: '',
                payment_purpose_id: 1,
                remarks: '',
                update_approved_chk: '0'
            }
        }
    },

    computed: {
        allSelected() {
            if (this.records.length === 0) return false;
            return this.records.every(record => {
                const id = record.iqama_renew_id || record.IqamaRenewId || record.id;
                return this.selectedEmployees.includes(id);
            });
        }
    },

    methods: {
        async searchingEmployeeIqamaRenewalExpenseRecords() {
            this.isSearching = true;
            const requestData = {
                'searchValue': this.searchForm.searchValue,
                'searchType': this.searchForm.searchType,
                'approval_status': this.searchForm.approval_status,
            };

            try {
                const response = await axios.post(IqamaRenewalExpencesSearchAPI, requestData);
                if (response.data.status == 200) {
                    this.records = response.data.data;
                    this.showTable = true;
                    // Reset selected employees when new data is loaded
                    this.selectedEmployees = [];
                }
            } catch (error) {
                console.error("Search Failed", error);
            } finally {
                this.isSearching = false;
            }
        },

        handleNumericInput(field) {
            if (this.editForm[field] === '' || this.editForm[field] === null || isNaN(this.editForm[field])) {
                this.editForm[field] = 0;
            }
            this.calculateTotalAmount();
        },

        calculateTotalAmount() {
            // প্রতিটি ফিল্ড খালি থাকলে বা NaN হলে 0 নিশ্চিত করবে
            const jf = parseFloat(this.editForm.jawazat_fee) || 0;
            const mf = parseFloat(this.editForm.maktab_alamal_fee) || 0;
            const bd = parseFloat(this.editForm.bd_amount) || 0;
            const mi = parseFloat(this.editForm.medical_insurance) || 0;
            const jp = parseFloat(this.editForm.jawazat_penalty) || 0;
            const ot = parseFloat(this.editForm.others_fee) || 0;

            this.editForm.total_amount = jf + mf + bd + mi + jp + ot;
        },

        openUpdateModal(employee) {
            this.editForm = {
                id: employee.IqamaRenewId || employee.iqama_renew_id || employee.id,
                jawazat_fee: employee.jawazat_fee || 0,
                maktab_alamal_fee: employee.maktab_alamal_fee || 0,
                bd_amount: employee.bd_amount || 0,
                medical_insurance: employee.medical_insurance || 0,
                jawazat_penalty: employee.jawazat_penalty || 0,
                others_fee: employee.others_fee || 0,
                total_amount: employee.total_amount || 0,
                duration: employee.duration || 1,
                renewal_date: employee.renewal_date || '',
                payment_number: employee.payment_number || '',
                payment_date: employee.payment_date || '',
                renewal_status: employee.renewal_status || 1,
                expense_paid_by: employee.expense_paid_by || 1,
                reference_emp_id: employee.reference_emp_id || '',
                iqama_expire_date: employee.iqama_expire_date || '',
                payment_purpose_id: employee.payment_purpose_id || 1,
                remarks: employee.remarks || '',
                update_approved_chk: employee.approved_status == 1 ? '1' : '0',
            };

            this.calculateTotalAmount();
            this.showUpdateModal = true;
        },

        closeUpdateModal() {
            this.showUpdateModal = false;
        },

        async updateIqamaRenewal() {
            this.isUpdating = true;
            try {
                const response = await axios.post(IqamaRenewalUpdateAPI, this.editForm);

                if (response.data.status === 200 || response.status === 200) {
                    toast.success(response.data.message || 'Successfully  updated!');
                    this.records = [];
                    this.searchingEmployeeIqamaRenewalExpenseRecords();
                    this.closeUpdateModal();
                } else {
                    toast.error(response.data.message || 'Failed to update record.');
                }
            } catch (error) {
                console.error("Update Failed:", error);
                if (error.response && error.response.data && error.response.data.message) {
                    toast.error(error.response.data.message);
                } else {
                    toast.error('An error occurred while updating.');
                }
            } finally {
                this.isUpdating = false;
            }
        },

        async deleteIqamaFee(IqamaRenewId, index) {
            if (!confirm("Are you sure you want to delete this record?")) {
                return;
            }
            try {
                const response = await axios.delete(`${IqamaRenewalfeedeleteAPI}/${IqamaRenewId}`);
                if (response.data.status === 200) {
                    toast.success(response.data.message);
                    this.records = [];
                    this.searchingEmployeeIqamaRenewalExpenseRecords();
                } else {
                    toast.error(response.data.message || 'Failed to delete record.');
                }
            } catch (error) {
                console.error("Delete Failed", error);
                toast.error('An error occurred while deleting.');
            }
        },

        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedEmployees = [];
            } else {
                const ids = this.records.map(record => record.iqama_renew_id || record.IqamaRenewId || record.id);
                this.selectedEmployees = ids;
            }
        },

        getRecordId(record) {
            return record.iqama_renew_id || record.IqamaRenewId || record.id;
        },




        async multiApproveIqamaRenewal() {

            this.isMultiUpdating = true;

            try {
                const response = await axios.post(MultiEmployeeApproveIqamaRenewalAPI, {
                    iqama_renewal_auto_id: this.selectedEmployees
                });


                if (response.data.status === 200) {
                    toast.success(response.data.message || ' Approve Sucessfully')
                    this.records = [];
                    this.searchingEmployeeIqamaRenewalExpenseRecords();
                }
            } catch (error) {
                toast.error('An error occurred while approving records.');

            } finally {
                this.isMultiUpdating = false;
            }
        }
    }
}
</script>

<style scoped>
.renewal-modal-dialog {
    max-height: 90vh;
}

.renewal-modal-dialog .modal-content {
    max-height: 90vh;
}

.renewal-modal-dialog .modal-body {
    overflow-y: auto;
}
</style>
