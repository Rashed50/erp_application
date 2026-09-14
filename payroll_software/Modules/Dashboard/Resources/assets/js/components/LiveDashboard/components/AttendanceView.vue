<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-2 sm:p-4 overflow-auto">

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center h-40">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                <span class="ml-3 text-gray-600">Loading attendance data...</span>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="flex flex-col justify-center items-center h-40 text-red-600 gap-2">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">Failed to load attendance data.</span>
                <button
                    class="text-xs underline text-purple-600 hover:text-purple-800"
                    @click="retry"
                >
                    Retry
                </button>
            </div>

            <!-- Desktop Table View -->
            <div v-else class="hidden sm:block overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-[#2878B9] text-white">
                            <th rowspan="2" class="border p-2">S.N</th>
                            <th rowspan="2" class="border p-2">Project Name</th>
                            <th colspan="8" class="border p-2 bg-emerald-700">Day Shift (Today)</th>
                            <th colspan="8" class="border p-2 bg-sky-700">Night Shift (Yesterday)</th>
                            <th rowspan="2" class="border p-2">%</th>
                        </tr>
                        <tr class="bg-[#C5AB57] text-white">
                            <th class="border p-2">Basic</th>
                            <th class="border p-2">Hourly</th>
                            <th class="border p-2 text-green-200">Total Present</th>
                            <th class="border p-2">Workforce</th>
                            <th class="border p-2 text-blue-200">Total Hours</th>
                            <th class="border p-2 text-blue-200">Asloob Hrs/Emp.</th>
                            <th class="border p-2 text-blue-200">Subcon Hrs/Emp.</th>
                            <th class="border p-2 text-red-200">Absent</th>
                            <th class="border p-2">Basic</th>
                            <th class="border p-2">Hourly</th>
                            <th class="border p-2 text-green-200">Total Present</th>
                            <th class="border p-2">Workforce</th>
                            <th class="border p-2 text-blue-200">Total Hours</th>
                            <th class="border p-2 text-blue-200">Asloob Hrs/Emp.</th>
                            <th class="border p-2 text-blue-200">Subcon Hrs/Emp.</th>
                            <th class="border p-2 text-red-200">Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in attendanceData"
                            :key="index"
                            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                        >
                            <td class="border p-2 font-medium">{{ index + 1 }}</td>
                            <td class="border p-2 text-left">{{ item.proj_name }}</td>
                            <!-- Day Shift -->
                            <td class="border p-2">{{ formatVal(item.day.total_basic_emp) }}</td>
                            <td class="border p-2">{{ formatVal(item.day.total_hourly_emp) }}</td>
                            <td class="border p-2 font-semibold text-green-600">
                                {{ formatVal(item.day.total_basic_emp + item.day.total_hourly_emp) }}
                            </td>
                            <td class="border p-2">{{ formatVal(item.day.workforce) }}</td>
                            <td class="border p-2 text-blue-600 font-semibold">{{ formatVal(item.day.total_hours) }}</td>
                            <td class="border p-2 text-blue-600 font-semibold">
                                {{ formatVal(item.day.asloob.total_hours) }} / {{ formatVal(item.day.asloob.employees) }}
                            </td>
                            <td class="border p-2 text-blue-600 font-semibold">
                                {{ formatVal(item.day.subcon.total_hours) }} / {{ formatVal(item.day.subcon.employees) }}
                            </td>
                            <td class="border p-2 text-red-600">
                                {{ formatVal(item.day.workforce - (item.day.total_basic_emp + item.day.total_hourly_emp)) }}
                            </td>
                            <!-- Night Shift -->
                            <td class="border p-2">{{ formatVal(item.night.total_basic_emp) }}</td>
                            <td class="border p-2">{{ formatVal(item.night.total_hourly_emp) }}</td>
                            <td class="border p-2 font-semibold text-green-600">
                                {{ formatVal(item.night.total_basic_emp + item.night.total_hourly_emp) }}
                            </td>
                            <td class="border p-2">{{ formatVal(item.night.workforce) }}</td>
                            <td class="border p-2 text-blue-600 font-semibold">{{ formatVal(item.night.total_hours) }}</td>
                            <td class="border p-2 text-blue-600 font-semibold">
                                {{ formatVal(item.night.asloob.total_hours) }} / {{ formatVal(item.night.asloob.employees) }}
                            </td>
                            <td class="border p-2 text-blue-600 font-semibold">
                                {{ formatVal(item.night.subcon.total_hours) }} / {{ formatVal(item.night.subcon.employees) }}
                            </td>
                            <td class="border p-2 text-red-600">
                                {{ formatVal(item.night.workforce - (item.night.total_basic_emp + item.night.total_hourly_emp)) }}
                            </td>
                            <td class="border p-2">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div v-if="!loading && !error" class="block sm:hidden space-y-4">
                <div
                    v-for="(item, index) in attendanceData"
                    :key="index"
                    class="bg-gray-50 border rounded-lg p-4 shadow-sm"
                >
                    <h3 class="font-bold text-lg mb-3">{{ item.proj_name }}</h3>

                    <div class="mb-4">
                        <h4 class="font-semibold text-green-700 mb-2">Day Shift (Today)</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>Basic: <span class="font-medium">{{ formatVal(item.day.total_basic_emp) }}</span></div>
                            <div>Hourly: <span class="font-medium">{{ formatVal(item.day.total_hourly_emp) }}</span></div>
                            <div class="col-span-2">
                                Total Present:
                                <span class="font-semibold text-green-600">
                                    {{ formatVal(item.day.total_basic_emp + item.day.total_hourly_emp) }}
                                </span>
                            </div>
                            <div>Workforce: <span class="font-medium">{{ formatVal(item.day.workforce) }}</span></div>
                            <div>Total Hours: <span class="font-semibold text-blue-600">{{ formatVal(item.day.total_hours) }}</span></div>
                            <div>Asloob Hrs/Emp.: <span class="font-medium text-blue-600">{{ formatVal(item.day.asloob.total_hours) }} / {{ formatVal(item.day.asloob.employees) }}</span></div>
                            <div>Subcon Hrs/Emp.: <span class="font-medium text-blue-600">{{ formatVal(item.day.subcon.total_hours) }} / {{ formatVal(item.day.subcon.employees) }}</span></div>
                            <div class="col-span-2">
                                Absent:
                                <span class="font-medium text-red-600">
                                    {{ formatVal(item.day.workforce - (item.day.total_basic_emp + item.day.total_hourly_emp)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-blue-700 mb-2">Night Shift (Yesterday)</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>Basic: <span class="font-medium">{{ formatVal(item.night.total_basic_emp) }}</span></div>
                            <div>Hourly: <span class="font-medium">{{ formatVal(item.night.total_hourly_emp) }}</span></div>
                            <div class="col-span-2">
                                Total Present:
                                <span class="font-semibold text-green-600">
                                    {{ formatVal(item.night.total_basic_emp + item.night.total_hourly_emp) }}
                                </span>
                            </div>
                            <div>Workforce: <span class="font-medium">{{ formatVal(item.night.workforce) }}</span></div>
                            <div>Total Hours: <span class="font-semibold text-blue-600">{{ formatVal(item.night.total_hours) }}</span></div>
                            <div>Asloob Hrs/Emp.: <span class="font-medium text-blue-600">{{ formatVal(item.night.asloob.total_hours) }} / {{ formatVal(item.night.asloob.employees) }}</span></div>
                            <div>Subcon Hrs/Emp.: <span class="font-medium text-blue-600">{{ formatVal(item.night.subcon.total_hours) }} / {{ formatVal(item.night.subcon.employees) }}</span></div>
                            <div class="col-span-2">
                                Absent:
                                <span class="font-medium text-red-600">
                                    {{ formatVal(item.night.workforce - (item.night.total_basic_emp + item.night.total_hourly_emp)) }}
                                </span>
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
import { useViewData }        from '../composables/useViewData.js';
import { fetchAttendanceData } from '../services/attendanceService.js';

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
// API response shape: { success, data: { attendance_datas: [...] }, timestamp }
const attendanceData = computed(() => data.value?.data?.attendance_datas ?? []);

// ── Activation watch ─────────────────────────────────────────────────
// When this view becomes active  → trigger API fetch
// When it becomes inactive       → abort the in-flight request
watch(
    () => props.isActive,
    (active) => {
        if (active) fetchData(fetchAttendanceData);
        else        cancel();
    },
    { immediate: true }
);

// ── Cleanup on destroy ───────────────────────────────────────────────
onUnmounted(cancel);

// ── Helpers ──────────────────────────────────────────────────────────
function formatVal(value) {
    if (value === undefined || value === null || value <= 0) return '-';
    return value;
}

function retry() {
    fetchData(fetchAttendanceData, true); // force = true bypasses cache TTL
}
</script>

<style scoped>
.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}
.overflow-x-auto::-webkit-scrollbar      { height: 6px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #f7fafc; }
.overflow-x-auto::-webkit-scrollbar-thumb { background-color: #cbd5e0; border-radius: 3px; }
</style>
