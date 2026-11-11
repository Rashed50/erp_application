import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import axios from 'axios'
import { t } from '@/i18n'

// Translation keys, resolved when the dialog opens so the current locale is used.
const actionMessages = {
    reject: {
        text: 'confirm.rejectText',
    },
    delete: {
        text: 'confirm.deleteText',
    },
}

export function useConfirmAction() {

    const confirmAndRequest = async ({
        action = 'default',
        title,
        text,
        confirmText = t('common.yes'),
        cancelText = t('common.no'),
        url,
        method = 'get',
        data = {},
    }) => {

        const config = actionMessages[action] || {}

        const result = await Swal.fire({
            title: title || t('confirm.title'),
            text: text || t(config.text || 'confirm.cannotRevert'),
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

            toast.error(resp.data?.message || t('common.actionFailed'))
            return false

        } catch (error) {
            toast.error(error.response?.data?.message || t('common.somethingWentWrong'))
            return false
        }
    }

    return { confirmAndRequest }
}
