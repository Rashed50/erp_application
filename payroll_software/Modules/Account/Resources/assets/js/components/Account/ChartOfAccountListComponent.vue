<template lang="">
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Ledger Accounts</h4>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4 text-right">
                        <a class="btn btn-md btn-primary waves-effect card_top_button" @click="addNew" v-if="hasPermission('accounts_ledger_account_create')">
                            <i class="fa fa-plus-circle mr-2"></i>New Account
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <div id="alltableinfo_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

                                <!-- Search and Page Set -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_length" id="alltableinfo_length">
                                            <label>Show
                                                <select v-model="perPage" @change="fetchChartOfAccounts"
                                                    class="custom-select custom-select-sm form-control form-control-sm">
                                                    <!-- <option value="5">5</option> -->
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
                                                <input v-model="search" @input="fetchChartOfAccounts" type="search" class="form-control form-control-sm" placeholder="">
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <!-- Table Date Showing -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="responsive table table-bordered custom_table mb-0 dataTable no-footer"
                                            role="grid" aria-describedby="alltableinfo_info">
                                            <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Type</th>
                                                    <th>Acc. Name</th>
                                                    <th>Number</th>
                                                    <th>Parent</th>
                                                    <!-- <th>Balance</th> -->
                                                    <th>Opening</th>
                                                    <th>Predefined</th>
                                                    <th>Status</th>
                                                    <th>IsClosed</th>
                                                    <th>Created</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(account, index) in chartOfAccounts.data" :key="account.id">
                                                    <td>{{ (chartOfAccounts.current_page - 1) * chartOfAccounts.per_page + index + 1 }}</td>
                                                    <td>
                                                        <span class="badge bg-primary p-2 w-100">
                                                            {{ account.acct_type_name }}
                                                        </span>
                                                    </td>
                                                    <td>{{ account.chart_of_acct_name }}</td>
                                                    <td>{{ account.chart_of_acct_number }}</td>
                                                    <td>{{ account.parent_account ? account.parent_account.chart_of_acct_name : 'N/A' }}</td>
                                                    <!-- <td>{{ account.acct_balance }}</td> -->
                                                    <td>{{ new Date(account.opening_date).toLocaleDateString() }}</td>
                                                    <td>{{ account.predefined ? 'Yes' : 'No' }}</td>
                                                    <td>{{ account.transaction_status ? 'Active' : 'Inactive' }}</td>
                                                    <td>{{ account.is_closed ? 'Yes' : 'No' }}</td>
                                                    <td>
                                                        <span>{{ account.created_user ? account.created_user.name : 'Unknown' }}</span><br>

                                                    </td>
                                                    <td>
                                                        <button class="btn btn-light btn-sm" v-if="hasPermission('accounts_ledger_account_edit')" @click="editAccount(account.chart_of_acct_id)">
                                                            <i class="fa fa-pencil-square fa-lg edit_icon"></i>
                                                        </button>
                                                        <!-- <button class="btn btn-light btn-sm" v-if="hasPermission('accounts_ledger_account_delete')" @click="deleteAccount(account.chart_of_acct_id)">
                                                               <i class="fa fa-trash fa-lg delete_icon"></i>
                                                        </button> -->
                                                     </td>
                                                </tr>

                                                <!-- No data available -->
                                                <tr v-if="!chartOfAccounts.data.length">
                                                    <td colspan="11" class="text-center">No data available in table</td>
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
                                            {{ chartOfAccounts.from || 0 }}
                                            to
                                            {{ chartOfAccounts.to || 0 }}
                                            of
                                            {{ chartOfAccounts.total || 0 }}
                                            entries
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers">
                                            <ul class="pagination">
                                                <li class="paginate_button page-item" :class="{ disabled: !chartOfAccounts.prev_page_url }">
                                                    <a href="#" @click.prevent="changePage(chartOfAccounts.current_page - 1)" class="page-link">Previous</a>
                                                </li>

                                                <li class="paginate_button page-item"
                                                    v-for="page in totalPages" :key="page" :class="{ active: page === chartOfAccounts.current_page }">
                                                    <a href="#" @click.prevent="changePage(page)" class="page-link">{{ page }}</a>
                                                </li>

                                                <li class="paginate_button page-item" :class="{ disabled: !chartOfAccounts.next_page_url }">
                                                    <a href="#" @click.prevent="changePage(chartOfAccounts.current_page + 1)"
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
    import { toast } from 'vue3-toastify';
    // role and permission composable
    import { inject } from 'vue';
    import {useAuth} from "../../../../../../../resources/js/components/useAuth.js";
    const auth = inject('auth')

    import { ChartOfAccountCreateGet, ChartOfAccountListAPI, closeSingleChartOfAccount, ChartOfAccountEditGet } from "../../routes";



    export default {
        setup() {
            const { hasPermission } = useAuth();

            return {
                hasPermission,
            };
        },
        data() {
            return {
                chartOfAccounts: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
                perPage: 10,
                search: '',
            };
        },
        computed: {
            totalPages() {
                return Array.from({ length: this.chartOfAccounts.last_page }, (_, i) => i + 1);
            }
        },

        methods: {
            async fetchChartOfAccounts(page = 1) {
                try {
                    const response = await axios.get(ChartOfAccountListAPI, {
                        params: {
                            per_page: this.perPage,
                            page: parseInt(page, 10),
                            search: this.search,
                        },
                    });
                    this.chartOfAccounts = response.data;
                    console.log(this.chartOfAccounts);
                } catch (error) {
                    console.error('Error fetching chart of accounts:', error);
                }
            },

            async deleteAccount(id) {
                if (confirm('Are you sure you want to close this account?')) {
                    try {
                        const response = await axios.get(`${closeSingleChartOfAccount}/${id}`);
                        toast.success(response.data.message);
                        this.fetchChartOfAccounts();
                    } catch (error) {
                        // console.error('Error deleting account:', error);
                        toast.error(error.response?.data?.message || 'An error occurred');
                    }
                }
            },

            editAccount(id) {
                console.log("Edit = ", id)
                window.location = `${ChartOfAccountEditGet}/${id}`;
            },

            changePage(page) {
                this.fetchChartOfAccounts(page);
            },

            addNew() {
                window.location = ChartOfAccountCreateGet;
            }

        },




        mounted() {
            this.fetchChartOfAccounts();
        }
    }
</script>



<style>
/* Add any necessary styles here */
</style>
