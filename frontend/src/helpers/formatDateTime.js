import { intlLocale } from '@/i18n'

// A timestamp from the API (e.g. created_at) as a short local date and time,
// or '-' when there is none.
export function formatDateTime(value) {
    if (!value) return '-'

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '-'

    return date.toLocaleString(intlLocale(), { dateStyle: 'medium', timeStyle: 'short' })
}
