<template>
    <div class="card p-4 shadow-sm border-0">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-success m-0">
                <i class="fa-solid fa-right-to-bracket me-2"></i>
                Attendance IN Management
            </h4>

            <button class="btn btn-outline-secondary btn-sm" @click="$emit('cancel')">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Back to List
            </button>
        </div>

        <!-- Search Section -->
        <div class="card bg-light border-0 p-3 mb-4">
            <h6 class="fw-bold mb-3">Search Attendance</h6>

            <div class="row g-3">
                <!-- Attendance Date -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">
                        Attendance Date
                    </label>
                    <input type="date" class="form-control" v-model="filters.date" />
                </div>

                <!-- Shift -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">
                        Shift
                    </label>
                    <select class="form-select" v-model="filters.shift_id">
                        <option value="">All Shifts</option>
                        <option v-for="shift in shifts" :key="shift.id" :value="shift.id">
                            {{ shift.name }}
                        </option>
                    </select>
                </div>

                <!-- Employee ID -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">
                        Employee ID
                    </label>
                    <input type="text" class="form-control" v-model.trim="filters.employee_id"
                        placeholder="Enter Employee ID" @keyup.enter="fetchEmployees" />
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary px-4" :disabled="loading" @click="fetchEmployees">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="fa-solid fa-search me-1"></i>
                        Search
                    </button>

                    <button class="btn btn-secondary" :disabled="loading" @click="resetFilters">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance IN Action -->
        <div class="card border-success p-3 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">
                        First Punch At (IN Time)
                    </label>

                    <input type="datetime-local" class="form-control" v-model="actionForm.first_punch_at" />
                </div>

                <div class="col-md-4">
                    <div class="fw-bold text-success mb-2">
                        Selected Employees:
                        {{ selectedEmployeeIds.length }}
                    </div>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-success w-100" :disabled="submitting ||
                        loading ||
                        selectedEmployeeIds.length === 0
                        " @click="submitBatchIn">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>

                        <i v-else class="fa-solid fa-check me-1"></i>
                        Submit Attendance IN
                    </button>
                </div>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="60">SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Shift</th>
                        <th>Attendance Date</th>
                        <th>First Punch At</th>
                        <th width="80" class="text-center">
                            <input type="checkbox" class="form-check-input" :checked="allSelected"
                                :disabled="!employeesList.length" @change="toggleSelectAll" />
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="loading">
                        <td colspan="7" class="text-center py-4">
                            Loading employees...
                        </td>
                    </tr>

                    <tr v-else-if="employeesList.length === 0">
                        <td colspan="7" class="text-center text-muted py-4">
                            No employee records found.
                        </td>
                    </tr>

                    <template v-else>
                        <tr v-for="(emp, index) in employeesList" :key="emp.emp_auto_id">
                            <td>{{ index + 1 }}</td>

                            <td>{{ emp.employee_id }}</td>

                            <td>{{ emp.name || '-' }}</td>

                            <td>
                                {{ emp.shift?.name || '-' }}
                            </td>

                            <td>{{ filters.date }}</td>

                            <td>
                                {{
                                    emp.attendance?.first_punch_at || '-'
                                }}
                            </td>

                            <td class="text-center">
                                <input type="checkbox" class="form-check-input" :value="emp.emp_auto_id"
                                    v-model="selectedEmployeeIds" />
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Record Count -->
        <div class="d-flex justify-content-between mt-2">
            <span class="small text-muted">
                Total Employees: {{ employeesList.length }}
            </span>
            <span class="small fw-bold text-success">
                Selected: {{ selectedEmployeeIds.length }}
            </span>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AttendanceIn',

    emits: ['cancel'],

    data() {
        const now = new Date();
        const localDate = new Date(
            now.getTime() - now.getTimezoneOffset() * 60000
        ).toISOString().slice(0, 10);

        return {
            loading: false,
            submitting: false,

            employeesList: [],
            shifts: [],
            selectedEmployeeIds: [],

            filters: {
                date: localDate,
                shift_id: '',
                employee_id: ''
            },

            actionForm: {
                first_punch_at: `${localDate}T09:00`
            }
        };
    },

    computed: {
        allSelected() {
            return (
                this.employeesList.length > 0 &&
                this.employeesList.every(emp =>
                    this.selectedEmployeeIds.includes(emp.emp_auto_id)
                )
            );
        }
    },

    watch: {
        'filters.date'(date) {
            if (date) {
                const time = this.actionForm.first_punch_at
                    ? this.actionForm.first_punch_at.slice(11, 16)
                    : '09:00';

                this.actionForm.first_punch_at = `${date}T${time}`;
            }

            this.employeesList = [];
            this.selectedEmployeeIds = [];
        },

        'filters.shift_id'() {
            this.employeesList = [];
            this.selectedEmployeeIds = [];
        },

        'filters.employee_id'() {
            this.employeesList = [];
            this.selectedEmployeeIds = [];
        }
    },

    mounted() {
        this.fetchShifts();
        this.fetchEmployees();
    },

    methods: {
        async fetchShifts() {
            try {
                const response = await axios.get('/api/shifts');

                const result = response.data;

                const list = Array.isArray(result)
                    ? result
                    : result.data || [];

                this.shifts = Array.isArray(list)
                    ? list
                    : list.data || [];

            } catch (error) {
                console.error('Shift fetch error:', error);
            }
        },

        async fetchEmployees() {
            if (!this.filters.date) {
                alert('Please select an attendance date.');
                return;
            }

            this.loading = true;
            this.selectedEmployeeIds = [];
            this.employeesList = [];

            try {
                const response = await axios.get(
                    '/api/admin/daily-attendances/search-in',
                    {
                        params: {
                            date: this.filters.date,
                            shift_id: this.filters.shift_id,
                            employee_id: this.filters.employee_id
                        }
                    }
                );

                const result = response.data;

                const list = Array.isArray(result)
                    ? result
                    : result.data || [];

                this.employeesList = Array.isArray(list)
                    ? list
                    : list.data || [];

            } catch (error) {
                console.error('Employee search error:', error);

                alert(
                    error.response?.data?.message ||
                    'Failed to search employees.'
                );
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            const now = new Date();

            const today = new Date(
                now.getTime() - now.getTimezoneOffset() * 60000
            ).toISOString().slice(0, 10);

            this.filters = {
                date: today,
                shift_id: '',
                employee_id: ''
            };

            this.actionForm.first_punch_at = `${today}T09:00`;

            this.selectedEmployeeIds = [];

            this.fetchEmployees();
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedEmployeeIds = [
                    ...new Set(
                        this.employeesList.map(
                            emp => emp.emp_auto_id
                        )
                    )
                ];
            } else {
                this.selectedEmployeeIds = [];
            }
        },

        async submitBatchIn() {
            if (this.selectedEmployeeIds.length === 0) {
                alert('Please select at least one employee.');
                return;
            }

            if (!this.filters.date) {
                alert('Please select attendance date.');
                return;
            }

            if (!this.actionForm.first_punch_at) {
                alert('Please select First Punch At.');
                return;
            }

            const punchDate =
                this.actionForm.first_punch_at.slice(0, 10);

            if (punchDate !== this.filters.date) {
                alert('IN Time date must match attendance date.');
                return;
            }

            if (
                !confirm(
                    `Submit Attendance IN for ${this.selectedEmployeeIds.length} employee(s)?`
                )
            ) {
                return;
            }

            this.submitting = true;

            try {
                const punch = this.actionForm.first_punch_at;

                const formattedPunch =
                    punch.replace('T', ' ') +
                    (punch.length === 16 ? ':00' : '');

                const payload = {
                    ids: this.selectedEmployeeIds,
                    attendance_date: this.filters.date,
                    shift_id: this.filters.shift_id || null,
                    first_punch_at: formattedPunch,
                    status: 'present'
                };

                const response = await axios.post(
                    '/api/admin/daily-attendances/batch-in',
                    payload
                );

                alert(
                    response.data.message ||
                    'Attendance IN saved successfully!'
                );

                this.selectedEmployeeIds = [];

                await this.fetchEmployees();

            } catch (error) {
                console.error('Attendance IN error:', error);

                const validationErrors =
                    error.response?.data?.errors;

                if (validationErrors) {
                    alert(
                        Object.values(validationErrors)
                            .flat()
                            .join('\n')
                    );
                } else {
                    alert(
                        error.response?.data?.message ||
                        'Failed to save Attendance IN.'
                    );
                }
            } finally {
                this.submitting = false;
            }
        }
    }
};
</script>
