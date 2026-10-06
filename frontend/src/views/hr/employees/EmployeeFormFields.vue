<template>
    <!-- Basic Information -->
    <h6 class="section-title">{{ $t('Basic Information') }}</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="employee_code">{{ $t('Employee ID:') }}</label>
                <input type="text" id="employee_code" class="form-control" v-model="form.employee_code"
                    :placeholder="options.next_employee_code ? $t('Auto: {code}', { code: options.next_employee_code }) : $t('Auto-generated')" />
                <div v-if="errors.employee_code" class="error-msg">{{ errors.employee_code }}</div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group mb-3">
                <label for="name">{{ $t('Full Name:') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" class="form-control" v-model="form.name" required />
                <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="father_name">{{ $t('Father\'s Name:') }}</label>
                <input type="text" id="father_name" class="form-control" v-model="form.father_name" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="mother_name">{{ $t('Mother\'s Name:') }}</label>
                <input type="text" id="mother_name" class="form-control" v-model="form.mother_name" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="date_of_birth">{{ $t('Date of Birth:') }}</label>
                <input type="date" id="date_of_birth" class="form-control" v-model="form.date_of_birth" />
                <div v-if="errors.date_of_birth" class="error-msg">{{ errors.date_of_birth }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="gender">{{ $t('Gender:') }}</label>
                <SearchSelect id="gender" v-model="form.gender" :items="options.genders" :item-title="translated"
                    :item-value="plain" :placeholder="$t('Select')" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="phone">{{ $t('Phone:') }}</label>
                <input type="text" id="phone" class="form-control" v-model="form.phone" />
                <div v-if="errors.phone" class="error-msg">{{ errors.phone }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="email">{{ $t('Email:') }}</label>
                <input type="email" id="email" class="form-control" v-model="form.email" />
                <div v-if="errors.email" class="error-msg">{{ errors.email }}</div>
            </div>
        </div>
    </div>

    <!-- Present Address -->
    <h6 class="section-title">{{ $t('Present Address') }}</h6>
    <LocationSelect v-model:division="form.division_id" v-model:district="form.district_id"
        v-model:upazila="form.upazila_id"
        :errors="{ division: errors.division_id, district: errors.district_id, upazila: errors.upazila_id }" />
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="address">{{ $t('Address (House, Road, Village):') }}</label>
                <textarea id="address" class="form-control" rows="2" v-model="form.address"></textarea>
            </div>
        </div>
    </div>

    <!-- Permanent Address (emp_details) -->
    <h6 class="section-title d-flex align-items-center justify-content-between">
        <span>{{ $t('Permanent Address') }}</span>
        <label class="same-address">
            <input type="checkbox" class="form-check-input me-1" v-model="sameAsPresent" />
            {{ $t('Same as present address') }}
        </label>
    </h6>
    <LocationSelect v-model:division="form.detail.permanent_division_id"
        v-model:district="form.detail.permanent_district_id" v-model:upazila="form.detail.permanent_upazila_id"
        :errors="{
            division: errors['detail.permanent_division_id'],
            district: errors['detail.permanent_district_id'],
            upazila: errors['detail.permanent_upazila_id'],
        }" />
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label>{{ $t('Address (House, Road, Village):') }}</label>
                <textarea class="form-control" rows="2" v-model="form.detail.permanent_address"></textarea>
            </div>
        </div>
    </div>

    <!-- Employment Information -->
    <h6 class="section-title">{{ $t('Employment Information') }}</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="department_id">{{ $t('Department:') }}</label>
                <SearchSelect id="department_id" v-model="form.department_id" :items="departmentItems"
                    :placeholder="$t('Select Department')" :error="errors.department_id"
                    :addable="can(['departments.create'])" :add-title="$t('Add Department')"
                    @add="openAdd('department')" />
                <div v-if="errors.department_id" class="error-msg">{{ errors.department_id }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="designation_id">{{ $t('Designation:') }}</label>
                <SearchSelect id="designation_id" v-model="form.designation_id" :items="designationItems"
                    :placeholder="$t('Select Designation')" :error="errors.designation_id"
                    :addable="can(['designations.create'])" :add-title="$t('Add Designation')"
                    @add="openAdd('designation')" />
                <div v-if="errors.designation_id" class="error-msg">{{ errors.designation_id }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="employment_type">{{ $t('Employment Type:') }} <span class="text-danger">*</span></label>
                <SearchSelect id="employment_type" v-model="form.employment_type" :items="options.employment_types"
                    :item-title="translated" :item-value="plain" :clearable="false" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="joining_date">{{ $t('Joining Date:') }} <span class="text-danger">*</span></label>
                <input type="date" id="joining_date" class="form-control" v-model="form.joining_date" required />
                <div v-if="errors.joining_date" class="error-msg">{{ errors.joining_date }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="status">{{ $t('Employment Status:') }} <span class="text-danger">*</span></label>
                <SearchSelect id="status" v-model="form.status" :items="options.statuses" :item-title="translated"
                    :item-value="plain" :clearable="false" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="last_working_date">
                    {{ $t('Last Working Date:') }}
                    <span class="text-danger" v-if="['Resigned', 'Terminated'].includes(form.status)">*</span>
                </label>
                <input type="date" id="last_working_date" class="form-control" v-model="form.last_working_date"
                    :min="form.joining_date" />
                <div v-if="errors.last_working_date" class="error-msg">{{ errors.last_working_date }}</div>
                <small class="text-muted">{{ $t('Payroll includes the employee up to this date.') }}</small>
            </div>
        </div>
    </div>

    <!-- Personal Details (emp_details) -->
    <h6 class="section-title">{{ $t('Personal Details') }}</h6>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label>{{ $t('National ID:') }}</label>
                <input type="text" class="form-control" v-model="form.detail.national_id" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label>{{ $t('Passport No:') }}</label>
                <input type="text" class="form-control" v-model="form.detail.passport_no" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label>{{ $t('Marital Status:') }}</label>
                <SearchSelect v-model="form.detail.marital_status" :items="MARITAL_STATUSES" :item-title="translated"
                    :item-value="plain" :placeholder="$t('Select')" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label>{{ $t('Blood Group:') }}</label>
                <SearchSelect v-model="form.detail.blood_group" :items="BLOOD_GROUPS" :item-title="plain"
                    :item-value="plain" :placeholder="$t('Select')" />
            </div>
        </div>
    </div>

    <!-- Salary Payment & Bank Details (emp_bank_details) -->
    <h6 class="section-title">{{ $t('Salary Payment & Bank Details') }}</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>{{ $t('Salary Payment Method:') }}</label>
                <SearchSelect v-model="form.detail.payment_method" :items="PAYMENT_METHODS" :item-title="translated"
                    :item-value="plain" :clearable="false" />
            </div>
        </div>
        <template v-if="form.detail.payment_method === 'Bank'">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label>{{ $t('Bank Name:') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.bank.bank_name" />
                    <div v-if="errors['bank.bank_name']" class="error-msg">{{ errors['bank.bank_name'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label>{{ $t('Branch:') }}</label>
                    <input type="text" class="form-control" v-model="form.bank.branch_name" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label>{{ $t('Account Name:') }}</label>
                    <input type="text" class="form-control" v-model="form.bank.account_name" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label>{{ $t('Account No:') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.bank.account_no" />
                    <div v-if="errors['bank.account_no']" class="error-msg">{{ errors['bank.account_no'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label>{{ $t('Routing No:') }}</label>
                    <input type="text" class="form-control" v-model="form.bank.routing_no" />
                </div>
            </div>
        </template>
    </div>

    <h6 class="section-title">{{ $t('Emergency Contact') }}</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>{{ $t('Name:') }}</label>
                <input type="text" class="form-control" v-model="form.detail.emergency_contact_name" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>{{ $t('Relation:') }}</label>
                <input type="text" class="form-control" v-model="form.detail.emergency_contact_relation" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>{{ $t('Phone:') }}</label>
                <input type="text" class="form-control" v-model="form.detail.emergency_contact_phone" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label>{{ $t('Notes:') }}</label>
                <textarea class="form-control" rows="2" v-model="form.detail.notes"></textarea>
            </div>
        </div>
    </div>

    <QuickAddDialog v-if="adding" v-model="addDialog" :title="$t(adding.title)" :endpoint="adding.endpoint"
        with-description @created="onCreated" />
</template>

<script setup>
import { t } from '@/i18n';
import { usePermission } from '@/composables/usePermission';
import LocationSelect from '@/components/common/LocationSelect.vue';
import QuickAddDialog from '@/components/common/QuickAddDialog.vue';
import SearchSelect from '@/components/common/SearchSelect.vue';

// Fields shared by the Add and Edit employee pages; `form.detail` maps to
// emp_details and `form.bank` to emp_bank_details.
const props = defineProps({
    form: { type: Object, required: true },
    errors: { type: Object, required: true },
    options: { type: Object, required: true },
})

const MARITAL_STATUSES = ['Single', 'Married', 'Divorced', 'Widowed']
const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']
const PAYMENT_METHODS = ['Cash', 'Bank']

// For lists of plain strings: the string is both the value and (translated) the label.
const plain = (value) => value
const translated = (value) => t(value)

// Active rows only, plus the employee's current one even if it was deactivated since.
const choosable = (rows, selectedId) => (rows || []).filter((row) => row.status || row.id === selectedId)
const departmentItems = computed(() => choosable(props.options.departments, props.form.department_id))
const designationItems = computed(() => choosable(props.options.designations, props.form.designation_id))

// "+" beside Department / Designation: create the missing one and select it.
const { can } = usePermission()
const ADDABLE = {
    department: { title: 'Add Department', endpoint: '/api/hr/departments', list: 'departments', field: 'department_id' },
    designation: { title: 'Add Designation', endpoint: '/api/hr/designations', list: 'designations', field: 'designation_id' },
}
const addDialog = ref(false)
const adding = ref(null)

const openAdd = (kind) => {
    adding.value = ADDABLE[kind]
    addDialog.value = true
}

const onCreated = (record) => {
    const { list, field } = adding.value
    const rows = [...(props.options[list] || []), { id: record.id, name: record.name, status: record.status }]
    props.options[list] = rows.sort((a, b) => a.name.localeCompare(b.name))
    props.form[field] = record.id
}

// Copies the present address into the permanent one while ticked.
const sameAsPresent = ref(false)
watch(
    () => [sameAsPresent.value, props.form.address, props.form.division_id, props.form.district_id, props.form.upazila_id],
    () => {
        if (!sameAsPresent.value) return
        const detail = props.form.detail
        detail.permanent_address = props.form.address
        detail.permanent_division_id = props.form.division_id
        detail.permanent_district_id = props.form.district_id
        detail.permanent_upazila_id = props.form.upazila_id
    },
)
</script>

<style scoped>
.section-title {
    font-weight: 700;
    color: #0d47a1;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 6px;
    margin: 10px 0 15px;
}

.same-address {
    font-weight: 400;
    font-size: 0.875rem;
    color: #495057;
    cursor: pointer;
}
</style>
