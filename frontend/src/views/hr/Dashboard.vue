<template lang="html">
    <Breadcrumb title="HR Dashboard" buttonText="Generate Salary" :buttonLink="{ name: 'admin_hr_payroll_generate' }"
        buttonIcon="fa-solid fa-calculator" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-progress-linear v-if="loading" indeterminate color="primary"></v-progress-linear>

            <template v-else-if="stats">
                <h6 class="text-muted mt-3 mb-2">Employees</h6>
                <div class="row g-3">
                    <div class="col-md-4" v-for="card in employeeCards" :key="card.label">
                        <v-card class="stat-card">
                            <div class="stat-icon" :class="card.color"><i :class="card.icon"></i></div>
                            <div>
                                <div class="stat-label">{{ card.label }}</div>
                                <div class="stat-value">{{ card.value }}</div>
                            </div>
                        </v-card>
                    </div>
                </div>

                <h6 class="text-muted mt-4 mb-2">Payroll - {{ monthLabel(stats.month) }}</h6>
                <div class="row g-3">
                    <div class="col-md-3" v-for="card in payrollCards" :key="card.label">
                        <v-card class="stat-card">
                            <div class="stat-icon" :class="card.color"><i :class="card.icon"></i></div>
                            <div>
                                <div class="stat-label">{{ card.label }}</div>
                                <div class="stat-value">{{ card.value }}</div>
                                <div class="stat-hint" v-if="card.hint">{{ card.hint }}</div>
                            </div>
                        </v-card>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <v-card style="padding: 15px;">
                            <h6 class="mb-3">Current month salary status</h6>
                            <div v-if="!Object.keys(stats.status_counts || {}).length" class="text-muted">
                                No salary generated for this month yet.
                            </div>
                            <div v-for="(count, status) in stats.status_counts" :key="status"
                                class="d-flex justify-content-between border-bottom py-2">
                                <span :class="salaryStatusClass(status)">{{ status }}</span>
                                <strong>{{ count }}</strong>
                            </div>
                        </v-card>
                    </div>
                    <div class="col-md-6">
                        <v-card style="padding: 15px;">
                            <h6 class="mb-3">Quick links</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <router-link v-for="link in quickLinks" :key="link.text" :to="link.to"
                                    class="primary-button" v-show="can(link.permissions)">
                                    <i :class="link.icon"></i> {{ link.text }}
                                </router-link>
                            </div>
                        </v-card>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import { money, monthLabel, salaryStatusClass } from './helpers';

const { can } = usePermission()

const stats = ref(null)
const loading = ref(false)

const employeeCards = computed(() => [
    { label: 'Total Employees', value: stats.value.total_employees, icon: 'fa-solid fa-users', color: 'bg-primary' },
    { label: 'Active Employees', value: stats.value.active_employees, icon: 'fa-solid fa-user-check', color: 'bg-success' },
    { label: 'Inactive / Left', value: stats.value.inactive_employees, icon: 'fa-solid fa-user-slash', color: 'bg-secondary' },
])

const payrollCards = computed(() => [
    { label: 'Current Month Salary', value: money(stats.value.current_month_salary), icon: 'fa-solid fa-money-bill-wave', color: 'bg-info', hint: 'Net of generated salaries' },
    { label: 'Generated Salary', value: stats.value.generated_salaries, icon: 'fa-solid fa-file-invoice-dollar', color: 'bg-primary', hint: `of ${stats.value.payroll_employees} on payroll` },
    { label: 'Pending Salary', value: stats.value.pending_salaries, icon: 'fa-solid fa-hourglass-half', color: 'bg-warning', hint: 'Not generated yet' },
    { label: 'Total Payroll Amount', value: money(stats.value.total_payroll_amount), icon: 'fa-solid fa-sack-dollar', color: 'bg-success', hint: `Net salary in ${stats.value.year}` },
])

const quickLinks = [
    { text: 'Employees', to: { name: 'admin_hr_employees_list' }, icon: 'fa-solid fa-users', permissions: ['employees.view'] },
    { text: 'Monthly Work', to: { name: 'admin_hr_works_entry' }, icon: 'fa-solid fa-calendar-check', permissions: ['employee-works.create', 'employee-works.update'] },
    { text: 'Generate Salary', to: { name: 'admin_hr_payroll_generate' }, icon: 'fa-solid fa-calculator', permissions: ['payroll.generate'] },
    { text: 'Salary Sheet', to: { name: 'admin_hr_salary_sheet' }, icon: 'fa-solid fa-table-list', permissions: ['payroll.view'] },
    { text: 'Reports', to: { name: 'admin_hr_reports' }, icon: 'fa-solid fa-chart-column', permissions: ['hr-reports.view'] },
]

onMounted(async () => {
    loading.value = true
    try {
        const { data } = await axios.get('/api/hr/dashboard')
        if (data.success) stats.value = data.data
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
.stat-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 18px;
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
    font-size: 22px;
    font-weight: 700;
}

.stat-hint {
    font-size: 12px;
    color: #9aa0a6;
}
</style>
