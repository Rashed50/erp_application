import axios from 'axios';

/**
 * Fetch current month's daily salary data for all projects.
 *
 * @param {AbortSignal} signal - AbortController signal for request cancellation
 * @returns {Promise<{success: boolean, data: Object, timestamp: string}>}
 */
export async function fetchSalaryData(signal) {
    const response = await axios.get('/live-dashboard/salary-data', { signal });
    return response.data;
}
