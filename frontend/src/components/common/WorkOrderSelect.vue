<template>
    <div class="form-group mb-3">
        <label for="work_order_id">Work Order:</label>
        <select id="work_order_id" class="form-control" v-model="workOrderId" :disabled="disabled || !customerId">
            <option value="">{{ placeholder }}</option>
            <option v-for="workOrder in workOrders" :key="workOrder.id" :value="workOrder.id">
                {{ workOrder.work_order_no }} - {{ workOrder.work_title }}
                (Paid: {{ money(workOrder.paid_amount) }}, Outstanding: {{ money(workOrder.outstanding_amount) }})
            </option>
        </select>
        <div v-if="error" class="error-msg">{{ error }}</div>
        <small v-if="selected" class="text-muted d-block">
            Total: {{ money(selected.total_amount) }} |
            Paid: {{ money(selected.paid_amount) }} ({{ selected.payments_count }} payment{{ selected.payments_count === 1 ? '' : 's' }}) |
            Outstanding: {{ money(selected.outstanding_amount) }} |
            Status: {{ selected.status }}
        </small>
        <small v-if="hint" class="text-muted d-block">{{ hint }}</small>
    </div>
</template>

<script setup>
import axios from 'axios';

// A customer's work orders, each with its paid and outstanding amounts.
// Reloads whenever the customer changes and clears a selection that does
// not belong to the new customer.
const props = defineProps({
    customerId: { type: [Number, String], default: '' },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
})

const workOrderId = defineModel({ type: [Number, String], default: '' })

const workOrders = ref([])
const loading = ref(false)

const selected = computed(() => workOrders.value.find((w) => w.id === workOrderId.value))

const placeholder = computed(() => {
    if (!props.customerId) return 'Select a customer first'
    if (loading.value) return 'Loading...'
    return workOrders.value.length ? 'No Work Order' : 'No work orders for this customer'
})

const money = (value) => Number(value || 0).toFixed(2)

const loadWorkOrders = async () => {
    workOrders.value = []
    if (!props.customerId) {
        workOrderId.value = ''
        return
    }

    loading.value = true
    try {
        const { data } = await axios.get(`/api/customers/${props.customerId}/work-orders`)
        workOrders.value = data.data ?? []
        if (workOrderId.value && !workOrders.value.some((w) => w.id === workOrderId.value)) {
            workOrderId.value = ''
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

watch(() => props.customerId, loadWorkOrders, { immediate: true })

defineExpose({ reload: loadWorkOrders })
</script>
