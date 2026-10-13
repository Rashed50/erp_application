import { t } from '@/i18n'
import { usePrintable } from '@/composables/usePrintable'

export function useMainMember() {
    const printable = usePrintable()

    // Prints in the shared company layout (header, watermark, signatures).
    const printTable = (items, options = {}) => {
        if (!items || !items.length) return

        printable.printTable({
            title: options.title ?? t('Retrieved Member List'),
            items: items.map((row) => ({ ...row, member_name: row.member_name ?? row.name })),
            columns: [
                { key: 'index', label: t('S/L') },
                { key: 'member_id', label: t('M-Id') },
                { key: 'din_no', label: t('DIN') },
                { key: 'member_name', label: t('Name') },
                { key: 'fathers_name', label: t('F. Name') },
                { key: 'mothers_name', label: t('M. Name') },
                { key: 'date_of_birth', label: t('DOB') },
                { key: 'nationality', label: t('Nationality') },
                { key: 'date_of_joining', label: t('DOJ') },
                { key: 'no_of_share', label: t('Share') },
                { key: 'amount', label: t('Amount'), align: 'right' },
                { key: 'nominee_name', label: t('Nominee Name') },
                { key: 'relation', label: t('Relation') },
                { key: 'address', label: t('Address') },
            ],
        })
    }

    return { printTable }
}
