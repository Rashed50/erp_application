<template lang="html">
    <Breadcrumb title="Create New User" buttonText="Back User" :buttonLink="{ name: 'admin_users' }"
        buttonIcon="list" />

    <div class="main-content-wrapper mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <!-- Table Card -->
                    <v-card style="padding: 5px; margin: 15px 0px;">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Name -->
                                    <div class="form-group mb-3">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" class="form-control" v-model="form.name"
                                            required />
                                        <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group mb-3">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" class="form-control" v-model="form.email"
                                            required />
                                        <div v-if="errors.email" class="error-msg">{{ errors.email }}</div>
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group mb-3">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" class="form-control"
                                            v-model="form.password" required />
                                        <div v-if="errors.password" class="error-msg">{{ errors.password }}</div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="form-group mb-3">
                                        <label for="password_confirmation">Confirm Password:</label>
                                        <input type="password" id="password_confirmation" class="form-control"
                                            v-model="form.password_confirmation" required />
                                    </div>

                                    <!-- Role Selection -->
                                    <div class="form-group mb-3">
                                        <label for="role">Assign Role:</label>
                                        <select id="role" class="form-control" v-model="form.role" required>
                                            <option value="" disabled>Select a Role</option>
                                            <option v-for="item in items" :key="item.id" :value="item.name">
                                                {{ item.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.role" class="error-msg">{{ errors.role }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-btn type="submit" class="text-none text-white mr-2" color="blue-darken-4"
                                        rounded="0" variant="flat" :disabled="isSubmitting" :loading="isSubmitting">
                                        Submit
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
import { useFetch } from '@/composables/useFetch';
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter } from 'vue-router';


const router = useRouter();
// store data property
// initial form data
const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: ''
})


const handleSubmit = async () => {
    const resp = await submit('/api/users', 'post')

    if (resp && resp.success) {
        try {
            if (form.role) {
                await axios.post(`/api/users/${resp.data.id}/roles`, { roles: [form.role] })
            }
            setToast('success', resp.message)
            router.push({ name: 'admin_users' })
        } catch (e) {
            setToast('error', e.response?.data?.message || 'User created, but role assignment failed.')
        }
    }
}

// fetch data property
const { items, fetchData } = useFetch('/api/roles', { per_page: 100 })

const loadRole = async () => {
    await fetchData()
}

onMounted(() => {
    loadRole()
})

</script>
