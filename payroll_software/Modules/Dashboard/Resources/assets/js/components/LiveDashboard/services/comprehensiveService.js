import axios from 'axios';

/**
 * Fetch comprehensive dashboard data:
 * subcontractor invoices, employee salary history, and chart data.
 *
 * @param {AbortSignal} signal - AbortController signal for request cancellation
 * @returns {Promise<{success: boolean, data: Object, timestamp: string}>}
 */
export async function fetchComprehensiveData(signal) {
    const response = await axios.get('/live-dashboard/comprehensive-data', { signal });
    return response.data;
}
