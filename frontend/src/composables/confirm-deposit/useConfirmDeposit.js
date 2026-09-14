import { useServerDataTable } from '@/composables/useServerDataTable'

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
        printConfig: (rows) => `
      <html>
      <head>
        <title>Confirm Deposit List</title>
        <style>
          table{width:100%;border-collapse:collapse}
          th,td{border:1px solid #000;padding:6px;font-size:12px}
        </style>
      </head>
      <body onload="window.print();window.onafterprint=()=>window.close()">
        <h3>Confirm Deposit List</h3>
        <table>
          <tr>
            <th>SL</th><th>M.No</th><th>Name</th><th>Mobile</th>
            <th>Amount</th><th>Method</th><th>Trnx</th><th>Date</th>
          </tr>
          ${rows.map(r => `
            <tr>
              <td>${r.DT_RowIndex}</td>
              <td>${r.member_number}</td>
              <td>${r.name}</td>
              <td>${r.mobile}</td>
              <td>${r.amount}</td>
              <td>${r.paymentOption}</td>
              <td>${r.tnx_id ?? ''}</td>
              <td>${r.created_at_formatted}</td>
            </tr>
          `).join('')}
        </table>
      </body>
      </html>
    `,
    })
}
