<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[calc(100vh-150px)]">

        <!-- ── Top Left: Subcontractor Section ─── -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white p-2 sm:p-1">
                <h2 class="text-base sm:text-lg font-semibold">🏗️ Subcontractor Invoice & Payment - Previous 3 Months</h2>
            </div>
            <div class="p-2 sm:p-4 overflow-auto h-[calc(100%-60px)]">

                <div v-if="loading" class="flex justify-center items-center h-40">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-3">Loading subcontractor data...</span>
                </div>

                <div v-else-if="error" class="flex flex-col justify-center items-center h-40 text-red-600 gap-2">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">Failed to load data.</span>
                    <button class="text-xs underline text-blue-600 hover:text-blue-800" @click="retry">Retry</button>
                </div>

                <div v-else-if="subcontractorData.length === 0" class="text-center text-gray-500 py-8">
                    No subcontractor data available
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border p-2 text-left font-semibold">Subcontractor</th>
                                <th
                                    v-for="(month, index) in monthHeaders"
                                    :key="index"
                                    class="border p-2 text-center font-semibold"
                                >
                                    {{ month }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(subcontractor, index) in subcontractorData"
                                :key="index"
                                class="hover:bg-gray-50"
                            >
                                <td class="border p-2 font-medium text-xs">{{ subcontractor.subcon_name }}</td>
                                <td
                                    v-for="(month, monthIndex) in subcontractor.months"
                                    :key="monthIndex"
                                    class="border p-1 text-right whitespace-nowrap"
                                >
                                    {{ formatCurrency(month.payment_amount) }}
                                    /
                                    {{ formatCurrency(month.invoice_amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Top Right: Employee Salary Section ─── -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-green-600 text-white p-2 sm:p-1">
                <h2 class="text-base sm:text-lg font-semibold">👥 Employee Salary - Previous 3 Months</h2>
            </div>
            <div class="p-2 sm:p-4 overflow-auto h-[calc(100%-60px)]">

                <div v-if="loading" class="flex justify-center items-center h-40">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                    <span class="ml-3">Loading employee data...</span>
                </div>

                <div v-else-if="employeeData.length === 0" class="text-center text-gray-500 py-8">
                    No salary data available
                </div>

                <div v-else>
                    <table class="min-w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border p-3 text-left font-semibold">Month</th>
                                <th class="border p-3 text-right font-semibold">Total Salary</th>
                                <th class="border p-3 text-right font-semibold">Paid</th>
                                <th class="border p-3 text-right font-semibold">Unpaid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in employeeData"
                                :key="index"
                                class="hover:bg-gray-50"
                            >
                                <td class="border p-3 font-medium">{{ item.month_year }}</td>
                                <td class="border p-3 text-right">
                                    {{ formatCurrency(item.total_salary) }}/{{ item.employee_count || 0 }}
                                </td>
                                <td class="border p-3 text-right text-green-600">
                                    {{ formatCurrency(item.paid) }}/{{ item.paid_employee_count || 0 }}
                                </td>
                                <td class="border p-3 text-right text-red-600">
                                    {{ formatCurrency(item.unpaid) }}/{{ item.unpaid_employee_count || 0 }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Bottom Left: Working Hours Bar Chart ─── -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-400 text-white p-2 sm:p-1">
                <h2 class="text-base sm:text-lg font-semibold">📊 Working Hours</h2>
            </div>
            <div class="p-2 sm:p-4 relative" style="height: calc(100% - 40px);">
                <canvas ref="barChartCanvas"></canvas>
            </div>
        </div>

        <!-- ── Bottom Right: Salary Payment Pie Charts ─── -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-400 text-white p-2 sm:p-1">
                <h2 class="text-base sm:text-lg font-semibold">📈 Salary Payment</h2>
            </div>
            <!-- Horizontally scrollable row — each pie gets a fixed min-width so
                 4–5 charts sit side by side without shrinking. -->
            <div
                class="overflow-x-auto"
                style="height: calc(100% - 40px);"
            >
                <div class="flex flex-row items-stretch gap-3 p-2 sm:p-4 h-full"
                     style="min-width: max-content;">
                    <div
                        v-for="(month, index) in pieMonthNames"
                        :key="index"
                        class="flex flex-col items-center flex-shrink-0"
                        style="width: 200px;"
                    >
                        <h5 class="text-center font-medium text-sm mb-1 whitespace-nowrap">{{ month }}</h5>
                        <div class="relative w-full flex-1">
                            <canvas :ref="(el) => setPieRef(el, index)"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import Chart from 'chart.js/auto';
import { useViewData }          from '../composables/useViewData.js';
import { fetchComprehensiveData } from '../services/comprehensiveService.js';

// ── Props ────────────────────────────────────────────────────────────
const props = defineProps({
    isActive: {
        type: Boolean,
        default: false,
    },
});

// ── Own data lifecycle ───────────────────────────────────────────────
const { loading, data, error, fetchData, cancel } = useViewData();

// ── Derived data from the API response ──────────────────────────────
// API response shape: { success, data: { subcontractor_data, employee_data, salary_data, man_hours }, timestamp }
const subcontractorData = computed(() => data.value?.data?.subcontractor_data ?? []);
const employeeData      = computed(() => data.value?.data?.employee_data      ?? []);
const salaryChartData   = computed(() => data.value?.data?.salary_data ?? { total_salary: [], total_paid: [], month_names: [] });
const manHoursData      = computed(() => data.value?.data?.man_hours   ?? { total_hours: [], total_employees: [], month_names: [] });

const monthHeaders = computed(() => {
    const first = subcontractorData.value[0];
    return first?.months?.map((m) => m.month_year) ?? [];
});

const pieMonthNames = computed(() => {
    const names = salaryChartData.value?.month_names ?? [];
    // Always keep exactly 3 slots so the v-for is stable
    return [names[0] ?? '', names[1] ?? '', names[2] ?? ''];
});

// ── Template refs ─────────────────────────────────────────────────────
const barChartCanvas = ref(null);
/** @type {(HTMLCanvasElement|null)[]} */
const pieCanvasRefs = [];

function setPieRef(el, index) {
    pieCanvasRefs[index] = el;
}

// ── Chart instances (non-reactive — no need to trigger re-renders) ───
/** @type {Chart|null} */
let barChartInstance = null;
/** @type {Chart[]} */
let pieChartInstances = [];
let chartsRendered = false;

// ── Chart rendering ──────────────────────────────────────────────────
function renderBarChart() {
    const canvas = barChartCanvas.value;
    if (!canvas) return;

    const labels     = manHoursData.value?.month_names  ?? [];
    const totalHours = manHoursData.value?.total_hours   ?? [];
    const totalEmp   = manHoursData.value?.total_employees ?? [];

    if (barChartInstance) {
        barChartInstance.destroy();
        barChartInstance = null;
    }

    barChartInstance = new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Hours',
                    backgroundColor: '#2ecc71',
                    data: totalHours,
                    yAxisID: 'y',
                },
                {
                    label: 'Emp',
                    backgroundColor: '#3498db',
                    data: totalEmp,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: { display: true, text: 'Total Hours' },
                    grid: { drawOnChartArea: false },
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: 'Total Employees' },
                },

            },
        },
    });
}

