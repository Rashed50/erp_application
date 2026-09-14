<template>
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-3">
                <h4 class="mb-0 font-weight-bold">
                    {{ active_form_title }}
                </h4>
            </div>
            <div class="col-md-7 text-right"></div>
            <div class="col-md-2 text-right">
                <button @click="toggleSearchForm" class="btn btn-sm btn-outline-primary">
                    Search
                    <i :class="showform === 1 ? 'fas fa-caret-up' : 'fas fa-caret-down'"></i>
                </button>
            </div>
        </div>

        <!-- Search Form Block -->
        <div v-if="showform === 1">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-end">
                        <!-- Project Selection -->
                        <div class="col-md-4 mb-2">
                            <label class="form-label font-weight-bold">Select Projects</label>
                            <Multiselect v-model="searchQuery.project_ids" mode="multiple" :options="projectOptions"
                                value-prop="value" track-by="label" label="label" placeholder="Select Projects"
                                :searchable="true" :close-on-deselect="false" :clear-on-select="false"
                                :create-option="false" :hide-selected="false" :caret="true" class="multiselect-blue" />
                        </div>

                        <!-- Date Input -->
                        <div class="col-md-3 mb-2">
                            <label class="form-label font-weight-bold">Date</label>
                            <input type="date" v-model="searchQuery.date" class="form-control" />
                        </div>

                        <!-- Multiple Employee IDs Input -->
                        <div class="col-md-3 mb-2">
                            <label class="form-label font-weight-bold">Employee IDs <small
                                    class="form-text text-muted">Separate multiple IDs with comma (,)</small>
                            </label>
                            <input type="text" v-model="searchQuery.emp_ids" placeholder="e.g. 1020, 1030, 1040"
                                class="form-control" />
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-2 mb-2">
                            <button @click="fetchRecords" class="btn btn-success w-100" :disabled="loading">
                                <i v-if="loading" class="fas fa-spinner fa-spin mr-1"></i>
                                {{ loading ? 'Searching...' : 'Search' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive">
                <table class="table table-bordered bg-white table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>S.N</th>
                            <th>Emp.ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Trade</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                <p class="mt-2 mb-0">Loading records...</p>
                            </td>
                        </tr>

                        <tr v-else-if="records.length === 0">
                            <td colspan="7" class="text-center py-4 text-muted">
                                No records found. 
                            </td>
                        </tr>

                        <tr v-else v-for="(row, index) in records" :key="row.emp_auto_id || index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.employee_id }}</td>
                            <td>{{ row.employee_name }}</td>
                            <td>{{ row.emp_io_date }}-{{ row.emp_io_month }}-{{ row.emp_io_year }}</td>
                            <td>{{ row.hourly_employee == 1 ? 'Hourly' : 'Basic' }}</td>
                            <td>{{ row.catg_name || 'N/A' }}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" @click="deleteRecord(row.emp_io_id)"
                                    title="Delete Record">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from "vue3-toastify";
import Multiselect from '@vueform/multiselect';
import "@vueform/multiselect/themes/default.css";
import { AttendanceBio } from '../../routes';

const today = new Date().toISOString().substr(0, 10);

export default {
    components: { Multiselect },

    props: {
        projects: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            loading: false,
            records: [],
            showform: 1,
            active_form_title: 'Search Attendance Bio Records',

            searchQuery: {
                project_ids: [],
                date: today,
                emp_ids: '',
                isNightShift: 0
            }
        };
    },

    computed: {
        projectOptions() {
            if (!Array.isArray(this.projects)) return [];
            return this.projects.map(p => ({
                value: p.proj_id,
                label: p.proj_name
            }));
        }
    },

    methods: {
        toggleSearchForm() {
            this.showform = this.showform === 1 ? null : 1;
            this.active_form_title = this.showform === 1 ? 'Search Attendance Bio Records' : '';
        },

        async fetchRecords() {
            this.loading = true;
            try {
                const response = await axios.get(AttendanceBio, {
                    params: {
                        project_ids: this.searchQuery.project_ids,
                        date: this.searchQuery.date,
                        emp_ids: this.searchQuery.emp_ids,
                        isNightShift: this.searchQuery.isNightShift
                    }
                });

                if (response.data.success) {
                    this.records = response.data.data;
                } else {
                    this.records = [];
                    toast.error(response.data.message || "No records found");
                }
            } catch (error) {
                console.error("Fetch records error:", error);
                toast.error(error.response?.data?.message || "Error occurred while fetching records");
            } finally {
                this.loading = false;
            }
        },

        deleteRecord(id) {
            // Delete logic
        }
    }
};
</script>