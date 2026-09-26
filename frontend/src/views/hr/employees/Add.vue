<template lang="html">
    <Breadcrumb title="Add New Employee" buttonText="Employee List" :buttonLink="{ name: 'admin_hr_employees_list' }"
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
                                Save Employee
                            </v-btn>
                        </form>
                    </v-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';
import EmployeeFormFields from './EmployeeFormFields.vue';
import { useEmployeeForm } from './useEmployeeForm';

const router = useRouter();

const { form, errors, errorMessage, isSubmitting, submit, options, loadOptions } = useEmployeeForm()

const handleSubmit = async () => {
    const resp = await submit('/api/hr/employees', 'post')

    if (resp && resp.success) {
        setToast('success', resp.message)
        // Straight to the details page, where salary and documents are added.
        router.push({ name: 'admin_hr_employee_show', params: { id: resp.data.id } })
    }
}

onMounted(() => {
    loadOptions()
})
</script>
