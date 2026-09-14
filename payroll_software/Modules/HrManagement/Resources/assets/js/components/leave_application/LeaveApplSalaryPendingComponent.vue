<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title">Leave Application Approved But Salary Pending</h4>
                        </div>
                        <div class="col-md-6 text-right">

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" v-model="filters.employee_id" @keyup.enter="searchRecords"
                                   class="form-control" placeholder="Search by Employee ID">
                        </div>
                        <div class="col-md-3">
                            <input type="date" v-model="filters.start_date" @change="searchRecords"
                                   class="form-control" placeholder="Start Date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" v-model="filters.end_date" @change="searchRecords"
                                   class="form-control" placeholder="End Date">
                        </div>
                        <div class="col-md-3">
                            <select v-model="filters.application_status" @change="searchRecords"
                                    class="form-select" hidden>
                                <!-- <option value="">All Status</option> -->
                                <option v-for="status in applicationStatuses" :key="status.leav_sta_auto_id"
                                        :value="status.leav_sta_auto_id">
                                    {{ status.status_title }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button @click="searchRecords" class="btn btn-primary" :disabled="loading">
                                <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                                Search
                            </button>
                            <button @click="resetFilters" class="btn btn-secondary ml-2">
                                Reset
                            </button>
                            <button @click="loadRecords" class="btn btn-primary ml-2">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                            <span class="ml-3 text-muted" v-if="records.length > 0">
                                Total: {{ pagination.total }} records
                            </span>
                        </div>
                    </div>

                    <!-- Records Table -->
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Emp. Name</th>
                                    <th>Iqama</th>
                                    <th>Sponsor</th>
                                    <th>Contact</th>
                                    <th>Trade <br> Project</th>
                                    <th>(Start-End)</th>
                                    <th>Appl. <br> Date</th>
                                    <th>Remarks <br> Comments </th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading && records.length === 0">
                                    <td colspan="10" class="text-center">
                                        <i class="fa fa-spinner fa-spin"></i> Loading...
                                    </td>
                                </tr>
                                <tr v-else-if="records.length === 0">
                                    <td colspan="10" class="text-center">No records found</td>
                                </tr>
                                <tr v-else v-for="(record, index) in records" :key="record.leav_auto_id">
                                    <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>

                                    <td>{{ record.employee_id }} <br> {{ record.employee_name }}</td>
                                    <td>
                                        {{ record.akama_no }} <br> {{ record.akama_expire_date }}
                                    </td>
                                    <td>{{ record.spons_name }}</td>
                                    <td>{{ record.mobile_no }}</td>
                                    <td>
                                        {{ record.catg_name }} <br> {{ record.proj_name }}
                                    </td>

                                    <td>
                                        {{ record.start_date }} <br> {{ record.end_date }}
                                    </td>
                                    <td>{{ record.appl_date }}</td>
                                    <td>{{ record.lev_reas_name }} <br> {{ record.description || '' }} <br> {{ record.admin_comments || '' }} </td>

                                    <td>
                                        <span class="badge" :class="getStatusClass(record.status_title)">
                                            {{ record.status_title }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" @click.prevent="openEditModal(record)"
                                           v-if="canUpdate" title="Edit">
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <span v-if="canReject">|</span>
                                        <a :href="getAttachmentUrl(record.exit_paper)" target="_blank" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-3" v-if="pagination.last_page > 1">
                        <div class="col-md-6">
                            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} records
                        </div>
                        <div class="col-md-6">
                            <nav>
                                <ul class="pagination justify-content-end">
                                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                                            Previous
                                        </a>
                                    </li>
                                    <li class="page-item" v-for="page in pagination.last_page" :key="page"
                                        :class="{ active: page === pagination.current_page }">
                                        <a class="page-link" href="#" @click.prevent="changePage(page)">
                                            {{ page }}
                                        </a>
                                    </li>
                                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="leave_application_edit_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Leave Application Update Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="updateLeaveApplication">
                        <div class="modal-body">
                            <input type="hidden" v-model="editForm.leav_auto_id">
                            <input type="hidden" v-model="editForm.emp_auto_id">

                            <!-- Employee Details -->
                            <div class="form-group row custom_form_group mb-1 pb-0">
                                <label class="col-sm-2 control-label">Employee </label>
                                <div class="col-sm-10">
                                    <span style="color:red">{{ editForm.employee_details }}</span>
                                </div>
                            </div>

                            <!-- Project -->
                            <div class="form-group row custom_form_group mb-1 pb-0">
                                <label class="col-sm-2 control-label text-right">Project:</label>
                                <div class="col-sm-10">
                                    <label class="control-label" style="font-weight: 100;">
                                        {{ editForm.project_name }}
                                    </label>
                                </div>
                            </div>

                            <!-- Mobile & Iqama -->
                            <div class="form-group row custom_form_group  mb-1 pb-0">
                                <label class="col-sm-2 control-label text-right">Mobile:</label>
                                <div class="col-sm-4">
                                    <label class="control-label" style="font-weight: 100;">
                                        {{ editForm.mobile_no }}
                                    </label>
                                </div>
                                <label class="col-sm-2 control-label text-right">Iqama:</label>
                                <div class="col-sm-4">
                                    <label class="control-label" style="font-weight: 100;">
                                        {{ editForm.akama_no }}
                                    </label>
                                </div>
                            </div>

                            <!-- Reference By -->
                            <div class="form-group row custom_form_group  mb-1 pb-0">
                                <label class="col-sm-3 control-label text-right">Reference By:</label>
                                <div class="col-sm-9">
                                    <label class="control-label" style="font-weight: 100;">
                                        {{ editForm.reference_by }}
                                    </label>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group row custom_form_group mb-1 pb-0">
                                <label class="col-sm-3 control-label text-right">Remarks:</label>
                                <div class="col-sm-9">
                                    <label class="control-label" style="font-weight: 100;">
                                        {{ editForm.description }}
                                    </label>
                                </div>
                            </div>

                            <!-- App Date & Days -->
                            <div class="form-group row custom_form_group mb-1 pb-0">
                                <label class="col-sm-2 control-label text-right">Appl.</label>
                                <div class="col-sm-4">
                                    <input type="date" v-model="editForm.appl_date" class="form-control" readonly>
                                </div>
                                <label class="col-sm-2 control-label text-right">Days</label>
                                <div class="col-sm-4">
                                    <input type="text" v-model="editForm.leav_days" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- Leave From & To -->
                            <div class="form-group row custom_form_group mb-1 pb-0">
                                <label class="col-sm-2 control-label">Leave </label>
                                <div class="col-sm-4">
                                    <input type="date" v-model="editForm.start_date" class="form-control" required>
                                </div>
                                <label class="col-sm-2 control-label">To</label>
                                <div class="col-sm-4">
                                    <input type="date" v-model="editForm.end_date" class="form-control" required>
                                </div>
                            </div>

                            <!-- Type & Status -->
                            <div class="form-group row custom_form_group mb-1 pb-0">
                                <label class="col-sm-2 control-label">Type</label>
                                <div class="col-sm-4">
                                    <select v-model="editForm.leave_reason_id" class="form-select" required>
                                        <option v-for="reason in leaveReasons" :key="reason.lev_reas_id"
                                                :value="reason.lev_reas_id">
                                            {{ reason.lev_reas_name }}
                                        </option>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label text-left">Status:</label>
                                <div class="col-sm-4">
                                    <select v-model="editForm.application_status" class="form-select" required>
                                        <option v-for="status in applicationStatuses" :key="status.leav_sta_auto_id"
                                                :value="status.leav_sta_auto_id">
                                            {{ status.status_title }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div class="form-group row custom_form_group mb-0 pb-0">
                                <label class="col-sm-2 control-label text-right">Comments:</label>
                                <div class="col-sm-10">
                                    <textarea v-model="editForm.admin_comments" class="form-control" rows="3"
                                              placeholder="Comments Here"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success" :disabled="updating">
                                <i v-if="updating" class="fa fa-spinner fa-spin"></i>
                                {{ updating ? 'Updating...' : 'Update' }}
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
import {LeaveApplicationSalaryPendingList,LeaveApplicationUpdateAPI,LeaveApplicationRejectAPI} from '../../routes.js'

export default {
    name: 'LeaveAppSalaryPendingList',

    props: {
        leaveReasons: {
            type: Array,
            required: true
        },
        applicationStatuses: {
            type: Array,
            required: true
        },
        canUpdate: {
            type: Boolean,
            default: false
        },
        canReject: {
            type: Boolean,
            default: false
        },


    },

    data() {
        return {
            records: [],
            loading: false,
            updating: false,
            filters: {
                employee_id: '',
                start_date: '',
                end_date: '',
                application_status: 4
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0,
                from: 0,
                to: 0
            },
            editForm: {
                leav_auto_id: '',
                emp_auto_id: '',
                employee_details: '',
                project_name: '',
                mobile_no: '',
                akama_no: '',
                reference_by: '',
                description: '',
                appl_date: '',
                leav_days: '',
                start_date: '',
                end_date: '',
                leave_reason_id: '',
                application_status: '',
                admin_comments: '',
                exit_paper: null,
                exit_paper_name: '',
                exit_paper_preview: null
            },
            errors: {}
        }
    },

    mounted() {
        this.loadRecords();
    },

    methods: {
        async loadRecords() {
            this.loading = true;
            try {

                const response = await axios.get(`${LeaveApplicationSalaryPendingList}`);

                this.records = response.data.data;
                this.pagination = response.data.pagination;
            } catch (error) {
                console.error('Error loading records:', error);
                toast.error('Failed to load records');
            } finally {
                this.loading = false;
            }
        },

        async searchRecords() {
            this.loading = true;
            try {
                const response = await axios.get(`${LeaveApplicationPendingList}`, {
                    params: {
                        ...this.filters,
                        page: this.pagination.current_page,
                        per_page: this.pagination.per_page
                    }
                });
                this.records = response.data.data;
                this.pagination = response.data.pagination;
            } catch (error) {
                console.error('Error searching records:', error);
                toast.error('Search failed');
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters = {
                employee_id: '',
                start_date: '',
                end_date: '',
                application_status: ''
            };
            this.pagination.current_page = 1;
            this.searchRecords();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.searchRecords();
            }
        },

        openEditModal(record) {
            this.errors = {};
            this.editForm = {
                leav_auto_id: record.leav_auto_id,
                emp_auto_id: record.emp_auto_id,
                employee_details: `${record.employee_id}, ${record.employee_name}`,
                project_name: record.proj_name || '',
                mobile_no: record.mobile_no || '',
                akama_no: record.akama_no || '',
                reference_by: record.reference_by || '',
                description: record.description || '',
                appl_date: record.appl_date || '',
                leav_days: record.leav_days ? `${record.leav_days} Days` : '',
                start_date: record.start_date || '',
                end_date: record.end_date || '',
                leave_reason_id: record.leave_reason_id || '',
                application_status: record.appl_status || '',
                admin_comments: record.admin_comments || '',
                exit_paper: null,
                exit_paper_name: '',
                exit_paper_preview: null
            };
            $('#leave_application_edit_modal').modal('show');
        },

        async updateLeaveApplication() {
            this.updating = true;
            this.errors = {};

            const formData = new FormData();
            formData.append('leav_auto_id', this.editForm.leav_auto_id);
            formData.append('emp_auto_id', this.editForm.emp_auto_id);
            formData.append('appl_date', this.editForm.appl_date);
            formData.append('leave_days', this.editForm.leav_days);
            formData.append('start_date', this.editForm.start_date);
            formData.append('end_date', this.editForm.end_date);
            formData.append('leave_reason_id', this.editForm.leave_reason_id);
            formData.append('application_status', this.editForm.application_status);
            formData.append('admin_comments', this.editForm.admin_comments);

            if (this.editForm.exit_paper) {
                formData.append('exit_paper', this.editForm.exit_paper);
            }

            try {
                const response = await axios.post(`${LeaveApplicationUpdateAPI}`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (response.data.status === 200) {
                    $('#leave_application_edit_modal').modal('hide');
                    toast.success(response.data.message || 'Leave application updated successfully');
                    this.searchRecords();
                } else {
                    toast.error(response.data.message || 'Update failed');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    const firstError = Object.values(this.errors)[0];
                    if (firstError && firstError[0]) {
                        toast.error(firstError[0]);
                    }
                } else {
                    toast.error(error.response?.data?.message || 'An error occurred');
                }
                console.error('Update error:', error);
            } finally {
                this.updating = false;
            }
        },




        getStatusClass(status) {
            const classes = {
                'Pending': 'badge-warning',
                'Approved': 'badge-success',
                'Rejected': 'badge-danger',
                'Processing': 'badge-primary',
                'Completed': 'badge-info'
            };
            return classes[status] || 'badge-secondary';
        },

        getAttachmentUrl(path) {
            if (!path) return '#';
            return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
        },

        formatDate(date) {
            if (!date) return '';
            return new Date(date).toISOString().split('T')[0];
        }
    }
}
</script>

<style scoped>
.custom_form_group {
    margin-bottom: 1rem;
}
.req_star {
    color: red;
}
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
.upload_image {
    max-height: 100px;
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 4px;
}
.ml-2 {
    margin-left: 0.5rem;
}
.ml-3 {
    margin-left: 1rem;
}
.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 11px;
}
.badge-warning {
    background-color: #ffc107;
    color: #212529;
}
.badge-success {
    background-color: #28a745;
    color: white;
}
.badge-danger {
    background-color: #dc3545;
    color: white;
}
.badge-secondary {
    background-color: #6c757d;
    color: white;
}
.badge-info {
    background-color: #17a2b8;
    color: white;
}
.delete_icon {
    color: #dc3545;
}
</style>
