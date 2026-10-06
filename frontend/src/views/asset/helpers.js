// Shared behaviour for the Asset setup screens (categories, sub categories, items).

import axios from 'axios'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import { t } from '@/i18n'

// Must match ItemName::TYPES on the backend.
export const ITEM_TYPES = [
    { id: 1, name: 'Asset' },
    { id: 2, name: 'Non-Asset' },
]

// Copies a 422 response's field errors into the form's reactive errors object,
// or toasts the message for any other failure.
export const fillValidationErrors = (err, errors) => {
    if (err.response?.status === 422) {
        const respErrors = err.response.data.data || {}
        for (const key in respErrors) {
            errors[key] = respErrors[key].join(' ')
        }
        return
    }

    toast.error(err.response?.data?.message || t('An error occurred while saving.'))
}

// Asks for confirmation, then flips an active record to inactive (or back).
// Resolves to true when the status was changed.
export const confirmStatusChange = async (url, isActive, name) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t(isActive ? 'Deactivate "{name}"?' : 'Activate "{name}"?', { name }),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t(isActive ? 'Yes, deactivate' : 'Yes, activate'),
        cancelButtonText: t('Cancel'),
    })

    if (!result.isConfirmed) return false

    try {
        const { data } = await axios.patch(url, { status: !isActive })
        if (data.success) {
            toast.success(data.message)
            return true
        }
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.somethingWentWrong'))
    }

    return false
}
