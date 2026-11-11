import { ref, computed } from 'vue'
import { useServerDataTable } from '@/composables/useServerDataTable'
import { t } from '@/i18n'

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
    printConfig: (rows) => ({
      title: t('Garbage Deposit List'),
      columns: [
        { key: 'DT_RowIndex', label: t('SL') },
        { key: 'member_number', label: t('M.No') },
        { key: 'name', label: t('Name') },
        { key: 'mobile', label: t('Mobile') },
        { key: 'amount', label: t('Amount'), align: 'right', format: (value) => Number(value || 0).toFixed(2) },
        { key: 'paymentOption', label: t('Method') },
        { key: 'tnx_id', label: t('Trnx ID') },
        { key: 'created_at_formatted', label: t('Date') },
      ],
      totalRow: {
        name: t('Total Amount:'),
        amount: rows.reduce((sum, r) => sum + (parseFloat(r.amount) || 0), 0).toFixed(2),
      },
    }),
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
