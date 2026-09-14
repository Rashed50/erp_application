<template>
    <div class="profile-container mt-5">
        <v-card class="pa-4">
            <v-tabs v-model="activeTab" color="primary">
                <v-tab value="profile">Profile Information</v-tab>
                <v-tab value="password">Change Password</v-tab>
            </v-tabs>

            <v-window v-model="activeTab">
                <!-- Profile Information Tab -->
                <v-window-item value="profile">
                    <form @submit.prevent="updateProfile" class="mt-4">
                        <div class="row">
                            <div class="col-md-12 text-center mb-4">
                                <div class="profile-image-wrapper">
                                    <img :src="profileImagePreview || profileForm.profile_image_url"
                                        class="profile-image" alt="Profile Image">
                                    <label for="image" class="image-upload-label">
                                        <i class="fa-solid fa-camera"></i>
                                    </label>
                                    <input type="file" id="image" ref="imageInput" @change="handleImageUpload"
                                        style="display: none">
                                </div>
                                <div v-if="errors.image" class="error-msg text-center">
                                    {{ errors.image }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="profileForm.name">
                                    <div v-if="errors.name" class="error-msg">{{ errors.name }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" v-model="profileForm.email">
                                    <div v-if="errors.email" class="error-msg">{{ errors.email }}</div>
                                </div>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <v-btn type="submit" color="primary" :loading="profileSubmitting"
                                    :disabled="profileSubmitting">
                                    Update Profile
                                </v-btn>
                            </div>
                        </div>
                    </form>
                </v-window-item>

                <!-- Change Password Tab -->
                <v-window-item value="password">
                    <form @submit.prevent="updatePassword" class="mt-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Current Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" v-model="passwordForm.current_password">
                                    <div v-if="passwordErrors.current_password" class="error-msg">
                                        {{ passwordErrors.current_password }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" v-model="passwordForm.new_password">
                                    <div v-if="passwordErrors.new_password" class="error-msg">
                                        {{ passwordErrors.new_password }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Confirm New Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                        v-model="passwordForm.new_password_confirmation">
                                </div>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <v-btn type="submit" color="primary" :loading="passwordSubmitting"
                                    :disabled="passwordSubmitting">
                                    Change Password
                                </v-btn>
                            </div>
                        </div>
                    </form>
                </v-window-item>
            </v-window>
        </v-card>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { toast } from 'vue3-toastify';
import axios from 'axios';

const activeTab = ref('profile');
const profileSubmitting = ref(false);
const passwordSubmitting = ref(false);
const imageInput = ref(null);

// Profile Form
const profileForm = reactive({
    name: '',
    email: '',
    image: null,
    profile_image_url: ''
});

const errors = reactive({});

// Password Form
const passwordForm = reactive({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

const passwordErrors = reactive({});

// Profile Image Preview
const profileImagePreview = ref(null);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            toast.error('Please upload a valid image (JPEG, PNG, JPG, GIF)');
            return;
        }

        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            toast.error('Image size should be less than 2MB');
            return;
        }

        profileForm.image = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            profileImagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Fetch Profile Data
const fetchProfile = async () => {
    try {
        const response = await axios.get('/apis/profile');
        if (response.data.status) {
            profileForm.name = response.data.data.name;
            profileForm.email = response.data.data.email;
            profileForm.profile_image_url = response.data.data.image_url;
        }
    } catch (error) {
        toast.error('Failed to load profile data');
    }
};

// Update Profile
const updateProfile = async () => {
    profileSubmitting.value = true;

    // Clear previous errors
    Object.keys(errors).forEach(key => delete errors[key]);

    const formData = new FormData();
    formData.append('name', profileForm.name);
    formData.append('email', profileForm.email);

    if (profileForm.image) {
        formData.append('image', profileForm.image);
    }

    try {
        const response = await axios.post('/apis/profile/update', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.status) {
            toast.success(response.data.message);
            profileForm.profile_image_url = response.data.data.image_url;
            profileImagePreview.value = null;

            // Reset file input
            if (imageInput.value) {
                imageInput.value.value = '';
            }
        }
    } catch (error) {
        if (error.response?.status === 422) {
            const validationErrors = error.response.data.errors;
            Object.assign(errors, validationErrors);

            Object.values(validationErrors).forEach(errArr => {
                errArr.forEach(msg => toast.error(msg));
            });
        } else {
            toast.error(error.response?.data?.message || 'Failed to update profile');
        }
    } finally {
        profileSubmitting.value = false;
    }
};

// Update Password
const updatePassword = async () => {
    passwordSubmitting.value = true;

    // Clear previous errors
    Object.keys(passwordErrors).forEach(key => delete passwordErrors[key]);

    try {
        const response = await axios.post('/apis/profile/update-password', passwordForm);

        if (response.data.status) {
            toast.success(response.data.message);
            // Reset password form
            passwordForm.current_password = '';
            passwordForm.new_password = '';
            passwordForm.new_password_confirmation = '';

            location.reload(); // Reload to logout the user after password change
        }
    } catch (error) {
        if (error.response?.status === 422) {
            const validationErrors = error.response.data.errors;
            Object.assign(passwordErrors, validationErrors);

            Object.values(validationErrors).forEach(errArr => {
                errArr.forEach(msg => toast.error(msg));
            });
        } else {
            toast.error(error.response?.data?.message || 'Failed to update password');
        }
    } finally {
        passwordSubmitting.value = false;
    }
};

onMounted(() => {
    fetchProfile();
});
</script>

<style scoped>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.profile-image-wrapper {
    position: relative;
    display: inline-block;
}

.profile-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #1976d2;
}

.image-upload-label {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #1976d2;
    color: white;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-upload-label:hover {
    background: #1565c0;
    transform: scale(1.1);
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #1976d2;
}

.error-msg {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

textarea.form-control {
    resize: vertical;
}
</style>
