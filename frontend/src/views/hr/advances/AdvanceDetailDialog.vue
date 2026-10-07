<template>
    <v-dialog :model-value="modelValue" @update:model-value="(val) => emit('update:modelValue', val)" max-width="860">
        <v-card v-if="advance">
            <v-card-title>
                {{ $t('Advance Salary') }} - {{ advance.employee_code }} {{ advance.employee_name }}
                <span :class="advanceStatusClass(advance.status)" class="ms-2">{{ $t(advance.status) }}</span>
            </v-card-title>
            <v-card-text>
                <div class="row g-2 mb-3">
                    <div class="col-md-3" v-for="stat in stats" :key="stat.label">
                        <div class="border rounded p-2 h-100">
                            <div class="small text-muted">{{ $t(stat.label) }}</div>
                            <div class="fw-bold" :class="stat.class">{{ stat.value }}</div>
                        </div>
                    </div>
                </div>
                <div class="small text-muted mb-3">
                    {{ $t('Given on') }} {{ advance.advance_date }} ·
                    {{ $t('{count} installments from {month}', { count: advance.installment_count, month: monthLabel(advance.deduction_start_month) }) }}
                    <span v-if="advance.purpose"> · {{ advance.purpose }}</span>
                    <span v-if="advance.remarks"> · {{ advance.remarks }}</span>
                </div>

                <h6 class="section-title">{{ $t('Recovery History') }}</h6>
                <v-table class="custom-bordered" density="compact">
                    <thead>
                        <tr>
                            <th>{{ $t('Date') }}</th>
                            <th>{{ $t('Type') }}</th>
                            <th>{{ $t('Salary Month') }}</th>
                            <th class="text-right">{{ $t('Amount') }}</th>
                            <th>{{ $t('Remarks') }}</th>
                            <th>{{ $t('Entry By') }}</th>
                            <th class="text-center">{{ $t('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!advance.recoveries?.length">
                            <td colspan="7" class="text-center py-3">{{ $t('Nothing recovered yet.') }}</td>
                        </tr>
                        <tr v-for="recovery in advance.recoveries" :key="recovery.id">
                            <td>{{ recovery.recovery_date }}</td>
                            <td>
                                <span :class="recovery.type === 'Cash' ? 'badge bg-success' : 'badge bg-info text-dark'">
                                    {{ $t(recovery.type === 'Cash' ? 'Cash' : 'Salary Deduction') }}
                                </span>
                            </td>
                            <td>{{ monthLabel(recovery.salary_month) }}</td>
                            <td class="text-right">{{ money(recovery.amount) }}</td>
                            <td>{{ recovery.remarks }}</td>
                            <td>{{ recovery.created_by_name }}</td>
                            <td class="text-center">
                                <v-btn v-if="recovery.type === 'Cash' && can(['employee-advances.recover'])" size="small"
                                    color="red-darken-2" variant="text" icon="mdi-delete"
                                    @click="deleteRecovery(recovery)"></v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <template v-if="advance.status === 'Running' && can(['employee-advances.recover'])">
                    <h6 class="section-title">{{ $t('Cash Repayment') }}</h6>
                    <form class="row" @submit.prevent="recordCash">
                        <div class="col-md-3">
                            <label>{{ $t('Amount:') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" :max="advance.outstanding_amount"
                                class="form-control" v-model.number="cash.amount" required />
                            <div v-if="errors.amount" class="error-msg">{{ errors.amount }}</div>
                        </div>
                        <div class="col-md-3">
                            <label>{{ $t('Date:') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" v-model="cash.recovery_date" required />
                            <div v-if="errors.recovery_date" class="error-msg">{{ errors.recovery_date }}</div>
                        </div>
                        <div class="col-md-4">
                            <label>{{ $t('Remarks:') }}</label>
                            <input type="text" class="form-control" v-model="cash.remarks" maxlength="255" />
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <v-btn type="submit" class="text-none text-white w-100" color="green-darken-2" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                {{ $t('Receive') }}
                            </v-btn>
                        </div>
                    </form>
                </template>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat"
                    @click="emit('update:modelValue', false)">
                    {{ $t('Close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import { usePermission } from '@/composables/usePermission';
import { advanceStatusClass, money, monthLabel } from '../helpers';
import { t } from '@/i18n';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    advanceId: { type: [Number, String], default: null },
})

const emit = defineEmits(['update:modelValue', 'changed'])

const { can } = usePermission()

const advance = ref(null)
const errors = reactive({})
const isSubmitting = ref(false)

const blankCash = () => ({ amount: '', recovery_date: new Date().toISOString().slice(0, 10), remarks: '' })
const cash = reactive(blankCash())

const stats = computed(() => [
    { label: 'Advance Amount', value: money(advance.value.amount) },
    { label: 'Monthly Installment', value: money(advance.value.installment_amount) },
    { label: 'Recovered', value: money(advance.value.recovered_amount), class: 'text-success' },
    { label: 'Outstanding', value: money(advance.value.outstanding_amount), class: 'text-danger' },
])

const load = async () => {
    advance.value = null
    try {
        const { data } = await axios.get(`/api/hr/employee-advances/${props.advanceId}`)
        advance.value = data.data
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to load the advance salary.'))
        emit('update:modelValue', false)
    }
}

watch(() => props.modelValue, (open) => {
    if (!open || !props.advanceId) return
    for (const key in errors) delete errors[key]
    Object.assign(cash, blankCash())
    load()
})

const recordCash = async () => {
    for (const key in errors) delete errors[key]
    isSubmitting.value = true

    try {
        const { data } = await axios.post(`/api/hr/employee-advances/${advance.value.id}/recoveries`, { ...cash })
        toast.success(data.message)
        advance.value = data.data
        Object.assign(cash, blankCash())
        emit('changed')
    } catch (e) {
        if (e.response?.status === 422) {
            const respErrors = e.response.data.data
            for (const key in respErrors) errors[key] = respErrors[key].join(' ')
        } else {
            toast.error(e.response?.data?.message || t('Failed to record the repayment.'))
        }
    } finally {
        isSubmitting.value = false
    }
}

const deleteRecovery = async (recovery) => {
    const result = await Swal.fire({
        title: t('Delete this cash repayment?'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, delete'),
    })
    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/hr/advance-recoveries/${recovery.id}`)
        toast.success(data.message)
        advance.value = data.data
        emit('changed')
    } catch (e) {
        toast.error(e.response?.data?.message || t('Failed to delete.'))
    }
}
</script>

<style scoped>
.section-title {
    font-weight: 700;
    color: #0d47a1;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 6px;
    margin: 15px 0 10px;
}
</style>
