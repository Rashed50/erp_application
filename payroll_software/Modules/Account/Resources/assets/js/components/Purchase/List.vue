<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <h4>Purchased Invoices</h4>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="filter-grid">

                        <div class="mb-1">
                            <label for="">Project</label>
                            <Multiselect v-model="project_id" :options="projectOptions" placeholder="Select Project"
                                :searchable="true" @input="searchByProject" />
                        </div>
                        <!-- Type Selection -->
                        <div class="mb-1">
                            <label for="">Type</label>
                            <Multiselect v-model="selectedType" :options="typeOptions" placeholder="Select Item Type"
                                :searchable="true" @input="fetchTypeData" />
                        </div>
                        <div class="mb-1">
                            <label for="">Supplier</label>
                            <Multiselect v-model="supplier_id" :options="supplierOptions" placeholder="Select Supplier"
                                :searchable="true" @input="searchBySupplier" />
                        </div>

                        <!-- Date Type Selection -->
                        <div class="mb-1">
                            <label for="">Date Type</label>
                            <Multiselect v-model="selectedDate" :options="dateOptions" placeholder="Select Date Type"
                                :searchable="true" @input="handleDateTypeSelection" />
                        </div>

                        <!-- From Date Input -->
                        <div class="mb-1">
                            <label for="">From</label>
                            <input type="date" class="form-control input_h" v-model="dateFrom" :disabled="!selectedDate"
                                @input="fetchTableData()">
                        </div>

                        <!-- To Date Input -->
                        <div class="mb-1">
                            <label for="">To</label>
                            <input type="date" class="form-control input_h" v-model="dateTO" :disabled="!selectedDate"
                                @input="fetchTableData()">
                        </div>

                        <!-- Report Type -->
                        <div class="mb-1">
                            <label for="">Report Type</label>
                            <select name="report_type" id="" class="form-control inpute_h" v-model="report_type">
                                <option value="1" selected>Purchased Report</option>
                                <option value="2" >Supplier Wise Purchase</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">

                        <!-- Reset Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-danger btn-sm px-3 py-1" @click="resetFilter">
                                <i class="fa fa-eraser me-2" aria-hidden="true"></i>
                                Reset
                            </button>
                        </div>

                        <!-- Download Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-info btn-sm px-3 py-1" @click="downloadPDF">
                                <i class="fa fa-download me-2" aria-hidden="true"></i>
                                Download
                            </button>
                        </div>

                        <!-- Create Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-success btn-sm px-3 py-1" @click="addNew">
                                <i class="fa fa-plus me-2" aria-hidden="true"></i>
                                Create
                            </button>
                        </div>
                    </div>


                    <!-- Purchase Invoices Table -->
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
                                                    :class="{ 'sky-border': search.length > 0 }"
                                                    class="form-control form-control-lg" placeholder="Search...">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table Date Showing -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table
                                            class="table  table-bordered custom_table mb-0 table-hover no-footer">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Type</th>
                                                    <th>Invoice No.</th>
                                                    <th>Supplier</th>
                                                    <th>Project</th>
                                                    <th>Purc.Date</th>
                                                    <th>Created</th>
                                                    <th>Total</th>
                                                    <th>Vat</th>
                                                    <th>Discount</th>
                                                    <th>Net Amount</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in items.data" :key="item.pur_id">
                                                    <td>{{ (items.current_page - 1) * items.per_page + index + 1 }}</td>
                                                    <td>
                                                        <span class="badge bg-primary fs-6 w-100 p-2">
                                                            <!-- {{ capitalize(item.purchase_type) }} -->
                                                            {{ item.purchase_type[0].toUpperCase() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <!-- <span class="badge bg-success fs-6 w-100 p-2">

                                                        </span> -->
                                                           {{ item.invoice_number }}
                                                    </td>
                                                    <td>{{ item.supplier?.supplier_name }}</td>
                                                    <td>{{ item.project?.proj_name }}</td>
                                                    <td>{{ formatDateTime(item.purchase_date).formattedDate }}</td>
                                                     <td>
                                                        <span>
                                                            {{ formatDateTime(item.created_at).formattedDate
                                                            }}
                                                        </span><br>
                                                        <!-- <span>{{ formatDateTime(item.created_at).formattedTime
                                                            }}
                                                        </span><br> -->
                                                    </td>
                                                    <td>{{ item.total_amount }}</td>
                                                    <td>{{ item.vat_amount }}</td>
                                                    <td>{{ item.discount_amount }}</td>
                                                    <td>{{ item.net_total }}</td>


                                                    <td>
                                                        <div class="flex-container">
                                                            <a class="view_btn" v-if="hasPermission('accounts_purchase_invoice_view')"  @click="viewPage(item.pur_id)">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a class="edit_btn"  v-if="hasPermission('accounts_purchase_invoice_edit')"  @click="editPage(item.pur_id)">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr v-if="!items.data.length">
                                                    <td colspan="12" class="text-center">No data available in table</td>
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
                                                {{ items.from || 0 }}
                                                to
                                                {{ items.to || 0 }}
                                                of
                                                {{ items.total || 0 }}
                                                entries
                                            </strong>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers">
                                            <ul class="pagination">
                                                <li class="paginate_button page-item"
                                                    :class="{ disabled: !items.prev_page_url }">
                                                    <a href="#" @click.prevent="changePage(items.current_page - 1)"
                                                        class="page-link">Previous</a>
                                                </li>

                                                <li class="paginate_button page-item" v-for="page in totalPages"
                                                    :key="page" :class="{ active: page === items.current_page }">
                                                    <a href="#" @click.prevent="changePage(page)" class="page-link">{{
                                                        page }}</a>
                                                </li>

                                                <li class="paginate_button page-item"
                                                    :class="{ disabled: !items.next_page_url }">
                                                    <a href="#" @click.prevent="changePage(items.current_page + 1)"
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
import Multiselect from '@vueform/multiselect'
import { toast } from 'vue3-toastify';
import { PurchaseInvoiceCreate, PurchaseInvoiceListAPI, PurchaseInvoiceDetails, PurchaseInvoiceEdit } from "../../routes";
import { data } from 'jquery';

// role and permission composable
import { inject } from 'vue';
import {useAuth} from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    components: {
        Multiselect,
    },


    props: {
        data: {
            type: Object,
            required: true
        }
    },


    data() {
        // Today
        const today = new Date();

        // 7 days ago
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(today.getDate() - 7);

        // 30 days ago
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);

        // helper function — convert date to YYYY-MM-DD
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };

        return {
            items: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            perPage: 10,

            // Search field
            search: '',

            // Filter field
            project_id:null,
            selectedType: null,
            supplier_id:null,
            selectedDate: 'issue_date',
            dateFrom: formatDate(thirtyDaysAgo),
            dateTO: formatDate(today),
            report_type: 1,
        };
    },


    computed: {
        // Options for the Multiselect dropdown
         projectOptions() {
            return [{ label: 'Select Type', value: null }, ...this.data.projects.map(d => ({ label: d.proj_name, value: d.proj_id }))];
        },

        typeOptions() {
            return [{ label: 'Select Type', value: null }, ...this.data.purchase_types.map(d => ({ label: d.name, value: d.value }))];
        },
        supplierOptions() {
            return [{ label: 'Select Supplier', value: null }, ...this.data.suppliers.map(d => ({ label: d.supplier_name, value: d.supplier_id }))];
        },

        dateOptions() {
            return [
                { label: 'Issue Date', value: 'issue_date' },
                { label: 'Purchase Date', value: 'purchase_date' },
                { label: 'Entry Date', value: 'created_at' },
            ];
        },

        totalPages() {
            return Array.from({ length: this.items.last_page }, (_, i) => i + 1);
        }
    },


    mounted() {
        this.fetchTableData();
    },


    methods: {
        async fetchTableData(page = 1, isDownload = false) {
            try {
                if (isDownload) {
                    // Open the PDF in a new tab
                    const params = new URLSearchParams({
                        per_page: this.perPage,
                        page: parseInt(page, 10),
                        search: this.search,
                        project_id:this.project_id || '',
                        type: this.selectedType || '',
                        supplier_id:this.supplier_id || '',
                        selectedDate: this.selectedDate || '',
                        dateFrom: this.dateFrom || '',
                        dateTO: this.dateTO || '',
                        is_download: 1,  // Set download flag
                        report_type: this.report_type
                    }).toString();

                    if (this.report_type == 2) {
                        if (this.supplier_id == null) {
                            toast.error('Please select a supplier');
                            return;
                        } else if (this.dateFrom == null && this.dateTO == null) {
                            toast.error('Please select a date range');
                            return;
                        }
                    }

                    // Open a new tab with the query params
                    window.open(`${PurchaseInvoiceListAPI}?${params}`, '_blank');
                } else {
                    // Fetch paginated data as usual
                    const response = await axios.get(PurchaseInvoiceListAPI, {
                        params: {
                            per_page: this.perPage,
                            page: parseInt(page, 10),
                            search: this.search,
                            project_id:this.project_id || '',
                            type: this.selectedType || '',
                              supplier_id:this.supplier_id || '',
                            selectedDate: this.selectedDate || '',
                            dateFrom: this.dateFrom || '',
                            dateTO: this.dateTO || ''
                        }
                    });
                    this.items = response.data;
                }
            } catch (error) {
                console.error('Error Fetching Items:', error);
            }
        },

        // async fetchTableData(page = 1, isDownload = false) {
        //     try {
        //         const response = await axios.get(PurchaseInvoiceListAPI, {
        //             params: {
        //                 per_page: this.perPage,
        //                 page: parseInt(page, 10),
        //                 search: this.search,

        //                 type: this.selectedType ? this.selectedType : '',
        //                 selectedDate: this.selectedDate ? this.selectedDate : '',
        //                 dateFrom: this.dateFrom ? this.dateFrom : '',
        //                 dateTO: this.dateTO ? this.dateTO : '',
        //                 is_download: isDownload ? 1 : 0
        //             },
        //             responseType: isDownload ? 'blob' : 'json' // Expect binary data for download
        //         });

        //         if (isDownload) {
        //             // Handle the download response
        //             const url = window.URL.createObjectURL(new Blob([response.data]));
        //             const link = document.createElement('a');
        //             link.href = url;
        //             link.setAttribute('download', `purchase_report_${new Date().toISOString()}.pdf`); // Set a file name for the PDF
        //             document.body.appendChild(link);
        //             link.click();
        //             document.body.removeChild(link);
        //         } else {
        //             this.items = response.data;
        //         }
        //     } catch (error) {
        //         console.error('Error Fetching Items:', error);
        //     }
        // },

        downloadPDF() {
            // Call the fetchTableData with isDownload flag set to true
            this.fetchTableData(1, true);
        },
         searchByProject(selected) {
            this.project_id = selected;
            this.fetchTableData();
        },

        fetchTypeData(selected) {
            this.selectedType = selected;
            this.fetchTableData();
        },

        searchBySupplier(selected) {
            this.supplier_id = selected;
            this.fetchTableData();
        },



        fetchDate(selected) {
            this.selectedDate = selected;
            // Enable or disable date fields based on the selection
            if (!selected) {
                this.dateFrom = null;
                this.dateTO = null;
            }
            this.fetchTableData();
        },

        resetFilter() {
            this.project_id = null
            this.selectedType = null;
            this.supplier_id = null,
            this.selectedDate = null;
            this.dateFrom = null;
            this.dateTO = null;
            this.search = '';
            this.fetchTableData();
        },

        changePage(page) {
            this.fetchTableData(page);
        },

        addNew() {
            window.location = PurchaseInvoiceCreate;
        },

        viewPage(id) {
            window.location = PurchaseInvoiceDetails + '/' + id;
        },

        editPage(id) {
            window.location = PurchaseInvoiceEdit + '/' + id;
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
    .btn_width{
        min-width: 50px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 8px;
        padding: 10px;
    }
    .filter-grid .mb-3 {
        min-width: 300px;
    }
    .input_h{
        height: 43px;
    }
    .flex-container{
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

    .view_btn:hover i{
        color: rgb(205, 11, 235);
    }
    .edit_btn:hover i{
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
