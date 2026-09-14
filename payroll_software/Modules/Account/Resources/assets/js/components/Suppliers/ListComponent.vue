<template>
    <div>

        <div class="card mt-2">
            <div class="card-header" style="background: rgb(239, 234, 234); color: rgb(55, 55, 212);">
                <div class="row">
                    <div class="col-md-8">
                        <h4>List of suppliers</h4>
                    </div>
                    <div class="col-md-4 text-right">
                        <a class="btn btn-md btn-primary waves-effect card_top_button" @click="addNew">
                            <i class="fa fa-plus-circle mr-2"></i>New Supplier
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">

                    <!-- Filter -->
                    <div class="row d-flex justify-content-center">
                        <div class="mb-3 col-md-3">
                            <Multiselect
                                v-model="selectedBranch"
                                :options="branchOptions"
                                placeholder="Select branch"
                                :searchable="true"
                                @input="onBranchChange" />
                        </div>

                        <div class="mb-3 col-md-3">
                            <Multiselect
                                v-model="selectedActive"
                                :options="activeOptions"
                                placeholder="Select active status"
                                :searchable="true"
                                @input="onActiveChange" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <div id="alltableinfo_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                                <!-- Search and Page Set -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_length" id="alltableinfo_length">
                                            <label>Show
                                                <select v-model="perPage" @change="fetchTableData"
                                                    class="custom-select custom-select-sm form-control form-control-sm">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                entries
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div id="alltableinfo_filter" class="dataTables_filter">
                                            <label>Search:
                                                <input v-model="search" @input="fetchTableData" type="search" class="form-control form-control-sm" placeholder="">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table Date Showing -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered custom_table mb-0 dataTable no-footer" role="grid" aria-describedby="alltableinfo_info">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Supplier Name</th>
                                                    <th>VAT Number</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Address</th>
                                                    <th>Balance</th>
                                                    <th>Status</th>
                                                     <th>Created</th>
                                                    <th>Updated</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(supplier, index) in suppliers.data" :key="supplier.supplier_id">
                                                    <td>{{ (suppliers.current_page - 1) * suppliers.per_page + index + 1 }}</td>
                                                    <td>{{ supplier.supplier_name }}</td>
                                                     <td>{{ supplier.vat_no }}</td>
                                                    <td>{{ supplier.supplier_email }}</td>
                                                    <td>{{ supplier.supplier_phone }}</td>
                                                    <td>{{ supplier.supplier_address }}</td>
                                                    <td>{{ supplier.current_balance }}</td>
                                                    <td>
                                                        <span :class="{'badge bg-success': supplier.active_status === 1, 'badge bg-danger': supplier.active_status === 0}">
                                                            {{ supplier.active_status === 1 ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span>{{ formatDateTime(supplier.updated_at).formattedDate }}</span><br>
                                                        <span>{{ formatDateTime(supplier.updated_at).formattedTime }}</span><br>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm" v-if="hasPermission('accounts_supplier_edit')" @click="editSupplier(supplier.supplier_id)">
                                                            Edit
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" v-if="hasPermission('accounts_supplier_delete')" @click="deleteSupplier(supplier.supplier_id)">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- No data available -->
                                                <tr v-if="!suppliers.data.length">
                                                    <td colspan="8" class="text-center">No data available in table</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <div class="dataTables_info" role="status" aria-live="polite">
                                            Showing
                                            {{ suppliers.from || 0 }}
                                            to
                                            {{ suppliers.to || 0 }}
                                            of
                                            {{ suppliers.total || 0 }}
                                            entries
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers">
                                            <ul class="pagination">
                                                <li class="paginate_button page-item" :class="{ disabled: !suppliers.prev_page_url }">
                                                    <a href="#" @click.prevent="changePage(suppliers.current_page - 1)" class="page-link">Previous</a>
                                                </li>

                                                <li class="paginate_button page-item"
                                                    v-for="page in totalPages" :key="page" :class="{ active: page === suppliers.current_page }">
                                                    <a href="#" @click.prevent="changePage(page)" class="page-link">{{ page }}</a>
                                                </li>

                                                <li class="paginate_button page-item" :class="{ disabled: !suppliers.next_page_url }">
                                                    <a href="#" @click.prevent="changePage(suppliers.current_page + 1)"
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
import { InventorySuppliersCreate, InventorySuppliersListAPI, InventorySuppliersDelete, InventorySuppliersEdit } from "../../routes";

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
        return {
            suppliers: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            perPage: 10,
            search: '',
            selectedBranch: null,
            selectedActive: null,
        };
    },

    computed: {
        branchOptions() {
            // console.log(this.data.branchs)
            return [{ label: 'Select branch', value: null }, ...this.data.branchs.map(d => ({ label: d.branch_name_en, value: d.braoff_auto_id }))];
        },

        activeOptions(){
            return [
                { label: 'Select active status', value:null },
                { label: 'Active', value: 1 },
                { label: 'Inactive', value: 0 }
            ];
        },

        totalPages() {
            return Array.from({ length: this.suppliers.last_page }, (_, i) => i + 1);
        }
    },

    mounted() {
    },

    methods: {
        async fetchTableData(page = 1) {
            // console.log("Branch =",this.selectedBranch ? this.selectedBranch : '')
            // console.log("Active =",this.selectedActive)
            try {
                const response = await axios.get(InventorySuppliersListAPI, {
                    params: {
                        per_page: this.perPage,
                        page: parseInt(page, 10),
                        search: this.search,

                        branch: this.selectedBranch ? this.selectedBranch : '',
                        active: this.selectedActive,
                    },
                });
                this.suppliers = response.data;
            } catch (error) {
                console.error('Error fetching suppliers:', error);
            }
        },

        onBranchChange(selected) {
            this.selectedBranch = selected;
            // console.log('Branch Changed:', selected);
            this.fetchTableData();
        },

        onActiveChange(selected) {
            this.selectedActive = selected;
            // console.log('Active Status Changed:', selected);
            this.fetchTableData();
        },

        async deleteSupplier(id) {
            if (confirm('Are you sure you want to delete this supplier?')) {
                try {
                    const response = await axios.delete(`${InventorySuppliersDelete}/${id}`);
                    toast.success(response.data.message);
                    this.fetchTableData();
                } catch (error) {
                    toast.error(error.response?.data?.message || 'An error occurred');
                }
            }
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

        editSupplier(id) {
            window.location = `${InventorySuppliersEdit}/${id}`;
        },

        changePage(page) {
            this.fetchTableData(page);
        },

        addNew() {
            window.location = InventorySuppliersCreate;
        }
    },

    mounted() {
        this.fetchTableData();
    }
}
</script>
