<template>
    <div class="card p-4 shadow-sm border-0">

        <!-- Search Section -->
        <div class="card bg-light border-success p-3 mb-4">
            <h6 class="fw-bold mb-3">Search Attendance</h6>

            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold">Attendance Date</label>
                    <input type="date" class="form-control" v-model="filters.date" />
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold">Shift</label>
                    <select class="form-select" v-model="filters.shift_id">
                        <option value="">All Shifts</option>
                        <option v-for="shift in shifts" :key="shift.id" :value="shift.id">
                            {{ shift.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold">Employee ID</label>
                    <input type="text" class="form-control" v-model.trim="filters.employee_id"
                        placeholder="Enter Employee ID" @keyup.enter="fetchEmployees" />
                </div>

                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button class="btn btn-primary w-100" :disabled="loading" @click="fetchEmployees">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="fa-solid fa-search me-1"></i>
                        Search
                    </button>

                    <button class="btn btn-secondary w-100" :disabled="loading" @click="resetFilters">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Show only after search -->
        <template v-if="hasSearched">

            <!-- Search Results -->
            <div class="table-responsive mb-3">
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
                                    :disabled="!employeesList.length || loading" @change="toggleSelectAll" />
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

                        <tr v-for="(emp, index) in employeesList" v-else :key="emp.emp_auto_id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ emp.employee_id }}</td>
                            <td>{{ emp.name || '-' }}</td>
                            <td>{{ emp.shift?.name || '-' }}</td>
                            <td>{{ searchedDate }}</td>
                            <td>{{ emp.attendance?.first_punch_at || '-' }}</td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input" :value="emp.emp_auto_id"
                                    v-model="selectedEmployeeIds" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="employeesList.length" class="d-flex justify-content-between mb-3">
                <span class="small text-muted">
                    Total Employees: {{ employeesList.length }}
                </span>

                <span class="small fw-bold text-success">
                    Selected: {{ selectedEmployeeIds.length }}
                </span>
            </div>

            <!-- Submit Section -->
            <div v-if="employeesList.length > 0" class="card border-success p-3 mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">First Punch At (IN Time)</label>
                        <input type="datetime-local" class="form-control" v-model="actionForm.first_punch_at" />
                    </div>

                    <div class="col-md-4">
                        <span class="fw-bold text-success">
                            Selected Employees: {{ selectedEmployeeIds.length }}
                        </span>
                    </div>

                    <div class="col-md-4 text-end">
                        <button class="btn btn-success px-4"
                            :disabled="submitting || loading || selectedEmployeeIds.length === 0"
                            @click="submitBatchIn">
                            <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
                            Submit
                        </button>
                    </div>
                </div>
            </div>

        </template>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

export default {
    name: 'AttendanceIn',
    emits: ['cancel'],

    data() {
        const today = this.localDate();

        return {
            loading: false,
            submitting: false,
            hasSearched: false,

            employeesList: [],
            shifts: [],
            selectedEmployeeIds: [],

            filters: {
                date: today,
                shift_id: '',
                employee_id: ''
            },

            searchedDate: '',

            actionForm: {
                first_punch_at: `${today}T09:00`
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

            this.clearResults();
        },

        'filters.shift_id'() {
            this.clearResults();
        },

        'filters.employee_id'() {
            this.clearResults();
        }
    },

    mounted() {
        this.fetchShifts();
    },

    methods: {
        localDate() {
            const now = new Date();
            return new Date(
                now.getTime() - now.getTimezoneOffset() * 60000
            ).toISOString().slice(0, 10);
        },

        clearResults() {
            this.hasSearched = false;
            this.employeesList = [];
            this.selectedEmployeeIds = [];
        },

        async fetchShifts() {
            try {
                const response = await axios.get('/api/shifts');
                this.shifts = response.data.data || response.data || [];
            } catch (error) {
                console.error('Shift fetch error:', error);
            }
        },

        async fetchEmployees() {
            if (this.loading) return;

            if (!this.filters.date) {
                toast.error('Please select attendance date.');
                return;
            }

            this.loading = true;
            this.hasSearched = true;
            this.selectedEmployeeIds = [];
            this.employeesList = [];

            try {
                const response = await axios.get('/api/attendances/search', {
                    params: {
                        date: this.filters.date,
                        shift_id: this.filters.shift_id,
                        employee_id: this.filters.employee_id
                    }
                });

                this.employeesList = response.data.data || response.data || [];
                this.searchedDate = this.filters.date;
            } catch (error) {
                console.error('Employee search error:', error);
                toast.error(
                    error.response?.data?.message || 'Failed to search employees.'
                );
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            const today = this.localDate();

            this.filters = {
                date: today,
                shift_id: '',
                employee_id: ''
            };

            this.actionForm.first_punch_at = `${today}T09:00`;
            this.clearResults();
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedEmployeeIds = [
                    ...new Set(this.employeesList.map(emp => emp.emp_auto_id))
                ];
            } else {
                this.selectedEmployeeIds = [];
            }
        },

        async submitBatchIn() {
            if (this.selectedEmployeeIds.length === 0) {
                toast.error('Please select at least one employee.');
                return;
            }

            if (!this.actionForm.first_punch_at) {
                toast.error('Please select First Punch At.');
                return;
            }

            const punchDate = this.actionForm.first_punch_at.slice(0, 10);

            if (punchDate !== this.searchedDate) {
                toast.error('IN Time date must match attendance date.');
                return;
            }

            if (!confirm(`Submit Attendance IN for ${this.selectedEmployeeIds.length} employee(s)?`)) {
                return;
            }

            this.submitting = true;

            try {
                const punch = this.actionForm.first_punch_at;
                const formattedPunch =
                    punch.replace('T', ' ') + (punch.length === 16 ? ':00' : '');

                const payload = {
                    ids: this.selectedEmployeeIds,
                    attendance_date: this.searchedDate,
                    shift_id: this.filters.shift_id || null,
                    first_punch_at: formattedPunch,
                    status: 'present'
                };

                const response = await axios.post('/api/attendance/manual', payload);

                toast.success(response.data?.message || 'Successfully In');
                this.clearResults();
            } catch (error) {
                console.error('Attendance IN error:', error);

                const validationErrors = error.response?.data?.errors;

                if (validationErrors) {
                    toast.error(Object.values(validationErrors).flat().join('\n'));
                } else {
                    toast.error(
                        error.response?.data?.message || 'Failed to save Attendance IN.'
                    );
                }
            } finally {
                this.submitting = false;
            }
        }
    }
};
</script>