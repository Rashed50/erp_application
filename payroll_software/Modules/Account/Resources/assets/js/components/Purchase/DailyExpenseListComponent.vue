<!-- resources/js/components/daily-expense/DailyExpenseList.vue -->
<template>
    <div class="card">
        <div class="card-header">
            <h4>Daily Expense Search</h4>
        </div>
        <div class="card-body">
            <!-- Search Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" v-model="filters.employee_id" placeholder="Employee ID" autofocus>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" v-model="filters.start_date" placeholder="Start Date">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" v-model="filters.end_date" placeholder="End Date">
                </div>
                <div class="col-md-3">
                    <select class="form-select" v-model="filters.expense_type">
                        <option value="">All Expense Types</option>
                        <option v-for="type in expenseTypes" :key="type.cost_type_id" :value="type.cost_type_id">
                            {{ type.cost_type_name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <button class="btn btn-primary" @click="searchRecords" :disabled="loading">
                        <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                        Search
                    </button>
                    <button class="btn btn-secondary ml-2" @click="resetFilters">
                        Reset
                    </button>
                </div>
            </div>

            <!-- Records Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Inv. No.</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Emp. ID</th>
                            <th>Project</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading && records.length === 0">
                            <td colspan="10" class="text-center">Loading...</td>
                        </tr>
                        <tr v-else-if="records.length === 0">
                            <td colspan="10" class="text-center">No records found</td>
                        </tr>
                        <tr v-else v-for="record, in records" :key="record.pur_id">

                            <td>{{ records.indexOf(record) + 1 }}</td>
                            <td>{{ record.invoice_number }}</td>
                            <td>{{ record.purchase_date }}</td>
                            <td> {{ record.cost_type_name?? " " }}  {{ record.purchase_type }}</td>
                            <td>{{ record.supplier_id }}</td>
                            <td>{{ record.project.proj_name }}</td>
                            <td>{{ formatAmount(record.net_total) }}</td>
                            <td>{{ record.description }}</td>
                            <td>
                                <a v-if="record.attachment" :href="getAttachmentUrl(record.attachment)" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" @click="editRecord(record)" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteRecord(record.pur_id)" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="records.length > 0">
                        <tr class="font-weight-bold">
                            <td colspan="6" class="text-right">Total:</td>
                            <td colspan="3">{{ formatAmount(totalAmount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div class="row mt-3" v-if="pagination.last_page > 1">
                <div class="col-md-12">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Previous</a>
                            </li>
                            <li class="page-item" v-for="page in pagination.last_page" :key="page" :class="{ active: page === pagination.current_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Expense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <daily-expense-form
                            v-if="editData"
                            :expense-types="expenseTypes"
                            :projects="projects"
                            ref="editForm"
                            @saved="onEditSaved"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { DailyExpenseSearchAPI } from '../../routes.js';


export default {
    // name: 'DailyExpenseListComponent',
    // components: {
    //     DailyExpenseForm
    // },
    props: {
        expenseTypes: {
            type: Array,
            required: true
        },
        projects: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            records: [],
            loading: false,
            filters: {
                employee_id: '',
                start_date: '',
                end_date: '',
                expense_type: ''
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            },
            editData: null
        }
    },
    computed: {
        totalAmount() {
            return this.records.reduce((sum, record) => sum + parseFloat(record.net_total || 0), 0);
        }
    },
    mounted() {
        this.searchRecords();
    },
    watch: {
        activeComponent(newVal) {
            // if (newVal === 'form') {
            //     // Focus after DOM update
            //     this.$nextTick(() => {
            //         if (this.$refs.formInput) {
            //             this.$refs.formInput.focus();
            //         }
            //     });
            // }
            console.log("List page actived");
        }
    },
    methods: {
        async searchRecords(page = 1) {
            this.loading = true;
            try {

                const response = await axios.get(DailyExpenseSearchAPI, {
                    params: {
                        ...this.filters,
                        page: page,
                        per_page: this.pagination.per_page
                    }
                });

                console.log('Search Response:', response.data);

                this.records = response.data.data;
                this.pagination = {
                    current_page: response.data.pagination.current_page,
                    last_page: response.data.pagination.last_page,
                    per_page: response.data.pagination.per_page,
                    total: response.data.total
                };

                //  'pagination' => [
                //     'current_page' => $records->currentPage(),
                //     'last_page' => $records->lastPage(),
                //     'per_page' => $records->perPage(),
                //     'total' => $records->total(),
                //     'has_more_pages' => $records->hasMorePages(),
                //     'from' => $records->firstItem(),
                //     'to' => $records->lastItem(),
                // ],


            } catch (error) {
                console.error('Error fetching records:', error);
                toast.error('Failed to fetch records');
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters = {
                employee_id: '',
                start_date: '',
                end_date: '',
                expense_type: ''
            };
            this.searchRecords();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.searchRecords(page);
            }
        },

        editRecord(record) {
            this.editData = record;
            $('#editModal').modal('show');
        },

        onEditSaved() {
            $('#editModal').modal('hide');
            this.searchRecords();
        },

        async deleteRecord(id) {
            if (!confirm('Are you sure you want to delete this record?')) {
                return;
            }

            try {
                await axios.delete(`/company/daily-expense/${id}`);
                toast.success('Record deleted successfully');
                this.searchRecords();
            } catch (error) {
                console.error('Error deleting record:', error);
                toast.error('Failed to delete record');
            }
        },

        formatAmount(amount) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0);
        },
         getAttachmentUrl(path)
        {
            return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
        },

    }
}
</script>

<style scoped>
.ml-2 {
    margin-left: 0.5rem;
}
.font-weight-bold {
    font-weight: bold;
}
</style>
