<template>
    <div class="form-group mb-3">
        <label for="work_order_id">{{ $t('Work Order:') }}</label>
        <select id="work_order_id" class="form-control" v-model="workOrderId" :disabled="disabled">
            <option value="">{{ loading ? $t('Loading...') : $t('No Work Order') }}</option>
            <option v-for="workOrder in workOrders" :key="workOrder.id" :value="workOrder.id">
                {{ workOrder.work_order_no }} - {{ workOrder.work_title }}
                <template v-if="workOrder.customer_name">({{ workOrder.customer_name }})</template>
            </option>
        </select>
        <div v-if="error" class="error-msg">{{ error }}</div>
        <small v-if="hint" class="text-muted d-block">{{ hint }}</small>
    </div>
</template>

<script setup>
import axios from 'axios';

// Every work order, for tagging a purchase or an income/expense entry with
// the work order it was spent on, whatever customer the work order is for.
defineProps({
    disabled: { type: Boolean, default: false },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
})

const workOrderId = defineModel({ type: [Number, String], default: '' })

const workOrders = ref([])
const loading = ref(false)

const loadWorkOrders = async () => {
    loading.value = true
    try {
        const { data } = await axios.get('/api/work-orders/options')
        workOrders.value = data.data ?? []
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

onMounted(loadWorkOrders)
</script>
