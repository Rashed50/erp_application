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
                <select id="gender" class="form-control" v-model="form.gender">
                    <option value="">{{ $t('Select') }}</option>
                    <option v-for="gender in options.genders" :key="gender" :value="gender">{{ $t(gender) }}</option>
                </select>
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
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="address">{{ $t('Present Address:') }}</label>
                <textarea id="address" class="form-control" rows="2" v-model="form.address"></textarea>
            </div>
        </div>
    </div>

    <!-- Employment Information -->
    <h6 class="section-title">{{ $t('Employment Information') }}</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="department">{{ $t('Department:') }}</label>
                <input type="text" id="department" class="form-control" list="department-options"
                    v-model="form.department" />
                <datalist id="department-options">
                    <option v-for="department in options.departments" :key="department" :value="department" />
                </datalist>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="designation">{{ $t('Designation:') }}</label>
                <input type="text" id="designation" class="form-control" list="designation-options"
                    v-model="form.designation" />
                <datalist id="designation-options">
                    <option v-for="designation in options.designations" :key="designation" :value="designation" />
                </datalist>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="employment_type">{{ $t('Employment Type:') }} <span class="text-danger">*</span></label>
                <select id="employment_type" class="form-control" v-model="form.employment_type" required>
                    <option v-for="type in options.employment_types" :key="type" :value="type">{{ $t(type) }}</option>
                </select>
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
                <select id="status" class="form-control" v-model="form.status" required>
                    <option v-for="status in options.statuses" :key="status" :value="status">{{ $t(status) }}</option>
                </select>
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

    <!-- Personal & Payment Details (emp_details) -->
    <h6 class="section-title">{{ $t('Personal & Payment Details') }}</h6>
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
                <select class="form-control" v-model="form.detail.marital_status">
                    <option value="">{{ $t('Select') }}</option>
                    <option v-for="status in ['Single', 'Married', 'Divorced', 'Widowed']" :key="status" :value="status">
                        {{ $t(status) }}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label>{{ $t('Blood Group:') }}</label>
                <select class="form-control" v-model="form.detail.blood_group">
                    <option value="">{{ $t('Select') }}</option>
                    <option v-for="group in ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']" :key="group" :value="group">
                        {{ group }}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label>{{ $t('Permanent Address:') }}</label>
                <textarea class="form-control" rows="2" v-model="form.detail.permanent_address"></textarea>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label>{{ $t('Salary Payment Method:') }}</label>
                <select class="form-control" v-model="form.detail.payment_method">
                    <option value="Cash">{{ $t('Cash') }}</option>
                    <option value="Bank">{{ $t('Bank') }}</option>
                </select>
            </div>
        </div>
        <template v-if="form.detail.payment_method === 'Bank'">
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label>{{ $t('Bank Name:') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.detail.bank_name" />
                    <div v-if="errors['detail.bank_name']" class="error-msg">{{ errors['detail.bank_name'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label>{{ $t('Branch:') }}</label>
                    <input type="text" class="form-control" v-model="form.detail.bank_branch" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label>{{ $t('Account No:') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.detail.bank_account_no" />
                    <div v-if="errors['detail.bank_account_no']" class="error-msg">{{ errors['detail.bank_account_no'] }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>{{ $t('Account Name:') }}</label>
                    <input type="text" class="form-control" v-model="form.detail.bank_account_name" />
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
</template>

<script setup>
// Fields shared by the Add and Edit employee pages; `form.detail` maps to emp_details.
defineProps({
    form: { type: Object, required: true },
    errors: { type: Object, required: true },
    options: { type: Object, required: true },
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
