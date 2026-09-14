import { ref, computed } from 'vue'
import { useServerDataTable } from '@/composables/useServerDataTable'

export function useGarbageDeposit() {
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

  const type = 2

  // useServerDataTable composable
  const table = useServerDataTable({
    endpoint: `/apis/deposits/${type}`,
    headers,
    defaultFilters,
    mapRow: (item, index, meta) => ({
      ...item,
      DT_RowIndex: index + 1 + (meta.page - 1) * meta.perPage,
      name: item.name || 'N/A',
      mobile: item.mobile || 'N/A',
      amount: parseFloat(item.amount) || 0, // ensure number
    }),
    printConfig: (rows) => {
      const totalAmount = rows.reduce((sum, r) => sum + (parseFloat(r.amount) || 0), 0)
      return `
        <html>
        <head>
          <title>Garbage Deposit List</title>
          <style>
            table { width:100%; border-collapse:collapse; }
            th, td { border:1px solid #000; padding:6px; font-size:12px; }
            th { background:#f3f3f3; }
          </style>
        </head>
        <body onload="window.print(); window.onafterprint=()=>window.close()">
          <h3>Garbage Deposit List</h3>
          <table>
            <thead>
              <tr>
                <th>SL</th><th>M.No</th><th>Name</th><th>Mobile</th>
                <th>Amount</th><th>Method</th><th>Trnx</th><th>Date</th>
              </tr>
            </thead>
            <tbody>
              ${rows.map(r => `
                <tr>
                  <td>${r.DT_RowIndex}</td>
                  <td>${r.member_number}</td>
                  <td>${r.name}</td>
                  <td>${r.mobile}</td>
                  <td>${r.amount.toFixed(2)}</td>
                  <td>${r.paymentOption}</td>
                  <td>${r.tnx_id ?? ''}</td>
                  <td>${r.created_at_formatted}</td>
                </tr>
              `).join('')}
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" style="text-align:right"><strong>Total Amount:</strong></td>
                <td colspan="4">${totalAmount.toFixed(2)}</td>
              </tr>
            </tfoot>
          </table>
        </body>
        </html>
      `
    },
  })

  // reactive totalAmount for UI
  const totalAmount = computed(() =>
    table.items.value.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0)
  )

  return {
    ...table,
    today,
    totalAmount,
  }
}
