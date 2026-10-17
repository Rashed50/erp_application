<template>
    <div class="process-page">

        <!-- Monthly Attendance Process Form -->
        <v-card class="process-card pa-4 mt-3" elevation="2" rounded="lg">
            <div class="d-flex flex-wrap align-items-center gap-3">

                <!-- Month -->
                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 text-nowrap">
                        {{ $t('Month:') }}
                    </label>

                    <input type="month" class="form-control month-input" v-model="filters.month"
                        :disabled="processing" />
                </div>

                <!-- Employee IDs -->
                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 text-nowrap">
                        {{ $t('Employee IDs:') }}
                    </label>

                    <input type="text" class="form-control employee-input" v-model="filters.employee_ids"
                        placeholder="e.g. 101,102,105" :disabled="processing" />
                </div>

                <!-- Process Button -->
                <v-btn class="text-none text-white" color="orange-darken-3" rounded="0" variant="flat" height="40"
                    min-width="120" :disabled="!filters.month" :loading="processing" @click="processRecords">
                    <i class="fa-solid fa-gears me-2"></i>
                    {{ $t('Process') }}
                </v-btn>

            </div>
        </v-card>

        <!-- Process Result -->
        <v-card v-if="result" class="result-card pa-4 mt-3" elevation="2" rounded="lg">
            <div class="text-center">
                <h6 class="mb-3">
                    {{ $t('Process result') }}
                </h6>

                <div class="d-flex flex-wrap justify-center gap-2">

                    <span class="badge bg-primary">
                        {{ monthLabel(resultMonth) }}
                    </span>

                    <span class="badge bg-success">
                        {{ $t('{count} employees processed', {
                            count: result.employees_processed
                        }) }}
                    </span>

                </div>
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
    name: 'Process',

    emits: ['cancel'],

    data() {
        return {
            processing: false,
            result: null,
            resultMonth: '',

            filters: {
                month: this.currentMonth(),
                employee_ids: ''
            }
        };
    },

    watch: {
        'filters.month'() {
            this.clearResult();
        },

        'filters.employee_ids'() {
            this.clearResult();
        }
    },

    methods: {

        // Current month in local time
        currentMonth() {
            const now = new Date();

            return new Date(
                now.getTime() -
                now.getTimezoneOffset() * 60000
            ).toISOString().slice(0, 7);
        },

        // Month label: 2026-10 -> October 2026
        monthLabel(value) {
            if (!value) return '';

            const [year, month] = value
                .split('-')
                .map(Number);

            return new Date(
                year,
                month - 1,
                1
            ).toLocaleString('en-US', {
                month: 'long',
                year: 'numeric'
            });
        },

        // Clear previous result
        clearResult() {
            this.result = null;
            this.resultMonth = '';
        },

        // Convert comma-separated employee IDs
        parseEmployeeIds(value) {
            if (!value || !value.trim()) {
                return [];
            }

            const values = value
                .split(',')
                .map(id => id.trim());

            // Allow only positive integer IDs
            if (
                values.some(id => !/^[1-9]\d*$/.test(id))
            ) {
                throw new Error(
                    'Enter valid Employee IDs separated by commas.'
                );
            }

            // Remove duplicate IDs
            return [...new Set(values.map(Number))];
        },

        // Process Monthly Attendance
        async processRecords() {

            if (this.processing) return;

            // Validate Month
            if (!this.filters.month) {
                toast.error(
                    t('Please select a month.')
                );
                return;
            }

            // Validate Employee IDs
            let employeeIds = [];

            try {
                employeeIds = this.parseEmployeeIds(
                    this.filters.employee_ids
                );
            } catch (error) {
                toast.error(t(error.message));
                return;
            }

            const selectedMonth = this.filters.month;
            const selectedIds = this.filters.employee_ids;

            const [year, month] = selectedMonth
                .split('-')
                .map(Number);

            // Employee-wise backend processing
            // will be implemented later.
            if (employeeIds.length > 0) {
                toast.info(
                    t(
                        'Employee-wise processing is not implemented yet.'
                    )
                );
                return;
            }

            // Confirmation
            const confirm = await Swal.fire({
                title: t('Process {month}?', {
                    month: this.monthLabel(selectedMonth)
                }),
                text: t(
                    'Existing monthly records for this month will be recalculated.'
                ),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: t('Yes, process'),
                cancelButtonText: t('Cancel')
            });

            if (!confirm.isConfirmed) return;

            // Prevent duplicate processing
            if (this.processing) return;

            // Avoid running outdated selections
            if (
                this.filters.month !== selectedMonth ||
                this.filters.employee_ids !== selectedIds
            ) {
                toast.info(
                    t('Filters changed. Please process again.')
                );
                return;
            }

            this.processing = true;
            this.clearResult();

            try {
                // Existing API: all employees
                const response = await axios.post(
                    '/api/attendance/generate-monthly-records',
                    {
                        year,
                        month
                    }
                );

                // Show result only for current filters
                if (
                    this.filters.month === selectedMonth &&
                    this.filters.employee_ids === selectedIds
                ) {
                    this.result = response.data?.data ?? null;
                    this.resultMonth = selectedMonth;
                }

                toast.success(
                    response.data?.message ||
                    t(
                        'Monthly work records generated successfully.'
                    )
                );

            } catch (error) {

                console.error(
                    'Monthly process error:',
                    error
                );

                const errors = error.response?.data?.errors;

                toast.error(
                    errors
                        ? Object.values(errors)
                            .flat()
                            .join(' ')
                        : (
                            error.response?.data?.message ||
                            t(
                                'Failed to generate monthly work records.'
                            )
                        )
                );

            } finally {
                this.processing = false;
            }
        }
    }
};
</script>

