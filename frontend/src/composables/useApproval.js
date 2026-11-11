import axios from 'axios'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import { t } from '@/i18n'

// Shared "Approve" action for every parent record that has approved_by /
// approved_at on the backend. `baseUrl` is the resource URL (e.g.
// '/api/customers'); `onApproved` refreshes the list after a successful approval.
export function useApproval(baseUrl, onApproved) {
    const approve = async (item, label = 'record') => {
        const result = await Swal.fire({
            title: t('approval.title', { label: t(label) }),
            text: t('approval.text'),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: t('approval.confirm'),
            cancelButtonText: t('common.cancel'),
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
            toast.error(e.response?.data?.message || t('approval.failed'))
        }
    }

    return { approve }
}
