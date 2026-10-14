<template>
    <div class="card p-4 shadow-sm border-0">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-danger m-0">
                <i class="fa-solid fa-right-from-bracket me-2"></i>
                Attendance OUT Management
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

                <!-- Search Buttons -->
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

        <!-- Attendance OUT Action -->
        <div class="card border-danger p-3 mb-4">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label small fw-bold">
                        Last Punch At (OUT Time)
                    </label>

                    <input type="datetime-local" class="form-control" v-model="actionForm.last_punch_at" />
                </div>

                <div class="col-md-4">
                    <div class="fw-bold text-danger mb-2">
                        Selected Employees:
                        {{ selectedEmployeeIds.length }}
                    </div>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-danger w-100" :disabled="submitting ||
                        loading ||
                        selectedEmployeeIds.length === 0
                        " @click="submitBatchOut">
                        <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>

                        <i v-else class="fa-solid fa-check me-1"></i>
                        Submit Attendance OUT
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
                        <th>First Punch (IN)</th>
                        <th>Last Punch (OUT)</th>
                        <th width="80" class="text-center">
                            <input type="checkbox" class="form-check-input" :checked="allSelected"
                                :disabled="!selectableEmployees.length" @change="toggleSelectAll" />
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="loading">
                        <td colspan="8" class="text-center py-4">
                            Loading employees...
                        </td>
                    </tr>

                    <tr v-else-if="employeesList.length === 0">
                        <td colspan="8" class="text-center text-muted py-4">
                            No attendance records found.
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
                                {{ emp.attendance?.first_punch_at || '-' }}
                            </td>

                            <td>
                                {{ emp.attendance?.last_punch_at || '-' }}
                            </td>

                            <td class="text-center">
                                <input type="checkbox" class="form-check-input" :value="emp.emp_auto_id"
                                    v-model="selectedEmployeeIds" :disabled="!emp.attendance?.first_punch_at" :title="!emp.attendance?.first_punch_at
                                            ? 'Attendance IN required'
                                            : ''
                                        " />
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="d-flex justify-content-between mt-2">
            <span class="small text-muted">
                Total Employees: {{ employeesList.length }}
            </span>

            <span class="small fw-bold text-danger">
                Selected: {{ selectedEmployeeIds.length }}
            </span>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

function getLocalDate() {
    const now = new Date();

    return new Date(
        now.getTime() - now.getTimezoneOffset() * 60000
    ).toISOString().slice(0, 10);
}

export default {
    name: 'AttendanceOut',

    emits: ['cancel'],

    data() {
        const today = getLocalDate();

        return {
            loading: false,
            submitting: false,

            employeesList: [],
            shifts: [],
            selectedEmployeeIds: [],

            filters: {
                date: today,
                shift_id: '',
                employee_id: ''
            },

            actionForm: {
                last_punch_at: `${today}T18:00`
            }
        };
    },

    computed: {
        selectableEmployees() {
            return this.employeesList.filter(
                emp => emp.attendance?.first_punch_at
            );
        },

        allSelected() {
            return this.selectableEmployees.length > 0 &&
                this.selectableEmployees.every(emp =>
                    this.selectedEmployeeIds.includes(emp.emp_auto_id)
                );
        }
    },

    watch: {
        'filters.date'(date) {
            if (date) {
                const time = this.actionForm.last_punch_at
                    ? this.actionForm.last_punch_at.slice(11, 16)
                    : '18:00';

                this.actionForm.last_punch_at = `${date}T${time}`;
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
                alert('Please select attendance date.');
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
                console.error('Attendance OUT search error:', error);

                alert(
                    error.response?.data?.message ||
                    'Failed to search attendance records.'
                );
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            const today = getLocalDate();

            this.filters = {
                date: today,
                shift_id: '',
                employee_id: ''
            };

            this.actionForm.last_punch_at = `${today}T18:00`;

            this.selectedEmployeeIds = [];

            this.fetchEmployees();
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedEmployeeIds = [
                    ...new Set(
                        this.selectableEmployees.map(
                            emp => emp.emp_auto_id
                        )
                    )
                ];
            } else {
                this.selectedEmployeeIds = [];
            }
        },

        async submitBatchOut() {
            if (!this.selectedEmployeeIds.length) {
                alert('Please select at least one employee.');
                return;
            }

            if (!this.filters.date) {
                alert('Please select attendance date.');
                return;
            }

            if (!this.actionForm.last_punch_at) {
                alert('Please select Last Punch At.');
                return;
            }

            const outDate =
                this.actionForm.last_punch_at.slice(0, 10);

            if (outDate < this.filters.date) {
                alert('OUT date cannot be before attendance date.');
                return;
            }

            const selectedRecords = this.employeesList.filter(
                emp => this.selectedEmployeeIds.includes(emp.emp_auto_id)
            );

            const invalidEmployee = selectedRecords.find(emp => {
                if (!emp.attendance?.first_punch_at) return true;

                const inTime = String(
                    emp.attendance.first_punch_at
                ).replace(' ', 'T').slice(0, 19);

                const outTime = this.actionForm.last_punch_at;

                return outTime < inTime;
            });

            if (invalidEmployee) {
                alert(
                    'OUT time must be after IN time for every selected employee.'
                );
                return;
            }

            if (!confirm(
                `Submit Attendance OUT for ${this.selectedEmployeeIds.length} employee(s)?`
            )) {
                return;
            }

            this.submitting = true;

            try {
                const punch = this.actionForm.last_punch_at;

                const formattedPunch =
                    punch.replace('T', ' ') +
                    (punch.length === 16 ? ':00' : '');

                const payload = {
                    ids: this.selectedEmployeeIds,
                    attendance_date: this.filters.date,
                    shift_id: this.filters.shift_id || null,
                    last_punch_at: formattedPunch
                };

                const response = await axios.post(
                    '/api/admin/daily-attendances/batch-out',
                    payload
                );

                alert(
                    response.data.message ||
                    'Attendance OUT saved successfully!'
                );

                this.selectedEmployeeIds = [];

                await this.fetchEmployees();

            } catch (error) {
                console.error('Attendance OUT error:', error);

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
                        'Failed to save Attendance OUT.'
                    );
                }
            } finally {
                this.submitting = false;
            }
        }
    }
};
</script>
