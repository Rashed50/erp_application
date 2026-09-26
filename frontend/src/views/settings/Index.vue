<template lang="html">
    <Breadcrumb title="Company Settings" />

    <div class="main-content-wrapper mt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <v-card elevation="2" class="rounded-lg overflow-hidden">
                        <div class="bg-blue-darken-4 px-4 py-3 d-flex align-items-center">
                            <v-icon color="white" class="me-2">mdi-cog-outline</v-icon>
                            <h5 class="mb-0 text-white font-weight-bold">Company Settings</h5>
                        </div>

                        <v-card-text class="p-4 p-md-5">
                            <form enctype="multipart/form-data" @submit.prevent="handleSubmit">
                                <div class="row g-4">

                                    <div class="col-12">
                                        <h6 class="text-blue-darken-4 fw-bold border-bottom pb-2 mb-3">
                                            <i class="fas fa-building me-2"></i>Company Information
                                        </h6>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label custom-label">Company Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" v-model="form.company_name" class="form-control custom-input"
                                            placeholder="Enter company name">
                                        <small v-if="errors.company_name" class="text-danger mt-1 d-block">{{
                                            errors.company_name }}</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label custom-label">Email Address</label>
                                        <input type="email" v-model="form.email" class="form-control custom-input"
                                            placeholder="info@company.com">
                                        <small v-if="errors.email" class="text-danger mt-1 d-block">{{ errors.email
                                        }}</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label custom-label">Phone</label>
                                        <input type="text" v-model="form.phone" class="form-control custom-input"
                                            placeholder="01XXXXXXXXX">
                                        <small v-if="errors.phone" class="text-danger mt-1 d-block">{{ errors.phone
                                        }}</small>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label custom-label">Address</label>
                                        <textarea v-model="form.address" class="form-control custom-input" rows="2"
                                            placeholder="Enter company address"></textarea>
                                        <small v-if="errors.address" class="text-danger mt-1 d-block">{{
                                            errors.address }}</small>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <h6 class="text-blue-darken-4 fw-bold border-bottom pb-2 mb-3">
                                            <i class="fas fa-image me-2"></i>Branding
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="upload-box p-3 border rounded bg-light">
                                            <label class="form-label fw-bold">Company Logo</label>
                                            <input type="file" ref="logoInput" class="form-control mb-2"
                                                accept=".jpg,.jpeg,.png,.webp,.svg" @change="handleLogoChange">
                                            <small class="text-muted">JPG, PNG, WEBP or SVG, max 2MB.</small>
                                            <div class="preview-wrapper mt-2">
                                                <img :src="logoPreview || logoPlaceholder" :data-placeholder="logoPlaceholder"
                                                    class="img-thumbnail shadow-sm" style="max-height: 80px;"
                                                    alt="Logo preview">
                                            </div>
                                            <v-btn v-if="logoPreview" size="small" variant="text" color="error"
                                                class="text-none mt-1" @click="removeLogo">
                                                Remove logo
                                            </v-btn>
                                            <small v-if="errors.logo" class="text-danger d-block">{{ errors.logo
                                            }}</small>
                                        </div>
                                    </div>

                                    <div class="col-12 text-end mt-4">
                                        <v-divider class="mb-4"></v-divider>
                                        <v-btn type="submit" size="large" color="blue-darken-4"
                                            class="px-8 rounded-pill shadow-lg" :loading="isSubmitting">
                                            <v-icon start>mdi-check-circle</v-icon>
                                            Save Settings
                                        </v-btn>
                                    </div>

                                </div>
                            </form>
                        </v-card-text>
                    </v-card>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import axios from 'axios'
import { toast } from 'vue3-toastify'
import Breadcrumb from '@/components/common/Breadcrumb.vue'
import { objectToFormData } from '@/helpers/objectToFormData'
import { useSettingStore } from '@/stores/settings'
import { logoPlaceholder } from '@/helpers/imagePlaceholder'

const settings = useSettingStore()

const form = reactive({
    company_name: '',
    email: '',
    phone: '',
    address: '',
    logo: null,
    remove_logo: 0,
})
const errors = reactive({})
const isSubmitting = ref(false)
const logoPreview = ref('')
const logoInput = ref(null)

const clearErrors = () => {
    for (const key in errors) delete errors[key]
}

const fillForm = (company) => {
    Object.assign(form, {
        company_name: company.company_name ?? '',
        email: company.email ?? '',
        phone: company.phone ?? '',
        address: company.address ?? '',
        logo: null,
        remove_logo: 0,
    })
    logoPreview.value = company.logo_url ?? ''
    if (logoInput.value) logoInput.value.value = ''
}

const handleLogoChange = (event) => {
    const file = event.target.files[0] ?? null
    form.logo = file
    form.remove_logo = 0
    logoPreview.value = file ? URL.createObjectURL(file) : (settings.company.logo_url ?? '')
}

const removeLogo = () => {
    form.logo = null
    form.remove_logo = 1
    logoPreview.value = ''
    if (logoInput.value) logoInput.value.value = ''
}

const handleSubmit = async () => {
    if (!form.company_name) {
        errors.company_name = 'The company name field is required.'
        return
    }

    isSubmitting.value = true
    clearErrors()

    try {
        const { data } = await axios.post('/api/settings', objectToFormData({ ...form }))

        if (data.success) {
            toast.success(data.message)
            // Updating the store refreshes the sidebar logo and name immediately.
            settings.setCompany(data.data)
            fillForm(data.data)
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const respErrors = err.response.data.data
            for (const key in respErrors) {
                errors[key] = respErrors[key].join(' ')
            }
        } else {
            toast.error(err.response?.data?.message || 'An error occurred while saving.')
        }
    } finally {
        isSubmitting.value = false
    }
}

onMounted(async () => {
    await settings.fetchCompany()
    fillForm(settings.company)
})
</script>
<style scoped>
.custom-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #444;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.custom-input {
    border: 1px solid #dce1e6;
    padding: 0.6rem 0.8rem;
    border-radius: 6px;
    transition: all 0.3s;
}

.custom-input:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.1);
}

.upload-box {
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border: 1px dashed #cbd5e0 !important;
}

.preview-wrapper {
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fdfdfd;
    border-radius: 4px;
}

.img-thumbnail {
    border: 2px solid #fff;
}
</style>
