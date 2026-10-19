<template>
    <div class="form-group mb-3">
        <label>{{ label }}</label>
        <SearchSelect v-model="employeeId" :items="employees" :loading="loading" :error="error"
            :item-title="(e) => `${e.employee_code} - ${e.name}${e.designation ? ` (${e.designation})` : ''}`"
            :placeholder="$t('Select an Employee')" />
    </div>
</template>

<script setup>
import axios from 'axios';
import SearchSelect from '@/components/common/SearchSelect.vue';

// Active employees, for recording who made a purchase or spent/received
// money (separate from the user who enters the record).
defineProps({
    label: { type: String, required: true },
    error: { type: String, default: '' },
})

const employeeId = defineModel({ type: [Number, String], default: '' })

const employees = ref([])
const loading = ref(false)

onMounted(async () => {
    loading.value = true
    try {
        const { data } = await axios.get('/api/hr/employees', { params: { per_page: 1000, status: 'Active' } })
        employees.value = data.data?.employees ?? []
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
})
</script>
