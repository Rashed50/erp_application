<template>
    <div class="row d-block">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body card_form p-3">
                    <div class="row align-items-end">

                        <!-- Employee ID / Iqama Search Input -->
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="font-weight-bold form-label">Employee ID:</label>
                            <input type="text" placeholder="Enter Emp ID" class="form-control"
                                v-model="searchForm.employee_id" @keyup.enter="searchingEmployeeCashRecords()" />
                        </div>

                        <!-- From Date Search Input -->
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="font-weight-bold form-label">From:</label>
                            <input type="date" class="form-control" v-model="searchForm.from_date"
                                @keyup.enter="searchingEmployeeCashRecords()" />
                        </div>

                        <!-- To Date Search Input -->
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="font-weight-bold form-label">To:</label>
                            <input type="date" class="form-control" v-model="searchForm.to_date"
                                @keyup.enter="searchingEmployeeCashRecords()" />
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-3">
                            <button type="button" @click="searchingEmployeeCashRecords()"
                                class="btn btn-primary waves-effect w-100" :disabled="isSearching">
                                <span v-if="isSearching">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Searching...
                                </span>
                                <span v-else>
                                    <i class="fas fa-search mr-1"></i> Search
                                </span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Payment List -->
    <div class="row" v-if="showTable">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Emp.ID</th>
                                            <th>Name</th>
                                            <th>Iqama No</th>
                                            <th>Project</th>
                                            <th>Date</th>
                                            <th>Month</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in searching_records" :key="item.id || index">
                                            <td>{{ item.employee_id || item.emp_id }}</td>
                                            <td>{{ item.employee_name }}</td>
                                            <td>{{ item.akama_no }}</td>
                                            <td>{{ item.proj_name }}</td>
                                            <td>{{ item.date || item.payment_date }}</td>
                                            <td>{{ item.month_name }}</td>
                                            <td>{{ item.adv_amount || item.pay_amount }}</td>
                                            <td>{{ item.adv_remarks || item.payment_remarks }}</td>
                                            <td>
                                                <button @click="openUpdateModal(item)"
                                                    style="background: none; border: none; padding: 0; cursor: pointer; color: #007bff; margin-right: 8px;">
                                                    <i class="fa fa-pencil-square fa-lg edit_icon"></i>
                                                </button>
                                                <button @click="deleteRecord(item.id)"
                                                    style="background: none; border: none; padding: 0; cursor: pointer; color: #dc3545;">
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
    </div>

    <!-- Edit Modal -->
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);"
        v-if="showUpdateModal">
        <div class="modal-dialog modal-lg modal-dialog-scrollable renewal-modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Iqama Renewal Expense</h5>
                    <button type="button" class="btn-close" @click="closeUpdateModal()"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" @submit.prevent="updateCashSubmit">
                        <div class="card-body card_form" style="padding-top: 10px;">
                            <div class="row">
                                <!-- Left Column: Form Inputs -->
                                <div class="col-md-7">
                                    <div class="form-group row custom_form_group mb-3">
                                        <label class="control-label col-md-4">Employee ID:</label>
                                        <div class="col-md-8">
                                            <input type="text" readonly required class="form-control"
                                                v-model="editForm.emp_id">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group mb-3">
                                        <label class="control-label col-md-4">Employee Name:</label>
                                        <div class="col-md-8">
                                            <input readonly type="text" class="form-control"
                                                v-model="editForm.employee_name">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group mb-3">
                                        <label class="control-label col-md-4">Amount:<span
                                                class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" placeholder="Input Amount"
                                                v-model="editForm.pay_amount" required>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group mb-3">
                                        <label class="control-label col-md-4">Date:</label>
                                        <div class="col-md-8">
                                            <input type="date" class="form-control" v-model="editForm.payment_date">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group mb-3">
                                        <label class="control-label col-md-4">Remarks:</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" placeholder="Type Here..."
                                                v-model="editForm.payment_remarks">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group mb-3">
                                        <label class="control-label col-md-4">Attachment:</label>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" ref="editFileInput"
                                                @change="handleEditFileUpload" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Attachment Preview -->
                                <div class="col-md-5 d-flex align-items-center justify-content-center">
                                    <div class="w-100">
                                        <label class="form-label text-muted small fw-bold">Attachment Preview:</label>

                                        <div class="border rounded p-2 bg-light shadow-sm d-flex justify-content-center align-items-center text-center"
                                            style="min-height: 250px; max-height: 280px;">

                                            <img v-if="editForm.attached_file_type === 1 && editForm.attached_file_url"
                                                :src="editForm.attached_file_url" class="img-fluid rounded"
                                                style="max-height: 260px; object-fit: contain;" alt="File Preview">

                                            <iframe
                                                v-else-if="editForm.attached_file_type === 2 && editForm.attached_file_url"
                                                :src="editForm.attached_file_url" width="100%" height="260px"></iframe>

                                            <div v-else-if="editForm.attached_file_url" class="text-muted small">
                                                Preview not supported for this file format.
                                            </div>

                                            <div v-else class="text-muted small">
                                                No Attachment Available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeUpdateModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="isUpdating">
                                {{ isUpdating ? 'Updating...' : 'Update' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import {
    CashRecordsSearchAPI,
    CashRecordsDeleteAPI,
    CashAddEmployeeAPI
} from '../../routes.js';

export default {
    name: 'EmployeeSearchForm',

    data() {
        return {
            isSearching: false,
            isUpdating: false,
            searching_records: [],
            showUpdateModal: false,
            showTable: false,
            searchForm: {
                employee_id: '',
                from_date: '',
                to_date: ''
            },
            editForm: {
                id: null,
                emp_id: '',
                employee_name: '',
                pay_amount: '',
                payment_date: '',
                payment_remarks: '',
                attached_file: null,
                attached_file_url: null,
                attached_file_type: 0
            }
        };
    },

    methods: {
        async searchingEmployeeCashRecords() {
            if (!this.searchForm.employee_id && !this.searchForm.from_date && !this.searchForm.to_date) {
                toast.error('Please enter Employee ID/Iqama or select a Date Range');
                return;
            }

            this.isSearching = true;

            const requestData = {
                employee_id: this.searchForm.employee_id,
                from_date: this.searchForm.from_date,
                to_date: this.searchForm.to_date,
            };

            try {
                const response = await axios.post(CashRecordsSearchAPI, requestData);

                if (response.data.status === 200) {
                    this.searching_records = response.data.data;
                    this.showTable = true;

                } else {
                    toast.error(response.data.message || 'No records found');
                }
            } catch (error) {
                //   console.error('Search Failed', error);
                toast.error('Search failed: ' + (error.response?.data?.message || error.message));
            } finally {
                this.isSearching = false;
            }
        },

        openUpdateModal(item) {
            this.editForm.id = item.id;
            this.editForm.emp_id = item.emp_id || item.employee_id;
            this.editForm.employee_name = item.employee_name;
            this.editForm.pay_amount = item.pay_amount || item.adv_amount;
            this.editForm.payment_date = item.payment_date || item.date;
            this.editForm.payment_remarks = item.payment_remarks || item.adv_remarks;

            this.editForm.attached_file = null;
            this.editForm.attached_file_type = 0;



            this.showUpdateModal = true;
        },

        closeUpdateModal() {
            this.showUpdateModal = false;
        },

        handleEditFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.editForm.attached_file = file;
                const name = file.name.toLowerCase();

                if (file.type.startsWith('image/')) {
                    this.editForm.attached_file_type = 1;
                } else if (file.type === 'application/pdf' || name.endsWith('.pdf')) {
                    this.editForm.attached_file_type = 2;
                } else {
                    this.editForm.attached_file_type = 0;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.editForm.attached_file_url = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async updateCashSubmit() {
            try {
                this.isUpdating = true;

                const formData = new FormData();
                formData.append('record_id', this.editForm.id || '');
                formData.append('emp_id', this.editForm.emp_id || '');
                formData.append('pay_amount', this.editForm.pay_amount || '');
                formData.append('payment_date', this.editForm.payment_date || '');
                formData.append('payment_remarks', this.editForm.payment_remarks || '');

                if (this.editForm.attached_file) {
                    formData.append('attached_file', this.editForm.attached_file);
                }

                const response = await axios.post(CashAddEmployeeAPI, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (response.data.status === 200 || response.data.success) {
                    toast.success(response.data.message || 'Updated Successfully');
                    this.closeUpdateModal();
                    await this.searchingEmployeeCashRecords();
                } else {
                    toast.error('Failed: ' + (response.data.message || 'Something went wrong'));
                }
            } catch (error) {
                console.error(error);
                toast.error('Error occurred, Please Try Again: ' + (error.response?.data?.message || error.message));
            } finally {
                this.isUpdating = false;
            }
        },

        async deleteRecord(id) {
            if (!confirm("Are you sure you want to delete this record?")) return;

            try {
                const response = await axios.delete(`${CashRecordsDeleteAPI}/${id}`);

                if (response.data.status === 200 || response.data.success) {
                    toast.success("Record deleted successfully!");
                    await this.searchingEmployeeCashRecords();
                } else {
                    toast.error("Failed to delete record.");
                }
            } catch (error) {
                console.error(error);
                toast.error("Error occurred while deleting: " + (error.response?.data?.message || error.message));
            }
        }
    }
};
</script>