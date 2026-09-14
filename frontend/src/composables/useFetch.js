import { ref, reactive, watch } from 'vue'
import axios from 'axios'

// The API wraps list payloads as { <entityName>: [...] } (or a plain array
// for unpaginated endpoints) — find the array without hardcoding the entity name.
function unwrapListPayload(payload) {
    if (Array.isArray(payload)) return payload
    if (payload && typeof payload === 'object') {
        const key = Object.keys(payload).find(k => k !== 'meta' && Array.isArray(payload[k]))
        if (key) return payload[key]
    }
    return []
}

export function useFetch(url, initialFilters = {}) {
  // data
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)

  // filters
  const filters = reactive({ ...initialFilters })

  // fetch function
  const fetchData = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(url, { params: { ...filters } })
      items.value = unwrapListPayload(response.data?.data)
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  // reset filters
  const resetFilters = () => {
    Object.keys(filters).forEach(key => (filters[key] = ''))
    fetchData()
  }

  // auto refetch on filter change
  watch(filters, fetchData, { deep: true })

  return {
    items,
    loading,
    error,
    filters,
    fetchData,
    resetFilters,
  }
}
