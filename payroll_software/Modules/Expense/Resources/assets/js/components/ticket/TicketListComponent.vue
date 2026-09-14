<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <h4>Ticket List</h4>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">
                <div class="row">

                    <!-- Tickets Table -->
                    <div class="col-12">
                        <div class="table-responsive">
                            <div id="alltableinfo_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                                <!-- Search and Page Set -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_length" id="alltableinfo_length">
                                            <label><strong>Show </strong>
                                                <select v-model="perPage" @change="fetchTableData"
                                                    class="custom-select custom-select-sm">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <strong> entries</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div id="alltableinfo_filter" class="dataTables_filter">
                                            <label><strong>Search:</strong>
                                                <input v-model="search" @input="fetchTableData" type="search"
                                                    class="form-control form-control-lg" placeholder="Search...">
                                            </label>
                                        </div>
                                    </div>
                                </div>

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
                                                    <th>Type</th>
                                                    <th>Ticket Number</th>
                                                    <th>Confirm Date</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total Price</th>
                                                    <th>Remarks</th>
                                                    <th>Reference By</th>
                                                    <th>Attachment</th>
                                                    <th>Created</th>
                                                    <th>Updated</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in tableData.data" :key="item.ticket_auto_id">
                                                    <td>
                                                        {{ (tableData.current_page - 1) * tableData.per_page + index +
                                                        1}}
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
                                                        </div>
                                                    </td>
                                                    <td>{{ item.ticket_type }}</td>
                                                    <td>{{ item.ticket_number }}</td>
                                                    <td>{{ formatDateTime(item.confirm_date).formattedDate }}</td>
                                                    <td>{{ item.qty }}</td>
                                                    <td>{{ item.unit_price }}</td>
                                                    <td>{{ item.total_price }}</td>
                                                    <td>{{ item.remarks }}</td>
                                                    <td>{{ item.reference_by }}</td>
                                                    <td>
                                                        <a v-if="item.attachment" class="fw-bold"
                                                            :href="getAttachmentUrl(item.attachment)"
                                                            target="_blank">View</a>
                                                        <span v-else class="text-danger fw-bold">Not Uploaded</span>

                                                    </td>
                                                    <td>
                                                        <span>{{ formatDateTime(item.created_at).formattedDate
                                                            }}</span><br>
                                                        <span>{{ formatDateTime(item.created_at).formattedTime }}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{ formatDateTime(item.updated_at).formattedDate
                                                            }}</span><br>
                                                        <span>{{ formatDateTime(item.updated_at).formattedTime }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="flex-container">
                                                            <a class="view_btn" @click="viewPage(item.ticket_auto_id)">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <!-- <a class="edit_btn" @click="editPage(item.ticket_auto_id)">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a> -->
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr v-if="tableData.data.length === 0">
                                                    <td colspan="4" class="text-center">No data available in table</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <div class="dataTables_info" role="status" aria-live="polite">
                                            <strong>
                                                Showing
                                                {{ tableData.from || 0 }}
                                                to
                                                {{ tableData.to || 0 }}
                                                of
                                                {{ tableData.total || 0 }}
                                                entries
                                            </strong>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers">
                                            <ul class="pagination">
                                                <li class="paginate_button page-item"
                                                    :class="{ disabled: !tableData.prev_page_url }">
                                                    <a href="#" @click.prevent="changePage(tableData.current_page - 1)"
                                                        class="page-link">Previous</a>
                                                </li>

                                                <li class="paginate_button page-item" v-for="page in totalPages"
                                                    :key="page" :class="{ active: page === tableData.current_page }">
                                                    <a href="#" @click.prevent="changePage(page)" class="page-link">{{
                                                        page }}</a>
                                                </li>

                                                <li class="paginate_button page-item"
                                                    :class="{ disabled: !tableData.next_page_url }">
                                                    <a href="#" @click.prevent="changePage(tableData.current_page + 1)"
                                                        class="page-link">Next
                                                    </a>
                                                </li>
                                            </ul>
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
</template>

<script>
import axios from 'axios';
// import { formatDateTime, capitalize } from '../utils/helpers'; // Custom helpers if needed
import { TicketListAPI } from "../../routes";

export default {
    data() {
        return {
            tableData: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            perPage: 10,
            search: ''
        };
    },

    computed: {
        totalPages() {
            return Array.from({ length: this.tableData.last_page }, (_, i) => i + 1);
        }
    },

    mounted() {
        this.fetchTableData();
    },

    methods: {
        async fetchTableData(page = 1) {
            try {
                const response = await axios.get(TicketListAPI, {
                    params: {
                        per_page: this.perPage,
                        page: parseInt(page, 10),
                        search: this.search
                    }
                });
                this.tableData = response.data;
                console.log("List Data =", response.data);
            } catch (error) {
                console.error('Error Fetching Tickets:', error);
            }
        },

        getAttachmentUrl(path) {

            // const baseUrl = `${window.location.origin}/storage/`;
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
        }
    }
};
</script>


<style scoped>
.btn_width {
    min-width: 50px;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    padding: 10px;
}

.filter-grid .mb-3 {
    min-width: 300px;
}

.input_h {
    height: 43px;
}

.flex-container {
    display: flex;
    flex-direction: row;
    justify-content: start;
    gap: 10px;
}

.flex-container a {
    font-size: 17px;
    text-align: center;
    cursor: pointer;
}

.view_btn:hover i {
    color: rgb(205, 11, 235);
}

.edit_btn:hover i {
    color: rgb(207, 46, 21);
}

.form-control {
    border: 2px solid #ccc;
    border-radius: 3px;
    padding: 8px;
    transition: border-color 0.3s;
    min-width: 300px;
}

.sky-border {
    border-color: skyblue;
    box-shadow: 0 0 5px rgba(135, 206, 250, 0.5);
}

.form-control:focus {
    border-color: dodgerblue;
    outline: none;
    box-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
}
</style>