function renderPieCharts() {
    const totalSalary = salaryChartData.value?.total_salary ?? [];
    const paidSalary  = salaryChartData.value?.total_paid   ?? [];

    pieChartInstances.forEach((c) => c?.destroy());
    pieChartInstances = [];

    for (let i = 0; i < 3; i++) {
        const canvas = pieCanvasRefs[i];
        if (!canvas) continue;

        const total  = totalSalary[i] ?? 0;
        const paid   = paidSalary[i]  ?? 0;
        const unpaid = total - paid;

        const chart = new Chart(canvas, {
            type: 'pie',
            data: {
                labels: ['Total', 'Paid', 'Unpaid'],
                datasets: [{
                    data: [total, paid, unpaid],
                    backgroundColor: ['#3498db', '#2ecc71', '#e74c3c'],
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label(ctx) {
                                const val = ctx.raw;
                                const pct = total > 0
                                    ? ((val / total) * 100).toFixed(1)
                                    : 0;
                                return `${ctx.label}: ${val.toLocaleString()} (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });
        pieChartInstances.push(chart);
    }
}

function safeRenderCharts() {
    nextTick(() => {
        requestAnimationFrame(() => {
            renderBarChart();
            renderPieCharts();
            chartsRendered = true;
        });
    });
}

function destroyAllCharts() {
    if (barChartInstance) {
        barChartInstance.destroy();
        barChartInstance = null;
    }
    pieChartInstances.forEach((c) => c?.destroy());
    pieChartInstances = [];
    chartsRendered = false;
}

// ── Render charts whenever data arrives or updates ───────────────────
watch(data, (newData) => {
    if (newData) safeRenderCharts();
});

// ── Activation watch ─────────────────────────────────────────────────
watch(
    () => props.isActive,
    (active) => {
        if (active) fetchData(fetchComprehensiveData);
        else        cancel();
    },
    { immediate: true }
);

// ── Initial chart render on mount (if data already cached) ──────────
onMounted(() => {
    if (data.value) safeRenderCharts();
});

// ── Cleanup ──────────────────────────────────────────────────────────
onBeforeUnmount(() => {
    destroyAllCharts();
    cancel();
});

// ── Helpers ──────────────────────────────────────────────────────────
function formatCurrency(amount) {
    return parseFloat(amount ?? 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });
}

function retry() {
    fetchData(fetchComprehensiveData, true);
}
</script>

<style scoped>
.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}
.overflow-x-auto::-webkit-scrollbar       { height: 6px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #f7fafc; }
.overflow-x-auto::-webkit-scrollbar-thumb { background-color: #cbd5e0; border-radius: 3px; }

/* Ensure the pie-chart scroll container shows its scrollbar at the bottom */
.overflow-x-auto:last-child {
    padding-bottom: 6px;
}
</style>
