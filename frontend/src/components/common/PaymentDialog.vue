<template>
    <v-dialog :model-value="modelValue" @update:model-value="(val) => emit('update:modelValue', val)" max-width="500">
        <v-card>
            <v-card-title>{{ title }}</v-card-title>
            <v-card-text>
                <p class="mb-3">
                    Due amount: <strong>{{ Number(dueAmount).toFixed(2) }}</strong>
                </p>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group mb-3">
                        <label>Amount:</label>
                        <input type="number" step="0.01" min="0.01" :max="dueAmount" class="form-control"
                            v-model.number="form.amount" required />
                        <div v-if="errors.amount" class="error-msg">{{ errors.amount }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Date:</label>
                        <input type="date" class="form-control" v-model="form.payment_date" required />
                        <div v-if="errors.payment_date" class="error-msg">{{ errors.payment_date }}</div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes:</label>
                        <input type="text" class="form-control" v-model="form.notes" />
                    </div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="emit('update:modelValue', false)">
                    Cancel
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    Record Payment
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    endpoint: { type: String, required: true },
    dueAmount: { type: Number, default: 0 },
    title: { type: String, default: 'Record Payment' },
})

const emit = defineEmits(['update:modelValue', 'recorded'])

const form = reactive({
    amount: null,
    payment_date: new Date().toISOString().slice(0, 10),
    notes: '',
})
const errors = reactive({})
const isSubmitting = ref(false)

watch(() => props.modelValue, (open) => {
    if (open) {
        form.amount = null
        form.payment_date = new Date().toISOString().slice(0, 10)
        form.notes = ''
        for (const key in errors) delete errors[key]
    }
})

const handleSubmit = async () => {
    isSubmitting.value = true
    for (const key in errors) delete errors[key]

    try {
        const { data } = await axios.post(props.endpoint, form)
        if (data.success) {
            toast.success(data.message)
            emit('recorded', data.data)
            emit('update:modelValue', false)
        }
    } catch (e) {
        if (e.response?.status === 422) {
            const respErrors = e.response.data.data
            for (const key in respErrors) errors[key] = respErrors[key].join(' ')
        } else {
            toast.error(e.response?.data?.message || 'Failed to record payment.')
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>
