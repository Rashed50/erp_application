<template>
    <div class="card p-4 shadow-sm border-0">

        <!-- Search Form -->
        <div class="card bg-light p-3 border-1 mb-4">
            <h6 class="text-muted mb-3">Search Filters</h6>
            <div class="row g-3 align-items-end">

                <!-- Date From -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" class="form-control" v-model="filters.date_from" />
                </div>

                <!-- Date To -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" class="form-control" v-model="filters.date_to" />
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Status</label>
                    <select class="form-select" v-model="filters.status">
                        <option value="">All Status</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="leave">Leave</option>
                        <option value="holiday">Holiday</option>
                        <option value="weekly_off">Weekly Off</option>
                        <option value="incomplete">Incomplete</option>
                        <option value="offsite">Offsite</option>
                    </select>
                </div>

                <!-- Employee ID -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Employee ID</label>
                    <input type="text" class="form-control" v-model.trim="filters.employee_id" placeholder="e.g. EMP001"
                        @keyup.enter="search" />
                </div>

                <!-- Search Button -->
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" :disabled="loading" @click="search">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="fa-solid fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="60">SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>First Punch (IN)</th>
                        <th>Last Punch (OUT)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="8" class="text-center text-muted py-4">Loading records...</td>
                    </tr>

                    <tr v-else-if="records.length === 0">
                        <td colspan="8" class="text-center text-muted py-4">No attendance records found.</td>
                    </tr>

                    <tr v-for="(item, index) in records" v-else :key="item.id">
                        <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                        <td>{{ item.employee?.employee_id || '-' }}</td>
                        <td>{{ item.employee?.name || 'N/A' }}</td>
                        <td>{{ item.attendance_date }}</td>
                        <td>
                            <span :class="getStatusBadge(item.status)" class="text-capitalize">
                                {{ item.status ? item.status.replace('_', ' ') : '-' }}
                            </span>
                        </td>
                        <td>{{ item.first_punch_at || '-' }}</td>
                        <td>{{ item.last_punch_at || '-' }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-danger" @click="$emit('delete-record', item.id)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="records.length > 0" class="d-flex justify-content-between align-items-center mt-3">
            <span class="small text-muted">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
                &middot; Total: {{ pagination.total }}
            </span>

            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary" :disabled="loading || pagination.current_page <= 1"
                    @click="fetchAttendances(pagination.current_page - 1)">
                    Previous
                </button>
                <button class="btn btn-sm btn-outline-secondary"
                    :disabled="loading || pagination.current_page >= pagination.last_page"
                    @click="fetchAttendances(pagination.current_page + 1)">
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

export default {
    name: 'AttendanceList',
    emits: ['delete-record'],

    data() {
        return {
            loading: false,
            records: [],

            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 25,
                total: 0
            },

            filters: {
                date_from: '',
                date_to: '',
                status: '',
                employee_id: '',
                per_page: 25
            }
        };
    },

    mounted() {
        this.fetchAttendances(1);
    },

    methods: {
        // Search / Enter always starts from page 1
        search() {
            this.fetchAttendances(1);
        },

        async fetchAttendances(page = 1) {
            if (this.loading) return;

            if (this.filters.date_from && this.filters.date_to &&
                this.filters.date_from > this.filters.date_to) {
                toast.error('Date From cannot be after Date To.');
                return;
            }

            this.loading = true;

            try {
                // Laravel paginate() response: { current_page, data: [...], last_page, per_page, total, ... }
                const response = await axios.get('/api/attendance', {
                    params: {
                        page,
                        date_from: this.filters.date_from || undefined,
                        date_to: this.filters.date_to || undefined,
                        status: this.filters.status || undefined,
                        employee_id: this.filters.employee_id || undefined,
                        per_page: this.filters.per_page
                    }
                });

                const res = response.data;

                this.records = Array.isArray(res.data) ? res.data : [];

                this.pagination = {
                    current_page: res.current_page ?? 1,
                    last_page: res.last_page ?? 1,
                    per_page: res.per_page ?? this.filters.per_page,
                    total: res.total ?? this.records.length
                };

                // Backend sends { data: [], message: 'Employee not found.' } for unknown employee
                if (this.records.length === 0 && res.message) {
                    toast.info(res.message);
                }
            } catch (error) {
                console.error('Error fetching attendance data:', error);
                toast.error(
                    error.response?.data?.message || 'Failed to load attendance records.'
                );
            } finally {
                this.loading = false;
            }
        },

        getStatusBadge(status) {
            switch (status) {
                case 'present': return 'badge bg-success';
                case 'late': return 'badge bg-warning text-dark';
                case 'half_day': return 'badge bg-info text-dark';
                case 'leave': return 'badge bg-secondary';
                case 'absent': return 'badge bg-danger';
                default: return 'badge bg-light text-dark border';
            }
        }
    }
};
</script>