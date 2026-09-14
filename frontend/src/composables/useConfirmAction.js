import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import axios from 'axios'

const actionMessages = {
    reject: {
        text: 'রিজেক্ট করলে ফিরিয়ে আনা যাবে না!',
    },
    delete: {
        text: 'ডিলিট করলে ফিরিয়ে আনা যাবে না!',
    },
}

export function useConfirmAction() {

    const confirmAndRequest = async ({
        action = 'default',
        title,
        text,
        confirmText = 'হ্যাঁ',
        cancelText = 'না!',
        url,
        method = 'get',
        data = {},
    }) => {

        const config = actionMessages[action] || {}

        const result = await Swal.fire({
            title: 'Are you sure?',
            text: text || config.text || 'You won’t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
        })

        if (!result.isConfirmed) return false

        try {
            const resp = await axios({ url, method, data })

            if (resp.data?.status === true) {
                toast.success(resp.data?.message)
                return true
            }

            toast.error(resp.data?.message || 'Action failed')
            return false

        } catch (error) {
            toast.error(error.response?.data?.message || 'Something went wrong')
            return false
        }
    }

    return { confirmAndRequest }
}
