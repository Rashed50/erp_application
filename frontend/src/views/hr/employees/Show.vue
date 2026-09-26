<template lang="html">
    <Breadcrumb :title="employee ? `${employee.name} (${employee.employee_code})` : 'Employee Details'"
        buttonText="Employee List" :buttonLink="{ name: 'admin_hr_employees_list' }" buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <v-progress-linear v-if="!employee" indeterminate color="primary"></v-progress-linear>

            <v-card v-else style="padding: 10px; margin-top: 15px;">
                <div class="d-flex justify-content-between align-items-center px-2 pb-2">
                    <div>
                        <span :class="employeeStatusClass(employee.status)">{{ employee.status }}</span>
                        <span class="ms-2 text-muted">{{ employee.designation }} · {{ employee.department }}</span>
                    </div>
                    <router-link v-if="can(['employees.update'])"
                        :to="{ name: 'admin_hr_employee_edit', params: { id: employee.id } }" class="primary-button">
                        <i class="fa-solid fa-pen"></i> Edit Employee
                    </router-link>
                </div>

                <v-tabs v-model="tab" color="blue-darken-4" show-arrows>
                    <v-tab value="basic">Basic Information</v-tab>
                    <v-tab value="employment">Employment Information</v-tab>
                    <v-tab value="salary" v-if="can(['salary-configs.view'])">Salary Information</v-tab>
                    <v-tab value="work" v-if="can(['employee-works.view'])">Work History</v-tab>
                    <v-tab value="salary-history" v-if="can(['payroll.view'])">Salary History</v-tab>
                    <v-tab value="documents">Documents / Files</v-tab>
                </v-tabs>

                <v-window v-model="tab" class="pt-3">
                    <!-- Basic Information -->
                    <v-window-item value="basic">
                        <table class="table table-bordered info-table">
                            <tbody>
                                <tr v-for="row in basicRows" :key="row[0]">
                                    <th>{{ row[0] }}</th>
                                    <td>{{ row[1] || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </v-window-item>

                    <!-- Employment Information -->
                    <v-window-item value="employment">
                        <table class="table table-bordered info-table">
                            <tbody>
                                <tr v-for="row in employmentRows" :key="row[0]">
                                    <th>{{ row[0] }}</th>
                                    <td>{{ row[1] || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </v-window-item>

                    <!-- Salary Information -->
                    <v-window-item value="salary">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div v-if="currentSalary">
                                Current monthly gross: <strong>{{ money(currentSalary.monthly_gross) }}</strong>
                                <span class="text-muted">(effective {{ currentSalary.effective_date }})</span>
                            </div>
                            <div v-else class="text-muted">No salary configuration yet.</div>
                            <v-btn v-if="can(['salary-configs.create'])" class="text-none text-white" color="blue-darken-4"
                                rounded="0" variant="flat" @click="openSalaryDialog(null)">
                                <i class="fa-solid fa-plus me-1"></i> {{ currentSalary ? 'Change Salary' : 'Add Salary' }}
                            </v-btn>
                        </div>
                        <v-table class="custom-bordered">
                            <thead>
                                <tr>
                                    <th>Effective Date</th>
                                    <th class="text-right">Basic</th>
                                    <th class="text-right">House Rent</th>
                                    <th class="text-right">Medical</th>
                                    <th class="text-right">Transport</th>
                                    <th class="text-right">Food</th>
                                    <th class="text-right">Other</th>
                                    <th class="text-right">Gross</th>
                                    <th class="text-right">OT Rate</th>
                                    <th class="text-right">Fixed Deduction</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!employee.salary_details.length">
                                    <td colspan="12" class="text-center py-3">No salary configuration.</td>
                                </tr>
                                <tr v-for="revision in employee.salary_details" :key="revision.id">
                                    <td>
                                        {{ revision.effective_date }}
                                        <div class="small text-muted" v-if="revision.remarks">{{ revision.remarks }}</div>
                                    </td>
                                    <td class="text-right">{{ money(revision.basic_salary) }}</td>
                                    <td class="text-right">{{ money(revision.house_rent) }}</td>
                                    <td class="text-right">{{ money(revision.medical_allowance) }}</td>
                                    <td class="text-right">{{ money(revision.transport_allowance) }}</td>
                                    <td class="text-right">{{ money(revision.food_allowance) }}</td>
                                    <td class="text-right">{{ money(revision.other_allowance) }}</td>
                                    <td class="text-right"><strong>{{ money(revision.monthly_gross) }}</strong></td>
                                    <td class="text-right">{{ money(revision.overtime_rate) }}</td>
                                    <td class="text-right">{{ money(revision.other_deduction) }}</td>
                                    <td class="text-center">
                                        <span :class="revision.status ? 'badge bg-success' : 'badge bg-secondary'">
                                            {{ revision.status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <template v-if="!revision.is_used_by_payroll">
                                            <v-btn v-if="can(['salary-configs.update'])" size="small" variant="text"
                                                color="blue-darken-3" @click="openSalaryDialog(revision)">Edit</v-btn>
                                            <v-btn v-if="can(['salary-configs.delete'])" size="small" variant="text"
                                                color="red-darken-2" @click="deleteRevision(revision)">Delete</v-btn>
                                        </template>
                                        <small v-else class="text-muted" title="Used by generated salaries">
                                            <i class="fa-solid fa-lock"></i> Used in payroll
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-window-item>

                    <!-- Work History -->
                    <v-window-item value="work">
                        <div class="d-flex justify-content-end mb-2" v-if="can(['employee-works.create'])">
                            <router-link class="primary-button"
                                :to="{ name: 'admin_hr_works_entry', query: { employee_id: employee.id } }">
                                <i class="fa-solid fa-calendar-plus"></i> Enter Monthly Work
                            </router-link>
                        </div>
                        <v-table class="custom-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Working</th>
                                    <th class="text-right">Present</th>
                                    <th class="text-right">Absent</th>
                                    <th class="text-right">Paid Leave</th>
                                    <th class="text-right">Unpaid Leave</th>
                                    <th class="text-right">OT Hours</th>
                                    <th class="text-right">Bonus</th>
                                    <th class="text-right">Other +</th>
                                    <th class="text-right">Other -</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!works.length">
                                    <td colspan="11" class="text-center py-3">No work records.</td>
                                </tr>
                                <tr v-for="work in works" :key="work.id">
                                    <td>
                                        <router-link
                                            :to="{ name: 'admin_hr_works_entry', query: { employee_id: employee.id, month: work.salary_month } }">
                                            {{ monthLabel(work.salary_month) }}
                                        </router-link>
                                    </td>
                                    <td class="text-right">{{ work.working_days }}</td>
                                    <td class="text-right">{{ work.present_days }}</td>
                                    <td class="text-right">{{ work.absent_days }}</td>
                                    <td class="text-right">{{ work.paid_leave_days }}</td>
                                    <td class="text-right">{{ work.unpaid_leave_days }}</td>
                                    <td class="text-right">{{ work.overtime_hours }}</td>
                                    <td class="text-right">{{ money(work.bonus) }}</td>
                                    <td class="text-right">{{ money(work.other_addition) }}</td>
                                    <td class="text-right">{{ money(work.other_deduction) }}</td>
                                    <td>{{ work.remarks }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-window-item>

                    <!-- Salary History -->
                    <v-window-item value="salary-history">
                        <v-table class="custom-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Basic</th>
                                    <th class="text-right">Allowances</th>
                                    <th class="text-right">Overtime</th>
                                    <th class="text-right">Bonus</th>
                                    <th class="text-right">Gross</th>
                                    <th class="text-right">Deduction</th>
                                    <th class="text-right">Net</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!salaries.length">
                                    <td colspan="9" class="text-center py-3">No salary generated yet.</td>
                                </tr>
                                <tr v-for="salary in salaries" :key="salary.id"
                                    :class="{ 'text-muted text-decoration-line-through': salary.status === 'Cancelled' }">
                                    <td>{{ monthLabel(salary.salary_month) }}</td>
                                    <td class="text-right">{{ money(salary.basic_salary) }}</td>
                                    <td class="text-right">{{ money(salary.total_allowance) }}</td>
                                    <td class="text-right">{{ money(salary.overtime_amount) }}</td>
                                    <td class="text-right">{{ money(salary.bonus) }}</td>
                                    <td class="text-right">{{ money(salary.gross_salary) }}</td>
                                    <td class="text-right">{{ money(salary.total_deduction) }}</td>
                                    <td class="text-right"><strong>{{ money(salary.net_salary) }}</strong></td>
                                    <td class="text-center">
                                        <span :class="salaryStatusClass(salary.status)"
                                            :title="salary.cancel_reason || ''">{{ salary.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-window-item>

                    <!-- Documents -->
                    <v-window-item value="documents">
                        <form v-if="can(['employees.update'])" class="row align-items-end mb-3"
                            @submit.prevent="uploadDocument">
                            <div class="col-md-3">
                                <label>Document Type:</label>
                                <select class="form-control" v-model="upload.document_type" required>
                                    <option v-for="type in documentTypes" :key="type" :value="type">{{ type }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Title:</label>
                                <input type="text" class="form-control" v-model="upload.title" />
                            </div>
                            <div class="col-md-4">
                                <label>File (PDF, image, Word; max 5 MB):</label>
                                <input type="file" class="form-control" ref="fileInput"
                                    accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                                    @change="upload.file = $event.target.files[0]" required />
                                <div v-if="uploadError" class="error-msg">{{ uploadError }}</div>
                            </div>
                            <div class="col-md-2">
                                <v-btn type="submit" class="text-none text-white w-100" color="blue-darken-4" rounded="0"
                                    variant="flat" :loading="uploading">
                                    Upload
                                </v-btn>
                            </div>
                        </form>
                        <v-table class="custom-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>File</th>
                                    <th class="text-right">Size</th>
                                    <th>Uploaded</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!employee.files.length">
                                    <td colspan="6" class="text-center py-3">No documents uploaded.</td>
                                </tr>
                                <tr v-for="file in employee.files" :key="file.id">
                                    <td>{{ file.document_type }}</td>
                                    <td>{{ file.title }}</td>
                                    <td>{{ file.file_name }}</td>
                                    <td class="text-right">{{ (file.file_size / 1024).toFixed(1) }} KB</td>
                                    <td>{{ file.created_at?.slice(0, 10) }}</td>
                                    <td class="text-center">
                                        <v-btn size="small" variant="text" color="blue-darken-3"
                                            @click="downloadDocument(file)">Download</v-btn>
                                        <v-btn v-if="can(['employees.update'])" size="small" variant="text"
                                            color="red-darken-2" @click="deleteDocument(file)">Delete</v-btn>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-window-item>
                </v-window>
            </v-card>
        </div>
    </div>

    <SalaryDetailDialog v-if="employee" v-model="salaryDialogOpen" :employee-id="employee.id"
        :revision="editingRevision" :base="currentSalary" @saved="loadEmployee" />
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { usePermission } from '@/composables/usePermission';
import { useRoute } from 'vue-router';
import { employeeStatusClass, money, monthLabel, salaryStatusClass } from '../helpers';
import SalaryDetailDialog from './SalaryDetailDialog.vue';

const route = useRoute();
const { can } = usePermission()

const documentTypes = ['NID', 'Passport', 'CV', 'Joining Document', 'Certificate', 'Other']

const tab = ref('basic')
const employee = ref(null)
const works = ref([])
const salaries = ref([])

const salaryDialogOpen = ref(false)
const editingRevision = ref(null)

const fileInput = ref(null)
const upload = reactive({ document_type: 'NID', title: '', file: null })
const uploading = ref(false)
const uploadError = ref('')

const basicRows = computed(() => {
    const e = employee.value
    return [
        ['Employee ID', e.employee_code],
        ['Name', e.name],
        ["Father's Name", e.father_name],
        ["Mother's Name", e.mother_name],
        ['Date of Birth', e.date_of_birth],
        ['Gender', e.gender],
        ['Phone', e.phone],
        ['Email', e.email],
        ['Present Address', e.address],
        ['Permanent Address', e.detail?.permanent_address],
        ['National ID', e.detail?.national_id],
        ['Passport No', e.detail?.passport_no],
        ['Marital Status', e.detail?.marital_status],
        ['Blood Group', e.detail?.blood_group],
        ['Emergency Contact', [e.detail?.emergency_contact_name, e.detail?.emergency_contact_relation, e.detail?.emergency_contact_phone].filter(Boolean).join(' · ')],
    ]
})

const employmentRows = computed(() => {
    const e = employee.value
    const detail = e.detail || {}
    return [
        ['Department', e.department],
        ['Designation', e.designation],
        ['Employment Type', e.employment_type],
        ['Status', e.status],
        ['Joining Date', e.joining_date],
        ['Last Working Date', e.last_working_date],
        ['Salary Payment Method', detail.payment_method],
        ['Bank', [detail.bank_name, detail.bank_branch].filter(Boolean).join(', ')],
        ['Account', [detail.bank_account_name, detail.bank_account_no].filter(Boolean).join(' - ')],
        ['Notes', detail.notes],
    ]
})

// The active revision with the latest effective date on or before today.
const currentSalary = computed(() => {
    const today = new Date().toISOString().slice(0, 10)
    return employee.value?.salary_details?.find((r) => r.status && r.effective_date <= today)
        ?? employee.value?.salary_details?.find((r) => r.status)
        ?? null
})

const loadEmployee = async () => {
    try {
        const { data } = await axios.get(`/api/hr/employees/${route.params.id}`)
        if (data.success) employee.value = data.data
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to load employee.')
    }
}

const loadWorks = async () => {
    if (!can(['employee-works.view'])) return
    const { data } = await axios.get('/api/hr/employee-works', { params: { employee_id: route.params.id, per_page: 120 } })
    works.value = data.data?.works ?? []
}

const loadSalaries = async () => {
    if (!can(['payroll.view'])) return
    const { data } = await axios.get('/api/hr/salary-histories', {
        params: { employee_id: route.params.id, include_cancelled: 1, per_page: 120 },
    })
    salaries.value = data.data?.salaries ?? []
}

const openSalaryDialog = (revision) => {
    editingRevision.value = revision
    salaryDialogOpen.value = true
}

const deleteRevision = async (revision) => {
    const result = await Swal.fire({
        title: 'Delete this salary configuration?',
        text: `Effective ${revision.effective_date}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
    })
    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/hr/salary-details/${revision.id}`)
        toast.success(data.message)
        loadEmployee()
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete.')
    }
}

const uploadDocument = async () => {
    uploadError.value = ''
    if (!upload.file) return

    const payload = new FormData()
    payload.append('document_type', upload.document_type)
    payload.append('title', upload.title)
    payload.append('file', upload.file)

    uploading.value = true
    try {
        const { data } = await axios.post(`/api/hr/employees/${route.params.id}/files`, payload)
        toast.success(data.message)
        upload.title = ''
        upload.file = null
        if (fileInput.value) fileInput.value.value = ''
        loadEmployee()
    } catch (e) {
        uploadError.value = e.response?.status === 422
            ? Object.values(e.response.data.data).flat().join(' ')
            : (e.response?.data?.message || 'Upload failed.')
    } finally {
        uploading.value = false
    }
}

// Files are private, so they are fetched with the auth token and saved from a blob.
const downloadDocument = async (file) => {
    try {
        const response = await axios.get(`/api/hr/employee-files/${file.id}/download`, { responseType: 'blob' })
        const url = URL.createObjectURL(response.data)
        const link = document.createElement('a')
        link.href = url
        link.download = file.file_name
        link.click()
        URL.revokeObjectURL(url)
    } catch (e) {
        toast.error('Failed to download the file.')
    }
}

const deleteDocument = async (file) => {
    const result = await Swal.fire({
        title: 'Delete this document?',
        text: file.file_name,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
    })
    if (!result.isConfirmed) return

    try {
        const { data } = await axios.delete(`/api/hr/employee-files/${file.id}`)
        toast.success(data.message)
        loadEmployee()
    } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to delete.')
    }
}

onMounted(() => {
    loadEmployee()
    loadWorks()
    loadSalaries()
})
</script>

<style scoped>
.info-table th {
    width: 220px;
    background: #f8f9fa;
    font-weight: 600;
}
</style>
