import axios from 'axios'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'

// Shared "Approve" action for every parent record that has approved_by /
// approved_at on the backend. `baseUrl` is the resource URL (e.g.
// '/api/customers'); `onApproved` refreshes the list after a successful approval.
export function useApproval(baseUrl, onApproved) {
    const approve = async (item, label = 'record') => {
        const result = await Swal.fire({
            title: 'Approve this ' + label + '?',
            text: 'Once approved, the approver is recorded against it.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve',
            cancelButtonText: 'Cancel',
        })

        if (!result.isConfirmed) return

        try {
            const resp = await axios.post(`${baseUrl}/${item.id}/approve`)
            if (resp.data.success) {
                toast.success(resp.data.message)
                onApproved()
            }
        } catch (e) {
            // Already approved (422) or missing the approve permission (403).
            toast.error(e.response?.data?.message || 'Failed to approve.')
        }
    }

    return { approve }
}
