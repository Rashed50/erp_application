<template>
    <div class="col-md-12">
        <!-- <div class="card">
            <div class="card-header mt-2">
                <h5 class="card-title">Search Component</h5>
            </div>
        </div> -->

        <div v-if="activities.length > 0" class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <div
                        id="alltableinfo_wrapper"
                        class="dataTables_wrapper dt-bootstrap4 no-footer"
                    >
                        <!-- Search & Per Page -->
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length">
                                    <label
                                        ><strong>Show </strong>
                                        <select
                                            v-model="perPage"
                                            class="custom-select custom-select-sm form-control form-control-sm"
                                            @change="fetchDailyActivities"
                                        >
                                            <option value="2">2</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <strong> entries</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_filter text-md-end">
                                    <label
                                        ><strong>Search:</strong>
                                        <input
                                            type="search"
                                            v-model="tableSearch"
                                            class="form-control form-control-sm"
                                            placeholder="Search..."
                                            @input="debouncedSearch"
                                        />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Table Date Showing -->
                        <div class="row">
                            <div class="col-sm-12">
                                <table
                                    class="table table-bordered table-hover custom_table mb-0"
                                >
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>Emp.ID</th>
                                            <th>Type</th>
                                            <th>Subject</th>
                                            <th>Details</th>
                                            <th>Responsible Person</th>
                                            <th>Status</th>
                                            <th>Status Remarks</th>
                                            <th>File</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                activity, index
                                            ) in activities"
                                            :key="activity.da_auto_id"
                                        >
                                            <td>
                                                {{
                                                    (pagination.current_page -
                                                        1) *
                                                        pagination.per_page +
                                                    index +
                                                    1
                                                }}
                                            </td>
                                            <td>
                                                {{ activity.for_emp_name }} ({{
                                                    activity.da_for_emp_id
                                                }})
                                            </td>
                                            <td>{{ activity.da_type_name }}</td>
                                            <td>{{ activity.da_subject }}</td>
                                            <td>{{ activity.da_details }}</td>
                                            <td>
                                                {{
                                                    activity.responsible_emp_name
                                                }}
                                                ({{
                                                    activity.responsible_emp_id
                                                }})
                                            </td>
                                            <td>
                                                <span
                                                    :class="`badge bg-${getStatusBadge(
                                                        activity.da_status
                                                    )}`"
                                                >
                                                    {{
                                                        getStatusText(
                                                            activity.da_status
                                                        )
                                                    }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ activity.da_status_remarks }}
                                            </td>
                                            <td>
                                                <a
                                                    v-if="
                                                        activity.da_attached_file
                                                    "
                                                    :href="
                                                        getFileUrl(
                                                            activity.da_attached_file
                                                        )
                                                    "
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i
                                                        class="fas fa-download"
                                                    ></i>
                                                </a>
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-primary"
                                                    @click="
                                                        openEditModal(
                                                            activity
                                                        )
                                                    "
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="d-flex justify-content-between align-items-center mt-3"
                        >
                            <div>
                                Showing {{ pagination.from || 0 }} to
                                {{ pagination.to || 0 }} of
                                {{ pagination.total || 0 }} entries
                            </div>
                            <ul class="pagination mb-0">
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: !pagination.prev_page_url,
                                    }"
                                >
                                    <a
                                        href="#"
                                        class="page-link"
                                        @click.prevent="
                                            changePage(
                                                pagination.current_page - 1
                                            )
                                        "
                                    >
                                        Previous
                                    </a>
                                </li>
                                <li
                                    class="page-item"
                                    v-for="page in pagination.last_page"
                                    :key="page"
                                    :class="{
                                        active:
                                            page === pagination.current_page,
                                    }"
                                >
                                    <a
                                        href="#"
                                        class="page-link"
                                        @click.prevent="changePage(page)"
                                        >{{ page }}</a
                                    >
                                </li>
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: !pagination.next_page_url,
                                    }"
                                >
                                    <a
                                        href="#"
                                        class="page-link"
                                        @click.prevent="
                                            changePage(
                                                pagination.current_page + 1
                                            )
                                        "
                                    >
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="editModal"
                class="modal-backdrop"
                @click.self="closeEditModal"
            >
                <div
                    class="modal-dialog custom-modal-width modal-lg"
                    role="document"
                >
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">
                                Daily Activity Update
                            </h5>
                            <button
                                type="button"
                                class="btn-close btn-close-white"
                                @click="closeEditModal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body p-4">
                            <form
                                @submit.prevent="updateDailyActivity"
                                enctype="multipart/form-data"
                            >
                                <!-- Responsible Employee Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Responsible Person</label
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
                                                Select Responsible Person
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

                                <!-- Status Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Status</label
                                    >
                                    <div class="col-md-9">
                                        <select
                                            v-model="formData.da_status"
                                            class="form-select"
                                            required
                                        >
                                            <option value="">
                                                Select Status
                                            </option>
                                            <option value="1">Created</option>
                                            <option value="5">
                                                Transfer To Other
                                            </option>
                                            <option value="10">
                                                In Progress
                                            </option>
                                            <option value="15">
                                                Completed
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Status Remarks Field -->
                                <div class="form-group row mb-3">
                                    <label class="col-md-3 col-form-label"
                                        >Status Remarks</label
                                    >
                                    <div class="col-md-9">
                                        <textarea
                                            v-model="formData.da_status_remarks"
                                            class="form-control"
                                            rows="3"
                                            placeholder="Enter status remarks"
                                            required
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button
                                        type="button"
                                        class="btn btn-secondary me-2"
                                        @click="closeEditModal"
                                    >
                                        <i class="fas fa-times me-2"></i>
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="isSubmitting"
                                    >
                                        <i class="fas fa-save me-2"></i>
                                        <span v-if="isSubmitting"
                                            >Updating...</span
                                        >
                                        <span v-else>Update</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activities.length === 0" class="card mt-3">
            <div class="card-body text-center">
                No daily activities found for this employee
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, reactive } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import { EmployeeSearchAPI, DailyActivitiesListAPI, DailyActivitiesUpdateAPI } from "../../routes";
// import { EmployeeSearchAPI } from "../../routes";

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

const isLoading = ref(false);
const searchInput = ref("");
const hasSearched = ref(false);
const empRecord = ref(null);
const activities = ref([]);
const tableSearch = ref("");
const perPage = ref(10);
const currentPage = ref(1);

//! For Edit Modal
const editModal = ref(false);
const isSubmitting = ref(false);
const editingActivity = ref(null);

const pagination = ref({
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
    total: 0,
    prev_page_url: null,
    next_page_url: null,
});

const formData = reactive({
    da_responsible_emp: "",
    da_status: "",
    da_status_remarks: "",
});

// Debounced search function
let searchTimeout = null;

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage.value = 1;
        fetchDailyActivities();
    }, 500);
};

const fetchDailyActivities = async () => {
    try {
        isLoading.value = true;

        // Prepare API parameters
        const params = {
            page: currentPage.value,
            per_page: perPage.value,
            search: tableSearch.value,
            employee_id: empRecord.value ? empRecord.value.employee_id : null,
        };

        const response = await axios.get(DailyActivitiesListAPI, { params });

        if (response.data.success) {
            activities.value = response.data.data;

            // Update pagination info
            if (response.data.pagination) {
                pagination.value = response.data.pagination;
            }
        } else {
            activities.value = [];
            toast.error("Failed to fetch daily activities");
        }
    } catch (error) {
        console.error("Error fetching daily activities:", error);
        activities.value = [];
        toast.error("Error fetching daily activities");
    } finally {
        isLoading.value = false;
    }
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    currentPage.value = page;
    fetchDailyActivities();
};

const getStatusText = (status) => {
    if (typeof status === "number" || !isNaN(status)) {
        switch (parseInt(status)) {
            case 1:
                return "Created";
            case 2:
                return "Transfer To Other";
            case 3:
                return "In Progress";
            case 4:
                return "Completed";
            default:
                return "Unknown";
        }
    }
    return status;
};

const getStatusBadge = (status) => {
    const statusText = getStatusText(status);

    switch (statusText.toLowerCase()) {
        case "created":
            return "primary";
        case "transfer to other":
            return "info";
        case "in progress":
            return "warning";
        case "completed":
            return "success";
        case "cancelled":
            return "danger";
        case "pending":
            return "warning";
        default:
            return "secondary";
    }
};

const getFileUrl = (path) => {
    return path.startsWith("http")
        ? path
        : `${window.location.origin}/storage/${path}`;
};

//! For Edit Modal --------------------------------------
const openEditModal = (activity) => {
    console.log("openEditModal", activity);
    editingActivity.value = activity.da_auto_id;

    console.log(activity.da_responsible_emp)

    formData.da_responsible_emp = activity.da_responsible_emp || "";
    formData.da_status = activity.da_status || "";
    formData.da_status_remarks = activity.da_status_remarks || "";

    editModal.value = true;
};

const closeEditModal = () => {
    editModal.value = false;
    editingActivity.value = null;
    resetForm();
};

const resetForm = () => {
    formData.da_responsible_emp = "";
    formData.da_status = "";
    formData.da_status_remarks = "";
};

const updateDailyActivity = async () => {
    try {
        isSubmitting.value = true;

        const payload = {
            da_responsible_emp: formData.da_responsible_emp,
            da_status: formData.da_status,
            da_status_remarks: formData.da_status_remarks
        };

        const response = await axios.put(
            `${DailyActivitiesUpdateAPI}/${editingActivity.value}`,
            payload
        );

        if (response.data.success) {
            toast.success('Update successful');
            closeEditModal();
            fetchDailyActivities();
        }
    } catch (error) {
        console.error('Error:', error);
        toast.error('Update failed');
    } finally {
        isSubmitting.value = false;
    }
};

// Watch for perPage changes
watch(perPage, () => {
    currentPage.value = 1;
    fetchDailyActivities();
});

onMounted(() => {
    // You can load initial data if needed
    fetchDailyActivities();
});
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
