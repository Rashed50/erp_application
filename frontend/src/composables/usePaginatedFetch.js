import { ref, reactive, watch } from 'vue'
import axios from 'axios'

// The API wraps list payloads as { <entityName>: [...], meta: {...} } — find
// the array without hardcoding the entity name so this stays generic across endpoints.
function unwrapListPayload(payload) {
    if (Array.isArray(payload)) return payload
    if (payload && typeof payload === 'object') {
        const key = Object.keys(payload).find(k => k !== 'meta' && Array.isArray(payload[k]))
        if (key) return payload[key]
    }
    return []
}

export function usePaginatedFetch(url, initialFilters = {}, options = {}) {
    // data
    const items = ref([])
    const loading = ref(false)
    const error = ref(null)

    const {
        perPage = 15,
        page = 1,
    } = options

    // pagination
    const pagination = reactive({
        page,
        perPage,
        total: 0,
        lastPage: 1,
    })

    // filters
    const filters = reactive({
        ...initialFilters,
    })

    // fetch function
    const fetchData = async () => {
        loading.value = true
        error.value = null

        try {
            const response = await axios.get(url, {
                params: {
                    page: pagination.page,
                    per_page: pagination.perPage === 'All' ? 1000000 : pagination.perPage,
                    ...filters,
                },
            })

            const payload = response.data?.data ?? {}
            const meta = payload.meta ?? {}

            items.value = unwrapListPayload(payload)
            pagination.total = meta.total ?? items.value.length
            pagination.lastPage = meta.last_page ?? 1
            if (pagination.perPage !== 'All') {
                pagination.page = meta.current_page ?? pagination.page
            }
        } catch (err) {
            error.value = err
        } finally {
            loading.value = false
        }
    }

    // pagination helpers
    const changePage = (page) => {
        pagination.page = page
    }

    const changePerPage = (perPage) => {
        pagination.perPage = perPage
        pagination.page = 1
    }

    const resetFilters = () => {
        Object.keys(filters).forEach(key => {
            filters[key] = ''
        })
        // pagination.page = 1
        fetchData();
    }

    // auto refetch on page/perPage change
    watch(
        () => [pagination.page, pagination.perPage],
        fetchData
    )

    return {
        items,
        loading,
        error,

        filters,
        pagination,

        fetchData,
        changePage,
        changePerPage,
        resetFilters,
    }
}
