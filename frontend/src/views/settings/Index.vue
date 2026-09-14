<template lang="html">
    <Breadcrumb title="Account Settings" />

    <div class="main-content-wrapper mt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <v-card elevation="2" class="rounded-lg overflow-hidden">
                        <div class="bg-blue-darken-4 px-4 py-3 d-flex align-items-center">
                            <v-icon color="white" class="me-2">mdi-cog-outline</v-icon>
                            <h5 class="mb-0 text-white font-weight-bold">Update Profile Settings</h5>
                        </div>

                        <v-card-text class="p-4 p-md-5">
                            <form enctype="multipart/form-data" @submit.prevent="handleSubmit">
                                <div class="row g-4">

                                    <div class="col-12">
                                        <h6 class="text-blue-darken-4 fw-bold border-bottom pb-2 mb-3">
                                            <i class="fas fa-user-circle me-2"></i>Personal Information
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label custom-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i
                                                    class="fas fa-user text-muted"></i></span>
                                            <input type="text" v-model="form.name"
                                                class="form-control custom-input border-start-0"
                                                placeholder="Enter full name">
                                        </div>
                                        <small v-if="errors.name" class="text-danger mt-1 d-block">{{ errors.name
                                        }}</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label custom-label">Designation</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i
                                                    class="fas fa-briefcase text-muted"></i></span>
                                            <input type="text" v-model="form.designation"
                                                class="form-control custom-input border-start-0"
                                                placeholder="e.g. Medical Specialist">
                                        </div>
                                        <small v-if="errors.designation" class="text-danger mt-1 d-block">{{
                                            errors.designation }}</small>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <h6 class="text-blue-darken-4 fw-bold border-bottom pb-2 mb-3">
                                            <i class="fas fa-address-book me-2"></i>Contact & Professional Info
                                        </h6>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label custom-label">Contact No</label>
                                        <input type="text" v-model="form.contact_no" class="form-control custom-input"
                                            placeholder="01XXXXXXXXX">
                                        <small v-if="errors.contact_no" class="text-danger mt-1 d-block">{{
                                            errors.contact_no }}</small>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label custom-label">Email Address</label>
                                        <input type="email" v-model="form.email" class="form-control custom-input"
                                            placeholder="example@mail.com">
                                        <small v-if="errors.email" class="text-danger mt-1 d-block">{{ errors.email
                                        }}</small>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label custom-label">Registration No</label>
                                        <input type="text" v-model="form.regis_no" class="form-control custom-input"
                                            placeholder="Reg-12345">
                                        <small v-if="errors.regis_no" class="text-danger mt-1 d-block">{{
                                            errors.regis_no }}</small>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label custom-label">Chamber Address</label>
                                        <textarea v-model="form.chamber_address" class="form-control custom-input"
                                            rows="2" placeholder="Enter full address"></textarea>
                                        <small v-if="errors.chamber_address" class="text-danger mt-1 d-block">{{
                                            errors.chamber_address }}</small>
                                    </div>

                                    <!-- company info -->
                                    <div class="col-md-12">
                                        <label class="form-label custom-label">Company Name</label>
                                        <input type="text" v-model="form.company_name" class="form-control custom-input"
                                            placeholder="Enter company name">
                                        <small v-if="errors.company_name" class="text-danger mt-1 d-block">{{
                                            errors.company_name }}</small>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label custom-label">Company Address</label>
                                        <textarea v-model="form.company_description" class="form-control custom-input"
                                            rows="2" placeholder="Enter company Address"></textarea>
                                        <small v-if="errors.company_description" class="text-danger mt-1 d-block">{{
                                            errors.company_description }}</small>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <h6 class="text-blue-darken-4 fw-bold border-bottom pb-2 mb-3">
                                            <i class="fas fa-image me-2"></i>Media & Branding
                                        </h6>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="upload-box p-3 border rounded bg-light">
                                            <label class="form-label fw-bold">Official Logo</label>
                                            <input type="file" class="form-control mb-2"
                                                @change="e => form.logo = e.target.files[0]">
                                            <div class="preview-wrapper mt-2">
                                                <img v-if="preview.logo" :src="preview.logo"
                                                    class="img-thumbnail shadow-sm" style="max-height: 80px;">
                                                <div v-else class="text-muted small">No logo uploaded</div>
                                            </div>
                                            <small v-if="errors.logo" class="text-danger d-block">{{ errors.logo
                                            }}</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="upload-box p-3 border rounded bg-light">
                                            <label class="form-label fw-bold">Doctor's Signature</label>
                                            <input type="file" class="form-control mb-2"
                                                @change="e => form.signature = e.target.files[0]">
                                            <div class="preview-wrapper mt-2">
                                                <img v-if="preview.signature" :src="preview.signature"
                                                    class="img-thumbnail shadow-sm" style="max-height: 80px;">
                                                <div v-else class="text-muted small">No signature uploaded</div>
                                            </div>
                                            <small v-if="errors.signature" class="text-danger d-block">{{
                                                errors.signature }}</small>
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Breadcrumb from '@/components/common/Breadcrumb.vue'
import { useStoreForm } from '@/composables/useStoreForm'
import { toast } from 'vue3-toastify'

const { image_path } = usePaths()

const preview = ref({
    logo: '',
    signature: ''
})

const {
    form,
    errors,
    isSubmitting,
    submit
} = useStoreForm({
    name: '',
    designation: '',
    contact_no: '',
    email: '',
    chamber_address: '',
    regis_no: '',
    footer_content: '',
    company_name: '',
    company_description: '',
    logo: null,
    signature: null,
})

const handleSubmit = async () => {
    try {
        const resp = await submit('/apis/setting-store-update', 'post')
        if (resp?.status) {
            toast.success(resp.message)
            loadSettings()
        }
    } catch (e) { }
}

const loadSettings = async () => {
    const { data } = await axios.get('/apis/settings')
    if (data.status) {
        Object.assign(form, data.data_list)
        preview.value.logo = data.data_list.logo_url
        preview.value.signature = data.data_list.signature_url
    }
}

onMounted(loadSettings)
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

.input-group-text {
    color: #64748b;
}
</style>
