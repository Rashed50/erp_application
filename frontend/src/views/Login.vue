<template>
    <div class="login-wrapper">
        <div class="login-header">
            <h4>Login Account</h4>
        </div>

        <form @submit.prevent="handleSubmitForm">
            <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
                <!-- Email -->
                <div class="text-subtitle-1 text-medium-emphasis">ইমেইল</div>
                <v-text-field density="compact" placeholder="আপনার ইমেইল লিখুন" prepend-inner-icon="mdi-email-outline"
                    variant="outlined" v-model="form.email" :error="!!errors.email"
                    :error-messages="errors.email ? [errors.email] : []"></v-text-field>

                <!-- Password -->
                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                    পাসওয়ার্ড
                </div>
                <v-text-field :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'" density="compact" placeholder="আপনার পাসওয়ার্ড লিখুন"
                    prepend-inner-icon="mdi-lock-outline" variant="outlined" @click:append-inner="visible = !visible"
                    v-model="form.password" :error="!!errors.password"
                    :error-messages="errors.password ? [errors.password] : []"></v-text-field>

                <!-- Submit -->
                <v-btn type="submit" class="mb-8 mt-1" color="blue" size="large" variant="tonal" block
                    :loading="loading">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span class="ml-2">সাবমিট করুন</span>
                </v-btn>
            </v-card>
        </form>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { setToast } from "@/helpers/toast";

const router = useRouter()
const auth = useAuthStore()
const visible = ref(false)
const loading = ref(false)

// Reactive form
const form = reactive({
    email: '',
    password: ''
})

// Reactive error object
const errors = reactive({
    email: '',
    password: ''
})

const clearErrors = () => {
    errors.email = ''
    errors.password = ''
}

const handleSubmitForm = async () => {
    clearErrors()
    loading.value = true

    try {
        const response = await auth.login(form.email, form.password)

        if (response.success) {
            setToast('success', response.message)
            router.push({ name: 'admin_dashboard' })
        } else {
            // Backend field errors are returned under `data`
            if (response.data) {
                for (const key in response.data) {
                    errors[key] = response.data[key].join(' ')
                }
            }

            // General message fallback
            if (response.message) {
                if (!errors.email) errors.email = response.message
                if (!errors.password) errors.password = response.message
            }
        }
    } catch (err) {
        console.error(err)
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.login-header {
    text-align: center;
    margin-bottom: 15px;
}

.login-header h4 {
    font-size: 29px;
}

.login-wrapper {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    max-width: 448px;
    padding: 0 16px;
    box-sizing: border-box;
}
</style>
