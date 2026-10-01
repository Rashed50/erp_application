<template lang="html">
    <Breadcrumb title="Account Reports" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-card style="padding: 15px; margin-top: 15px;">
                <div class="row align-items-end g-2">
                    <div class="col-md-3">
                        <label>{{ $t('Report:') }}</label>
                        <select class="form-select" v-model="reportKey">
                            <option v-for="(item, key) in reports" :key="key" :value="key">{{ $t(item.title) }}</option>
                        </select>
                    </div>
                    <div class="col-md-2" v-if="has('reportType')">
                        <label>{{ $t('Type:') }}</label>
                        <select class="form-select" v-model="filters.report_type">
                            <option value="as_of_date">{{ $t('As of date') }}</option>
                            <option value="as_of_period">{{ $t('For the period') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2" v-if="has('fromDate')">
                        <label>{{ $t('From Date:') }}</label>
                        <input type="date" class="form-control" v-model="filters.from_date" />
                    </div>
                    <div class="col-md-2" v-if="has('toDate')">
                        <label>{{ $t(has('fromDate') ? 'To Date:' : 'As of Date:') }}</label>
                        <input type="date" class="form-control" v-model="filters.to_date" />
                    </div>
                    <div class="col-md-3" v-if="has('account')">
                        <label>{{ $t('Account:') }}</label>
                        <select class="form-select" v-model="filters.account_id">
                            <option value="">{{ $t('Select an account') }}</option>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                                {{ account.account_number }} - {{ account.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4" v-if="has('accounts')">
                        <label>{{ $t(report.accountsLabel || 'Accounts:') }}</label>
                        <v-autocomplete v-model="filters.account_ids" :items="accounts" :item-title="accountTitle"
                            item-value="id" multiple chips closable-chips density="compact" variant="outlined"
                            hide-details :placeholder="$t('All Accounts')" />
                    </div>
                    <div class="col-md-4" v-if="has('suppliers')">
                        <label>{{ $t('Suppliers:') }}</label>
                        <v-autocomplete v-model="filters.supplier_ids" :items="suppliers" item-title="name"
                            item-value="id" multiple chips closable-chips density="compact" variant="outlined"
                            hide-details :placeholder="$t('All Suppliers')" />
                    </div>
                    <div class="col-md-4" v-if="has('customers')">
                        <label>{{ $t('Customers:') }}</label>
                        <v-autocomplete v-model="filters.customer_ids" :items="customers" item-title="name"
                            item-value="id" multiple chips closable-chips density="compact" variant="outlined"
                            hide-details :placeholder="$t('All Customers')" />
                    </div>
                    <div class="col-md-3" v-if="has('customer')">
                        <label>{{ $t('Customer:') }}</label>
                        <select class="form-select" v-model="filters.customer_id">
                            <option value="">{{ $t('All Customers') }}</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-auto d-flex gap-2">
                        <v-btn class="text-none text-white" color="blue-darken-3" rounded="0" variant="flat"
                            :loading="loading" @click="run">
                            {{ $t('Show') }}
                        </v-btn>
                        <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                            :disabled="!rows.length" @click="print">
                            <i class="fa-solid fa-print me-1"></i> {{ $t('Print') }}
                        </v-btn>
                    </div>
                </div>
            </v-card>

            <v-card style="padding: 5px; margin-top: 15px;">
                <div v-if="note" class="px-3 pt-2 fw-bold" :class="note.class">{{ $t(note.text, note.params) }}</div>
                <div class="table-responsive">
                    <v-table class="custom-bordered" density="compact">
                        <thead>
                            <tr>
                                <th v-for="column in columns" :key="column.key"
                                    :class="column.align === 'right' ? 'text-right' : 'text-left'">
                                    {{ $t(column.label) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td :colspan="columns.length" class="text-center py-4">
                                    <v-progress-linear indeterminate color="primary"></v-progress-linear>
                                </td>
                            </tr>
                            <tr v-else-if="!rows.length">
                                <td :colspan="columns.length" class="text-center py-4">
                                    {{ message ? $t(message) : $t('No records found.') }}
                                </td>
                            </tr>
                            <tr v-else v-for="(row, index) in rows" :key="index" :class="{ 'fw-bold': row.__emphasis }">
                                <td v-for="column in columns" :key="column.key"
                                    :class="column.align === 'right' ? 'text-right' : 'text-left'">
                                    {{ cell(column, row) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="rows.length && totalRow">
                            <tr class="fw-bold">
                                <td v-for="column in columns" :key="column.key"
                                    :class="column.align === 'right' ? 'text-right' : 'text-left'">
                                    {{ cell(column, totalRow) }}
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
import { useRoute, useRouter } from 'vue-router';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { useFetch } from '@/composables/useFetch';
import { usePrintable } from '@/composables/usePrintable';
import { money } from '@/views/hr/helpers';
import { t } from '@/i18n';

const { printTable } = usePrintable()
const route = useRoute()
const router = useRouter()

const ALL = 100000
const BASE = '/api/accounting/reports'

// Cells hold English phrases (row labels, types) that are translated on display.
const text = (value) => (value ? t(value) : '')
const amount = (value) => (value === '' || value === null || value === undefined ? '' : money(value))
const drCr = (balance, side) => (balance === '' || balance === undefined ? '' : `${money(balance)} ${side ?? ''}`.trim())

const column = (key, label, extra = {}) => ({ key, label, ...extra })
const moneyColumn = (key, label) => column(key, label, { align: 'right', format: amount })

// A labelled summary line (opening/closing balances, P&L and balance sheet figures).
const labelRow = (labelKey, label, values = {}) => ({ [labelKey]: label, __label: true, __emphasis: true, ...values })

// Each report: its endpoint, filters, columns and how to read rows, totals and notes from the response.
const reports = {
    general_ledger: {
        title: 'General Ledger Report',
        endpoint: `${BASE}/general-ledger`,
        filters: ['account', 'fromDate', 'toDate'],
        required: ['account_id', 'from_date', 'to_date'],
        columns: () => [
            column('date', 'Date'),
            column('tr_no', 'Voucher No'),
            column('general_particular', 'Particulars'),
            column('particular', 'Details'),
            moneyColumn('debit', 'Debit'),
            moneyColumn('credit', 'Credit'),
            column('balance', 'Balance', { align: 'right', format: (value, row) => drCr(value, row.balance_type) }),
        ],
        rows: (data) => [
            labelRow('general_particular', 'Opening Balance', { balance: data.opening_balance, balance_type: data.opening_balance_type }),
            ...data.transactions,
        ],
        totals: (data) => labelRow('general_particular', 'Closing Balance', {
            debit: data.total_debit,
            credit: data.total_credit,
            balance: data.closing_balance,
            balance_type: data.closing_balance_type,
        }),
        note: (data) => ({ text: '{number} - {name}', params: { number: data.account.account_number ?? '', name: data.account.name } }),
    },
    trial_balance: {
        title: 'Trial Balance',
        endpoint: `${BASE}/trial-balance`,
        filters: ['reportType', 'fromDate', 'toDate'],
        required: ['from_date', 'to_date'],
        columns: () => filters.report_type === 'as_of_period'
            ? [
                column('account_no', 'Account No'),
                column('account_name', 'Account Name'),
                moneyColumn('opening_debit', 'Opening Debit'),
                moneyColumn('opening_credit', 'Opening Credit'),
                moneyColumn('debit', 'Period Debit'),
                moneyColumn('credit', 'Period Credit'),
                moneyColumn('closing_debit', 'Closing Debit'),
                moneyColumn('closing_credit', 'Closing Credit'),
            ]
            : [
                column('account_no', 'Account No'),
                column('account_name', 'Account Name'),
                moneyColumn('debit', 'Debit'),
                moneyColumn('credit', 'Credit'),
            ],
        rows: (data) => data.accounts,
        totals: (data) => labelRow('account_name', 'Total', { debit: data.total_debit, credit: data.total_credit }),
    },
    profit_loss: {
        title: 'Profit & Loss',
        endpoint: `${BASE}/profit-loss`,
        filters: ['fromDate', 'toDate'],
        columns: () => [column('label', 'Particulars', { format: text }), moneyColumn('amount', 'Amount')],
        rows: (data) => [
            { label: 'Total Revenue', amount: data.total_revenue },
            { label: 'Total Expense', amount: data.total_expense },
        ],
        totals: (data) => labelRow('label', data.profit_or_loss > 0 ? 'Net Profit' : data.profit_or_loss < 0 ? 'Net Loss' : 'Break-Even', {
            amount: Math.abs(data.profit_or_loss),
        }),
        note: (data) => data.profit_or_loss > 0
            ? { text: 'Profit: {amount}', params: { amount: money(data.profit_or_loss) }, class: 'text-success' }
            : data.profit_or_loss < 0
                ? { text: 'Loss: {amount}', params: { amount: money(Math.abs(data.profit_or_loss)) }, class: 'text-danger' }
                : { text: 'Break-Even: No profit, no loss.' },
    },
    balance_sheet: {
        title: 'Balance Sheet',
        endpoint: `${BASE}/balance-sheet`,
        filters: ['toDate'],
        columns: () => [column('label', 'Particulars', { format: text }), moneyColumn('amount', 'Amount')],
        rows: (data) => [
            { label: 'Total Assets', amount: data.total_assets },
            { label: 'Total Liabilities', amount: data.total_liabilities },
            { label: "Total Owner's Equity", amount: data.total_equity },
        ],
        totals: (data) => labelRow('label', "Liabilities + Owner's Equity", { amount: data.total_liabilities + data.total_equity }),
        note: (data) => data.is_balanced
            ? { text: 'The balance sheet is balanced.', class: 'text-success' }
            : { text: 'The balance sheet is not balanced.', class: 'text-danger' },
    },
    cash_transactions: {
        title: 'Cash Transaction',
        endpoint: `${BASE}/cash-transactions`,
        filters: ['accounts', 'fromDate', 'toDate'],
        columns: () => [
            column('date', 'Date'),
            column('tr_no', 'Voucher No'),
            column('ledger_name', 'Ledger Name'),
            column('particular', 'Details'),
            moneyColumn('debit', 'Debit'),
            moneyColumn('credit', 'Credit'),
        ],
        rows: (data) => data.rows,
        totals: (data) => labelRow('ledger_name', 'Total', { debit: data.total_debit, credit: data.total_credit }),
    },
    supplier_statement: {
        title: 'Supplier Report',
        endpoint: `${BASE}/supplier-statement`,
        filters: ['suppliers', 'fromDate', 'toDate'],
        columns: () => partyColumns('Supplier'),
        rows: (data) => [labelRow('party_name', 'Previous Balance', { due: data.previous_balance }), ...data.rows],
        totals: (data) => labelRow('party_name', 'Total', { amount: data.total_amount, paid: data.total_paid, due: data.total_due }),
    },
    supplier_balances: {
        title: 'Supplier Outstanding Balance',
        endpoint: `${BASE}/supplier-balances`,
        filters: [],
        columns: () => [
            column('name', 'Supplier'),
            column('contact_person', 'Contact Person'),
            column('phone', 'Phone'),
            moneyColumn('opening_balance', 'Opening Balance'),
            moneyColumn('current_balance', 'Current Balance'),
        ],
        rows: (data) => data.rows,
        totals: (data) => labelRow('name', 'Total', {
            opening_balance: data.total_opening_balance,
            current_balance: data.total_current_balance,
        }),
    },
    customer_statement: {
        title: 'Sales Report (Customer Statement)',
        endpoint: `${BASE}/customer-statement`,
        filters: ['customers', 'fromDate', 'toDate'],
        columns: () => partyColumns('Customer'),
        rows: (data) => [labelRow('party_name', 'Previous Balance', { due: data.previous_balance }), ...data.rows],
        totals: (data) => labelRow('party_name', 'Total', { amount: data.total_amount, paid: data.total_paid, due: data.total_due }),
    },
    sales_register: {
        title: 'Sales Report',
        endpoint: `${BASE}/sales`,
        filters: ['customer', 'fromDate', 'toDate'],
        columns: () => [
            column('issue_date', 'Issue Date'),
            column('invoice_number', 'Invoice No'),
            column('customer_name', 'Customer'),
            column('due_date', 'Due Date'),
            moneyColumn('total_amount', 'Total Amount'),
            moneyColumn('discount_amount', 'Discount'),
            moneyColumn('vat_amount', 'VAT'),
            moneyColumn('net_total', 'Net Total'),
            moneyColumn('paid_amount', 'Paid Amount'),
            moneyColumn('due_amount', 'Due Amount'),
        ],
        rows: (data) => data.rows,
        totals: (data) => labelRow('customer_name', 'Total', data.totals),
    },
    expense_details: {
        title: 'Expense Details',
        endpoint: `${BASE}/expense-details`,
        filters: ['accounts', 'fromDate', 'toDate'],
        accountsLabel: 'Credit Accounts:',
        columns: () => [
            column('purchase_date', 'Purchase Date'),
            column('invoice_number', 'Invoice No'),
            column('supplier_name', 'Supplier'),
            column('ledger_name', 'Credit Account'),
            column('debit_account_name', 'Debit Account'),
            column('purchase_type', 'Type', { format: text }),
            column('notes', 'Notes'),
            moneyColumn('vat_amount', 'VAT'),
            moneyColumn('net_total', 'Net Total'),
        ],
        rows: (data) => data.rows,
        totals: (data) => labelRow('supplier_name', 'Total', { vat_amount: data.total_vat, net_total: data.total_net_amount }),
    },
    sales_purchase_summary: {
        title: 'Sales & Purchase Summary',
        endpoint: `${BASE}/sales-purchase-summary`,
        filters: ['fromDate', 'toDate'],
        required: ['from_date', 'to_date'],
        columns: () => [
            column('date', 'Date'),
            column('type', 'Type', { format: text }),
            column('reference', 'Invoice No'),
            column('description', 'Description'),
            moneyColumn('debit', 'Purchase'),
            moneyColumn('credit', 'Sale'),
            moneyColumn('balance', 'Balance'),
        ],
        rows: (data) => data.rows,
        totals: (data) => labelRow('description', 'Total', { debit: data.total_purchase, credit: data.total_sales, balance: data.balance }),
    },
}

function partyColumns(partyLabel) {
    return [
        column('date', 'Date'),
        column('party_name', partyLabel),
        column('invoice_no', 'Invoice No'),
        column('transaction_type', 'Type', { format: text }),
        moneyColumn('amount', 'Amount'),
        moneyColumn('paid', 'Paid'),
        moneyColumn('due', 'Due'),
    ]
}

const firstOfMonth = () => {
    const now = new Date()
    return new Date(now.getFullYear(), now.getMonth(), 1, 12).toISOString().slice(0, 10)
}
const today = () => new Date().toISOString().slice(0, 10)

const reportKey = ref(reports[route.query.report] ? route.query.report : 'general_ledger')
const report = computed(() => reports[reportKey.value])
const columns = computed(() => report.value.columns())
const has = (filter) => report.value.filters.includes(filter)

const filters = reactive({
    report_type: 'as_of_date',
    from_date: firstOfMonth(),
    to_date: today(),
    account_id: '',
    account_ids: [],
    supplier_ids: [],
    customer_ids: [],
    customer_id: '',
})

const data = ref(null)
const loading = ref(false)
const message = ref('')

const rows = computed(() => (data.value ? report.value.rows(data.value) : []))
const totalRow = computed(() => (data.value && report.value.totals ? report.value.totals(data.value) : null))
const note = computed(() => (data.value && report.value.note ? report.value.note(data.value) : null))

const { items: accounts, fetchData: loadAccounts } = useFetch('/api/ledger-accounts', { per_page: ALL })
const { items: suppliers, fetchData: loadSuppliers } = useFetch('/api/suppliers', { per_page: ALL })
const { items: customers, fetchData: loadCustomers } = useFetch('/api/customers', { per_page: ALL })

const accountTitle = (account) => `${account.account_number ?? ''} - ${account.name}`

const cell = (col, row) => {
    const value = row[col.key]
    if (row.__label && col.key === Object.keys(row)[0]) return text(value)
    return col.format ? col.format(value, row) : (value ?? '')
}

// Only the filters the report uses are sent; empty dates and lists are left out.
const params = () => {
    const current = report.value
    const result = {}
    if (current.filters.includes('reportType')) result.report_type = filters.report_type
    if (current.filters.includes('fromDate') && filters.from_date) result.from_date = filters.from_date
    if (current.filters.includes('toDate') && filters.to_date) result.to_date = filters.to_date
    if (current.filters.includes('account') && filters.account_id) result.account_id = filters.account_id
    if (current.filters.includes('customer') && filters.customer_id) result.customer_id = filters.customer_id
    for (const [filter, key] of [['accounts', 'account_ids'], ['suppliers', 'supplier_ids'], ['customers', 'customer_ids']]) {
        if (current.filters.includes(filter) && filters[key].length) result[key] = filters[key]
    }
    return result
}

const run = async () => {
    data.value = null
    message.value = ''
    const missing = (report.value.required || []).some((key) => !filters[key])
    if (missing) {
        message.value = report.value.required.includes('account_id') && !filters.account_id
            ? 'Select an account to see its ledger.'
            : 'Select the date range to see this report.'
        return
    }

    loading.value = true
    try {
        const response = await axios.get(report.value.endpoint, { params: params() })
        data.value = response.data.data
    } catch (e) {
        const errors = e.response?.status === 422 ? e.response.data.data : null
        message.value = errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message || t('Failed to load the report.'))
    } finally {
        loading.value = false
    }
}

const print = () => {
    const subtitle = [
        has('fromDate') && filters.from_date ? filters.from_date : null,
        has('toDate') && filters.to_date ? filters.to_date : null,
    ].filter(Boolean).join(' - ')

    const items = totalRow.value ? [...rows.value, totalRow.value] : rows.value
    printTable({
        title: `${t(report.value.title)}${subtitle ? ` (${subtitle})` : ''}`,
        items,
        columns: columns.value.map((col) => ({
            ...col,
            label: t(col.label),
            format: (value, row) => cell(col, row),
        })),
    })
}

watch(reportKey, (key) => {
    router.replace({ query: { ...route.query, report: key } })
    run()
})

watch(() => route.query.report, (key) => {
    if (reports[key] && key !== reportKey.value) reportKey.value = key
})

onMounted(() => {
    loadAccounts()
    loadSuppliers()
    loadCustomers()
    run()
})
</script>
