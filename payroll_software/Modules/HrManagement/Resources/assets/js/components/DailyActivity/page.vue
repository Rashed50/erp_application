<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-end gap-2">
                                <button
                                    @click.prevent="openSection('create')"
                                    class="btn btn-primary"
                                >
                                    <i
                                        class="fa fa-plus me-2"
                                        aria-hidden="true"
                                    ></i>
                                    Create
                                </button>
                                <button
                                    @click.prevent="openSection('search')"
                                    class="btn btn-primary"
                                >
                                    <i
                                        class="fa fa-search me-2"
                                        aria-hidden="true"
                                    ></i>
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="currentSection === 'search'" class="row">
            <SearchComponent :data="data" />
        </div>

        <div v-if="currentSection === 'create'" class="row">
            <!-- Create Modal -->
            <div
                v-if="createModal"
                class="modal-backdrop"
                @click.self="closeCreateModal"
            >
                <div
                    class="modal-dialog custom-modal-width modal-lg"
                    role="document"
                >
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">
                                Daily Activity Create
                            </h5>
                            <button
                                type="button"
                                class="btn-close btn-close-white"
                                @click="closeCreateModal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body p-4">
                            <form
                                @submit.prevent="handleSubmit"
                                enctype="multipart/form-data"
                            >
                                <!-- Subject Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Subject</label
                                    >
                                    <div class="col-md-9">
                                        <input
                                            v-model="formData.da_subject"
                                            type="text"
                                            class="form-control"
                                            required
                                            placeholder="Enter activity subject"
                                        />
                                    </div>
                                </div>

                                <!-- Details Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Details</label
                                    >
                                    <div class="col-md-9">
                                        <textarea
                                            v-model="formData.da_details"
                                            class="form-control"
                                            rows="3"
                                            placeholder="Enter activity details"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Activity Type Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Activity Type</label
                                    >
                                    <div class="col-md-9">
                                        <select
                                            v-model="formData.da_type_id"
                                            class="form-select"
                                            required
                                        >
                                            <option value="">
                                                Select Type
                                            </option>
                                            <option
                                                v-for="type in data.activity_types"
                                                :key="type.id"
                                                :value="type.id"
                                            >
                                                {{ type.da_type_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- For Employee Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >For Employee</label
                                    >
                                    <div class="col-md-9">
                                        <input
                                            v-model="formData.da_for_emp_id"
                                            @input="handleEmployeeSearchInput"
                                            type="text"
                                            class="form-control"
                                            required
                                            placeholder="Enter employee id"
                                            ref="employeeSearchInput"
                                        />
                                        <span
                                            v-if="empRecord"
                                            class="mt-2 d-block"
                                        >
                                            {{ empRecord.employee_name }} ({{
                                                empRecord.employee_id
                                            }})
                                        </span>
                                        <span
                                            v-if="showResults && !empRecord"
                                            class="text-danger mt-2 d-block"
                                        >
                                            Employee Not Found!
                                        </span>
                                    </div>
                                </div>

                                <!-- Responsible Employee Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Responsible</label
                                    >
                                    <div class="col-md-9">
                                        <select
                                            v-model="
                                                formData.da_responsible_emp
                                            "
                                            class="form-select"
                                            required
                                        >
                                            <option value="">
                                                Select Responsible
                                            </option>
                                            <option
                                                v-for="emp in data.responsibles_emp"
                                                :key="emp.employee_id"
                                                :value="emp.employee_id"
                                            >
                                                {{ emp.employee_name }} ({{
                                                    emp.employee_id
                                                }})
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- File Attachment Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Attachment</label
                                    >
                                    <div class="col-md-9">
                                        <input
                                            type="file"
                                            class="form-control"
                                            @change="handleFileUpload"
                                            ref="fileInput"
                                        />
                                        <small class="text-muted"
                                            >Max file size: 5MB</small
                                        >
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button
                                        type="button"
                                        class="btn btn-secondary me-2"
                                        @click="closeCreateModal"
                                    >
                                        <i class="fas fa-times me-2"></i>
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="isSubmitting"
                                    >
                                        <i class="fas fa-plus me-2"></i>
                                        <span v-if="isSubmitting"
                                            >Creating...</span
                                        >
                                        <span v-else>Create</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div v-if="currentSection === 'multiple'" class="row">
            <h1>Multiple</h1>
        </div> -->
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import SearchComponent from "./Search.vue";
import { EmployeeSearchAPI } from "../../routes.js";

const props = defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({
            activity_types: [],
            responsibles_emp: [],
        }),
    },
});

