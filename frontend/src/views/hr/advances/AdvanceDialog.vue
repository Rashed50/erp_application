<template>
    <v-dialog :model-value="modelValue" @update:model-value="(val) => emit('update:modelValue', val)" max-width="720">
        <v-card>
            <v-card-title>{{ $t(advance ? 'Edit Advance Salary' : 'New Advance Salary') }}</v-card-title>
            <v-card-text>
                <p class="text-muted small mb-3">
                    {{ $t('The monthly installment is deducted automatically when the salary is generated, from the deduction start month until the advance is fully recovered.') }}
                </p>
                <div v-if="hasRecoveries" class="alert alert-info small">
                    {{ $t('Installments have already been recovered: the deduction start month is fixed and the amount cannot be less than {amount}.', { amount: money(advance.recovered_amount) }) }}
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>{{ $t('Employee:') }} <span class="text-danger">*</span></label>
                                <SearchSelect v-if="!advance" v-model="form.employee_id" :items="employees"
                                    :item-title="(e) => `${e.employee_code} - ${e.name} (${e.designation || $t('No designation')})`"
                                    :placeholder="$t('Select an employee')" />
                                <input v-else type="text" class="form-control" disabled
                                    :value="`${advance.employee_code} - ${advance.employee_name}`" />
                                <div v-if="errors.employee_id" class="error-msg">{{ errors.employee_id }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Advance Date:') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" v-model="form.advance_date" required />
                                <div v-if="errors.advance_date" class="error-msg">{{ errors.advance_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Advance Amount:') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="1" class="form-control" v-model.number="form.amount"
                                    @input="fillInstallment" required />
                                <div v-if="errors.amount" class="error-msg">{{ errors.amount }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Deduction Start Month:') }} <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" v-model="form.deduction_start_month"
                                    :disabled="hasRecoveries" required />
                                <div v-if="errors.deduction_start_month" class="error-msg">{{ errors.deduction_start_month }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Number of Installments:') }} <span class="text-danger">*</span></label>
                                <input type="number" step="1" min="1" max="120" class="form-control"
                                    v-model.number="form.installment_count" @input="fillInstallment" required />
                                <div v-if="errors.installment_count" class="error-msg">{{ errors.installment_count }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Monthly Installment:') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control"
                                    v-model.number="form.installment_amount" required />
                                <div v-if="errors.installment_amount" class="error-msg">{{ errors.installment_amount }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>{{ $t('Purpose:') }}</label>
                                <input type="text" class="form-control" v-model="form.purpose" maxlength="150"
                                    :placeholder="$t('e.g. Medical')" />
                                <div v-if="errors.purpose" class="error-msg">{{ errors.purpose }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>{{ $t('Remarks:') }}</label>
                                <input type="text" class="form-control" v-model="form.remarks" />
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border mb-0">
                        {{ $t('Recovered in about {months} months, ending {month}.', { months: schedule.months, month: monthLabel(schedule.lastMonth) }) }}
                    </div>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="emit('update:modelValue', false)">
                    {{ $t('Cancel') }}
                </v-btn>
                <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                    :disabled="isSubmitting" :loading="isSubmitting" @click="handleSubmit">
                    {{ $t('Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import SearchSelect from '@/components/common/SearchSelect.vue';
import { currentMonth, money, monthLabel } from '../helpers';
import { t } from '@/i18n';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    // The advance to edit, or null to add a new one.
    advance: { type: Object, default: null },
    employees: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const blank = () => ({
    employee_id: '',
    advance_date: new Date().toISOString().slice(0, 10),
    amount: '',
    installment_count: 1,
    installment_amount: '',
    deduction_start_month: currentMonth(),
    purpose: '',
    remarks: '',
})

const form = reactive(blank())
const errors = reactive({})
const isSubmitting = ref(false)

const hasRecoveries = computed(() => Number(props.advance?.recovered_amount || 0) > 0)

// Same default as the API: amount / installments.
const fillInstallment = () => {
    const amount = Number(form.amount) || 0
    const count = Number(form.installment_count) || 0
    if (amount > 0 && count > 0) form.installment_amount = Math.round((amount / count) * 100) / 100
}

const schedule = computed(() => {
    const outstanding = (Number(form.amount) || 0) - Number(props.advance?.recovered_amount || 0)
    const installment = Number(form.installment_amount) || 0
    const months = installment > 0 && outstanding > 0 ? Math.ceil(outstanding / installment) : 0
    if (!form.deduction_start_month || !months) return { months: 0, lastMonth: form.deduction_start_month }

    const [year, month] = form.deduction_start_month.split('-').map(Number)
    const last = new Date(year, month - 1 + months - 1, 1)
    const lastMonth = `${last.getFullYear()}-${String(last.getMonth() + 1).padStart(2, '0')}`
    return { months, lastMonth }
})

// Reset the form each time the dialog opens.
watch(() => props.modelValue, (open) => {
    if (!open) return
    for (const key in errors) delete errors[key]
    Object.assign(form, blank())
    if (props.advance) {
        for (const key of Object.keys(form)) {
            if (props.advance[key] !== undefined && props.advance[key] !== null) form[key] = props.advance[key]
        }
    }
})

const handleSubmit = async () => {
    isSubmitting.value = true
    for (const key in errors) delete errors[key]

    try {
        const { data } = props.advance
            ? await axios.put(`/api/hr/employee-advances/${props.advance.id}`, { ...form })
            : await axios.post('/api/hr/employee-advances', { ...form })

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
            toast.error(e.response?.data?.message || t('Failed to save the advance salary.'))
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>
