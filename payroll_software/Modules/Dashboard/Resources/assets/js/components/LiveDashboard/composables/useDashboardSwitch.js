import { ref, onMounted, onUnmounted } from 'vue';

/** View identifiers — order determines auto-switch sequence. */
export const VIEWS = ['attendance', 'salary', 'comprehensive'];

/** Auto-switch interval (30 seconds). */
const SWITCH_INTERVAL_MS = 60_000;

/**
 * Debounce delay for manual dot clicks.
 * Prevents duplicate API calls when the user clicks rapidly.
 */
const DEBOUNCE_MS = 300;

/**
 * Composable for dashboard auto-switching and manual navigation.
 *
 * Responsibilities (and ONLY these):
 *  - Maintain activeViewIndex
 *  - Run a single 30s auto-switch interval
 *  - Support debounced manual switching that resets the interval
 *  - Clean up all timers on unmount (memory-safe)
 *
 * This composable knows NOTHING about API calls or data.
 */
export function useDashboardSwitch() {
    const activeViewIndex = ref(0);

    /** @type {number|null} */
    let timer = null;

    /** @type {number|null} */
    let debounceTimeout = null;

    /** Start (or restart) the auto-switch interval — always clears first to prevent duplicates. */
    function startTimer() {
        stopTimer();
        timer = setInterval(() => {
            activeViewIndex.value = (activeViewIndex.value + 1) % VIEWS.length;
        }, SWITCH_INTERVAL_MS);
    }

    /** Clear the active interval. */
    function stopTimer() {
        if (timer !== null) {
            clearInterval(timer);
            timer = null;
        }
    }

    /**
     * Manually switch to a view by index.
     *
     * Debounced: rapid clicks are collapsed into one switch.
     * Timer reset: the 30-second countdown restarts from the moment of the switch.
     *
     * @param {number} index - target view index (0-based)
     */
    function switchTo(index) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            activeViewIndex.value = index;
            startTimer(); // Restart the interval from this point
        }, DEBOUNCE_MS);
    }

    onMounted(startTimer);

    onUnmounted(() => {
        stopTimer();
        clearTimeout(debounceTimeout);
    });

    return { activeViewIndex, views: VIEWS, switchTo };
}
