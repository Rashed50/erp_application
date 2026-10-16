<template>
    <div>
        <!-- Search filters (label and field side by side) -->
        <v-card style="padding: 15px; margin-top: 15px;">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Fixed widths for the date fields, so the calendar icon never overlaps the next label -->
                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 text-nowrap">{{ $t('From:') }}</label>
                    <input type="date" class="form-control" style="width: 170px;" v-model="filters.date_from" />
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 text-nowrap">{{ $t('To:') }}</label>
                    <input type="date" class="form-control" style="width: 170px;" v-model="filters.date_to" />
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 text-nowrap">{{ $t('Status:') }}</label>
                    <select class="form-select" style="width: 140px;" v-model="filters.status">
                        <option value="">{{ $t('Select') }}</option>
                        <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ $t(s.label) }}
                        </option>
                    </select>
                </div>

                <!-- Takes all the remaining width -->
                <div class="d-flex align-items-center gap-2" style="flex: 1 1 240px;">
                    <label class="mb-0 text-nowrap">{{ $t('Employee ID:') }}</label>
                    <input type="text" class="form-control" v-model.trim="filters.employee_id" placeholder="e.g. EMP001"
                        @keyup.enter="search" />
                </div>

                <v-btn class="text-none text-white" color="blue-darken-3" rounded="0" variant="flat" height="36"
                    min-width="110" :loading="loading" @click="search">
                    <i class="fa-solid fa-search me-1"></i> {{ $t('Search') }}
                </v-btn>
            </div>
        </v-card>

        <!-- Data table -->
        <v-card style="padding: 10px; margin-top: 15px;">
            <div class="table-responsive">
                <v-table class="custom-bordered" density="compact">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">{{ $t('SL') }}</th>
                            <th>{{ $t('ID') }}</th>
                            <th>{{ $t('Name') }}</th>
                            <th>{{ $t('Date') }}</th>
                            <th>{{ $t('Status') }}</th>
                            <th>{{ $t('IN Time') }}</th>
                            <th>{{ $t('OUT Time') }}</th>
                            <th class="text-center" width="120">{{ $t('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="8" class="text-center text-muted py-3">{{ $t('Loading records...') }}</td>
                        </tr>

                        <tr v-else-if="!records.length">
                            <td colspan="8" class="text-center text-muted py-3">
                                {{ $t('No attendance records found.') }}
                            </td>
                        </tr>

                        <tr v-for="(item, index) in records" v-else :key="item.id">
                            <td class="text-center">
                                {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
                            </td>
                            <td>{{ item.employee?.employee_id || item.employee_auto_id || '-' }}</td>
                            <td>{{ item.employee?.name || 'N/A' }}</td>
                            <td>{{ dateOnly(item.attendance_date) }}</td>
                            <td>
                                <span :class="getStatusBadge(item.status)" class="text-capitalize">
                                    {{ item.status ? item.status.replace('_', ' ') : '-' }}
                                </span>
                            </td>
                            <td>{{ timeOnly(item.first_punch_at) }}</td>
                            <td>{{ timeOnly(item.last_punch_at) }}</td>
                            <td class="text-center">
                                <v-btn class="me-1" color="blue-darken-3" icon size="x-small" variant="tonal"
                                    :title="$t('Edit')" @click="openEdit(item)">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </v-btn>
                                <v-btn color="red-darken-3" icon size="x-small" variant="tonal" :title="$t('Delete')"
                                    @click="deleteRecord(item)">
                                    <i class="fa-solid fa-trash"></i>
                                </v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </div>

            <!-- Pagination -->
            <div v-if="records.length" class="d-flex justify-content-between align-items-center mt-3">
                <span class="small text-muted">
                    {{ $t('Page {current} of {last}', { current: pagination.current_page, last: pagination.last_page })
                    }}
                    · {{ $t('Total: {count}', { count: pagination.total }) }}
                </span>

                <div class="d-flex gap-2">
                    <v-btn class="text-none" size="small" rounded="0" variant="outlined"
                        :disabled="loading || pagination.current_page <= 1"
                        @click="fetchAttendances(pagination.current_page - 1)">
                        {{ $t('Previous') }}
                    </v-btn>
                    <v-btn class="text-none" size="small" rounded="0" variant="outlined"
                        :disabled="loading || pagination.current_page >= pagination.last_page"
                        @click="fetchAttendances(pagination.current_page + 1)">
                        {{ $t('Next') }}
                    </v-btn>
                </div>
            </div>
        </v-card>

        <!-- Edit modal -->
        <v-dialog v-model="editDialog" max-width="600" persistent>
            <v-card style="padding: 20px;">
                <h6 class="mb-3">{{ $t('Edit Attendance') }}</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label>{{ $t('Employee ID:') }}</label>
                        <input type="text" class="form-control" :value="editForm.employee_code" disabled />
                    </div>

                    <div class="col-md-6">
                        <label>{{ $t('Name:') }}</label>
                        <input type="text" class="form-control" :value="editForm.name" disabled />
                    </div>

                    <div class="col-md-6">
                        <label>{{ $t('Attendance Date:') }}</label>
                        <input type="text" class="form-control" :value="editForm.attendance_date" disabled />
                    </div>

                    <div class="col-md-6">
                        <label>{{ $t('Status:') }}</label>
                        <select class="form-select" v-model="editForm.status">
                            <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ $t(s.label) }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>{{ $t('First Punch (IN):') }}</label>
                        <input type="datetime-local" class="form-control" v-model="editForm.first_punch_at" />
                    </div>

                    <div class="col-md-6">
                        <label>{{ $t('Last Punch (OUT):') }}</label>
                        <input type="datetime-local" class="form-control" v-model="editForm.last_punch_at" />
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" :disabled="saving"
                        @click="closeEdit">
                        {{ $t('Cancel') }}
                    </v-btn>
                    <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                        :loading="saving" @click="updateRecord">
                        <i class="fa-solid fa-check me-1"></i> {{ $t('Update') }}
                    </v-btn>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import { t } from '@/i18n';

export default {
    name: 'AttendanceList',

    data() {
        const today = this.localDate();

        return {
            loading: false,
            saving: false,
            records: [],

            statusOptions: [
                { value: 'present', label: 'Present' },
                { value: 'absent', label: 'Absent' },
                { value: 'late', label: 'Late' },
                { value: 'half_day', label: 'Half Day' },
                { value: 'leave', label: 'Leave' },
                { value: 'holiday', label: 'Holiday' },
                { value: 'weekly_off', label: 'Weekly Off' },
                { value: 'incomplete', label: 'Incomplete' },
                { value: 'offsite', label: 'Offsite' }
            ],

            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 25,
                total: 0
            },

            // Date From / Date To default to today
            filters: {
                date_from: today,
                date_to: today,
                status: '',
                employee_id: '',
                per_page: 25
            },

            editDialog: false,
            editForm: {
                id: null,
                employee_code: '',
                name: '',
                attendance_date: '',
                status: 'present',
                first_punch_at: '',
                last_punch_at: ''
            }
        };
    },

    mounted() {
        this.fetchAttendances(1);
    },

    methods: {
        // Today's date in local time (YYYY-MM-DD)
        localDate() {
            const now = new Date();
            return new Date(
                now.getTime() - now.getTimezoneOffset() * 60000
            ).toISOString().slice(0, 10);
        },

        // "2026-10-16T00:00:00.000000Z" -> "2026-10-16"
        dateOnly(value) {
            return value ? String(value).slice(0, 10) : '-';
        },

        // "2026-10-15T14:46:00.000000Z" -> "02:46 PM"
        // Read straight from the string (no Date object) so no timezone shift happens.
        timeOnly(value) {
            if (!value) return '-';

            const match = String(value).match(/(\d{2}):(\d{2})/);
            if (!match) return '-';

            let hour = parseInt(match[1], 10);
            const period = hour >= 12 ? 'PM' : 'AM';
            hour = hour % 12 || 12;

            return `${String(hour).padStart(2, '0')}:${match[2]} ${period}`;
        },

        // "2026-10-15T09:00:00.000000Z" or "2026-10-15 09:00:00" -> "2026-10-15T09:00" (for datetime-local)
        toInput(value) {
            return value ? String(value).replace(' ', 'T').slice(0, 16) : '';
        },

        // "2026-10-15T09:00" -> "2026-10-15 09:00:00" (for API), empty -> null
        toApi(value) {
            if (!value) return null;
            return value.replace('T', ' ') + (value.length === 16 ? ':00' : '');
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
        },

        // Search / Enter always starts from page 1
        search() {
            this.fetchAttendances(1);
        },

        async fetchAttendances(page = 1) {
            if (this.loading) return;

            if (this.filters.date_from && this.filters.date_to &&
                this.filters.date_from > this.filters.date_to) {
                toast.error(t('Date From cannot be after Date To.'));
                return;
            }

            this.loading = true;

            try {
                // Laravel paginate() response: { current_page, data: [...], last_page, per_page, total }
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
                if (!this.records.length && res.message) {
                    toast.info(res.message);
                }
            } catch (error) {
                console.error('Error fetching attendance data:', error);
                toast.error(
                    error.response?.data?.message || t('Failed to load attendance records.')
                );
            } finally {
                this.loading = false;
            }
        },

        openEdit(item) {
            this.editForm = {
                id: item.id,
                employee_code: item.employee?.employee_id || item.employee_auto_id || '',
                name: item.employee?.name || '',
                attendance_date: this.dateOnly(item.attendance_date),
                status: item.status || 'present',
                first_punch_at: this.toInput(item.first_punch_at),
                last_punch_at: this.toInput(item.last_punch_at)
            };
            this.editDialog = true;
        },

        closeEdit() {
            this.editDialog = false;
        },

        async updateRecord() {
            const { first_punch_at, last_punch_at } = this.editForm;

            if (first_punch_at && last_punch_at && last_punch_at < first_punch_at) {
                toast.error(t('Last Punch (OUT) cannot be before First Punch (IN).'));
                return;
            }

            this.saving = true;

            try {
                const response = await axios.put(`/api/attendance/${this.editForm.id}`, {
                    status: this.editForm.status,
                    first_punch_at: this.toApi(first_punch_at),
                    last_punch_at: this.toApi(last_punch_at)
                });

                toast.success(response.data?.message || t('Attendance updated successfully.'));
                this.closeEdit();
                await this.fetchAttendances(this.pagination.current_page);
            } catch (error) {
                console.error('Attendance update error:', error);

                const errors = error.response?.data?.errors;
                toast.error(
                    errors
                        ? Object.values(errors).flat().join(' ')
                        : (error.response?.data?.message || t('Failed to update attendance.'))
                );
            } finally {
                this.saving = false;
            }
        },

        async deleteRecord(item) {
            const confirm = await Swal.fire({
                title: t('Delete this attendance record?'),
                text: `${item.employee?.name || ''} - ${this.dateOnly(item.attendance_date)}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: t('Yes, delete')
            });
            if (!confirm.isConfirmed) return;

            try {
                const response = await axios.delete(`/api/attendance/${item.id}`);
                toast.success(response.data?.message || t('Attendance deleted successfully.'));

                // If the last row of a page was deleted, go back one page
                const page = this.records.length === 1 && this.pagination.current_page > 1
                    ? this.pagination.current_page - 1
                    : this.pagination.current_page;
                await this.fetchAttendances(page);
            } catch (error) {
                console.error('Attendance delete error:', error);
                toast.error(
                    error.response?.data?.message || t('Failed to delete attendance.')
                );
            }
        }
    }
};
</script>