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
                            <v-card class="stat-card">
                                <div class="stat-icon" :class="card.color"><i :class="card.icon"></i></div>
                                <div>
                                    <div class="stat-label">{{ $t(card.label) }}</div>
                                    <div class="stat-value">{{ money(card.value) }}</div>
                                </div>
                            </v-card>
                        </div>
                    </div>

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
    </v-card>
</template>

<script setup>
import axios from 'axios'
import { toast } from 'vue3-toastify'
import { t } from '@/i18n'
import { useAuthStore } from '@/stores/auth'
import { usePermission } from '@/composables/usePermission'
import { money } from '@/views/hr/helpers'

const auth = useAuthStore()
const { can } = usePermission()

const summary = ref(null)
const loading = ref(false)
const filters = reactive({ from_date: '', to_date: '' })

const cards = computed(() => [
    { label: 'Total Sales', value: summary.value.total_sales, icon: 'fa-solid fa-cart-shopping', color: 'bg-primary' },
    { label: 'Total Purchase', value: summary.value.total_purchase, icon: 'fa-solid fa-truck', color: 'bg-info' },
    { label: 'Total Receivable', value: summary.value.total_receivable, icon: 'fa-solid fa-hand-holding-dollar', color: 'bg-success' },
    { label: 'Total Payable', value: summary.value.total_payable, icon: 'fa-solid fa-file-invoice-dollar', color: 'bg-danger' },
    { label: 'Bank Balance', value: summary.value.bank_balance, icon: 'fa-solid fa-building-columns', color: 'bg-warning' },
])

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
</style>
