<template>
    <div class="card p-4 shadow-sm border-0">
        <h4 class="mb-4 text-primary">
            <i class="fa-solid fa-list me-2"></i> Attendance List & Search Management
        </h4>

        <!-- Search Form matching Controller Parameters -->
        <div class="card bg-light p-3 border-0 mb-4">
            <h6 class="text-muted mb-3">Search Filters ($request)</h6>
            <div class="row g-3">
             

                <!-- Date From -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" class="form-control" v-model="filters.date_from" />
                </div>

                <!-- Date To -->
                <div class="col-md-2">
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
                    <input type="text" class="form-control" v-model="filters.employee_id" placeholder="e.g. EMP001" />
                </div>

                <!-- Search & Reset Buttons -->
                <div class="col-md-2 d-flex align-items-end gap-1">
                    <button class="btn btn-primary w-50" @click="fetchAttendances">Search</button>
                    <button class="btn btn-secondary w-50" @click="resetFilters">Reset</button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
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
                    <tr v-for="(item, index) in records" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.employee ? item.employee.employee_id : item.employee_auto_id }}</td>
                        <td>{{ item.employee ? item.employee.name : 'N/A' }}</td>
                        <td>{{ item.attendance_date }}</td>
                        <td><span :class="getStatusBadge(item.status)">{{ item.status }}</span></td>
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
    </div>
</template>

<script>
export default {
    name: 'AttendanceList',
    data() {
        return {
            loading: false,
            records: [],
            filters: {
         
                date_from: '',
                date_to: '',
                status: '',
                employee_id: '',
                per_page: 25
            }
        }
    },
    mounted() {
        this.fetchAttendances();
    },
    methods: {
        async fetchAttendances() {
            this.loading = true;
            try {
                // Building query parameters matching Laravel Controller ($request)
                const params = new URLSearchParams();
                if (this.filters.date_from) params.append('date_from', this.filters.date_from);
                if (this.filters.date_to) params.append('date_to', this.filters.date_to);
                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.employee_id) params.append('employee_id', this.filters.employee_id);
                params.append('per_page', this.filters.per_page);

                // Axios API integration:
                // const response = await axios.get(`/api/admin/daily-attendances?${params.toString()}`);
                // this.records = response.data.data || response.data;

                this.records = [];
            } catch (error) {
                console.error('Error fetching attendance data:', error);
            } finally {
                this.loading = false;
            }
        },
        resetFilters() {
            this.filters = {
                date_from: '',
                date_to: '',
                status: '',
                employee_id: '',
                per_page: 25
            };
            this.fetchAttendances();
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
}
</script>