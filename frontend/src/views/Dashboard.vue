<template lang="html">
    <v-card class="dashboard-wrapper">
        <div class="card-header">
            <h4>{{ $t('common.adminDashboard') }}</h4>
            <p>{{ $t('Welcome, {name}!', { name: auth.user.name ?? $t('Boss') }) }}</p>
        </div>

        <div class="card-body">
            <template v-if="can(['account-reports.view'])">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-2">
                    <h6 class="text-muted mb-0">{{ $t('Accounting Overview') }}</h6>
                    <form class="d-flex flex-wrap align-items-end gap-2" @submit.prevent="loadSummary">
                        <div>
                            <label class="form-label mb-0 small">{{ $t('From Date') }}</label>
                            <input type="date" class="form-control form-control-sm" v-model="filters.from_date">
                        </div>
                        <div>
                            <label class="form-label mb-0 small">{{ $t('To Date') }}</label>
                            <input type="date" class="form-control form-control-sm" v-model="filters.to_date">
                        </div>
                        <v-btn type="submit" size="small" color="blue-darken-4" class="text-none" :loading="loading">
                            {{ $t('Filter') }}
                        </v-btn>
                    </form>
                </div>
                <small class="text-muted d-block mb-3">
                    {{ $t('The date range applies to sales and purchases; balances are current.') }}
                </small>

                <v-progress-linear v-if="loading && !summary" indeterminate color="primary"></v-progress-linear>

                <template v-if="summary">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg" v-for="card in cards" :key="card.label">
                            <v-card class="stat-card dashboard-clickable" role="button" tabindex="0"
                                :aria-label="$t('View details for {name}', { name: $t(card.label) })"
                                @click="openDetails(card)"
                                @keydown.enter.prevent="openDetails(card)"
                                @keydown.space.prevent="openDetails(card)">
                                <div class="stat-icon" :class="card.color"><i :class="card.icon"></i></div>
                                <div>
                                    <div class="stat-label">{{ $t(card.label) }}</div>
                                    <div class="stat-value">{{ money(card.value) }}</div>
                                </div>
                            </v-card>
                        </div>
                    </div>

                    <section class="mt-4">
                        <h6 class="text-muted mb-3">{{ $t('Activity Summary') }}</h6>
                        <div v-for="period in activityPeriods" :key="period.key" class="mb-4">
                            <h6 class="mb-2">{{ $t(period.label) }}</h6>
                            <div class="row g-3">
                                <div class="col-sm-6 col-lg-4" v-for="metric in period.metrics" :key="metric.label">
                                    <v-card class="stat-card dashboard-clickable" role="button" tabindex="0"
                                        :aria-label="$t('View details for {name}', { name: $t(metric.label) })"
                                        @click="openDetails({ ...metric, periodLabel: period.label, periodKey: period.key })"
                                        @keydown.enter.prevent="openDetails({ ...metric, periodLabel: period.label, periodKey: period.key })"
                                        @keydown.space.prevent="openDetails({ ...metric, periodLabel: period.label, periodKey: period.key })">
                                        <div class="stat-icon" :class="metric.color"><i :class="metric.icon"></i></div>
                                        <div>
                                            <div class="stat-label">{{ $t(metric.label) }}</div>
                                            <div class="stat-value">{{ money(metric.value) }}</div>
                                            <small v-if="metric.count !== undefined" class="text-muted">
                                                {{ $t('{count} work orders', { count: metric.count }) }}
                                            </small>
                                        </div>
                                    </v-card>
                                </div>
                            </div>
                        </div>
                    </section>

                    <v-card class="mt-3" style="padding: 15px;" v-if="summary.banks.length > 1">
                        <h6 class="mb-3">{{ $t('Bank Balances') }}</h6>
                        <div v-for="bank in summary.banks" :key="bank.id"
                            class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ bank.name }} <small class="text-muted" v-if="bank.account_number">({{ bank.account_number }})</small></span>
                            <strong>{{ money(bank.balance) }}</strong>
                        </div>
                    </v-card>
                </template>
            </template>

            <div v-else class="dashboard-content">
                {{ $t('Dashboard content goes here. You can add your dashboard components, charts, and statistics in this section.') }}
            </div>
        </div>

        <v-dialog v-model="detailsOpen" max-width="1200">
            <v-card>
                <v-card-title class="d-flex justify-content-between align-items-center">
                    <span>{{ detailsTitle }}</span>
                    <v-btn variant="text" class="text-none" @click="detailsOpen = false">{{ $t('Close') }}</v-btn>
                </v-card-title>
                <v-card-text>
                    <div v-if="detailsCard && ['sales', 'purchase'].includes(detailsCard.id)" class="text-muted small mb-3">
                        {{ detailsDateRange }}
                    </div>
                    <v-progress-linear v-if="detailsLoading" indeterminate color="primary" class="mb-3"></v-progress-linear>
                    <div v-else-if="detailsData && detailsRows.length" class="table-responsive">
                        <v-table density="compact">
                            <thead>
                                <tr>
                                    <th v-for="column in detailsColumns" :key="column.key" :class="{ 'text-end': column.type === 'money' }">
                                        {{ $t(column.label) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in detailsRows" :key="row.id ?? index">
                                    <td v-for="column in detailsColumns" :key="column.key" :class="{ 'text-end': column.type === 'money' }">
                                        {{ detailValue(row, column) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="detailsTotalRow">
                                <tr class="font-weight-bold">
                                    <th v-for="column in detailsColumns" :key="column.key" :class="{ 'text-end': column.type === 'money' }">
                                        {{ detailsTotalRow[column.key] ?? '' }}
                                    </th>
                                </tr>
                            </tfoot>
                        </v-table>
                    </div>
                    <div v-else-if="detailsData" class="text-center text-muted py-5">{{ $t('No records found.') }}</div>
                </v-card-text>
                <v-card-actions class="justify-content-end">
                    <v-btn color="primary" class="text-none" :disabled="!detailsRows.length" @click="printDetails">
                        {{ $t('Print / Save as PDF') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script setup>
import axios from 'axios'
import { toast } from 'vue3-toastify'
import { t } from '@/i18n'
import { useAuthStore } from '@/stores/auth'
import { usePermission } from '@/composables/usePermission'
import { usePrintable } from '@/composables/usePrintable'
import { money } from '@/views/hr/helpers'

const auth = useAuthStore()
const { can } = usePermission()

const summary = ref(null)
const loading = ref(false)
const filters = reactive({ from_date: '', to_date: '' })
const detailsOpen = ref(false)
const detailsLoading = ref(false)
const detailsCard = ref(null)
const detailsData = ref(null)
const { printTable } = usePrintable()

const cards = computed(() => [
    { id: 'sales', label: 'Total Sales', value: summary.value.total_sales, icon: 'fa-solid fa-cart-shopping', color: 'bg-primary' },
    { id: 'purchase', label: 'Total Purchase', value: summary.value.total_purchase, icon: 'fa-solid fa-truck', color: 'bg-info' },
    { id: 'receivable', label: 'Total Receivable', value: summary.value.total_receivable, icon: 'fa-solid fa-hand-holding-dollar', color: 'bg-success' },
    { id: 'payable', label: 'Total Payable', value: summary.value.total_payable, icon: 'fa-solid fa-file-invoice-dollar', color: 'bg-danger' },
    { id: 'bank_balance', label: 'Bank Balance', value: summary.value.bank_balance, icon: 'fa-solid fa-building-columns', color: 'bg-warning' },
])

const activityPeriods = computed(() => {
    const activity = summary.value?.activity_summary
    if (!activity) return []

    return [
        { key: 'today', label: 'Today' },
        { key: 'this_month', label: 'This Month' },
        { key: 'this_year', label: 'This Year' },
    ].map((period) => ({
        ...period,
        metrics: [
            {
                id: 'expense',
                label: 'Expense',
                value: activity[period.key].expense,
                periodKey: period.key,
                icon: 'fa-solid fa-money-bill-transfer',
                color: 'bg-danger',
            },
            {
                id: 'collection',
                label: 'Collection',
                value: activity[period.key].collection,
                periodKey: period.key,
                icon: 'fa-solid fa-hand-holding-dollar',
                color: 'bg-success',
            },
            {
                id: 'work_orders',
                label: 'Work Orders',
                value: activity[period.key].work_orders_amount,
                count: activity[period.key].work_orders_count,
                periodKey: period.key,
                icon: 'fa-solid fa-clipboard-list',
                color: 'bg-primary',
            },
        ],
    }))
})

const detailsTitle = computed(() => {
    if (!detailsCard.value) return ''
    const title = t(detailsCard.value.label)
    return detailsCard.value.periodLabel ? `${title} - ${t(detailsCard.value.periodLabel)}` : title
})

const detailsDateRange = computed(() => {
    const from = filters.from_date || t('All dates')
    const to = filters.to_date || t('All dates')
    return `${t('From Date')}: ${from} | ${t('To Date')}: ${to}`
})

const detailColumnDefinitions = {
    sales: [
        { key: 'invoice_number', label: 'Invoice' },
        { key: 'customer_name', label: 'Customer' },
        { key: 'issue_date', label: 'Date' },
        { key: 'net_total', label: 'Net Total', type: 'money', totalKey: 'net_total' },
        { key: 'paid_amount', label: 'Paid', type: 'money', totalKey: 'paid_amount' },
        { key: 'due_amount', label: 'Due', type: 'money', totalKey: 'due_amount' },
    ],
    purchase: [
        { key: 'invoice_number', label: 'Invoice' },
        { key: 'supplier_name', label: 'Supplier' },
        { key: 'purchase_date', label: 'Date' },
        { key: 'net_total', label: 'Net Total', type: 'money', totalKey: 'net_total' },
        { key: 'vat_amount', label: 'VAT', type: 'money', totalKey: 'vat_amount' },
    ],
    receivable: [
        { key: 'name', label: 'Customer' },
        { key: 'phone', label: 'Phone' },
        { key: 'opening_balance', label: 'Opening Balance', type: 'money', totalKey: 'opening_balance' },
        { key: 'current_balance', label: 'Current Balance', type: 'money', totalKey: 'current_balance' },
    ],
    payable: [
        { key: 'name', label: 'Supplier' },
        { key: 'phone', label: 'Phone' },
        { key: 'opening_balance', label: 'Opening Balance', type: 'money', totalKey: 'opening_balance' },
        { key: 'current_balance', label: 'Current Balance', type: 'money', totalKey: 'current_balance' },
    ],
    bank_balance: [
        { key: 'name', label: 'Account' },
        { key: 'account_number', label: 'Account Number' },
        { key: 'balance', label: 'Balance', type: 'money', totalKey: 'balance' },
    ],
    expense: [
        { key: 'transaction_date', label: 'Date' },
        { key: 'reference_no', label: 'Reference' },
        { key: 'account_name', label: 'Expense Account' },
        { key: 'payment_account_name', label: 'Payment Account' },
        { key: 'amount', label: 'Amount', type: 'money', totalKey: 'amount' },
        { key: 'description', label: 'Description' },
    ],
    collection: [
        { key: 'transaction_date', label: 'Date' },
        { key: 'customer_name', label: 'Customer' },
        { key: 'invoice_no', label: 'Invoice' },
        { key: 'credit', label: 'Amount', type: 'money', totalKey: 'credit' },
        { key: 'notes', label: 'Notes' },
    ],
    work_orders: [
        { key: 'work_order_no', label: 'Work Order' },
        { key: 'work_title', label: 'Title' },
        { key: 'customer_name', label: 'Customer' },
        { key: 'issue_date', label: 'Date' },
        { key: 'total_amount', label: 'Total Amount', type: 'money', totalKey: 'total_amount' },
        { key: 'collected_amount', label: 'Collected', type: 'money', totalKey: 'collected_amount' },
        { key: 'due_amount', label: 'Due', type: 'money', totalKey: 'due_amount' },
    ],
}

const detailsColumns = computed(() => detailColumnDefinitions[detailsCard.value?.id] ?? [])
const detailsRows = computed(() => detailsData.value?.rows ?? [])

const detailsTotalRow = computed(() => {
    if (!detailsData.value || !detailsColumns.value.some((column) => column.totalKey)) return null
    const totals = detailsData.value.totals ?? detailsData.value
    const row = {}
    let hasTotal = false

    detailsColumns.value.forEach((column, index) => {
        if (column.totalKey) {
            let value = totals[column.totalKey]
            if (value === undefined && detailsCard.value.id === 'purchase' && column.totalKey === 'net_total') {
                value = detailsData.value.total_net_amount
            }
            if (value === undefined && detailsCard.value.id === 'purchase' && column.totalKey === 'vat_amount') {
                value = detailsData.value.total_vat
            }
            if (value === undefined && detailsCard.value.id === 'payable') {
                value = detailsData.value[`total_${column.totalKey}`]
            }
            if (value !== undefined) {
                row[column.key] = money(value)
                hasTotal = true
            }
        } else if (index === 0) {
            row[column.key] = t('Total')
        }
    })

    return hasTotal ? row : null
})

const detailValue = (row, column) => {
    const value = row[column.key]
    return column.type === 'money' ? money(value) : value || '-'
}

const openDetails = async (card) => {
    detailsCard.value = card
    detailsData.value = null
    detailsOpen.value = true
    detailsLoading.value = true

    const params = { card: card.id }
    if (card.periodKey) {
        params.period = card.periodKey
    } else if (['sales', 'purchase'].includes(card.id)) {
        if (filters.from_date) params.from_date = filters.from_date
        if (filters.to_date) params.to_date = filters.to_date
    }

    try {
        const { data } = await axios.get('/api/accounting/reports/dashboard-details', { params })
        if (data.success) detailsData.value = data.data
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to load dashboard details.'))
    } finally {
        detailsLoading.value = false
    }
}

const printDetails = () => {
    printTable({
        title: detailsTitle.value,
        columns: detailsColumns.value.map((column) => ({
            label: t(column.label),
            key: column.key,
            align: column.type === 'money' ? 'right' : 'left',
            ...(column.type === 'money' ? { format: (value) => money(value) } : {}),
        })),
        items: detailsRows.value,
        totalRow: detailsTotalRow.value,
    })
}

const loadSummary = async () => {
    loading.value = true
    try {
        const params = Object.fromEntries(Object.entries(filters).filter(([, value]) => value))
        const { data } = await axios.get('/api/accounting/reports/dashboard', { params })
        if (data.success) summary.value = data.data
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to load dashboard.'))
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    if (can(['account-reports.view'])) loadSummary()
})
</script>

<style scoped>
.dashboard-wrapper {
    margin: 15px 10px 10px;
    padding: 0 !important;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 15px;
}

.card-header p {
    font-size: 18px;
    color: red;
    font-weight: 600;
}

.card-header-custom {
    background: #fff;
    border-radius: 4px 4px 0 0;
}

.card-body {
    padding: 10px 15px;
}

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
    border-radius: 8px;
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
}

.dashboard-clickable {
    cursor: pointer;
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}

.dashboard-clickable:hover,
.dashboard-clickable:focus-visible {
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.16);
    transform: translateY(-2px);
    outline: none;
}
</style>