const currentSection = ref(null);
const showResults = ref(false);
const empRecord = ref(null);
const createModal = ref(false);
const isSubmitting = ref(false);
const fileInput = ref(null);
const activityTypes = ref([]);
const employees = ref([]);

const formData = ref({
    da_subject: "",
    da_details: "",
    da_type_id: "",
    da_for_emp_id: "",
    da_created_by: "",
    da_responsible_emp: "",
    da_status: "1",
    da_attached_file: null,
});

const resetForm = () => {
    formData.value = {
        da_subject: "",
        da_details: "",
        da_type_id: "",
        da_for_emp_id: "",
        da_responsible_emp: "",
        da_status: "1",
        da_attached_file: null,
    };
    if (fileInput.value) {
        fileInput.value.value = "";
    }
};

const openSection = (section) => {
    currentSection.value = section;

    if (section === "create") {
        openCreateModal();
    }
};

const openCreateModal = () => {
    createModal.value = true;
};
const closeCreateModal = () => {
    console.log("closeCreateModal");
    createModal.value = false;
    resetForm();
};
const handleFileUpload = (event) => {
    formData.value.da_attached_file = event.target.files[0];
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    try {
        const formPayload = new FormData();

        Object.keys(formData.value).forEach((key) => {
            if (formData.value[key] !== null) {
                formPayload.append(key, formData.value[key]);
            }
        });

        const response = await axios.post(
            "/admin/hrmanagement/daily-activity/create/",
            formPayload,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        if (response.data.success) {
            toast.success("Activity created successfully");
            closeCreateModal();
        }
    } catch (error) {
        console.error("Error creating activity:", error);
        toast.error(
            error.response?.data?.message || "Failed to create activity"
        );
    } finally {
        isSubmitting.value = false;
    }
};

const isLoading = ref(false);
const employeeSearchInput = ref(null);

const handleEmployeeSearchInput = async () => {
    empRecord.value = null;
    showResults.value = false;

    // Only search if input length > 2
    if (formData.value.da_for_emp_id.length > 2) {
        await searchEmployeeRecord();
    }
};

const searchEmployeeRecord = async () => {
    if (!formData.value.da_for_emp_id.trim()) {
        toast.error("Please enter valid search criteria");
        return;
    }

    isLoading.value = true;
    showResults.value = false;

    try {
        const response = await axios.post(EmployeeSearchAPI, {
            search_by: "employee_id",
            employee_searching_value: formData.value.da_for_emp_id.trim(),
        });

        if (response.data.success) {
            empRecord.value = response.data.findEmployee?.[0] || null;
            showResults.value = true;
        } else {
            empRecord.value = null;
            showResults.value = true;
        }
    } catch (error) {
        console.error("Error searching employee:", error);
        toast.error(
            error.response?.data?.message || "An error occurred while searching"
        );
        empRecord.value = null;
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.modal-title {
    font-weight: 600;
}

.form-control,
.form-select {
    border-radius: 0.25rem;
    padding: 0.5rem 0.75rem;
}

.btn {
    padding: 0.5rem 1.25rem;
    border-radius: 0.25rem;
    font-weight: 500;
}

.custom-modal-width {
    max-width: 600px !important;
    width: 100%;
}
</style>
