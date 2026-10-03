<template lang="html">
    <Breadcrumb title="Edit Role" buttonText="Back Roles" :buttonLink="{ name: 'admin_roles' }" buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <v-card style="padding: 5px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Role Name -->
                                    <div class="form-group">
                                        <label for="">{{ $t('Role Name:') }}</label>
                                        <input type="text" class="form-control" v-model="form.name" required>
                                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                    </div>

                                    <!-- Permissions -->
                                    <div class="form-group mt-4">
                                        <label>{{ $t('Assign Permissions:') }}</label>
                                        <PermissionChecklist v-model="form.permissions" />
                                        <div v-if="errors.permissions" class="error-msg">{{ errors.permissions }}</div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        {{ $t('Update') }}
                                    </v-btn>
                                </div>
                            </div>
                        </form>
                    </v-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Breadcrumb from '@/components/common/Breadcrumb.vue';
import PermissionChecklist from '@/views/roles/PermissionChecklist.vue';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { t } from '@/i18n';

const router = useRouter();
const route = useRoute();

// Form state
const form = reactive({
    name: '',
    permissions: [],
});
const errors = reactive({});
const isSubmitting = ref(false);


// Load the role being edited; the permission catalog is loaded by PermissionChecklist.
const loadRole = async () => {
    try {
        const { data } = await axios.get(`/api/roles/${route.params.id}`)

        if (data.success) {
            form.name = data.data.name
            form.permissions = [...(data.data.permissions ?? [])]
        }
    } catch (e) {
        console.error(e)
    }
}

const clearErrors = () => {
    for (const key in errors) {
        delete errors[key];
    }
};

// Submit updated role
const handleSubmit = async () => {
    isSubmitting.value = true;
    clearErrors();
    try {
        const resp = await axios.put(`/api/roles/${route.params.id}`, form);
        if (resp.data && resp.data.success) {
            setToast('success', resp.data.message);
            router.push({ name: 'admin_roles' });
        }
    } catch (e) {
        if (e.response && e.response.status === 422) {
            const respErrors = e.response.data.data;
            if (respErrors) {
                for (const key in respErrors) {
                    errors[key] = respErrors[key].join(' ');
                }
            }
        } else {
            setToast('error', e.response?.data?.message || t('An unexpected error occurred.'));
            console.error(e);
        }
    } finally {
        isSubmitting.value = false;
    }
}

onMounted(() => {
    loadRole()
})
</script>
