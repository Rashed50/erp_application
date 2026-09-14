<template>
    <div class="min-h-screen bg-gray-100 p-2 sm:p-4">

        <!-- ─── Header: Navigation dots + view title ─── -->
        <div class="text-center mb-2 sm:mb-4">
            <div class="flex flex-col sm:flex-row justify-center items-center gap-2 sm:gap-4">
                <div class="flex items-center gap-3">
                    <!-- Navigation dots — one per view -->
                    <button
                        v-for="(_, index) in VIEWS"
                        :key="index"
                        class="w-4 h-4 sm:w-3 sm:h-3 rounded-full cursor-pointer transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-1"
                        :class="dotClasses[index]"
                        :aria-label="`Switch to ${VIEW_TITLES[index]}`"
                        :aria-current="activeViewIndex === index ? 'true' : 'false'"
                        @click="switchTo(index)"
                    />
                    <span class="text-xs sm:text-sm text-gray-500">Auto-switching every 60s</span>
                </div>
            </div>

            <!-- Active view title -->
            <div class="text-lg sm:text-xl lg:text-2xl font-semibold text-blue-600 mt-2 transition-all duration-300">
                {{ VIEW_TITLES[activeViewIndex] }}
            </div>
        </div>

        <!-- ─── Views ─────────────────────────────────────────────────
             Strategy: lazy-mount on first activation, keep-alive via v-show.
             - v-if: only mount a view after it has been first activated
             - v-show: toggle visibility without unmounting (preserves cache)
             - :is-active: each view drives its own API lifecycle
        ────────────────────────────────────────────────────────────── -->
        <AttendanceView
            v-if="mountedViews.includes(0)"
            v-show="activeViewIndex === 0"
            :is-active="activeViewIndex === 0"
        />

        <SalaryView
            v-if="mountedViews.includes(1)"
            v-show="activeViewIndex === 1"
            :is-active="activeViewIndex === 1"
        />

        <ComprehensiveView
            v-if="mountedViews.includes(2)"
            v-show="activeViewIndex === 2"
            :is-active="activeViewIndex === 2"
        />

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useDashboardSwitch, VIEWS } from './composables/useDashboardSwitch.js';

// Import using defineAsyncComponent
import { defineAsyncComponent } from 'vue';
const AttendanceView = defineAsyncComponent(() => import('./components/AttendanceView.vue'));
const SalaryView = defineAsyncComponent(() => import('./components/SalaryView.vue'));
const ComprehensiveView = defineAsyncComponent(() => import('./components/ComprehensiveView.vue'));

// ── Switching logic (only thing this container manages) ──────────────
const { activeViewIndex, switchTo } = useDashboardSwitch();

// ── View metadata (display only — no API knowledge) ──────────────────
const VIEW_TITLES = [
    '📋 Daily Attendance Summary (1/3)',
    '💰 Daily Salary Summary (2/3)',
    '📊 Report in Graph/Chart (3/3)',
];

const DOT_ACTIVE_CLASSES   = ['bg-purple-500 focus:ring-purple-400', 'bg-orange-500 focus:ring-orange-400', 'bg-blue-500 focus:ring-blue-400'];
const DOT_INACTIVE_CLASS   = 'bg-gray-300 focus:ring-gray-400';

const dotClasses = computed(() =>
    VIEWS.map((_, i) =>
        activeViewIndex.value === i ? DOT_ACTIVE_CLASSES[i] : DOT_INACTIVE_CLASS
    )
);

// ── Lazy mount tracking ───────────────────────────────────────────────
// A view's DOM is only created the first time it becomes active.
// After that, v-show keeps it alive so the view retains its cached data.
const mountedViews = ref([]);

watch(
    activeViewIndex,
    (newIndex) => {
        if (!mountedViews.value.includes(newIndex)) {
            mountedViews.value = [...mountedViews.value, newIndex];
        }
    },
    { immediate: true } // mount view 0 on component creation
);
</script>

<style scoped>
</style>
