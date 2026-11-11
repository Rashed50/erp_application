<template>
    <div class="login-page">
        <div class="login-language">
            <LanguageSwitcher />
        </div>

        <main class="login-shell">
            <section class="login-banner">
                <div class="banner-copy">
                    <h1>{{ $t('auth.loginTitle') }}</h1>
                    <p>{{ $t('auth.loginSubtitle') }}</p>
                </div>
                <div class="login-illustration" aria-hidden="true">
                    <i class="illustration-clock fa-solid fa-clock"></i>
                    <i class="illustration-chat fa-solid fa-comment-dots"></i>
                    <div class="illustration-plant"><i class="fa-solid fa-seedling"></i></div>
                    <div class="illustration-screen"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="illustration-person"><i class="fa-solid fa-user-tie"></i></div>
                    <div class="illustration-desk"></div>
                </div>
            </section>

            <section class="login-card">
                <div class="brand-lockup">
                    <div class="brand-logo">
                        <!-- Company logo comes from settings, with a local fallback. -->
                        <img :src="branding.logo_url || logoPlaceholder" :data-placeholder="logoPlaceholder"
                            :alt="companyName">
                    </div>
                    <span class="company-name">{{ companyName }}</span>
                </div>

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
            </section>
        </main>

        <p class="login-footer">&copy; {{ year }} {{ companyName }}. {{ $t('auth.rightsReserved') }}</p>
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
    position: relative;
    display: flex;
    min-height: 100vh;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 54px 18px 24px;
    background: #f5f6fb;
}

.login-language {
    position: absolute;
    top: 20px;
    right: 24px;
    z-index: 2;
}

.login-shell {
    width: min(100%, 562px);
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 7px;
    background: #fff;
    box-shadow: 0 18px 55px rgba(43, 55, 96, 0.09);
    animation: login-arrive 420ms ease-out both;
}

.login-banner {
    position: relative;
    display: flex;
    height: 164px;
    align-items: center;
    overflow: hidden;
    padding: 24px 30px;
    color: #536de0;
    background: #d9defa;
}

.banner-copy {
    position: relative;
    z-index: 1;
    width: 58%;
}

.banner-copy h1 {
    margin: 0 0 8px;
    font-size: 21px;
    font-weight: 700;
    line-height: 1.3;
}

.banner-copy p {
    max-width: 255px;
    margin: 0;
    font-size: 15px;
    line-height: 1.55;
    color: #6578d4;
}

.login-illustration {
    position: absolute;
    right: 12px;
    bottom: 0;
    width: 224px;
    height: 142px;
    color: #507b89;
}

.illustration-clock,
.illustration-chat {
    position: absolute;
    color: #fff;
}

.illustration-clock {
    top: 15px;
    left: 80px;
    font-size: 20px;
}

.illustration-chat {
    top: 27px;
    right: 12px;
    font-size: 20px;
}

.illustration-plant {
    position: absolute;
    bottom: 29px;
    left: 25px;
    width: 22px;
    height: 39px;
    display: grid;
    place-items: center;
    color: #598e77;
    font-size: 25px;
}

.illustration-screen {
    position: absolute;
    right: 69px;
    bottom: 33px;
    display: grid;
    width: 67px;
    height: 43px;
    place-items: center;
    border: 3px solid #7ca7b3;
    border-radius: 4px;
    background: #91bdc7;
    color: #557d8b;
    font-size: 20px;
}

.illustration-screen::after {
    position: absolute;
    bottom: -13px;
    width: 3px;
    height: 11px;
    background: #7898a0;
    content: '';
}

.illustration-person {
    position: absolute;
    right: 28px;
    bottom: 31px;
    z-index: 1;
    display: grid;
    width: 37px;
    height: 59px;
    place-items: center;
    border-radius: 20px 20px 7px 7px;
    background: #f2b39a;
    color: #855d62;
    font-size: 28px;
}

.illustration-desk {
    position: absolute;
    right: 0;
    bottom: 22px;
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.95);
}

.illustration-desk::after {
    position: absolute;
    top: 4px;
    right: 52px;
    width: 3px;
    height: 18px;
    background: #7898a0;
    content: '';
}

.brand-lockup {
    display: flex;
    min-height: 70px;
    align-items: flex-end;
    gap: 14px;
    margin: -39px 0 25px;
    position: relative;
    z-index: 1;
}

.brand-logo {
    display: grid;
    width: 90px;
    height: 90px;
    flex: 0 0 90px;
    place-items: center;
    padding: 14px;
    border: 7px solid #fff;
    border-radius: 50%;
    background: #eff2f8;
}

.brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.company-name {
    max-width: calc(100% - 104px);
    padding-bottom: 5px;
    color: #44526b;
    font-size: 14px;
    font-weight: 600;
    overflow-wrap: anywhere;
}

.login-card {
    width: 100%;
    max-width: none;
    padding: 0 34px 32px;
}

.field-label {
    display: block;
    margin-bottom: 7px;
    color: #3d4658;
    font-size: 14px;
    font-weight: 500;
}

.login-card :deep(.v-field) {
    border-radius: 5px;
    background: #fff;
}

.login-card :deep(.v-field__outline) {
    --v-field-border-opacity: 0.2;
}

