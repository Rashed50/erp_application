// Shared behaviour for the Asset screens (setup, procurement, stock, custody, disposal).

import axios from 'axios'
import { reactive } from 'vue'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import { t } from '@/i18n'

// Must match ItemName::TYPES on the backend.
export const ITEM_TYPES = [
    { id: 1, name: 'Asset' },
    { id: 2, name: 'Non-Asset' },
]

// Must match ItemName::TRACK_TYPES on the backend.
export const TRACK_TYPES = [
    { id: 'quantity', name: 'By Quantity' },
    { id: 'serial', name: 'By Serial Number' },
]

// Must match InventoryWarehouse::TYPES on the backend.
export const WAREHOUSE_TYPES = [
    { id: 'center', name: 'Center Warehouse' },
    { id: 'zonal', name: 'Zonal Warehouse' },
    { id: 'project_store', name: 'Project Store' },
]

const STATUS_CLASSES = {
    submitted: 'bg-warning text-dark',
    requested: 'bg-warning text-dark',
    draft: 'bg-secondary',
    approved: 'bg-info text-dark',
    partially_purchased: 'bg-primary',
    purchased: 'bg-success',
    partially_received: 'bg-primary',
    received: 'bg-success',
    in_transit: 'bg-primary',
    in_stock: 'bg-success',
    issued: 'bg-primary',
    assigned: 'bg-primary',
    returned: 'bg-success',
    damaged: 'bg-danger',
    disposed: 'bg-dark',
    rejected: 'bg-danger',
    cancelled: 'bg-secondary',
}

// "partially_received" -> "Partially Received", the English key for $t().
export const statusLabel = (status) =>
    String(status ?? '').split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')

export const statusClass = (status) => `badge ${STATUS_CLASSES[status] || 'bg-secondary'}`

// Plain number for quantities, without trailing zeros ("5.000" -> 5).
export const qty = (value) => Number(value ?? 0)

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

// Errors on line items ("items.0.quantity") and workflow errors ("status")
// have no field of their own on the form, so they are shown together.
export const otherErrors = (errors, fields) =>
    Object.entries(errors).filter(([key]) => !fields.includes(key)).map(([, message]) => message)

// Shared dropdown data for the inventory screens (items, stores, suppliers,
// employees, asset conditions), loaded once per screen.
export const useInventoryLookups = () => {
    const lookups = reactive({ items: [], warehouses: [], suppliers: [], employees: [], conditions: [] })

    const loadLookups = async () => {
        try {
            const { data } = await axios.get('/api/asset/lookups')
            Object.assign(lookups, data.data || {})
        } catch (err) {
            toast.error(err.response?.data?.message || t('common.failedToLoad'))
        }
    }

    const itemById = (itemId) => lookups.items.find(item => item.item_id === Number(itemId))

    return { lookups, loadLookups, itemById }
}

// Units of a serial-tracked item held in a store.
export const fetchSerials = async (warehouseId, itemId) => {
    if (!warehouseId || !itemId) return []
    try {
        const { data } = await axios.get('/api/asset/lookups/serials', { params: { warehouse_id: warehouseId, item_id: itemId } })
        return data.data || []
    } catch (err) {
        toast.error(err.response?.data?.message || t('common.failedToLoad'))
        return []
    }
}

// Asks for confirmation, then POSTs a workflow action (approve, dispatch, …).
// Resolves to the response's data on success, or null.
export const confirmWorkflowAction = async (url, text, payload = {}) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: t('common.yes'),
        cancelButtonText: t('Cancel'),
    })

    if (!result.isConfirmed) return null

    try {
        const { data } = await axios.post(url, payload)
        if (data.success) {
            toast.success(data.message)
            return data.data
        }
    } catch (err) {
        const errors = err.response?.data?.data
        const firstError = errors && typeof errors === 'object' ? Object.values(errors).flat()[0] : null
        toast.error(firstError || err.response?.data?.message || t('common.somethingWentWrong'))
    }

    return null
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
