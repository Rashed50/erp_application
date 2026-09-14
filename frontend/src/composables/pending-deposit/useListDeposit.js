import { toast } from 'vue3-toastify'

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

    // print table
    const printTable = () => {
        if (!items.value.length) return

        const win = window.open('', '_blank')

        let html = `
            <html>
            <head>
            <title>Pending Deposit List - Destiny Multi-purpose Co-operative Society Ltd</title>
            <style>
                body { font-family: Arial, sans-serif }
                table { width: 100%; border-collapse: collapse; margin-top: 10px }
                th, td { border: 1px solid #000; padding: 6px; font-size: 12px }
                th { background: #f3f3f3 }
            </style>
            </head>
            <body>
            <h3>Pending Deposit List - Destiny Multi-purpose Co-operative Society Ltd</h3>
            <table>
                <thead>
                <tr>
                    <th>SL/No</th>
                    <th>M.Number</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Trnx ID</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
        `

        items.value.forEach((row, i) => {
            html += `
            <tr>
                <td>${row.DT_RowIndex}</td>
                <td>${row.member_number}</td>
                <td>${row.name}</td>
                <td>${row.mobile}</td>
                <td>${row.amount}</td>
                <td>${row.paymentOption}</td>
                <td>${row.tnx_id ?? ''}</td>
                <td>${row.created_at_formatted}</td>
            </tr>
            `
        })

        html += `
                </tbody>
            </table>

            <script>
                window.onload = function () {
                window.print()
                window.onafterprint = function () {
                    window.close()
                }
                }
            </script>

            </body>
            </html>
        `

        win.document.open()
        win.document.write(html)
        win.document.close()
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
