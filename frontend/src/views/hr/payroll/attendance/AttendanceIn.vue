<template>
    <div>
        <!-- Step 1: search (label and field side by side) -->
        <v-card style="padding: 15px; margin-top: 15px;">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-nowrap">{{ $t('Attendance Date:') }}</label>
                        <input type="date" class="form-control" v-model="filters.date" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-nowrap">{{ $t('Shift:') }}</label>
                        <select class="form-select" v-model="filters.shift_id">
                            <option value="">{{ $t('Select Shift') }}</option>
                            <option value="day">{{ $t('Day Shift') }}</option>
                            <option value="night">{{ $t('Night Shift') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-nowrap">{{ $t('Employee ID / Name:') }}</label>
                        <input type="text" class="form-control" v-model.trim="filters.employee_id"
                            :placeholder="$t('Enter Employee ID')" @keyup.enter="fetchEmployees" />
                    </div>
                </div>

                <div class="col-md-2">
                    <v-btn class="text-none text-white w-100" color="blue-darken-3" rounded="0" variant="flat"
                        :loading="loading" @click="fetchEmployees">
                        <i class="fa-solid fa-search me-1"></i> {{ $t('Search') }}
                    </v-btn>
                </div>
            </div>
        </v-card>

        <!-- Step 2: results & submit -->
        <v-card v-if="hasSearched" style="padding: 10px; margin-top: 15px;">

            <!-- Submit section (above the table) -->
            <div v-if="employeesList.length" class="row g-3 align-items-center mb-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-nowrap">{{ $t('IN Time:') }}</label>
                        <input type="datetime-local" class="form-control" v-model="actionForm.first_punch_at" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-nowrap">{{ $t('Status:') }}</label>
                        <select class="form-select" v-model="actionForm.status">
                            <option value="present">{{ $t('Present') }}</option>
                            <option value="late">{{ $t('Late') }}</option>
                            <option value="absent">{{ $t('Absent') }}</option>
                            <option value="half_day">{{ $t('Half Day') }}</option>
                            <option value="leave">{{ $t('On Leave') }}</option>
                            <option value="holiday">{{ $t('Holiday') }}</option>
                            <option value="weekly_off">{{ $t('Weekly Off') }}</option>
                            <option value="incomplete">{{ $t('Incomplete') }}</option>
                            <option value="offsite">{{ $t('Offsite') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Total / selected sits right beside the Submit button -->
                <div class="col-md-5 d-flex justify-content-end align-items-center gap-3">
                    <span class="text-muted small">
                        {{ $t('Total {count} employees', { count: employeesList.length }) }} ·
                        <!-- <strong>{{ $t('{count} selected', { count: selectedEmployeeIds.length }) }}</strong> -->
                    </span>

                    <v-btn class="text-none text-white" color="green-darken-3" rounded="0" variant="flat" size="small"
                        :disabled="!selectedEmployeeIds.length" :loading="submitting" @click="submitBatchIn">
                        <i class="fa-solid fa-check me-1"></i> {{ $t('Submit') }} ({{ selectedEmployeeIds.length }})
                    </v-btn>
                </div>
            </div>

            <div class="table-responsive">
                <v-table class="custom-bordered" density="compact">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">{{ $t('SL') }}</th>
                            <th>{{ $t('Employee ID') }}</th>
                            <th>{{ $t('Name') }}</th>
                            <th>{{ $t('Shift') }}</th>
                            <th>{{ $t('Attendance Date') }}</th>
                            <th>{{ $t('Status') }}</th>
                            <th class="text-center" width="80">
                                <input type="checkbox" class="form-check-input" :checked="allSelected"
                                    :disabled="!employeesList.length || loading"
                                    @change="toggleSelectAll($event.target.checked)" />
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td colspan="7" class="text-center py-3">{{ $t('Loading employees...') }}</td>
                        </tr>

                        <tr v-else-if="!employeesList.length">
                            <td colspan="7" class="text-center text-muted py-3">
                                {{ $t('No employee records found.') }}
                            </td>
                        </tr>

                        <tr v-for="(emp, index) in employeesList" v-else :key="emp.emp_auto_id">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td>{{ emp.employee_id || emp.id || '-' }}</td>
                            <td>{{ emp.name || '-' }}</td>
                            <td>{{ emp.shift?.name || emp.shift_name || '-' }}</td>
                            <td>{{ searchedDate }}</td>
                            <td>
                                <span v-if="emp.attendance?.status" :class="statusClass(emp.attendance.status)"
                                    class="text-capitalize">
                                    {{ emp.attendance.status.replace('_', ' ') }}
                                </span>
                                <span v-else>-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input" :value="emp.emp_auto_id"
                                    v-model="selectedEmployeeIds" />
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </div>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import { t } from '@/i18n';

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
            selectedEmployeeIds: [],
            searchedDate: '',

            filters: {
                date: today,
                shift_id: '',
                employee_id: ''
            },

            actionForm: {
                first_punch_at: `${today}T09:00`,
                status: 'present'
            }
        };
    },

    computed: {
        allSelected() {
            return (
                this.employeesList.length > 0 &&
                this.selectedEmployeeIds.length === this.employeesList.length
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

    methods: {
        localDate() {
            const now = new Date();
            return new Date(
                now.getTime() - now.getTimezoneOffset() * 60000
            ).toISOString().slice(0, 10);
        },

        statusClass(status) {
            switch (status) {
                case 'present': return 'badge bg-success';
                case 'late': return 'badge bg-warning text-dark';
                case 'half_day': return 'badge bg-info text-dark';
                case 'leave': return 'badge bg-secondary';
                case 'absent': return 'badge bg-danger';
                default: return 'badge bg-light text-dark border';
            }
        },

        clearResults() {
            this.hasSearched = false;
            this.employeesList = [];
            this.selectedEmployeeIds = [];
        },

        async fetchEmployees() {
            if (this.loading) return;

            if (!this.filters.date) {
                toast.error(t('Please select attendance date.'));
                return;
            }

            this.loading = true;
            this.hasSearched = true;
            this.selectedEmployeeIds = [];
            this.employeesList = [];

            try {
                const response = await axios.get('/api/get-employees-for-attendance-in', {
                    params: {
                        date: this.filters.date,
                        shift_id: this.filters.shift_id,
                        employee_id: this.filters.employee_id
                    }
                });

                const payload = response.data?.data ?? response.data;
                let list = payload?.employees ?? payload ?? [];
                if (!Array.isArray(list)) list = list?.data ?? [];

                // Every row MUST have a unique emp_auto_id, otherwise checkboxes share one value.
                this.employeesList = list.map(emp => ({
                    ...emp,
                    emp_auto_id: emp.emp_auto_id ?? emp.id,
                    employee_id: emp.employee_id ?? emp.emp_id ?? emp.emp_code ?? ''
                }));

                this.searchedDate = this.filters.date;
            } catch (error) {
                console.error('Employee search API error:', error);
                toast.error(
                    error.response?.data?.message || t('Failed to fetch employee records.')
                );
            } finally {
                this.loading = false;
            }
        },

        toggleSelectAll(checked) {
            this.selectedEmployeeIds = checked
                ? this.employeesList.map(emp => emp.emp_auto_id)
                : [];
        },

        async submitBatchIn() {
            if (this.selectedEmployeeIds.length === 0) {
                toast.error(t('Please select at least one employee.'));
                return;
            }

            if (!this.actionForm.first_punch_at) {
                toast.error(t('Please select IN Time.'));
                return;
            }

            if (this.actionForm.first_punch_at.slice(0, 10) !== this.searchedDate) {
                toast.error(t('IN Time date must match attendance date.'));
                return;
            }

            const confirm = await Swal.fire({
                title: t('Submit Attendance IN?'),
                text: t('{count} employee(s) on {date}', {
                    count: this.selectedEmployeeIds.length,
                    date: this.searchedDate
                }),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: t('Yes, submit')
            });
            if (!confirm.isConfirmed) return;

            this.submitting = true;

            try {
                const punch = this.actionForm.first_punch_at;
                const formattedPunch =
                    punch.replace('T', ' ') + (punch.length === 16 ? ':00' : '');

                const response = await axios.post('/api/attendance/check-in', {
                    ids: this.selectedEmployeeIds,
                    attendance_date: this.searchedDate,
                    shift_id: this.filters.shift_id || null,
                    first_punch_at: formattedPunch,
                    status: this.actionForm.status
                });

                toast.success(response.data?.message || t('Attendance IN saved successfully!'));
                await this.fetchEmployees();
            } catch (error) {
                console.error('Attendance IN API Error:', error);

                const errors = error.response?.data?.errors;
                toast.error(
                    errors
                        ? Object.values(errors).flat().join(' ')
                        : (error.response?.data?.message || t('Failed to save Attendance IN.'))
                );
            } finally {
                this.submitting = false;
            }
        }
    }
};
</script>