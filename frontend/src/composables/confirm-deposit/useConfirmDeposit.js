import { useServerDataTable } from '@/composables/useServerDataTable'
import { t } from '@/i18n'

export function useConfirmDeposit() {
    const today = new Date().toISOString().substr(0, 10)

    const headers = [
        { title: 'S/L', key: 'DT_RowIndex' },
        { title: 'M Number', key: 'member_number' },
        { title: 'Name', key: 'name' },
        { title: 'Mobile', key: 'mobile' },
        { title: 'Method', key: 'paymentOption' },
        { title: 'Amount', key: 'amount' },
        { title: 'Trnx ID', key: 'tnx_id' },
        { title: 'Date', key: 'created_at_formatted' },
    ]

    const defaultFilters = {
        mobile: '',
        trnxId: '',
        payment_method: '',
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

    let type = 1;
    return useServerDataTable({
        endpoint: `/apis/deposits/${type}`,
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
            title: t('Confirm Deposit List'),
            columns: [
                { key: 'DT_RowIndex', label: t('SL') },
                { key: 'member_number', label: t('M.No') },
                { key: 'name', label: t('Name') },
                { key: 'mobile', label: t('Mobile') },
                { key: 'amount', label: t('Amount'), align: 'right' },
                { key: 'paymentOption', label: t('Method') },
                { key: 'tnx_id', label: t('Trnx ID') },
                { key: 'created_at_formatted', label: t('Date') },
            ],
        }),
    })
}
