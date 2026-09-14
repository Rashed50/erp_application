<template>
    <div>

        <!-- Advance Menu Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                                <!-- can('ticket_approval') -->
                                <button @click.prevent="openSection('status')"
                                    :class="{ 'btn-primary': currentSection === 'status', 'btn-outline-primary': currentSection !== 'status' }"
                                    class="btn me-2">
                                    <i class="fa fa-pencil-square-o me-2" aria-hidden="true"></i>
                                    Status
                                </button>
                                <!-- endcan -->
                            </div>

                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                                <!-- @can('ticket_add') -->
                                <button @click.prevent="openSection('create')"
                                    :class="{ 'btn-primary': currentSection === 'create', 'btn-outline-primary': currentSection !== 'create' }"
                                    class="btn me-2">
                                    <i class="fa fa-plus me-2" aria-hidden="true"></i>
                                    Create
                                </button>
                                <!-- @endcan -->
                            </div>

                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                                <!-- @can('ticket_search') -->
                                <button @click.prevent="openSection('search')"
                                    :class="{ 'btn-primary': currentSection === 'search', 'btn-outline-primary': currentSection !== 'search' }"
                                    class="btn me-2">
                                    <i class="fa fa-search me-2" aria-hidden="true"></i>
                                    Search
                                </button>
                                <!-- @endcan -->
                            </div>

                            <div class="col-sm-1"></div>
                            <div class="col-sm-1">
                                <!-- @can('ticket_search') -->
                                <button @click.prevent="openSection('report')" data-toggle="modal"
                                    data-target="#report_modal"
                                    :class="{ 'btn-primary': currentSection === 'report', 'btn-outline-primary': currentSection !== 'report' }"
                                    class="btn me-2">
                                    <i class="fa fa-file-text me-2" aria-hidden="true"></i>
                                    Report
                                </button>
                                <!-- @endcan -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Approved/Pending Status -->
        <div v-if="currentSection === 'status'" class="row">
            <div>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Approved/Pending Section</h4>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">

                            <!-- Tickets Table -->
                            <div class="col-12">
                                <div class="table-responsive">
                                    <div id="alltableinfo_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                                        <!-- Table Date Showing -->
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table
                                                    class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
                                                    <thead>
                                                        <tr>
                                                            <th>S/N</th>
                                                            <!-- <th>Ticket For</th> -->
                                                            <th>Employee Details</th>
                                                            <th>Ticket Details</th>
                                                            <th>Qty</th>
                                                            <th>Expense</th>
                                                            <th>Remarks</th>
                                                            <th>Created at</th>
                                                            <!-- <th>Updated</th> -->
                                                            <th>Approval</th>
                                                            <th>File</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Show Loading Icon if Data is Loading -->
                                                        <tr v-if="loading">
                                                            <td colspan="10" class="text-center">
                                                                <i class="fa fa-spinner fa-spin fs-1"></i> Loading...
                                                            </td>
                                                        </tr>

                                                        <!-- Show Ticket Data -->
                                                        <tr v-for="(item, index) in tableData.data"
                                                            :key="item.ticket_auto_id">
                                                            <td>
                                                                {{ (tableData.current_page - 1) * tableData.per_page +
                                                                index +
                                                                1 }}
                                                            </td>



                                                            <td>
                                                                <div class="d-flex" style="flex-direction: column;">

                                                                    <span><strong>Emp. ID:</strong>
                                                                        {{ item.employee_info.employee_id }}
                                                                    </span>

                                                                    <span><strong>Name: </strong>
                                                                        {{ item.employee_info.employee_name }}
                                                                    </span>

                                                                    <span><strong>Iqama: </strong>
                                                                        {{ item.employee_info.akama_no  }}
                                                                    </span>

                                                                    <span><strong class="text-primary">Reference By:
                                                                        </strong>
                                                                        {{ item.reference_by }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="d-flex" style="flex-direction: column;">
                                                                    <span><strong>Ticket Type:</strong>
                                                                        {{ item.ticket_type }}
                                                                    </span>

                                                                    <span><strong>Ticket No:</strong>
                                                                        {{ item.ticket_number }}
                                                                    </span>

                                                                    <span><strong>Paid By:</strong>
                                                                        <span class="text-capitalize ms-2"
                                                                            v-if="item.paid_by == 'self'">Self</span>
                                                                        <span class="text-capitalize ms-2"
                                                                            v-if="item.paid_by == 'company'">Company</span>
                                                                    </span>

                                                                    <span><strong>Date:</strong>
                                                                        {{
                                                                        formatDateTime(item.confirm_date).formattedDate
                                                                        }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            <td>{{ item.qty }}</td>

                                                            <td class="text-right">
                                                                <span>{{ item.total_price }} SAR</span>
                                                            </td>
                                                            <td>For {{ capitalize(item.ticket_for) }} <br> {{ item.remarks }}</td>

                                                            <td>
                                                                <span>{{ formatDateTime(item.created_at).formattedDate
                                                                    }}</span><br>
                                                                <span>{{ formatDateTime(item.created_at).formattedTime
                                                                    }}</span>
                                                            </td>

                                                            <td>
                                                                <div class="toggle-container">
                                                                    <input type="checkbox"
                                                                        :id="'toggle-' + item.ticket_auto_id"
                                                                        class="toggle-input" v-model="item.is_approved"
                                                                        @change="approvedStatus(item)">
                                                                    <label :for="'toggle-' + item.ticket_auto_id"
                                                                        class="toggle-label"></label>
                                                                </div>
                                                                <div v-if="!item.is_approved">
                                                                    <a href="" @click="deleteATicket(item.ticket_auto_id)" >
                                                                        <i class="fa fa-trash  text-danger fa-lg" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>

                                                                <!-- Route::delete('delete/{id}', [TicketController::class, 'delete'])->name('ticket.delete'); -->
                                                            </td>

                                                            <td class="text-center">
                                                                <a  class="fw-bold"
                                                                    :href="getAttachmentUrl(item.attachment)"
                                                                    target="_blank">
                                                                    <i class="fa fa-file-text fs-1"  aria-hidden="true"></i>
                                                                </a>

                                                            </td>

                                                        </tr>

                                                        <!-- No data available message -->
                                                        <tr v-if="!loading && tableData.data.length === 0">
                                                            <td colspan="10" class="text-center">No data available in
                                                                table</td>
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
                </div>
            </div>
        </div>

        <!-- Create Section -->
        <div v-if="currentSection === 'create'" class="row">
            <form @submit.prevent="TicketSubmit" enctype="multipart/form-data">
                <div style="display: flex; justify-content: center; align-items: center;">

                    <div class="col-md-4" style="position: relative;">
                        <i class="fa fa-search search-icon" aria-hidden="true"></i>
                        <input type="text" class="form-control search_emp" v-model="search_employee"
                            @input="handleInput" placeholder="Search by Employee ID/ Passport" required
                            ref="searchEmployee">
                    </div>

                </div>

                <div v-if="employeeInfo">
                    <div class="card-body" style="display: flex; justify-content: center; align-items: center;">
                        <div class="col-md-10">
                            <table class="table table-striped border">
                                <tbody>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>{{ employeeInfo.employee_name }}</th>
                                    </tr>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>{{ employeeInfo.employee_id }}</th>

                                    </tr>
                                    <tr>
                                        <th>Passport NO</th>
                                        <th>{{ employeeInfo.passfort_no }}</th>
                                    </tr>
                                    <tr>
                                        <th>Iqama NO</th>
                                        <th>{{ employeeInfo.akama_no }}</th>
                                    </tr>
                                    <tr>
                                        <th>Phone NO</th>
                                        <th>{{ employeeInfo.mobile_no }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row border-top">
                        <div class="col-md-6">
                            <h4>Create New Ticket </h4>
                        </div>
                        <div class="col-md-6 text-right"></div>
                    </div>

                    <!-- body -->
                    <div class="card mt-0">
                        <div class="card-body">
                            <div class="row mb-4" id="main">

                                <!-- Left Column -->
                                <div class="col-md-5" id="sub-1">
                                    <!-- Ticket For -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Ticket For <span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select v-model="ticket_for" class="form-select" required>
                                                <option disabled value="">Select ticket for</option>
                                                <option v-for="ticket in data.ticketForOptions" :key="ticket"
                                                    :value="ticket">
                                                    {{ ticket.charAt(0).toUpperCase() + ticket.slice(1) }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Ticket Type -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Ticket Type <span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select v-model="ticket_type" class="form-select" required>
                                                <option disabled value="" selected>Select item</option>
                                                <option v-for="tk_type in data.ticketTypes" :key="tk_type"
                                                    :value="tk_type">
                                                    {{ tk_type.charAt(0).toUpperCase() + tk_type.slice(1) }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Paid By -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Paid By <span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select v-model="paid_by" class="form-select" required>
                                                <option disabled value="" selected>Select item</option>
                                                <option value="1" selected>Company</option>
                                                <option value="2">Self</option>
                                                <!-- <option v-for="p_by in data.paidByOptions" :key="p_by" :value="p_by">
                                                    {{ p_by.charAt(0).toUpperCase() + p_by.slice(1) }}
                                                </option> -->
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Ticket Number -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Ticket Number</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input class="form-control inpute_height" placeholder="Ticket Number"
                                                v-model="ticket_number" />
                                        </div>
                                    </div>

                                    <!-- Confirm Date -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Confirm Date<span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <!-- <input type="datetime-local" class="form-control" v-model="confirm_date"
                                                required /> -->
                                            <input type="date" class="form-control" v-model="confirm_date" required />
                                        </div>
                                    </div>

                                    <!-- Reference By -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Reference By</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input class="form-control inpute_height" placeholder="Reference..."
                                                v-model="reference_by" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1"></div>

                                <!-- Right Column (if needed) -->
                                <div class="col-md-5" id="sub-2">

                                    <!-- Quantity -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Quantity <span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" min="1" class="form-control" v-model="qty" required
                                                @input="totalCalculate" />
                                        </div>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Unit Price<span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" step="0.01" class="form-control" v-model="unit_price"
                                                @input="totalCalculate" />
                                        </div>
                                    </div>

                                    <!-- Total Price -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Total Price<span class="req_star">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" step="0.01" class="form-control" v-model="total_price"
                                                readonly />
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Remarks</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input class="form-control inpute_height" placeholder="Remarks..."
                                                v-model="remarks" />
                                        </div>
                                    </div>

                                    <!-- Attachment -->
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label>Attachment</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control" @change="onFileChange" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-success btn-sm mt-2" type="submit" :disabled="uploading">
                                <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                                <i v-else class="fa fa-check"></i>
                                &nbsp; Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Search Section -->
        <div v-if="currentSection === 'search'" class="row">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <h5 class="card-title">Search Section</h5><br><br>
                        <div class="col-sm-3">
                            <input type="text" v-model="searchInpute" class="form-control"
                                placeholder="Input Employee ID/Passport No" @input="searchField" ref="searchInput">
                        </div>
                        <div class="col-sm-3">
                            <input type="date" v-model="searchDateFrom" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <input type="date" v-model="searchDateTo" class="form-control">
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" @click="searchTicketInformation()"
                                class="btn btn-primary waves-effect">Search</button>
                        </div>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
            </div>

            <div>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Search Data</h4>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">

                            <!-- Tickets Table -->
                            <div class="col-12">
                                <div class="table-responsive">
                                    <div id="alltableinfo_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                                        <!-- Table Date Showing -->
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table
                                                    class="table table-bordered custom_table mb-0 dataTable table-hover no-footer">
                                                    <thead>
                                                        <tr>
                                                            <th>S/N</th>
                                                            <th>Ticket For</th>
                                                            <th>Employee Details</th>
                                                            <th>Ticket Details.</th>
                                                            <th>Remarks</th>
                                                            <th>Qty * Price</th>
                                                            <th>Total</th>
                                                            <th>Created</th>
                                                            <th>Approval</th>
                                                            <th>Attachment</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Show Loading Icon if Data is Loading -->
                                                        <tr v-if="loading">
                                                            <td colspan="10" class="text-center">
                                                                <i class="fa fa-spinner fa-spin fs-1"></i> Loading...
                                                            </td>
                                                        </tr>

                                                        <!-- Show Data -->
                                                        <tr v-for="(item, index) in searchTableData.data"
                                                            :key="item.ticket_auto_id">
                                                            <td>
                                                                {{ (tableData.current_page - 1) * tableData.per_page +
                                                                index +
                                                                1 }}
                                                            </td>

                                                            <td>{{ capitalize(item.ticket_for) }}</td>

                                                            <td>
                                                                <div class="d-flex" style="flex-direction: column;">
                                                                    <span><strong>Name: </strong>
                                                                        {{ item.employee_info.employee_name }}
                                                                    </span>

                                                                    <span><strong>Employee ID:</strong>
                                                                        {{ item.employee_info.employee_id }}
                                                                    </span>

                                                                    <span><strong>Passport NO: </strong>
                                                                        {{ item.employee_info.passfort_no }}
                                                                    </span>

                                                                    <span><strong class="text-primary">Reference By:
                                                                        </strong>
                                                                        {{ item.reference_by }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="d-flex" style="flex-direction: column;">
                                                                    <span><strong>Ticket Type:</strong>
                                                                        {{ item.ticket_type }}
                                                                    </span>

                                                                    <span><strong>Ticket Number:</strong>
                                                                        {{ item.ticket_number }}
                                                                    </span>

                                                                    <span><strong>Paid By:</strong>
                                                                        <span class="text-capitalize ms-2"
                                                                            v-if="item.paid_by == 'self'">Self</span>
                                                                        <span class="text-capitalize ms-2"
                                                                            v-else-if="item.paid_by == 'company'">Company</span>

                                                                    </span>



                                                                    <span><strong>Date:</strong>
                                                                        {{
                                                                        formatDateTime(item.confirm_date).formattedDate
                                                                        }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            <td>{{ item.remarks }}</td>

                                                            <td>{{ item.qty }} * {{ item.unit_price }}</td>

                                                            <td class="text-right">
                                                                <span>{{ item.total_price }}/-</span>
                                                            </td>

                                                            <td>
                                                                <span>{{ formatDateTime(item.created_at).formattedDate
                                                                    }}</span><br>
                                                                <span>{{ formatDateTime(item.created_at).formattedTime
                                                                    }}</span>
                                                            </td>

                                                            <td>
                                                                <span class="text-success fw-bold"
                                                                    v-if="item.is_approved">Approved By</span>
                                                                <span class="text-warning fw-bold"
                                                                    v-if="!item.is_approved">Pending</span>

                                                                <p v-if="item.is_approved">
                                                                    <span>{{ item.approved_by.name }}</span><br>
                                                                    <!-- <span>Email: {{ item.approved_by.email }}</span><br> -->
                                                                </p>
                                                            </td>

                                                            <td class="text-center">
                                                                <a v-if="item.attachment" class="fw-bold"
                                                                    :href="getAttachmentUrl(item.attachment)"
                                                                    target="_blank">
                                                                    <i class="fa fa-file-text fs-1"
                                                                        aria-hidden="true"></i>
                                                                </a>
                                                                <!-- <span v-else class="text-danger fw-bold">Not
                                                                    Uploaded</span> -->
                                                            </td>
                                                        </tr>

                                                        <!-- No data available message -->
                                                        <tr v-if="!loading && searchTableData.data.length === 0">
                                                            <td colspan="10" class="text-center">No data available in
                                                                table</td>
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
                </div>
            </div>
        </div>




        <!-- Modal -->
        <div class="modal fade" id="report_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Report Download</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" @submit.prevent="reportDownload" target="_blank">
                            <div class="d-flex gap-2 mb-2 w-100">
                                <div class="w-100">
                                    <label for="">From:</label>
                                    <input type="date" required class="form-control w-100" v-model="report.from">
                                </div>
                                <div class="w-100">
                                    <label for="">To:</label>
                                    <input type="date" required class="form-control w-100" v-model="report.to">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="">Ticket Type:</label>
                                <select class="form-select" required v-model="report.type">
                                    <option value="">Select Project</option>
                                    <option value="1">Ticket Details</option>
                                    <option value="2">Ticket Summary</option>
                                </select>
                            </div>

                            <div class=" mt-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-dismiss="modal">
                                    <i class="fa fa-times me-2" aria-hidden="true"></i>
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-arrow-down me-2" aria-hidden="true"></i>
                                    Download
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { TicketStoreAPI, EmployeeSearchAPI, TicketList, TicketListAPI, TicketSearchAPI, TicketReport,TicketDeleteAPI } from "../../routes.js";


export default {
    data() {
        const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
        return {
            // Approved/Pending Section
            tableData: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            searchTableData: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            perPage: 10,
            search: '',

            // Create Section
            currentSection: null,
            ticket_for: 'employee', // Default value
            emp_auto_id: null,
            guest_auto_id: null,
            ticket_type: null,
            paid_by: 2, // Default to 'Company'
            ticket_number: '',
            confirm_date: today,
            qty: 1,
            unit_price: 0,
            total_price: 0,
            remarks: '',
            reference_by: '',
            attachment: null,
            uploading: false,

            search_employee: null,
            employeeInfo: null,
            multipleEmployees: [],
            employeeError: false,

            searchInpute:'',
            searchDateFrom:null,
            searchDateTo:null,
            loading: false,

            report:{
                from: null,
                to: null,
                type: null
            },

        };
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },

    computed: {
    },

    mounted() {
    },

    methods: {
        openSection(section) {
            this.currentSection = section;

            if (section === 'status') {
                this.fetchTableData();
            }

            if (section === 'create') {
                this.$nextTick(() => {
                    this.$refs.searchEmployee.focus();
                });
            }

            if (section === 'search') {
                this.$nextTick(() => {
                    this.$refs.searchInput.focus();
                });
            }
        },

        // Approved/Pending Section --------------------------------------
        async fetchTableData(page = 1) {
            this.loading = true;
            try {
                const response = await axios.get(TicketListAPI, {
                    params: {
                        per_page: this.perPage,
                        page: parseInt(page, 10),
                        search: this.search
                    }
                });

                this.loading = false;
                this.tableData = response.data;

                this.tableData.data.forEach(item => {
                    item.is_approved = item.is_approved === 1;
                });

                // console.log("Fetched Table Data:", this.tableData);
                // this.tableData.data.forEach(item => {
                //     console.log(`Ticket ID: ${item.ticket_auto_id}, Approved Status: ${item.is_approved}`);
                // });

            } catch (error) {
                console.error('Error Fetching Tickets:', error);
            }
        },

        getAttachmentUrl(path) {
            // const baseUrl = `${window.location.origin}/storage/app/public`;
            // return `${baseUrl}${path}`;
             return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
        },

        changePage(page) {
            this.fetchTableData(page);
        },

        viewPage(id) {
            window.location = `${TicketDetails}/${id}`;
        },

        editPage(id) {
            window.location = `${TicketEdit}/${id}`;
        },


        formatDateTime(date) {
            if (!date) return '';

            // Format the date as DD/MM/YYYY
            const formattedDate = new Intl.DateTimeFormat('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(new Date(date));

            // Format the time as hh:mm AM/PM
            const formattedTime = new Intl.DateTimeFormat('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            }).format(new Date(date));

            return { formattedDate, formattedTime };
        },

        capitalize(value) {
            if (!value) return '';
            return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        },


        async approvedStatus(item) {
            try {
                // console.log("Ticket ID:", item.ticket_auto_id);
                // console.log("Approval Status:", item.is_approved ? 'Approved' : 'Pending');

                const approvalStatus = item.is_approved ? 1 : 0;

                const response = await axios.patch(`/admin/expense/ticket/tickets/${item.ticket_auto_id}`, {
                    is_approved: approvalStatus
                });

                if (response.data.message === 'Ticket approval status updated successfully.') {
                    toast.success("Ticket approval status updated.");
                    this.fetchTableData();
                } else {
                    toast.error("Failed to update approval status.");
                }
            } catch (error) {
                console.error('Error updating approval status:', error);
                item.is_approved = !item.is_approved;
                toast.error("Failed to update approval status.");
            }
        },

        // Create Section ------------------------------------------------
        totalCalculate() {
            this.total_price = this.qty * this.unit_price;
        },

        async TicketSubmit() {
            this.uploading = true;
            const formData = new FormData();

            if (this.employeeInfo) {
                formData.append('emp_auto_id', this.employeeInfo.emp_auto_id);
            } else {
                toast.error('Please Give Valid Employee ID.');
                this.uploading = false;
                return;
            }
             debugger;
            console.log("Employee Auto ID:",  this.paid_by); // Debugging line


            formData.append('ticket_for', this.ticket_for);
            formData.append('guest_auto_id', this.guest_auto_id);
            formData.append('ticket_type', this.ticket_type);
            formData.append('paid_by', this.paid_by);
            formData.append('ticket_number', this.ticket_number);
            formData.append('confirm_date', this.confirm_date);
            formData.append('qty', this.qty);
            formData.append('unit_price', this.unit_price);

            if (this.unit_price <= 0) {
                toast.error('Unit Price should be greater than 0.');
                this.uploading = false;
                return;
            }

            formData.append('total_price', this.total_price);

            if (this.total_price <= 0) {
                toast.error('Total Price should be greater than 0.');
                this.uploading = false;
                return;
            }

            formData.append('remarks', this.remarks);
            formData.append('reference_by', this.reference_by);

            if (this.attachment) {
                formData.append('attachment', this.attachment);
            }

            try {
                // Send the request
                const response = await axios.post(TicketStoreAPI, formData);
                toast.success('Ticket created successfully!');

                this.resetForm();

                // Optionally redirect to ticket list or reset data as needed
                // setTimeout(() => {
                //     window.location = TicketList;
                // }, 1000);
            } catch (error) {
                toast.error('Error creating ticket.');
            } finally {
                this.uploading = false;
            }
        },

        async deleteATicket(id) {
            if (!confirm("Are you sure you want to delete this Record?")) {
                return;
            }

            try {
                const res = await axios.delete(TicketDeleteAPI + id);

                if (res.data.status == 200) {
                    toast.success('Record deleted successfully!');
                  // this.opensection('status');
                   this.fetchTableData();
                } else {
                    toast.error('Failed to delete Record.');
                }
            } catch (error) {
                console.error('Error deleting record:', error);
                toast.error('Failed to delete Record.');
            }
        },

        // Function to reset all form fields
        resetForm() {
            this.search_employee = '';
            this.ticket_for = '';
            this.ticket_type = '';
            this.paid_by = '2'; // Default to 'Company'
            this.ticket_number = '';
            this.confirm_date = '';
            this.qty = 1;
            this.unit_price = 0;
            this.total_price = 0;
            this.remarks = '';
            this.reference_by = '';
            this.attachment = null;

            // Optionally reset employee information
            this.employeeInfo = null;

            // Optionally reset other variables if needed
            // this.guest_auto_id = null;
        },

        onFileChange(event) {
            this.attachment = event.target.files[0];
        },

        async searchEmployee() {
            // Reset error and info states
            this.employeeInfo = null;
            this.multipleEmployees = [];
            this.employeeError = false;

            // Define search parameters dynamically
            const searchType = this.determineCreateSearchType(this.search_employee);
            const searchValue = this.search_employee;

            // Ensure there's input to search for
            if (!searchValue) {
                console.error("Please Enter Employee ID/Iqama/Passport Number");
                return;
            }

            try {
                const response = await axios.post(EmployeeSearchAPI, {
                    search_by: searchType,
                    employee_searching_value: searchValue
                });

                if (response.data.status !== 200) {
                    this.employeeError = true;
                    return;
                }

                const foundEmployees = response.data.findEmployee;
                if (foundEmployees.length > 1) {
                    this.multipleEmployees = foundEmployees;
                } else if (foundEmployees.length === 1) {
                    this.employeeInfo = foundEmployees[0];
                } else {
                    this.employeeError = true;
                }
            } catch (error) {
                console.error("Error searching employee:", error);
            }
        },


        handleInput() {
            this.search_employee = this.search_employee.toUpperCase();
            this.searchEmployee();
        },

        // Determine search type based on the first character
        determineCreateSearchType(value) {
            const firstChar = value.charAt(0).toLowerCase();
            return isNaN(firstChar) ? 'passfort_no' : 'employee_id';
        },



        // Search Section ------------------------------------------------
        determineSearchType(value) {
            const firstChar = value.charAt(0).toLowerCase();
            return isNaN(firstChar) ? 'passport_no' : 'employee_id';
        },


        searchField() {
            this.searchInpute = this.searchInpute.toUpperCase();
        },


        async searchTicketInformation() {
            this.employeeError = false;
            this.loading = true;

            const searchInputType = this.determineSearchType(this.searchInpute);

            // console.log("Value =", this.searchInpute)

            let params = {
                dateFrom: this.searchDateFrom,
                dateTo: this.searchDateTo
            };

            if (searchInputType === 'employee_id') {
                params.employeeId = this.searchInpute;
            } else if (searchInputType === 'passport_no') {
                params.passportId = this.searchInpute;
            }

            try {
                const response = await axios.get(TicketSearchAPI, { params });

                if (response.data.status !== 200) {
                    this.employeeError = true;
                    return;
                }

                // console.log("Data =", response.data)
                this.searchTableData.data = response.data.data;
                this.searchTableData.total = response.data.total;
            } catch (error) {
                console.error('Search error:', error);
                this.employeeError = true;
            } finally {
                this.loading = false;
            }
        },



        // Report Section ------------------------------------------------
        async reportDownload() {
            const queryParams = new URLSearchParams({
                from : this.report.from,
                to   : this.report.to,
                type : this.report.type,
            }).toString();

            const url = `${TicketReport}?${queryParams}`;
            window.open(url, '_blank');
        }
    }
};
</script>



<style scoped>
/* Add any specific styles here */
.toggle-container {
    display: flex;
    align-items: center;
}

.toggle-input {
    display: none;
}

.toggle-label {
    width: 60px;
    height: 30px;
    background-color: #ccc;
    border-radius: 50px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.toggle-label::before {
    content: '';
    position: absolute;
    top: 4px;
    left: 4px;
    width: 22px;
    height: 22px;
    background-color: white;
    border-radius: 50%;
    transition: left 0.3s ease;
}

.toggle-input:checked+.toggle-label {
    background-color: #4CAF50;
}

.toggle-input:checked+.toggle-label::before {
    left: 34px;
}

.search_emp {
    border: solid black 1px;
    outline: none;
    border-radius: 7px;
    height: 35px;
    padding-left: 40px;
    /* আইকন এবং টেক্সটের মধ্যে যথাযথ জায়গা */
    transition: border-color 0.3s ease;
}

.search_emp:focus {
    border-color: skyblue;
    /* input focus হলে বর্ডারের রঙ পরিবর্তন */
}

/* আইকন বড় করার জন্য */
.search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    /* আইকনের আকার বড় করা */
    color: #333;
    /* আইকনের রং */
    transition: color 0.3s ease;
    /* আইকনের রঙের পরিবর্তনের জন্য ট্রানজিশন */
}

/* যখন input focus হবে, তখন আইকনের রঙ পরিবর্তন হবে */
.search_emp:focus~.search-icon {
    color: skyblue;
    /* focus হলে আইকনের রঙ skyblue হবে */
}
</style>
