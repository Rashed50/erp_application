<template lang="html">
    <Breadcrumb title="Sales Dashboard" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card class="p-3 mb-3">
                <form class="row g-2 align-items-end" @submit.prevent="load">
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-0 small">{{ $t('From Date:') }}</label>
                        <input type="date" class="form-control" v-model="filters.from_date" />
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-0 small">{{ $t('To Date:') }}</label>
                        <input type="date" class="form-control" v-model="filters.to_date" />
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-0 small">{{ $t('Work Order Status:') }}</label>
                        <select class="form-select" v-model="filters.status">
                            <option value="">{{ $t('All statuses') }}</option>
                            <option v-for="name in statusOptions" :key="name" :value="name">{{ $t(name) }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <v-btn type="submit" color="blue-darken-4" class="text-none" :loading="loading">
                            <i class="fa-solid fa-filter me-2"></i> {{ $t('Filter') }}
                        </v-btn>
                        <v-btn class="text-none" variant="outlined" @click="reset" :disabled="loading">
                            {{ $t('Reset') }}
                        </v-btn>
                    </div>
                </form>
                <small class="text-muted d-block mt-2">
                    {{ $t('The date range applies to work order and invoice issue dates. The status filter applies to the work order totals and status overview.') }}
                </small>
            </v-card>

            <v-progress-linear v-if="loading && !data" indeterminate color="primary"></v-progress-linear>

            <template v-if="data">
                <!-- Summary cards -->
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-xl-3" v-for="card in cards" :key="card.label">
                        <v-card class="stat-card">
                            <div class="stat-icon" :style="{ background: card.color }"><i :class="card.icon"></i></div>
                            <div class="min-w-0">
                                <div class="stat-label">{{ $t(card.label) }}</div>
                                <div class="stat-value">{{ money(card.amount) }}</div>
                                <div class="stat-sub">{{ card.sub }}</div>
                            </div>
                        </v-card>
                    </div>
                </div>

                <!-- Status overview -->
                <div class="row g-3 mb-3">
                    <div class="col-lg-7">
                        <v-card class="p-3 h-100">
                            <h6 class="mb-3">{{ $t('Work Order Status Overview') }}</h6>
                            <div class="row g-2">
                                <div class="col-sm-6 col-xl-4" v-for="row in data.status_overview" :key="row.status">
                                    <div class="status-tile" :style="{ borderLeftColor: statusColor(row.status) }">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold">{{ $t(row.status) }}</span>
                                            <span class="badge" :style="{ background: statusColor(row.status) }">{{ row.count }}</span>
                                        </div>
                                        <div class="status-amount">{{ money(row.total_amount) }}</div>
                                        <small class="text-muted">{{ $t('Received') }}: {{ money(row.paid_amount) }}</small>
                                    </div>
                                </div>
                            </div>
                        </v-card>
                    </div>
                    <div class="col-lg-5">
                        <v-card class="p-3 h-100">
                            <h6 class="mb-3">{{ $t('Amount by Status') }}</h6>
                            <div class="chart-box"><canvas ref="statusCanvas"></canvas></div>
                        </v-card>
                    </div>
                </div>

                <!-- Sales performance -->
                <div class="row g-3 mb-3">
                    <div class="col-lg-5">
                        <v-card class="p-3 h-100">
                            <h6 class="mb-3">{{ $t('Overall Sales Summary') }}</h6>
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr v-for="row in salesRows" :key="row.label">
                                        <td>{{ $t(row.label) }}</td>
                                        <td class="text-end fw-semibold">{{ row.value }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small">
                                    <span>{{ $t('Collection Rate') }}</span>
                                    <span>{{ data.sales_summary.collection_rate }}%</span>
                                </div>
                                <v-progress-linear :model-value="Math.min(data.sales_summary.collection_rate, 100)"
                                    color="success" height="8" rounded></v-progress-linear>
                            </div>
                        </v-card>
                    </div>
                    <div class="col-lg-7">
                        <v-card class="p-3 h-100">
                            <h6 class="mb-3">{{ $t('Monthly Sales vs Received') }}</h6>
                            <div class="chart-box">
                                <canvas ref="monthlyCanvas"></canvas>
                            </div>
                            <div v-if="!data.monthly_sales.length" class="text-muted text-center small">
                                {{ $t('No invoices in this period.') }}
                            </div>
                        </v-card>
                    </div>
                </div>

                <!-- Due details -->
                <v-card class="p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ $t('Due Details (Completed Work Orders)') }}</h6>
                        <span class="badge bg-danger fs-6">{{ $t('Outstanding') }}: {{ money(data.outstanding_due.amount) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ $t('Customer') }}</th>
                                    <th>{{ $t('Work Order No') }}</th>
                                    <th>{{ $t('Work Title') }}</th>
                                    <th class="text-end">{{ $t('Total Amount') }}</th>
                                    <th class="text-end">{{ $t('Paid Amount') }}</th>
                                    <th class="text-end">{{ $t('Due Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in data.due_rows" :key="row.id">
                                    <td>{{ row.customer_name }}</td>
                                    <td>{{ row.work_order_no }}</td>
                                    <td>{{ row.work_title }}</td>
                                    <td class="text-end">{{ money(row.total_amount) }}</td>
                                    <td class="text-end">{{ money(row.paid_amount) }}</td>
                                    <td class="text-end text-danger fw-semibold">{{ money(row.due_amount) }}</td>
                                </tr>
                                <tr v-if="!data.due_rows.length">
                                    <td colspan="6" class="text-center text-muted py-3">
                                        {{ $t('No completed work orders with an outstanding due.') }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="data.due_rows.length">
                                <tr class="fw-bold">
                                    <td colspan="3">{{ $t('Total') }}</td>
                                    <td class="text-end">{{ money(dueTotals.total) }}</td>
                                    <td class="text-end">{{ money(dueTotals.paid) }}</td>
                                    <td class="text-end text-danger">{{ money(dueTotals.due) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </v-card>
            </template>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue'
import { toast } from 'vue3-toastify'
import {
    ArcElement, BarController, BarElement, CategoryScale, Chart, DoughnutController, Legend, LinearScale, Tooltip,
} from 'chart.js'
import Breadcrumb from '@/components/common/Breadcrumb.vue'
import { t } from '@/i18n'
import { money } from '@/views/hr/helpers'

Chart.register(ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Legend, LinearScale, Tooltip)

const STATUS_COLORS = {
    Pending: '#f59e0b',
    'In Progress': '#2563eb',
    Completed: '#16a34a',
    Cancelled: '#dc2626',
}
const statusColor = (status) => STATUS_COLORS[status] ?? '#6b7280'

const filters = reactive({ from_date: '', to_date: '', status: '' })
const data = ref(null)
const loading = ref(false)
const statusOptions = ref([])
const statusCanvas = ref(null)
const monthlyCanvas = ref(null)
let statusChart = null
let monthlyChart = null

const cards = computed(() => {
    const d = data.value
    return [
        {
            label: 'Total Work Orders', amount: d.work_orders.total_amount, icon: 'fa-solid fa-clipboard-list',
            color: '#2563eb', sub: `${d.work_orders.count} ${t('work orders')}`,
        },
        {
            label: 'Completed Work Orders', amount: d.completed.total_amount, icon: 'fa-solid fa-circle-check',
            color: '#16a34a', sub: `${d.completed.count} ${t('completed')}`,
        },
        {
            label: 'Outstanding Due', amount: d.outstanding_due.amount, icon: 'fa-solid fa-hourglass-half',
            color: '#dc2626', sub: `${d.outstanding_due.count} ${t('completed work orders unpaid')}`,
        },
        {
            label: 'Total Sales', amount: d.sales_summary.total_sales, icon: 'fa-solid fa-cart-shopping',
            color: '#7c3aed', sub: `${d.sales_summary.invoice_count} ${t('invoices')}`,
        },
    ]
})

const salesRows = computed(() => {
    const s = data.value.sales_summary
    return [
        { label: 'Total Sales', value: money(s.total_sales) },
        { label: 'Total Received', value: money(s.total_received) },
        { label: 'Outstanding Amount', value: money(s.outstanding) },
        { label: 'Invoices', value: s.invoice_count },
        { label: 'Average Invoice', value: money(s.average_invoice) },
        { label: 'Largest Invoice', value: money(s.largest_invoice) },
    ]
})

const dueTotals = computed(() => {
    const rows = data.value.due_rows
    const sum = (key) => rows.reduce((total, row) => total + row[key], 0)
    return { total: sum('total_amount'), paid: sum('paid_amount'), due: sum('due_amount') }
})

const drawCharts = async () => {
    await nextTick()
    statusChart?.destroy()
    monthlyChart?.destroy()

    if (statusCanvas.value) {
        const rows = data.value.status_overview
        statusChart = new Chart(statusCanvas.value, {
            type: 'doughnut',
            data: {
                labels: rows.map((row) => t(row.status)),
                datasets: [{ data: rows.map((row) => row.total_amount), backgroundColor: rows.map((row) => statusColor(row.status)) }],
            },
            options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
        })
    }

    if (monthlyCanvas.value) {
        const rows = data.value.monthly_sales
        monthlyChart = new Chart(monthlyCanvas.value, {
            type: 'bar',
            data: {
                labels: rows.map((row) => row.month),
                datasets: [
                    { label: t('Sales'), data: rows.map((row) => row.sales), backgroundColor: '#7c3aed' },
                    { label: t('Received'), data: rows.map((row) => row.received), backgroundColor: '#16a34a' },
                ],
            },
            options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } },
        })
    }
}

const load = async () => {
    loading.value = true
    try {
        const params = Object.fromEntries(Object.entries(filters).filter(([, value]) => value))
        const response = await axios.get('/api/accounting/reports/sales-dashboard', { params })
        if (response.data.success) {
            data.value = response.data.data
            statusOptions.value = data.value.statuses
            await drawCharts()
        }
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to load dashboard.'))
    } finally {
        loading.value = false
    }
}

const reset = () => {
    Object.assign(filters, { from_date: '', to_date: '', status: '' })
    load()
}

onMounted(load)

onBeforeUnmount(() => {
    statusChart?.destroy()
    monthlyChart?.destroy()
})
</script>

<style scoped>
.stat-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 18px;
    height: 100%;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    flex-shrink: 0;
}

.stat-label {
    font-size: 13px;
    color: #6c757d;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.stat-sub {
    font-size: 12px;
    color: #6c757d;
}

.status-tile {
    border: 1px solid #e5e7eb;
    border-left: 5px solid #6b7280;
    border-radius: 6px;
    padding: 10px 12px;
    height: 100%;
}

.status-amount {
    font-size: 18px;
    font-weight: 700;
}

.chart-box {
    position: relative;
    height: 260px;
}

.min-w-0 {
    min-width: 0;
}
</style>
