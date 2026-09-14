import { ref } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'

export function useServerDataTable({
  endpoint,
  headers = [],
  defaultFilters = {},
  mapRow = (item, index, meta) => item,
  printConfig = null,
}) {

  // table state
  const items = ref([])
  const total = ref(0)
  const loading = ref(false)

  const page = ref(1)
  const perPage = ref(25)
  const sortBy = ref('created_at')
  const sortDesc = ref(false)

  const filters = ref({ ...defaultFilters })
  const selected = ref([])

  // fetch data
  const fetchData = async () => {
    loading.value = true
    try {
      const { data } = await axios.get(endpoint, {
        params: {
          page: page.value,
          perPage: perPage.value,
          sortBy: sortBy.value,
          sortDesc: sortDesc.value,
          ...filters.value,
        },
      })

      items.value = data.data.map((item, index) =>
        mapRow(item, index, {
          page: page.value,
          perPage: perPage.value,
        })
      )

      total.value = data.total
    } catch (e) {
      console.error(e)
      toast.error('Failed to load data')
    } finally {
      loading.value = false
    }
  }

  // table options
  const onOptionsUpdate = (options) => {
    page.value = options.page
    perPage.value = options.itemsPerPage

    if (options.sortBy?.length) {
      sortBy.value = options.sortBy[0].key
      sortDesc.value = options.sortBy[0].order === 'desc'
    }

    fetchData()
  }

  // reload
  const reload = () => {
    filters.value = { ...defaultFilters }
    page.value = 1
    perPage.value = 25
    sortBy.value = 'created_at'
    sortDesc.value = false
    fetchData()
  }

  // print
  const printTable = () => {
    if (!printConfig || !items.value.length) return

    const win = window.open('', '_blank')
    win.document.write(printConfig(items.value))
    win.document.close()
  }

  return {
    headers,
    items,
    total,
    loading,
    page,
    perPage,
    sortBy,
    sortDesc,
    filters,
    selected,
    fetchData,
    reload,
    onOptionsUpdate,
    printTable,
  }
}
