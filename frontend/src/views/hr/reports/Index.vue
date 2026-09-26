<template lang="html">
    <Breadcrumb title="HR & Payroll Reports" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 15px; margin-top: 15px;">
                <div class="row align-items-end g-2">
                    <div class="col-md-3">
                        <label>Report:</label>
                        <select class="form-select" v-model="reportKey">
                            <option v-for="(report, key) in reports" :key="key" :value="key">{{ report.title }}</option>
                        </select>
                    </div>
                    <div class="col-md-2" v-if="report.filters.includes('month')">
                        <label>Month:</label>
                        <input type="month" class="form-control" v-model="filters.month" />
                    </div>
                    <div class="col-md-2" v-if="report.filters.includes('year')">
                        <label>Year:</label>
                        <input type="number" min="2000" max="2100" class="form-control" v-model.number="filters.year" />
                    </div>
                    <div class="col-md-3" v-if="report.filters.includes('employee')">
                        <label>Employee:</label>
                        <select class="form-select" v-model="filters.employee_id">
                            <option value="">{{ report.requiresEmployee ? 'Select an employee' : 'All Employees' }}</option>
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                {{ employee.employee_code }} - {{ employee.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2" v-if="report.filters.includes('department')">
                        <label>Department:</label>
                        <select class="form-select" v-model="filters.department">
                            <option value="">All</option>
                            <option v-for="department in options.departments" :key="department" :value="department">
                                {{ department }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2" v-if="report.filters.includes('designation')">
                        <label>Designation:</label>
                        <select class="form-select" v-model="filters.designation">
                            <option value="">All</option>
                            <option v-for="designation in options.designations" :key="designation" :value="designation">
                                {{ designation }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2" v-if="report.filters.includes('status')">
                        <label>Status:</label>
                        <select class="form-select" v-model="filters.status">
                            <option value="">All</option>
                            <option v-for="status in report.statuses" :key="status" :value="status">{{ status }}</option>
                        </select>
                    </div>
                    <div class="col-md-auto d-flex gap-2">
                        <v-btn class="text-none text-white" color="blue-darken-3" rounded="0" variant="flat"
                            :loading="loading" @click="run">
                            Show
                        </v-btn>
                        <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                            :disabled="!rows.length" @click="print">
                            <i class="fa-solid fa-print me-1"></i> Print
                        </v-btn>
                    </div>
                </div>
            </v-card>

            <v-card style="padding: 5px; margin-top: 15px;">
                <div class="table-responsive">
                    <v-table class="custom-bordered" density="compact">
                        <thead>
                            <tr>
                                <th v-for="column in report.columns" :key="column.key"
                                    :class="column.align === 'right' ? 'text-right' : 'text-left'">
                                    {{ column.label }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td :colspan="report.columns.length" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                </td>
                            </tr>
                            <tr v-else-if="!rows.length">
                                <td :colspan="report.columns.length" class="text-center py-4">
                                    {{ message || 'No records found.' }}
                                </td>
                            </tr>
                            <tr v-else v-for="(row, index) in rows" :key="index">
                                <td v-for="column in report.columns" :key="column.key"
                                    :class="column.align === 'right' ? 'text-right' : 'text-left'">
                                    {{ cell(column, row, index) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="rows.length && totalRow">
                            <tr class="fw-bold">
                                <td v-for="column in report.columns" :key="column.key"
                                    :class="column.align === 'right' ? 'text-right' : 'text-left'">
                                    {{ totalRow[column.key] ?? '' }}
                                </td>
                            </tr>
                        </tfoot>
                    </v-table>
                </div>
            </v-card>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePrintable } from '@/composables/usePrintable';
import { useSettingStore } from '@/stores/settings';
import { SALARY_STATUSES, currentMonth, money, monthLabel } from '../helpers';

const { printTable } = usePrintable()
const settings = useSettingStore()

const ALL = 100000

const moneyColumn = (key, label) => ({ key, label, align: 'right', format: money, sum: true })

// Each report: its endpoint, filters, columns and how to read the rows.
const reports = {
    employees: {
        title: 'Employee List',
        endpoint: '/api/hr/employees',
        filters: ['department', 'designation', 'status'],
        statuses: ['Active', 'Inactive', 'Resigned', 'Terminated'],
        rows: (data) => data.employees ?? [],
        columns: [
            { key: 'index', label: '#' },
            { key: 'employee_code', label: 'Employee ID' },
            { key: 'name', label: 'Name' },
            { key: 'department', label: 'Department' },
            { key: 'designation', label: 'Designation' },
            { key: 'employment_type', label: 'Type' },
            { key: 'phone', label: 'Phone' },
            { key: 'joining_date', label: 'Joining Date' },
            { key: 'status', label: 'Status' },
        ],
    },
    work: {
        title: 'Employee Work History',
        endpoint: '/api/hr/employee-works',
        filters: ['month', 'employee', 'department', 'designation'],
        rows: (data) => data.works ?? [],
        columns: [
            { key: 'salary_month', label: 'Month', format: monthLabel },
            { key: 'employee_code', label: 'ID' },
            { key: 'employee_name', label: 'Name' },
            { key: 'department', label: 'Department' },
            { key: 'working_days', label: 'Working', align: 'right' },
            { key: 'present_days', label: 'Present', align: 'right' },
            { key: 'absent_days', label: 'Absent', align: 'right' },
            { key: 'paid_leave_days', label: 'Paid Leave', align: 'right' },
            { key: 'unpaid_leave_days', label: 'Unpaid Leave', align: 'right' },
            { key: 'overtime_hours', label: 'OT Hours', align: 'right' },
            moneyColumn('bonus', 'Bonus'),
        ],
    },
    payroll: {
        title: 'Monthly Payroll',
        endpoint: '/api/hr/salary-histories',
        filters: ['month', 'department', 'designation', 'status'],
        statuses: SALARY_STATUSES,
        rows: (data) => data.salaries ?? [],
        columns: [
            { key: 'index', label: '#' },
            { key: 'employee_code', label: 'ID' },
            { key: 'employee_name', label: 'Name' },
            { key: 'designation', label: 'Designation' },
            moneyColumn('basic_salary', 'Basic'),
            moneyColumn('total_allowance', 'Allowances'),
            moneyColumn('overtime_amount', 'Overtime'),
            moneyColumn('bonus', 'Bonus'),
            moneyColumn('gross_salary', 'Gross'),
            moneyColumn('total_deduction', 'Deduction'),
            moneyColumn('net_salary', 'Net'),
            { key: 'status', label: 'Status' },
        ],
    },
    employeeSalary: {
        title: 'Employee Salary History',
        endpoint: '/api/hr/salary-histories',
        filters: ['employee', 'status'],
        statuses: SALARY_STATUSES,
        requiresEmployee: true,
        extraParams: { include_cancelled: 1 },
        rows: (data) => data.salaries ?? [],
        columns: [
            { key: 'salary_month', label: 'Month', format: monthLabel },
            { key: 'designation', label: 'Designation' },
            moneyColumn('basic_salary', 'Basic'),
            moneyColumn('total_allowance', 'Allowances'),
            moneyColumn('overtime_amount', 'Overtime'),
            moneyColumn('gross_salary', 'Gross'),
            moneyColumn('total_deduction', 'Deduction'),
            moneyColumn('net_salary', 'Net'),
            { key: 'status', label: 'Status' },
        ],
    },
    department: {
        title: 'Department-wise Payroll',
        endpoint: '/api/hr/reports/department-wise',
        filters: ['month', 'status'],
        statuses: SALARY_STATUSES.filter((s) => s !== 'Cancelled'),
        rows: (data) => data.rows ?? [],
        columns: [
            { key: 'department', label: 'Department' },
            { key: 'employees', label: 'Employees', align: 'right', sum: true },
            moneyColumn('basic_salary', 'Basic'),
            moneyColumn('total_allowance', 'Allowances'),
            moneyColumn('overtime_amount', 'Overtime'),
            moneyColumn('gross_salary', 'Gross'),
            moneyColumn('total_deduction', 'Deduction'),
            moneyColumn('net_salary', 'Net'),
        ],
    },
    summary: {
        title: 'Salary Summary',
        endpoint: '/api/hr/reports/salary-summary',
        filters: ['year', 'department'],
        rows: (data) => data.rows ?? [],
        columns: [
            { key: 'month', label: 'Month', format: monthLabel },
            { key: 'employees', label: 'Employees', align: 'right' },
            moneyColumn('gross_salary', 'Gross'),
            moneyColumn('total_deduction', 'Deduction'),
            moneyColumn('net_salary', 'Net'),
            moneyColumn('paid_amount', 'Paid'),
            moneyColumn('unpaid_amount', 'Unpaid'),
        ],
    },
}

const reportKey = ref('payroll')
const report = computed(() => reports[reportKey.value])

const filters = reactive({
    month: currentMonth(),
    year: new Date().getFullYear(),
    employee_id: '',
    department: '',
    designation: '',
    status: '',
})

const rows = ref([])
const loading = ref(false)
const message = ref('')
const employees = ref([])
const options = ref({ departments: [], designations: [] })

const cell = (column, row, index) => {
    if (column.key === 'index') return index + 1
    const value = row[column.key]
    return column.format ? column.format(value, row) : value
}

const totalRow = computed(() => {
    const summed = report.value.columns.filter((c) => c.sum)
    if (!summed.length) return null
    const total = { [report.value.columns[0].key]: 'Total' }
    for (const column of summed) {
        const sum = rows.value.reduce((acc, row) => acc + (Number(row[column.key]) || 0), 0)
        total[column.key] = column.format ? column.format(sum) : sum
    }
    return total
})

const params = () => {
    const current = report.value
    const result = { per_page: ALL, ...(current.extraParams || {}) }
    if (current.filters.includes('month')) result.month = filters.month
    if (current.filters.includes('year')) result.year = filters.year
    if (current.filters.includes('employee') && filters.employee_id) result.employee_id = filters.employee_id
    for (const key of ['department', 'designation', 'status']) {
        if (current.filters.includes(key) && filters[key]) result[key] = filters[key]
    }
    return result
}

const run = async () => {
    rows.value = []
    message.value = ''
    if (report.value.requiresEmployee && !filters.employee_id) {
        message.value = 'Select an employee to see their salary history.'
        return
    }

    loading.value = true
    try {
        const { data } = await axios.get(report.value.endpoint, { params: params() })
        rows.value = report.value.rows(data.data ?? {})
    } catch (e) {
        message.value = e.response?.data?.message || 'Failed to load the report.'
    } finally {
        loading.value = false
    }
}

const print = () => {
    const subtitle = [
        report.value.filters.includes('month') ? monthLabel(filters.month) : null,
        report.value.filters.includes('year') ? filters.year : null,
        filters.department && report.value.filters.includes('department') ? filters.department : null,
    ].filter(Boolean).join(' · ')

    printTable({
        title: `${report.value.title}${subtitle ? ` - ${subtitle}` : ''}`,
        logo: settings.company?.logo_url,
        items: totalRow.value ? [...rows.value, { ...totalRow.value, __total: true }] : rows.value,
        columns: report.value.columns.map((column) => ({
            ...column,
            key: column.key === 'index' ? '__index' : column.key,
            format: (value, row) => {
                if (row.__total) return value ?? ''
                if (column.key === 'index') return rows.value.indexOf(row) + 1
                return column.format ? column.format(value, row) : (value ?? '')
            },
        })),
    })
}

watch(reportKey, () => {
    filters.status = ''
    run()
})

onMounted(async () => {
    try {
        const [optionsResponse, employeesResponse] = await Promise.all([
            axios.get('/api/hr/employees/options'),
            axios.get('/api/hr/employees', { params: { per_page: ALL } }),
        ])
        options.value = optionsResponse.data.data
        employees.value = employeesResponse.data.data?.employees ?? []
    } catch (e) {
        console.error(e)
    }
    run()
})
</script>
