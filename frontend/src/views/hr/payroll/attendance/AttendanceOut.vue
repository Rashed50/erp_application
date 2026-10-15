<template>
    <div class="card p-4 shadow-sm border-0">

        <!-- Search Section -->
        <div class="bg-light p-3 mb-4 border-bottom">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold">Attendance Date</label>
                    <input type="date" class="form-control" v-model="filters.date" />
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold">Shift</label>
                    <select class="form-select" v-model="filters.shift_id">
                        <option value="">Select Shift</option>
                        <option value="day">Day Shift</option>
                        <option value="night">Night Shift</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-8">
                    <label class="form-label small fw-bold">Employee ID / Name</label>
                    <input type="text" class="form-control" v-model.trim="filters.employee_id"
                        placeholder="Enter Employee ID" @keyup.enter="fetchEmployees" />
                </div>

                <!-- Separate Search Button -->
                <div class="col-md-2 col-sm-4">
                    <button class="btn btn-primary w-100" :disabled="loading" @click="fetchEmployees">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="fa-solid fa-search me-1"></i>
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Show only after search -->
        <template v-if="hasSearched">

            <!-- Submit Section (above the table) -->
            <div v-if="employeesList.length > 0" class="card border-danger bg-light p-3 mb-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold mb-1">Last Punch At (OUT Time)</label>
                        <input type="datetime-local" class="form-control" v-model="actionForm.last_punch_at" />
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold mb-1">Status</label>
                        <select class="form-select" v-model="actionForm.status">
                            <option value="present">Present</option>
                            <option value="early_leave">Early Leave</option>
                            <option value="overtime">Overtime</option>
                        </select>
                    </div>

                    <div class="col-md-3 text-center">
                        <span class="fw-bold text-danger small">
                            Selected: {{ selectedEmployeeIds.length }} / {{ employeesList.length }}
                        </span>
                    </div>

                    <div class="col-md-2 text-end">
                        <!-- Smaller submit button -->
                        <button class="btn btn-danger btn-sm px-4"
                            :disabled="submitting || loading || selectedEmployeeIds.length === 0"
                            @click="submitBatchOut">
                            <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
                            Submit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Results Table -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">SL</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Shift</th>
                            <th>Attendance Date</th>
                            <th width="80" class="text-center">
                                <input type="checkbox" class="form-check-input" :checked="allSelected"
                                    :disabled="!employeesList.length || loading" @change="toggleSelectAll" />
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td colspan="6" class="text-center py-4">
                                Loading employees...
                            </td>
                        </tr>

                        <tr v-else-if="employeesList.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">
                                No attendance records found.
                            </td>
                        </tr>

                        <tr v-for="(emp, index) in employeesList" v-else :key="emp.emp_auto_id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ emp.employee_id || '-' }}</td>
                            <td>{{ emp.name || '-' }}</td>
                            <td>{{ emp.shift?.name || emp.shift_name || '-' }}</td>
                            <td>{{ searchedDate }}</td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input" :value="emp.emp_auto_id"
                                    v-model="selectedEmployeeIds" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </template>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

export default {
    name: 'AttendanceOut',
    emits: ['cancel'],

    data() {
        const today = this.localDate();

        return {
            loading: false,
            submitting: false,
            hasSearched: false,

            employeesList: [],
            selectedEmployeeIds: [],

            filters: {
                date: today,
                shift_id: '',
                employee_id: ''
            },

            searchedDate: '',

            actionForm: {
                last_punch_at: `${today}T18:00`,
                status: 'present'
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
                const time = this.actionForm.last_punch_at
                    ? this.actionForm.last_punch_at.slice(11, 16)
                    : '18:00';

                this.actionForm.last_punch_at = `${date}T${time}`;
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
                const response = await axios.get('/api/get-employees-for-attendance-out', {
                    params: {
                        date: this.filters.date,
                        shift_id: this.filters.shift_id,
                        employee_id: this.filters.employee_id
                    }
                });

                console.log('OUT response:', response.data);

                const payload = response.data?.data ?? response.data;
                let list = payload?.employees ?? [];

                if (!Array.isArray(list)) list = list.data || [];

                this.employeesList = list.map(emp => ({
                    ...emp,
                    emp_auto_id: emp.emp_auto_id ?? emp.id,
                    employee_id: emp.employee_id ?? emp.emp_id ?? emp.emp_code ?? ''
                }));

                this.searchedDate = this.filters.date;
            } catch (error) {
                console.error('Attendance OUT search API error:', error);
                toast.error(
                    error.response?.data?.message ||
                    `Failed to fetch attendance records (${error.response?.status || error.message})`
                );
            } finally {
                this.loading = false;
            }
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedEmployeeIds = this.employeesList.map(emp => emp.emp_auto_id);
            } else {
                this.selectedEmployeeIds = [];
            }
        },

        async submitBatchOut() {
            if (this.selectedEmployeeIds.length === 0) {
                toast.error('Please select at least one employee.');
                return;
            }

            if (!this.actionForm.last_punch_at) {
                toast.error('Please select Last Punch At.');
                return;
            }

            const outDate = this.actionForm.last_punch_at.slice(0, 10);

            if (outDate !== this.searchedDate) {
                toast.error('OUT Time date must match attendance date.');
                return;
            }

            if (!confirm(`Submit Attendance OUT for ${this.selectedEmployeeIds.length} employee(s)?`)) {
                return;
            }

            this.submitting = true;

            try {
                const punch = this.actionForm.last_punch_at;
                const formattedPunch =
                    punch.replace('T', ' ') + (punch.length === 16 ? ':00' : '');

                const payload = {
                    ids: this.selectedEmployeeIds,
                    attendance_date: this.searchedDate,
                    shift_id: this.filters.shift_id || null,
                    last_punch_at: formattedPunch,
                    status: this.actionForm.status
                };

                const response = await axios.post('/api/check-out', payload);

                toast.success(response.data?.message || 'Attendance OUT saved successfully!');
                await this.fetchEmployees();
            } catch (error) {
                console.error('Attendance OUT API Error:', error);

                const validationErrors = error.response?.data?.errors;

                if (validationErrors) {
                    toast.error(Object.values(validationErrors).flat().join('\n'));
                } else {
                    toast.error(
                        error.response?.data?.message || 'Failed to save Attendance OUT.'
                    );
                }
            } finally {
                this.submitting = false;
            }
        }
    }
};
</script>