.login-card :deep(.v-field--focused .v-field__outline) {
    --v-field-border-opacity: 0.8;
}

.login-submit {
    min-height: 46px;
    border-radius: 5px;
    background: #596fe2 !important;
    box-shadow: none;
    color: #fff !important;
    font-weight: 600;
    letter-spacing: 0;
    text-transform: none;
    transition: background-color 160ms ease, transform 160ms ease;
}

.login-submit:hover {
    background: #465dcf !important;
    transform: translateY(-1px);
}

.login-footer {
    margin: 22px 0 0;
    color: #707b8b;
    font-size: 13px;
    line-height: 1.5;
    text-align: center;
}

@keyframes login-arrive {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 520px) {
    .login-page {
        justify-content: center;
        padding: 68px 14px 20px;
    }

    .login-language {
        top: 16px;
        right: 16px;
    }

    .login-banner {
        height: 150px;
        padding: 20px 22px;
    }

    .banner-copy {
        width: 65%;
    }

    .banner-copy h1 {
        font-size: 19px;
    }

    .banner-copy p {
        max-width: 190px;
        font-size: 13px;
    }

    .login-illustration {
        right: -64px;
        transform: scale(0.82);
        transform-origin: bottom left;
    }

    .login-card {
        padding: 0 22px 26px;
    }

    .brand-lockup {
        margin-bottom: 21px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .login-shell {
        animation: none;
    }

    .login-submit {
        transition: none;
    }
}
</style>
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
    background: #f6f8f6;
}

/* ---------- Brand panel ---------- */
.brand-panel {
    position: relative;
    flex: 1 1 auto;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 64px clamp(48px, 8vw, 120px);
    color: #fff;
    overflow: hidden;
    background:
        repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.035) 0 1px, transparent 1px 34px),
        linear-gradient(145deg, #173e39 0%, #20554e 62%, #28756a 100%);
}

/* Quiet geometric detail behind the brand */
.brand-panel::before,
.brand-panel::after {
    content: '';
    position: absolute;
    pointer-events: none;
}

.brand-panel::before {
    width: min(58vw, 760px);
    aspect-ratio: 1;
    top: 50%;
    right: -24%;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 36px;
    transform: translateY(-50%) rotate(35deg);
}

.brand-panel::after {
    width: 9px;
    height: 96px;
    left: 0;
    top: 50%;
    background: #9bd4bd;
    transform: translateY(-50%);
}

.brand-content {
    position: relative;
    z-index: 1;
    width: min(100%, 480px);
    text-align: left;
}

.brand-panel-logo {
    width: 104px;
    height: 104px;
    margin: 0 0 34px;
    padding: 14px;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 16px 36px rgba(5, 28, 25, 0.24);
}

.brand-panel-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.brand-panel-name {
    margin-bottom: 14px;
    font-size: 34px;
    font-weight: 700;
    line-height: 1.18;
    overflow-wrap: anywhere;
}

.brand-panel-tagline {
    margin: 0;
    max-width: 400px;
    font-size: 16px;
    line-height: 1.75;
    color: rgba(255, 255, 255, 0.76);
}

.brand-panel-footer {
    position: absolute;
    bottom: 20px;
    z-index: 1;
    margin: 0;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.62);
}

/* ---------- Form panel ---------- */
.login-main {
    position: relative;
    flex: 0 0 clamp(420px, 34vw, 560px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 76px clamp(36px, 5vw, 72px) 48px;
    background: #fbfcfa;
}

.login-language {
    position: absolute;
    top: 24px;
    right: 28px;
}

.login-card {
    width: 100%;
    max-width: 410px;
}

/* Shown only on small screens, where the brand panel is hidden */
.brand-logo {
    display: none;
    align-items: center;
    gap: 12px;
    margin-bottom: 52px;
    color: #173e39;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.brand-logo img {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    object-fit: contain;
}

.login-title {
    font-size: 26px;
    font-weight: 700;
    line-height: 1.25;
    color: #173e39;
    margin-bottom: 8px;
}

.login-subtitle {
    font-size: 14px;
    line-height: 1.6;
    color: #74817c;
    margin-bottom: 32px;
}

.field-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #34443e;
    margin-bottom: 8px;
}

.login-card :deep(.v-field) {
    border-radius: 10px;
    background: #fff;
}

.login-card :deep(.v-field__outline) {
    --v-field-border-opacity: 0.16;
}

.login-card :deep(.v-field--focused .v-field__outline) {
    --v-field-border-opacity: 0.9;
}

.login-submit {
    color: #fff !important;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0;
    min-height: 52px;
    border-radius: 10px;
    background: #1b6559 !important;
    box-shadow: 0 8px 18px rgba(27, 101, 89, 0.18);
    transition: background-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
}

.login-submit:hover {
    background: #154f46 !important;
    box-shadow: 0 10px 22px rgba(27, 101, 89, 0.24);
    transform: translateY(-1px);
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
        padding: 76px 28px 40px;
    }

    .login-card {
        max-width: 430px;
    }

    .brand-logo {
        margin-bottom: 40px;
        justify-content: center;
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

    .login-title {
        font-size: 23px;
    }
}
</style>
