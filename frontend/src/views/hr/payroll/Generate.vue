<template lang="html">
    <Breadcrumb title="Generate Monthly Salary" buttonText="Salary Sheet" :buttonLink="{ name: 'admin_hr_salary_sheet' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <!-- Step 1: month -->
            <v-card style="padding: 15px; margin-top: 15px;">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label>Salary Month:</label>
                        <input type="month" class="form-control" v-model="month" />
                    </div>
                    <div class="col-md-3">
                        <v-btn class="text-none text-white" color="blue-darken-3" rounded="0" variant="flat"
                            :loading="loading" @click="loadPreview">
                            <i class="fa-solid fa-eye me-1"></i> Preview Salary
                        </v-btn>
                    </div>
                    <div class="col-md-6 text-muted small">
                        Loads active employees on the month's payroll, their salary configuration for the month and
                        their work record, and calculates the salary. Nothing is saved until you confirm.
                    </div>
                </div>
            </v-card>

            <!-- Step 2: preview & review -->
            <v-card v-if="preview" style="padding: 10px; margin-top: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Salary preview - {{ monthLabel(preview.month) }}</h6>
                    <div class="text-muted small">
                        {{ preview.totals.generatable }} of {{ preview.totals.employees }} employees ready ·
                        Net total <strong>{{ money(selectedNetTotal) }}</strong> for {{ selected.length }} selected
                    </div>
                </div>

                <div class="table-responsive">
                    <v-table class="custom-bordered" density="compact">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" class="form-check-input" :checked="allSelected"
                                        @change="toggleAll($event.target.checked)" />
                                </th>
                                <th>Employee</th>
                                <th class="text-right">Days</th>
                                <th class="text-right">Basic</th>
                                <th class="text-right">Allowances</th>
                                <th class="text-right">Overtime</th>
                                <th class="text-right">Bonus / Other</th>
                                <th class="text-right">Gross</th>
                                <th class="text-right">Absence / Unpaid</th>
                                <th class="text-right">Other Ded.</th>
                                <th class="text-right">Net</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!preview.rows.length">
                                <td colspan="12" class="text-center py-3">No employees on payroll for this month.</td>
                            </tr>
                            <tr v-for="row in preview.rows" :key="row.employee_id"
                                :class="{ 'table-warning': !row.can_generate }">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input" :value="row.employee_id"
                                        v-model="selected" :disabled="!row.can_generate" />
                                </td>
                                <td>
                                    <strong>{{ row.employee_code }}</strong> - {{ row.employee_name }}
                                    <div class="small text-muted">{{ row.designation }} · {{ row.department }}</div>
                                </td>
                                <template v-if="row.calculation">
                                    <td class="text-right">
                                        {{ row.calculation.employed_days }}/{{ row.calculation.days_in_month }}
                                        <div class="small text-muted" title="present / working">
                                            {{ row.calculation.present_days }}/{{ row.calculation.working_days }} present
                                        </div>
                                    </td>
                                    <td class="text-right">{{ money(row.calculation.basic_salary) }}</td>
                                    <td class="text-right">{{ money(row.calculation.total_allowance) }}</td>
                                    <td class="text-right">{{ money(row.calculation.overtime_amount) }}</td>
                                    <td class="text-right">{{ money(row.calculation.bonus + row.calculation.other_addition) }}</td>
                                    <td class="text-right"><strong>{{ money(row.calculation.gross_salary) }}</strong></td>
                                    <td class="text-right">
                                        {{ money(row.calculation.absence_deduction + row.calculation.unpaid_leave_deduction) }}
                                    </td>
                                    <td class="text-right">{{ money(row.calculation.other_deduction) }}</td>
                                    <td class="text-right"><strong>{{ money(row.calculation.net_salary) }}</strong></td>
                                </template>
                                <td v-else colspan="9" class="text-muted">-</td>
                                <td>
                                    <span v-if="row.existing_salary" :class="salaryStatusClass(row.existing_salary.status)">
                                        {{ row.existing_salary.status }}
                                    </span>
                                    <span v-else-if="row.can_generate" class="badge bg-light text-dark">New</span>
                                    <div v-for="issue in row.issues" :key="issue" class="small text-danger">{{ issue }}</div>
                                    <div v-if="row.can_generate && row.existing_salary" class="small text-muted">
                                        Will be recalculated
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="preview.rows.length">
                            <tr class="fw-bold">
                                <td colspan="7" class="text-right">Total (all ready employees)</td>
                                <td class="text-right">{{ money(preview.totals.gross_salary) }}</td>
                                <td colspan="2" class="text-right">{{ money(preview.totals.total_deduction) }}</td>
                                <td class="text-right">{{ money(preview.totals.net_salary) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </v-table>
                </div>

                <!-- Step 3: confirm -->
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <v-btn class="text-none" color="grey-lighten-3" rounded="0" variant="flat" @click="loadPreview">
                        Refresh
                    </v-btn>
                    <v-btn class="text-none text-white" color="blue-darken-4" rounded="0" variant="flat"
                        :disabled="!selected.length" :loading="generating" @click="generate">
                        <i class="fa-solid fa-check me-1"></i> Confirm & Generate ({{ selected.length }})
                    </v-btn>
                </div>
            </v-card>

            <v-card v-if="result" style="padding: 15px; margin-top: 15px;">
                <h6>Generation result</h6>
                <p class="mb-2">
                    <span class="badge bg-success me-1">{{ result.created }} created</span>
                    <span class="badge bg-info text-dark me-1">{{ result.regenerated }} recalculated</span>
                    <span class="badge bg-warning text-dark">{{ result.skipped.length }} skipped</span>
                </p>
                <ul class="small mb-2" v-if="result.skipped.length">
                    <li v-for="skip in result.skipped" :key="skip.employee_code">
                        {{ skip.employee_code }} - {{ skip.name }}: {{ skip.reason }}
                    </li>
                </ul>
                <router-link :to="{ name: 'admin_hr_salary_sheet', query: { month } }" class="primary-button">
                    <i class="fa-solid fa-table-list"></i> Review in Salary Sheet
                </router-link>
            </v-card>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { currentMonth, money, monthLabel, salaryStatusClass } from '../helpers';

const month = ref(currentMonth())
const preview = ref(null)
const selected = ref([])
const result = ref(null)
const loading = ref(false)
const generating = ref(false)

const generatableIds = computed(() => (preview.value?.rows ?? []).filter((r) => r.can_generate).map((r) => r.employee_id))
const allSelected = computed(() => generatableIds.value.length > 0 && selected.value.length === generatableIds.value.length)
const selectedNetTotal = computed(() => (preview.value?.rows ?? [])
    .filter((r) => selected.value.includes(r.employee_id))
    .reduce((sum, r) => sum + (r.calculation?.net_salary ?? 0), 0))

const toggleAll = (checked) => {
    selected.value = checked ? [...generatableIds.value] : []
}

const loadPreview = async () => {
    if (!month.value) return
    loading.value = true
    result.value = null
    try {
        const { data } = await axios.get('/api/hr/payroll/preview', { params: { month: month.value } })
        preview.value = data.data
        selected.value = [...generatableIds.value]
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to load the salary preview.')
    } finally {
        loading.value = false
    }
}

const generate = async () => {
    const confirm = await Swal.fire({
        title: `Generate salary for ${monthLabel(month.value)}?`,
        text: `${selected.value.length} employees · net ${money(selectedNetTotal.value)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate',
    })
    if (!confirm.isConfirmed) return

    generating.value = true
    try {
        const { data } = await axios.post('/api/hr/payroll/generate', { month: month.value, employee_ids: selected.value })
        toast.success(data.message)
        await loadPreview()
        result.value = data.data
    } catch (e) {
        const errors = e.response?.data?.data
        toast.error(errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message || 'Failed to generate salary.'))
    } finally {
        generating.value = false
    }
}

watch(month, () => {
    preview.value = null
    result.value = null
})

onMounted(loadPreview)
</script>
