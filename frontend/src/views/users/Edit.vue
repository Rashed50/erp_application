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

                                    <div class="mb-3 mt-3">
                                        <h5 class="mb-3">Update Password</h5>
                                        <p class="text-muted">Leave blank if you don't want to change the password.</p>
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group mb-3">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" class="form-control"
                                            v-model="form.password" />
                                        <div v-if="errors.password" class="error-msg">{{ errors.password }}</div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="form-group mb-3">
                                        <label for="password_confirmation">Confirm Password:</label>
                                        <input type="password" id="password_confirmation" class="form-control"
                                            v-model="form.password_confirmation" />
                                    </div>

                                    <!-- Role Selection -->
                                    <div class="form-group mb-3" v-if="form.role != 'Super Admin'">
                                        <label for="role">Assign Role:</label>
                                        <select id="role" class="form-control" v-model="form.role" required>
                                            <option value="" disabled>Select a Role</option>
                                            <option v-for="item in roles" :key="item.id" :value="item.name">
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
import { useStoreForm } from '@/composables/useStoreForm';
import { setToast } from "@/helpers/toast";
import { useRouter, useRoute } from 'vue-router';


const router = useRouter();
const route = useRoute();
// store data property
// initial form data
const { form, errors, isSubmitting, submit } = useStoreForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: ''
})

const roles = ref([])


const handleSubmit = async () => {
    // Password fields are optional on update — don't send them blank.
    if (!form.password) {
        delete form.password
        delete form.password_confirmation
    }

    const resp = await submit(`/api/users/${route.params.id}`, 'put')

    if (resp && resp.success) {
        try {
            if (form.role) {
                await axios.post(`/api/users/${route.params.id}/roles`, { roles: [form.role] })
            }
            setToast('success', resp.message)
            router.push({ name: 'admin_users' })
        } catch (e) {
            setToast('error', e.response?.data?.message || 'User updated, but role assignment failed.')
        }
    }
}


// Load user + the role catalog for the dropdown
const loadEditData = async () => {
    try {
        const [userResp, rolesResp] = await Promise.all([
            axios.get(`/api/users/${route.params.id}`),
            axios.get('/api/roles', { params: { per_page: 100 } }),
        ])

        if (userResp.data.success) {
            const user = userResp.data.data
            form.name = user.name
            form.email = user.email
            form.role = user.roles?.[0] ?? ''
        }

        roles.value = rolesResp.data.data?.roles ?? []
    } catch (e) {
        console.error(e)
    }
}


onMounted(() => {
    loadEditData()
})

</script>
