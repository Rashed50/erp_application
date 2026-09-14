<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-2 sm:p-4 overflow-auto">

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center h-40">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
                <span class="ml-3 text-gray-600">Loading salary data...</span>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="flex flex-col justify-center items-center h-40 text-red-600 gap-2">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">Failed to load salary data.</span>
                <button
                    class="text-xs underline text-orange-600 hover:text-orange-800"
                    @click="retry"
                >
                    Retry
                </button>
            </div>

            <!-- Desktop Table View -->
            <div v-else class="hidden sm:block overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead class="bg-[#2878B9] text-white">
                        <tr>
                            <th class="border p-2">S.N</th>
                            <th class="border p-2 sticky left-0 z-20 bg-[#2878B9]">Project Name</th>
                            <th
                                v-for="day in Math.min(daysInMonth, 20)"
                                :key="day"
                                class="border p-1"
                            >
                                {{ day }}
                            </th>
                            <th class="border p-2 bg-emerald-700">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(project, index) in salaryData.slice(0, 15)"
                            :key="index"
                            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                        >
                            <td class="border p-2 font-medium">{{ index + 1 }}</td>
                            <td
                                class="border p-2 sticky left-0 z-10 font-medium text-left"
                                :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                            >
                                {{ project.proj_name }}
                            </td>
                            <td
                                v-for="day in Math.min(daysInMonth, 20)"
                                :key="day"
                                class="border p-1 text-xs"
                            >
                                {{ project.dailySalary[day] || '-' }}
                            </td>
                            <td class="border p-2 bg-emerald-50 font-bold text-emerald-700">
                                {{ calculateProjectTotal(project) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div v-if="!loading && !error" class="block sm:hidden space-y-4">
                <div
                    v-for="(project, index) in salaryData.slice(0, 15)"
                    :key="index"
                    class="bg-gray-50 border rounded-lg p-4 shadow-sm"
                >
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-lg">{{ project.proj_name }}</h3>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Total</div>
                            <div class="font-bold text-emerald-700">{{ calculateProjectTotal(project) }}</div>
                        </div>
                    </div>
                    <div class="text-sm">
                        <h4 class="font-semibold mb-2">Daily Salary Breakdown:</h4>
                        <div class="grid grid-cols-7 gap-1 text-xs">
                            <div
                                v-for="day in Math.min(daysInMonth, 31)"
                                :key="day"
                                class="text-center p-1 bg-white rounded border"
                            >
                                <div class="font-medium">{{ day }}</div>
                                <div>{{ project.dailySalary[day] || '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { computed, watch, onUnmounted } from 'vue';
import { useViewData }   from '../composables/useViewData.js';
import { fetchSalaryData } from '../services/salaryService.js';

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
// API response shape: { success, data: { salary_datas: [...], days_in_month: N }, timestamp }
const salaryData  = computed(() => data.value?.data?.salary_datas  ?? []);
const daysInMonth = computed(() => data.value?.data?.days_in_month ?? 30);

// ── Activation watch ─────────────────────────────────────────────────
watch(
    () => props.isActive,
    (active) => {
        if (active) fetchData(fetchSalaryData);
        else        cancel();
    },
    { immediate: true }
);

// ── Cleanup on destroy ───────────────────────────────────────────────
onUnmounted(cancel);

// ── Helpers ──────────────────────────────────────────────────────────
function calculateProjectTotal(project) {
    let total = 0;
    for (let day = 1; day <= daysInMonth.value; day++) {
        const value = project.dailySalary[day];
        if (value && value !== '-') {
            total += parseInt(value.replace(/,/g, ''), 10) || 0;
        }
    }
    return total > 0 ? total.toLocaleString() : '-';
}

function retry() {
    fetchData(fetchSalaryData, true);
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
</style>
