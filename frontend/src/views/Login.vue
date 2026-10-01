<template>
    <div class="login-page">
        <aside class="brand-panel">
            <div class="brand-content">
                <!-- Logo from Company Settings; falls back to the placeholder if missing or broken -->
                <div class="brand-panel-logo">
                    <img :src="branding.logo_url || logoPlaceholder" :data-placeholder="logoPlaceholder"
                        :alt="companyName">
                </div>
                <h1 class="brand-panel-name">{{ companyName }}</h1>
                <p class="brand-panel-tagline">{{ $t('auth.brandTagline') }}</p>
            </div>
            <p class="brand-panel-footer">&copy; {{ year }} {{ companyName }}</p>
        </aside>

        <main class="login-main">
            <div class="login-language">
                <LanguageSwitcher />
            </div>

            <div class="login-card">
                <div class="brand-logo">
                    <img :src="branding.logo_url || logoPlaceholder" :data-placeholder="logoPlaceholder"
                        :alt="companyName">
                    <span>{{ companyName }}</span>
                </div>

                <h2 class="login-title">{{ $t('auth.loginTitle') }}</h2>
                <p class="login-subtitle">{{ $t('auth.loginSubtitle') }}</p>

                <form @submit.prevent="handleSubmitForm" novalidate>
                    <label class="field-label" for="login-email">{{ $t('auth.email') }}</label>
                    <v-text-field id="login-email" type="email" autocomplete="username" density="comfortable"
                        :placeholder="$t('auth.emailPlaceholder')" variant="outlined" color="primary"
                        v-model="form.email" :error="!!errors.email"
                        :error-messages="errors.email ? [errors.email] : []" autofocus></v-text-field>

                    <label class="field-label" for="login-password">{{ $t('auth.password') }}</label>
                    <v-text-field id="login-password" :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                        :type="visible ? 'text' : 'password'" autocomplete="current-password" density="comfortable"
                        :placeholder="$t('auth.passwordPlaceholder')" variant="outlined" color="primary"
                        @click:append-inner="visible = !visible"
                        v-model="form.password" :error="!!errors.password"
                        :error-messages="errors.password ? [errors.password] : []"></v-text-field>

                    <v-btn type="submit" class="login-submit mt-2" size="large" block :loading="loading">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span class="ml-2">{{ $t('auth.submit') }}</span>
                    </v-btn>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { setToast } from "@/helpers/toast";
import { logoPlaceholder } from '@/helpers/imagePlaceholder'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'

const router = useRouter()
const auth = useAuthStore()
const visible = ref(false)
const loading = ref(false)
const year = new Date().getFullYear()

// Public branding (name + logo); a failed request just leaves the placeholder in place
const branding = reactive({ company_name: '', logo_url: '' })
const companyName = computed(() => branding.company_name || 'ERP')

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/branding')
        if (data.success) Object.assign(branding, data.data)
    } catch (err) {
        console.error(err)
    }
})

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
.login-page {
    display: flex;
    width: 100%;
    min-height: 100vh;
    align-items: stretch;
    justify-content: flex-start;
    padding: 0;
    background: #fff;
}

/* ---------- Brand panel ---------- */
.brand-panel {
    position: relative;
    flex: 1 1 auto;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 48px 8%;
    color: #fff;
    overflow: hidden;
    background:
        repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0 2px, transparent 2px 28px),
        linear-gradient(135deg, #2f3fa8 0%, #586eea 55%, #7f93f5 100%);
}

/* Soft light glows */
.brand-panel::before,
.brand-panel::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.brand-panel::before {
    width: 520px;
    height: 520px;
    top: -180px;
    right: -180px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, transparent 70%);
}

.brand-panel::after {
    width: 420px;
    height: 420px;
    bottom: -160px;
    left: -140px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
}

.brand-content {
    position: relative;
    z-index: 1;
    width: min(100%, 520px);
    text-align: center;
}

.brand-panel-logo {
    width: 120px;
    height: 120px;
    margin: 0 auto 28px;
    padding: 16px;
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 20px 45px rgba(20, 30, 90, 0.3);
}

.brand-panel-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.brand-panel-name {
    margin-bottom: 12px;
    font-size: 30px;
    font-weight: 700;
    line-height: 1.3;
    overflow-wrap: anywhere;
}

.brand-panel-tagline {
    margin: 0 auto;
    max-width: 440px;
    font-size: 16px;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.8);
}

.brand-panel-footer {
    position: absolute;
    bottom: 20px;
    z-index: 1;
    margin: 0;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.65);
}

/* ---------- Form panel ---------- */
.login-main {
    position: relative;
    flex: 0 0 clamp(390px, 26vw, 500px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 72px 48px 40px;
    background: #fff;
}

.login-language {
    position: absolute;
    top: 20px;
    right: 24px;
}

.login-card {
    width: 100%;
    max-width: 430px;
}

/* Shown only on small screens, where the brand panel is hidden */
.brand-logo {
    display: none;
    align-items: center;
    gap: 12px;
    margin-bottom: 48px;
    color: #111827;
    font-size: 20px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.brand-logo img {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.login-title {
    font-size: 20px;
    font-weight: 700;
    color: #586eea;
    margin-bottom: 4px;
}

.login-subtitle {
    font-size: 14px;
    color: #737d92;
    margin-bottom: 28px;
}

.field-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #3b4354;
    margin-bottom: 6px;
}

.login-submit {
    color: #fff !important;
    font-weight: 600;
    text-transform: none;
    background: #586eea !important;
    box-shadow: none;
}

/* ---------- Small screens ---------- */
@media (max-width: 900px) {
    .login-page {
        min-height: 100vh;
        flex-direction: column;
    }

    .login-main {
        flex: 1 0 auto;
        min-height: 100vh;
        padding: 76px 24px 40px;
    }

    .login-card {
        max-width: 430px;
    }

    .brand-logo {
        margin-bottom: 40px;
    }

    .brand-panel {
        display: none;
    }

    .brand-logo {
        display: flex;
    }

    .login-title,
    .login-subtitle {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .login-main {
        padding-right: 20px;
        padding-left: 20px;
    }

    .login-language {
        right: 20px;
    }
}
</style>
