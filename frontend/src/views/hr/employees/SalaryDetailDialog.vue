<template>
    <v-dialog :model-value="modelValue" @update:model-value="(val) => emit('update:modelValue', val)" max-width="720">
        <v-card>
            <v-card-title>{{ revision ? 'Edit Salary Configuration' : 'New Salary Configuration' }}</v-card-title>
            <v-card-text>
                <p class="text-muted small mb-3" v-if="!revision">
                    A salary change is saved as a new revision from its effective date. Months before that date
                    keep using the earlier revision.
                </p>
                <form @submit.prevent="handleSubmit">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Effective Date: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" v-model="form.effective_date" required />
                                <div v-if="errors.effective_date" class="error-msg">{{ errors.effective_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-4" v-for="field in amountFields" :key="field.key">
                            <div class="form-group mb-3">
                                <label>{{ field.label }}:</label>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    v-model.number="form[field.key]" :required="field.key === 'basic_salary'" />
                                <div v-if="errors[field.key]" class="error-msg">{{ errors[field.key] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Absence Deduction Based On:</label>
                                <select class="form-control" v-model="form.deduction_basis">
                                    <option value="basic">Basic salary</option>
                                    <option value="gross">Basic + allowances</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3 mt-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="salary_status"
                                        v-model="form.status" />
                                    <label class="form-check-label" for="salary_status">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Remarks:</label>
                                <input type="text" class="form-control" v-model="form.remarks"
                                    placeholder="e.g. Annual increment" />
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border mb-0">
                        Monthly gross (before overtime and deductions): <strong>{{ money(monthlyGross) }}</strong>
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
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { money } from '../helpers';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    employeeId: { type: [Number, String], required: true },
    // The revision to edit, or null to add a new one (prefilled from `base`).
    revision: { type: Object, default: null },
    base: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const amountFields = [
    { key: 'basic_salary', label: 'Basic Salary' },
    { key: 'house_rent', label: 'House Rent' },
    { key: 'medical_allowance', label: 'Medical Allowance' },
    { key: 'transport_allowance', label: 'Transport Allowance' },
    { key: 'food_allowance', label: 'Food Allowance' },
    { key: 'other_allowance', label: 'Other Allowance' },
    { key: 'overtime_rate', label: 'Overtime Rate (per hour)' },
    { key: 'other_deduction', label: 'Fixed Monthly Deduction' },
]

const blank = () => ({
    effective_date: new Date().toISOString().slice(0, 10),
    basic_salary: 0,
    house_rent: 0,
    medical_allowance: 0,
    transport_allowance: 0,
    food_allowance: 0,
    other_allowance: 0,
    overtime_rate: 0,
    other_deduction: 0,
    deduction_basis: 'basic',
    status: true,
    remarks: '',
})

const form = reactive(blank())
const errors = reactive({})
const isSubmitting = ref(false)

const monthlyGross = computed(() => ['basic_salary', 'house_rent', 'medical_allowance', 'transport_allowance', 'food_allowance', 'other_allowance']
    .reduce((sum, key) => sum + (Number(form[key]) || 0), 0))

// Reset the form each time the dialog opens.
watch(() => props.modelValue, (open) => {
    if (!open) return
    for (const key in errors) delete errors[key]
    const source = props.revision ?? props.base
    Object.assign(form, blank())
    if (source) {
        for (const key of Object.keys(form)) {
            if (source[key] !== undefined && source[key] !== null) form[key] = source[key]
        }
        if (!props.revision) {
            form.effective_date = new Date().toISOString().slice(0, 10)
            form.remarks = ''
        }
    }
})

const handleSubmit = async () => {
    isSubmitting.value = true
    for (const key in errors) delete errors[key]

    try {
        const { data } = props.revision
            ? await axios.put(`/api/hr/salary-details/${props.revision.id}`, { ...form })
            : await axios.post(`/api/hr/employees/${props.employeeId}/salary-details`, { ...form })

        if (data.success) {
            toast.success(data.message)
            emit('saved')
            emit('update:modelValue', false)
        }
    } catch (e) {
        if (e.response?.status === 422) {
            const respErrors = e.response.data.data
            for (const key in respErrors) errors[key] = respErrors[key].join(' ')
        } else {
            toast.error(e.response?.data?.message || 'Failed to save salary configuration.')
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>
