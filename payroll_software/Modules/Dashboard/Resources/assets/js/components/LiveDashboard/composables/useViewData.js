import { ref } from 'vue';

/** How long (ms) fetched data stays fresh before a re-fetch is allowed. */
const CACHE_TTL_MS = 25_000;

/**
 * Reusable composable for per-view data lifecycle management.
 *
 * Each view component calls this once to get its own isolated state:
 *   - loading / data / error / lastFetchedAt
 *   - fetchData(fetchFn, force?) — triggers the API call
 *   - cancel()                  — aborts the in-flight request
 *
 * Design decisions:
 *  - AbortController is recreated on every fetch so a cancelled signal is never reused.
 *  - Cache TTL prevents redundant re-fetches when the user switches back quickly.
 *  - AbortError / CanceledError are intentional and never stored as errors.
 */
export function useViewData() {
    const loading      = ref(false);
    const data         = ref(null);
    const error        = ref(null);
    const lastFetchedAt = ref(null);

    /** @type {AbortController|null} */
    let controller = null;

    /**
     * Execute a fetch function with full lifecycle management.
     *
     * @param {function(AbortSignal): Promise<any>} fetchFn
     * @param {boolean} [force=false] - bypass the cache TTL check
     */
    async function fetchData(fetchFn, force = false) {
        const age = lastFetchedAt.value
            ? Date.now() - lastFetchedAt.value
            : Infinity;

        if (!force && age < CACHE_TTL_MS) {
            return; // Data is still fresh — skip the round-trip
        }

        // Cancel any pending request before starting a new one
        if (controller) {
            controller.abort();
        }
        controller = new AbortController();

        loading.value = true;
        error.value   = null;

        try {
            const result = await fetchFn(controller.signal);
            data.value         = result;
            lastFetchedAt.value = Date.now();
        } catch (err) {
            // AbortError / CanceledError are intentional — treat them as no-ops
            if (err.name !== 'AbortError' && err.name !== 'CanceledError') {
                error.value = err;
            }
        } finally {
            loading.value = false;
        }
    }

    /**
     * Abort the current in-flight request (e.g., view became inactive).
     * Safe to call even if no request is in progress.
     */
    function cancel() {
        if (controller) {
            controller.abort();
            controller = null;
        }
    }

    return { loading, data, error, lastFetchedAt, fetchData, cancel };
}
