<template lang="html">
    <Breadcrumb title="Monthly Work Entry" buttonText="Work History" :buttonLink="{ name: 'admin_hr_works_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <v-card style="padding: 15px; margin: 15px 0px;">
                        <!-- Step 1: employee + month -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Employee: <span class="text-danger">*</span></label>
                                    <select class="form-control" v-model="selection.employee_id">
                                        <option value="">Select an employee</option>
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                            {{ employee.employee_code }} - {{ employee.name }}
                                            ({{ employee.designation || 'No designation' }})
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label>Salary Month: <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" v-model="selection.month" />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                <span v-if="lookingUp" class="text-muted">Loading...</span>
                                <span v-else-if="existingId" class="badge bg-info text-dark">Editing existing record</span>
                                <span v-else-if="selection.employee_id && selection.month" class="badge bg-success">
                                    New record
                                </span>
                            </div>
                        </div>

                        <div v-if="isLocked" class="alert alert-warning">
                            <i class="fa-solid fa-lock me-1"></i>
                            The salary for this month is already approved or paid, so this record can no longer be changed.
                            Record any correction as an adjustment in a later month.
                        </div>
                        <div v-else-if="hasSalary" class="alert alert-info">
                            A salary has already been generated for this month. After saving, generate the salary
                            again to apply the change.
                        </div>

                        <!-- Step 2: figures -->
                        <form v-if="selection.employee_id && selection.month && !lookingUp" @submit.prevent="handleSubmit">
                            <fieldset :disabled="isLocked">
                                <h6 class="section-title">Attendance (days)</h6>
                                <div class="row">
                                    <div class="col-md-4" v-for="field in dayFields" :key="field.key">
                                        <div class="form-group mb-3">
                                            <label>{{ field.label }}:</label>
                                            <input type="number" step="0.5" min="0" max="31" class="form-control"
                                                v-model.number="form[field.key]" required />
                                            <div v-if="errors[field.key]" class="error-msg">{{ errors[field.key] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center">
                                        <div :class="dayCheck.ok ? 'text-success' : 'text-danger'">
                                            <i :class="dayCheck.ok ? 'fa-solid fa-circle-check' : 'fa-solid fa-triangle-exclamation'"></i>
                                            Recorded {{ dayCheck.recorded }} of {{ form.working_days || 0 }} working days
                                        </div>
                                    </div>
                                </div>

                                <h6 class="section-title">Overtime, Bonus & Adjustments</h6>
                                <div class="row">
                                    <div class="col-md-3" v-for="field in amountFields" :key="field.key">
                                        <div class="form-group mb-3">
                                            <label>{{ field.label }}:</label>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                v-model.number="form[field.key]" required />
                                            <div v-if="errors[field.key]" class="error-msg">{{ errors[field.key] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label>Remarks:</label>
                                            <input type="text" class="form-control" v-model="form.remarks" />
                                        </div>
                                    </div>
                                </div>

                                <div v-if="errors.salary_month" class="error-msg mb-2">{{ errors.salary_month }}</div>
                                <div v-if="errors.employee_id" class="error-msg mb-2">{{ errors.employee_id }}</div>

                                <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4" rounded="0"
                                    variant="flat" :disabled="isSubmitting || !dayCheck.ok" :loading="isSubmitting">
                                    {{ existingId ? 'Update Work Record' : 'Save Work Record' }}
                                </v-btn>
                                <v-btn v-if="existingId && can(['employee-works.delete'])" class="text-none" color="red-darken-2"
                                    rounded="0" variant="outlined" @click="handleDelete">
                                    Delete
                                </v-btn>
                            </fieldset>
                        </form>
                    </v-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import { useRoute } from 'vue-router';
import { currentMonth } from '../helpers';

const route = useRoute();
const { can } = usePermission()

const dayFields = [
    { key: 'working_days', label: 'Working Days' },
    { key: 'present_days', label: 'Present Days' },
    { key: 'absent_days', label: 'Absent Days' },
    { key: 'paid_leave_days', label: 'Paid Leave' },
    { key: 'unpaid_leave_days', label: 'Unpaid Leave' },
]

const amountFields = [
    { key: 'overtime_hours', label: 'Overtime Hours' },
    { key: 'bonus', label: 'Bonus' },
    { key: 'other_addition', label: 'Other Addition' },
    { key: 'other_deduction', label: 'Other Deduction' },
]

const blank = () => ({
    working_days: 26,
    present_days: 26,
    absent_days: 0,
    paid_leave_days: 0,
    unpaid_leave_days: 0,
    overtime_hours: 0,
    bonus: 0,
    other_addition: 0,
    other_deduction: 0,
    remarks: '',
})

const employees = ref([])
const selection = reactive({
    employee_id: route.query.employee_id ? Number(route.query.employee_id) : '',
    month: route.query.month || currentMonth(),
})

const form = reactive(blank())
const errors = reactive({})
const existingId = ref(null)
const isLocked = ref(false)
const hasSalary = ref(false)
const lookingUp = ref(false)
const isSubmitting = ref(false)

// Present + absent + leave may not exceed working days (same rule as the API).
const dayCheck = computed(() => {
    const recorded = ['present_days', 'absent_days', 'paid_leave_days', 'unpaid_leave_days']
        .reduce((sum, key) => sum + (Number(form[key]) || 0), 0)
    return { recorded, ok: recorded <= (Number(form.working_days) || 0) }
})

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

// Load the record for the selected employee + month, or start a blank one.
const lookup = async () => {
    clearErrors()
    existingId.value = null
    isLocked.value = false
    hasSalary.value = false
    Object.assign(form, blank())

    if (!selection.employee_id || !selection.month) return

    lookingUp.value = true
    try {
        const { data } = await axios.get('/api/hr/employee-works/find', {
            params: { employee_id: selection.employee_id, month: selection.month },
        })
        const work = data.data.work
        isLocked.value = data.data.is_locked
        hasSalary.value = data.data.has_salary
        if (work) {
            existingId.value = work.id
            for (const key of Object.keys(form)) form[key] = work[key] ?? form[key]
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to load the work record.')
    } finally {
        lookingUp.value = false
    }
}

watch(() => [selection.employee_id, selection.month], lookup)

const handleSubmit = async () => {
    clearErrors()
    isSubmitting.value = true

    try {
        const { data } = existingId.value
            ? await axios.put(`/api/hr/employee-works/${existingId.value}`, { ...form })
            : await axios.post('/api/hr/employee-works', {
                ...form,
                employee_id: selection.employee_id,
                salary_month: selection.month,
            })

        if (data.success) {
            toast.success(data.message)
            existingId.value = data.data.id
        }
    } catch (e) {
        if (e.response?.status === 422) {
            const respErrors = e.response.data.data
            for (const key in respErrors) errors[key] = respErrors[key].join(' ')
        } else {
            toast.error(e.response?.data?.message || 'Failed to save the work record.')
        }
    } finally {
        isSubmitting.value = false
    }
}

const handleDelete = async () => {
    const result = await Swal.fire({
        title: 'Delete this work record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
    })
    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/hr/employee-works/${existingId.value}`)
        toast.success(data.message)
        lookup()
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete.')
    }
}

onMounted(async () => {
    try {
        // Anyone who may be on some payroll: active staff plus those who left recently.
        const { data } = await axios.get('/api/hr/employees', { params: { per_page: 1000 } })
        employees.value = data.data?.employees ?? []
    } catch (e) {
        console.error(e)
    }
    lookup()
})
</script>

<style scoped>
.section-title {
    font-weight: 700;
    color: #0d47a1;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 6px;
    margin: 10px 0 15px;
}
</style>
