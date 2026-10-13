import { useServerDataTable } from '@/composables/useServerDataTable'
import { t } from '@/i18n'

export function useReportDeposit() {
    const today = new Date().toISOString().substr(0, 10)

    const headers = [
        { title: 'ক্রম', key: 'DT_RowIndex' },
        { title: 'তারিখ', key: 'created_at' },
        { title: 'নাম', key: 'name' },
        { title: 'সদস্য নাম্বার', key: 'member_number' },
        { title: 'মোবাইল নাম্বার', key: 'mobile' },
        { title: 'মাধ্যম', key: 'payment_option' },
        { title: 'টাকা', key: 'amount' },
    ]

    const defaultFilters = {
        depoFilter: '',
        paymentMethodFilter: '',
        start_date: '',
        end_date: '',
    }

    const items = ref([])
    const total = ref(0)
    const loading = ref(false)
    const perPage = ref(25)
    const page = ref(1)
    const sortBy = ref('created_at')
    const sortDesc = ref(false)

    return useServerDataTable({
        endpoint: `/apis/deposit/reports`,
        headers,
        defaultFilters,
        mapRow: (item, index, meta) => ({
            ...item,
            DT_RowIndex:
                index + 1 + (meta.page - 1) * meta.perPage,
            name: item.name || 'N/A',
            mobile: item.mobile || 'N/A',
        }),
        printConfig: () => ({
            title: t('Deposit Report'),
            columns: [
                { key: 'DT_RowIndex', label: 'ক্রম' },
                { key: 'created_at', label: 'তারিখ' },
                { key: 'name', label: 'নাম' },
                { key: 'member_number', label: 'সদস্য নাম্বার' },
                { key: 'mobile', label: 'মোবাইল নাম্বার' },
                { key: 'payment_option', label: 'মাধ্যম' },
                { key: 'amount', label: 'টাকা', align: 'right' },
            ],
        }),
    })
}
