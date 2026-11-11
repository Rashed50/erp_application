import { toast } from 'vue3-toastify'
import { t } from '@/i18n'
import { usePrintable } from '@/composables/usePrintable'

export function useListDeposit() {
    const today = new Date().toISOString().substr(0, 10)

    // filters
    const filters = ref({
        mobile: '',
        trnxId: '',
        payment_method: '',
        start_date: '',
        end_date: '',
    })

    // table headers
    const headers = [
        { title: '', key: 'data-table-select', sortable: false },
        { title: 'S/L', key: 'DT_RowIndex' },
        { title: 'M Number', key: 'member_number' },
        { title: 'Name', key: 'name' },
        { title: 'Mobile', key: 'mobile' },
        { title: 'Method', key: 'paymentOption' },
        { title: 'Amount', key: 'amount' },
        { title: 'Trnx ID', key: 'tnx_id' },
        { title: 'Date', key: 'created_at_formatted' },
        { title: 'Action', key: 'action', sortable: false },
    ]

    // table state
    const items = ref([])
    const total = ref(0)
    const loading = ref(false)
    const perPage = ref(25)
    const page = ref(1)
    const sortBy = ref('created_at')
    const sortDesc = ref(false)

    // date menu
    const startDateMenu = ref(false)
    const endDateMenu = ref(false)

    // selected rows
    const selected = ref([])

    // fetch list
    const fetchData = async () => {
        loading.value = true
        let type = 0;
        try {
            const { data } = await axios.get(`/apis/deposits/${type}`, {
                params: {
                    page: page.value,
                    perPage: perPage.value,
                    sortBy: sortBy.value,
                    sortDesc: sortDesc.value,
                    ...filters.value,
                },
            })

            items.value = data.data.map((item, index) => ({
                ...item,
                DT_RowIndex: index + 1 + (page.value - 1) * perPage.value,
                name: item.name || 'N/A',
                mobile: item.mobile || 'N/A',
            }))

            total.value = data.total
        } catch (err) {
            console.error(err)
        } finally {
            loading.value = false
        }
    }

    // table option change
    const onOptionsUpdate = (options) => {
        page.value = options.page
        perPage.value = options.itemsPerPage

        if (options.sortBy?.length) {
            sortBy.value = options.sortBy[0].key
            sortDesc.value = options.sortBy[0].order === 'desc'
        }

        fetchData()
    }

    // Reload Data Table

    const defaultFilters = {
        mobile: '',
        trnxId: '',
        payment_method: '',
        start_date: '',
        end_date: '',
    }

    const reload = () => {
        // filters reset
        filters.value = { ...defaultFilters }

        // table state reset
        items.value = []
        total.value = 0
        page.value = 1
        perPage.value = 25
        sortBy.value = 'created_at'
        sortDesc.value = false

        loading.value = false
        fetchData()
    }

    const approved = async (id) => {

        try {
            const res = await axios.get(`/apis/pending-deposit-approve/${id}`)

            if (res.data.status == true) {
                toast.success(res.data.message)
                fetchData()
            }
            else {
                toast.error(res.data.message)
                return res
            }
        } catch (error) {
            toast.error(error)
            throw error
        }

    }

    // bulk approve (example)
    const approveSelected = async () => {
        if (!selected.value.length) {
            alert('No rows selected!')
            return
        }

        console.log(selected.value)
    }

    // print table (shared company layout)
    const printable = usePrintable()
    const printTable = () => {
        if (!items.value.length) return

        printable.printTable({
            title: t('Pending Deposit List'),
            items: items.value,
            columns: [
                { key: 'DT_RowIndex', label: t('SL/No') },
                { key: 'member_number', label: t('M.Number') },
                { key: 'name', label: t('Name') },
                { key: 'mobile', label: t('Mobile') },
                { key: 'amount', label: t('Amount'), align: 'right' },
                { key: 'paymentOption', label: t('Payment') },
                { key: 'tnx_id', label: t('Trnx ID') },
                { key: 'created_at_formatted', label: t('Date') },
            ],
        })
    }


    return {
        today,
        filters,
        headers,
        items,
        total,
        loading,
        perPage,
        page,
        sortBy,
        sortDesc,
        startDateMenu,
        endDateMenu,
        selected,
        fetchData,
        onOptionsUpdate,
        approveSelected,
        approved,
        reload,
        printTable,
    }
}
