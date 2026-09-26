<template lang="html">
    <Breadcrumb title="Edit Employee" buttonText="Employee List" :buttonLink="{ name: 'admin_hr_employees_list' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <v-card style="padding: 15px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <EmployeeFormFields :form="form" :errors="errors" :options="options" />
                            <div v-if="errorMessage" class="error-msg mb-2">{{ errorMessage }}</div>
                            <v-btn type="submit" class="text-none text-white" color="blue-darken-4" rounded="0"
                                variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                Update Employee
                            </v-btn>
                        </form>
                    </v-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';
import EmployeeFormFields from './EmployeeFormFields.vue';
import { emptyEmployeeDetail, useEmployeeForm } from './useEmployeeForm';

const router = useRouter();
const route = useRoute();

const { form, errors, errorMessage, isSubmitting, submit, options, loadOptions } = useEmployeeForm()

const handleSubmit = async () => {
    const resp = await submit(`/api/hr/employees/${route.params.id}`, 'put')

    if (resp && resp.success) {
        setToast('success', resp.message)
        router.push({ name: 'admin_hr_employee_show', params: { id: route.params.id } })
    }
}

const loadEmployee = async () => {
    try {
        const { data } = await axios.get(`/api/hr/employees/${route.params.id}`)
        if (data.success) {
            const employee = data.data
            for (const key of Object.keys(form)) {
                if (key !== 'detail') form[key] = employee[key] ?? ''
            }
            const detail = { ...emptyEmployeeDetail() }
            for (const key of Object.keys(detail)) {
                detail[key] = employee.detail?.[key] ?? detail[key]
            }
            form.detail = detail
        }
    } catch (e) {
        console.error(e)
    }
}

onMounted(() => {
    loadOptions()
    loadEmployee()
})
</script>
