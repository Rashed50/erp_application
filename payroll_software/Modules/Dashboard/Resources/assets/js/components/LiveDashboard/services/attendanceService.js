import axios from 'axios';

/**
 * Fetch today's attendance data for all projects.
 *
 * @param {AbortSignal} signal - AbortController signal for request cancellation
 * @returns {Promise<{success: boolean, data: Object, timestamp: string}>}
 */
export async function fetchAttendanceData(signal) {
    const response = await axios.get('/live-dashboard/attendance-data', { signal });
    return response.data;
}
