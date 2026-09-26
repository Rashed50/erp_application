<template lang="html">
    <Breadcrumb title="Salary Sheet" buttonText="Generate Salary" :buttonLink="{ name: 'admin_hr_payroll_generate' }"
        buttonIcon="fa-solid fa-calculator" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="row align-items-center mb-2">
                    <div class="col-md-2">
                        <input type="month" class="form-control" v-model="filters.month" />
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" v-model="filters.department">
                            <option value="">All Departments</option>
                            <option v-for="department in options.departments" :key="department" :value="department">
                                {{ department }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" v-model="filters.status">
                            <option value="">All (except cancelled)</option>
                            <option v-for="status in SALARY_STATUSES" :key="status" :value="status">{{ status }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end gap-2">
                        <v-btn v-if="can(['payroll.approve'])" class="text-none text-white" color="blue-darken-3"
                            rounded="0" variant="flat" :disabled="!selectedWith('Generated').length"
                            @click="changeStatus('approve', 'Generated')">
                            Approve ({{ selectedWith('Generated').length }})
                        </v-btn>
                        <v-btn v-if="can(['payroll.pay'])" class="text-none text-white" color="green-darken-2" rounded="0"
                            variant="flat" :disabled="!selectedWith('Approved').length"
                            @click="changeStatus('pay', 'Approved')">
                            Mark Paid ({{ selectedWith('Approved').length }})
                        </v-btn>
                        <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="print">
                            <i class="fa-solid fa-print me-1"></i> Print
                        </v-btn>
                    </div>
                </div>

                <div class="table-responsive">
                    <v-table class="custom-bordered" density="compact">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" class="form-check-input" :checked="allSelected"
                                        @change="toggleAll($event.target.checked)" />
                                </th>
                                <th>Month</th>
                                <th>Employee</th>
                                <th class="text-right">Basic</th>
                                <th class="text-right">Allowances</th>
                                <th class="text-right">OT + Bonus</th>
                                <th class="text-right">Gross</th>
                                <th class="text-right">Deduction</th>
                                <th class="text-right">Net</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="11" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                </td>
                            </tr>
                            <tr v-else-if="!items.length">
                                <td colspan="11" class="text-center py-4">No salaries found.</td>
                            </tr>
                            <tr v-else v-for="item in items" :key="item.id">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input" :value="item.id" v-model="selected"
                                        :disabled="!['Generated', 'Approved'].includes(item.status)" />
                                </td>
                                <td>{{ monthLabel(item.salary_month) }}</td>
                                <td>
                                    {{ item.employee_code }} - {{ item.employee_name }}
                                    <div class="small text-muted">{{ item.designation }} · {{ item.department }}</div>
                                </td>
                                <td class="text-right">{{ money(item.basic_salary) }}</td>
                                <td class="text-right">{{ money(item.total_allowance) }}</td>
                                <td class="text-right">{{ money(item.overtime_amount + item.bonus + item.other_addition) }}</td>
                                <td class="text-right">{{ money(item.gross_salary) }}</td>
                                <td class="text-right">{{ money(item.total_deduction) }}</td>
                                <td class="text-right"><strong>{{ money(item.net_salary) }}</strong></td>
                                <td class="text-center">
                                    <span :class="salaryStatusClass(item.status)" :title="item.cancel_reason || ''">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <v-btn size="small" variant="text" color="blue-darken-3" @click="showDetail(item)">
                                        View
                                    </v-btn>
                                    <v-btn v-if="can(['payroll.cancel']) && ['Generated', 'Approved'].includes(item.status)"
                                        size="small" variant="text" color="red-darken-2" @click="cancel(item)">
                                        Cancel
                                    </v-btn>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="items.length">
                            <tr class="fw-bold">
                                <td colspan="6" class="text-right">Total ({{ totals.count }} salaries, all pages)</td>
                                <td class="text-right">{{ money(totals.gross_salary) }}</td>
                                <td class="text-right">{{ money(totals.total_deduction) }}</td>
                                <td class="text-right">{{ money(totals.net_salary) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </v-table>
                </div>

                <BasePagination :current-page="pagination.page" :per-page="pagination.perPage" :total="pagination.total"
                    :last-page="pagination.lastPage" @page-change="changePage" @per-page-change="changePerPage" />
            </v-card>
        </div>
    </div>

    <!-- Salary breakdown -->
    <v-dialog v-model="detailOpen" max-width="640">
        <v-card v-if="detail">
            <v-card-title>
                {{ detail.employee_code }} - {{ detail.employee_name }} · {{ monthLabel(detail.salary_month) }}
            </v-card-title>
            <v-card-text>
                <div class="row">
                    <div class="col-6">
                        <h6>Earnings</h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr><td>Basic</td><td class="text-end">{{ money(detail.basic_salary) }}</td></tr>
                                <tr><td>House Rent</td><td class="text-end">{{ money(detail.house_rent) }}</td></tr>
                                <tr><td>Medical</td><td class="text-end">{{ money(detail.medical_allowance) }}</td></tr>
                                <tr><td>Transport</td><td class="text-end">{{ money(detail.transport_allowance) }}</td></tr>
                                <tr><td>Food</td><td class="text-end">{{ money(detail.food_allowance) }}</td></tr>
                                <tr><td>Other Allowance</td><td class="text-end">{{ money(detail.other_allowance) }}</td></tr>
                                <tr>
                                    <td>Overtime ({{ detail.overtime_hours }}h × {{ money(detail.overtime_rate) }})</td>
                                    <td class="text-end">{{ money(detail.overtime_amount) }}</td>
                                </tr>
                                <tr><td>Bonus</td><td class="text-end">{{ money(detail.bonus) }}</td></tr>
                                <tr><td>Other Addition</td><td class="text-end">{{ money(detail.other_addition) }}</td></tr>
                                <tr class="fw-bold"><td>Gross</td><td class="text-end">{{ money(detail.gross_salary) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <h6>Deductions</h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Absence ({{ detail.absent_days }} × {{ money(detail.per_day_rate) }})</td>
                                    <td class="text-end">{{ money(detail.absence_deduction) }}</td>
                                </tr>
                                <tr>
                                    <td>Unpaid Leave ({{ detail.unpaid_leave_days }} × {{ money(detail.per_day_rate) }})</td>
                                    <td class="text-end">{{ money(detail.unpaid_leave_deduction) }}</td>
                                </tr>
                                <tr><td>Other Deductions</td><td class="text-end">{{ money(detail.other_deduction) }}</td></tr>
                                <tr class="fw-bold"><td>Total</td><td class="text-end">{{ money(detail.total_deduction) }}</td></tr>
                            </tbody>
                        </table>
                        <h6>Attendance</h6>
                        <div class="small">
                            Employed {{ detail.employed_days }}/{{ detail.days_in_month }} days ·
                            Working {{ detail.working_days }} · Present {{ detail.present_days }} ·
                            Paid leave {{ detail.paid_leave_days }}
                        </div>
                        <div class="small text-muted mt-1">Day rate based on {{ detail.deduction_basis === 'gross' ? 'basic + allowances' : 'basic' }}</div>
                    </div>
                </div>
                <div class="alert alert-success mb-0 d-flex justify-content-between">
                    <strong>Net Salary</strong><strong>{{ money(detail.net_salary) }}</strong>
                </div>
                <div class="small text-muted mt-2" v-if="detail.cancel_reason">Cancelled: {{ detail.cancel_reason }}</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="detailOpen = false">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import BasePagination from '@/components/common/BasePagination.vue';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePaginatedFetch } from '@/composables/usePaginatedFetch';
import { usePermission } from '@/composables/usePermission';
import { usePrintable } from '@/composables/usePrintable';
import { useSettingStore } from '@/stores/settings';
import { useRoute } from 'vue-router';
import { SALARY_STATUSES, currentMonth, money, monthLabel, salaryStatusClass } from '../helpers';

const route = useRoute();
const { can } = usePermission()
const { printTable } = usePrintable()
const settings = useSettingStore()

const {
    items,
    payload,
    loading,
    filters,
    pagination,
    fetchData,
    changePage,
    changePerPage,
} = usePaginatedFetch('/api/hr/salary-histories', {
    month: route.query.month || currentMonth(),
    department: '',
    status: '',
}, { perPage: 50 })

const totals = computed(() => payload.value.totals ?? { count: 0, gross_salary: 0, total_deduction: 0, net_salary: 0 })
const options = ref({ departments: [] })
const selected = ref([])
const detail = ref(null)
const detailOpen = ref(false)

watch(items, () => {
    selected.value = []
})

// No search button here: reload as soon as a filter changes.
// (Changing the page already triggers a fetch, so only fetch directly on page 1.)
watch(filters, () => {
    if (pagination.page !== 1) {
        pagination.page = 1
    } else {
        fetchData()
    }
})

const selectable = computed(() => items.value.filter((i) => ['Generated', 'Approved'].includes(i.status)).map((i) => i.id))
const allSelected = computed(() => selectable.value.length > 0 && selected.value.length === selectable.value.length)
const toggleAll = (checked) => {
    selected.value = checked ? [...selectable.value] : []
}
const selectedWith = (status) => items.value.filter((i) => selected.value.includes(i.id) && i.status === status).map((i) => i.id)

const changeStatus = async (action, fromStatus) => {
    const ids = selectedWith(fromStatus)
    const label = action === 'approve' ? 'Approve' : 'Mark as paid'
    const confirm = await Swal.fire({
        title: `${label} ${ids.length} salaries?`,
        text: action === 'approve'
            ? 'Approved salaries can no longer be recalculated.'
            : 'Paid salaries can no longer be changed or cancelled.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, ${label.toLowerCase()}`,
    })
    if (!confirm.isConfirmed) return

    try {
        const { data } = await axios.post(`/api/hr/salary-histories/${action}`, { ids })
        toast.success(data.message)
        fetchData()
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to update salaries.')
    }
}

const cancel = async (item) => {
    const { value: reason, isConfirmed } = await Swal.fire({
        title: `Cancel salary of ${item.employee_name}?`,
        text: 'The record is kept for audit and the month can be generated again for this employee.',
        input: 'text',
        inputPlaceholder: 'Reason for cancelling',
        inputValidator: (value) => (!value ? 'A reason is required.' : undefined),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Cancel salary',
        cancelButtonText: 'Keep',
    })
    if (!isConfirmed) return

    try {
        const { data } = await axios.post(`/api/hr/salary-histories/${item.id}/cancel`, { reason })
        toast.success(data.message)
        fetchData()
    } catch (e) {
        const errors = e.response?.data?.data
        toast.error(errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message || 'Failed to cancel.'))
    }
}

const showDetail = (item) => {
    detail.value = item
    detailOpen.value = true
}

const print = async () => {
    const { data } = await axios.get('/api/hr/salary-histories', { params: { ...filters, per_page: 100000 } })
    printTable({
        title: `Salary Sheet - ${monthLabel(filters.month) || 'All months'}`,
        logo: settings.company?.logo_url,
        items: data.data?.salaries ?? [],
        columns: [
            { key: 'index', label: '#' },
            { key: 'employee_code', label: 'ID' },
            { key: 'employee_name', label: 'Name' },
            { key: 'designation', label: 'Designation' },
            { key: 'basic_salary', label: 'Basic', align: 'right', format: money },
            { key: 'total_allowance', label: 'Allowances', align: 'right', format: money },
            { key: 'overtime_amount', label: 'Overtime', align: 'right', format: money },
            { key: 'gross_salary', label: 'Gross', align: 'right', format: money },
            { key: 'total_deduction', label: 'Deduction', align: 'right', format: money },
            { key: 'net_salary', label: 'Net', align: 'right', format: money },
            { key: 'status', label: 'Status' },
        ],
    })
}

onMounted(async () => {
    fetchData()
    try {
        const { data } = await axios.get('/api/hr/employees/options')
        if (data.success) options.value = data.data
    } catch (e) {
        console.error(e)
    }
})
</script>
