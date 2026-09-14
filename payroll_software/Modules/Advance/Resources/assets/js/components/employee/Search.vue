<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header mt-2">
                <h5 class="card-title">Employee Advance Records</h5>
            </div>
            <div class="row form-group">
                <div class="col-md-2"></div>
                <label class="col-md-2 control-label">Searching by</label>
                <div class="col-md-3">
                    <select v-model="searchBy" class="form-select">
                        <option value="employee_id">Employee ID</option>
                        <option value="akama_no">Iqama Number</option>
                        <option value="passfort_no">Passport</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <form @submit.prevent="searchAdvanceRecords" class="d-flex">
                        <input type="text" v-model="searchInput" ref="searchInput"
                            placeholder="Enter ID/Iqama/Passport No" class="form-control" required
                            @keyup.enter="searchAdvanceRecords" />
                        <button type="submit" class="btn btn-primary ms-2">SEARCH</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Employee Advance Records Table -->
        <div v-if="showResults" class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover custom_table mb-0">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Emp.ID</th>
                                <th>Name</th>
                                <th>Iqama</th>
                                <th>Salary</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Purpose</th>
                                <th>Remarks</th>
                                <th>By</th>
                                <th>At</th>
                                <th>File</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(record, index) in advanceRecords" :key="record.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ record.employee_id }}</td>
                                <td>{{ record.employee_name }}</td>
                                <td>{{ record.akama_no || record.passport_no }}</td>
                                <td>{{ record.hourly_employee == 1 ? 'Hourly' : 'Basic Salary' }}</td>
                                <td>{{ record.adv_amount }}</td>
                                <td>{{ formatDate(record.date) }}</td>
                                <td>{{ record.purpose }}</td>
                                <td>{{ record.adv_remarks }}</td>
                                <td>{{ record.inserted_by }}</td>
                                <td>{{ formatDateTime(record.created_at) }}</td>
                                <td>
                                    <div class="text-center">
                                        <a v-if="record.advance_paper" :href="getAttachmentUrl(record.advance_paper)"
                                            target="_blank" >  <i class="fa fa-eye text-info fs-7" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <a @click.prevent="openEditModal(record)" v-if="hasPermission('employee_advance_edit')">
                                            <i class="fa fa-pencil-square-o fs-7 text-warning" aria-hidden="true"></i>
                                        </a>

                                        <a class="mt-2" @click.prevent="confirmDelete(record)" v-if="hasPermission('employee_advance_delete')">
                                            <i class="fa fa-trash fa-md delete_icon" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                            <tr v-if="advanceRecords.length === 0">
                                <td colspan="13" class="text-center">No advance records found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="editModal" class="modal-backdrop" @click.self="closeModal">
            <div class="modal-dialog custom-modal-width" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #4D55CC;">
                        <h5 class="modal-title" style="color: white;">Employee Advance Update</h5>
                        <button type="button" class="close" @click="closeModal">
                            <span style="color: white;" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="updateAdvanceRecord">
                            <input type="hidden" v-model="editForm.id">
                            <input type="hidden" v-model="editForm.emp_auto_id">

                            <div class="form-group row mb-3">
                                <label class="col-md-3 col-form-label">Purpose:</label>
                                <div class="col-md-9">
                                    <select v-model="editForm.adv_purpose_id" class="form-select" required>
                                        <option value="">Select Purpose</option>
                                        <option v-for="pur in data.purpose" :key="pur.id" :value="pur.id">
                                            {{ pur.purpose }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-md-3 col-form-label">Amount: <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" v-model="editForm.adv_amount" class="form-control"
                                        placeholder="Enter Amount" step="1" min="1" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-md-3 col-form-label">Advance Date: <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="date" v-model="editForm.adv_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Remarks:</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="editForm.adv_remarks" class="form-control"
                                        placeholder="Enter Remarks">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-sm-3 control-label">Upload:</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                 Browse… <input type="file" @change="handleFileUpload" ref="fileInput" accept="image/*,.pdf">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" :value="fileName" readonly />
                                    </div>
                                </div>

                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" @click="closeModal">Cancel</button>
                                <button type="submit" class="btn"
                                    style="background: #4D55CC; color: white;">Update</button>
                            </div>
                        </form>

                        <div class="form-group row custom_form_group">
                                <div class="col-sm-12">
                                    <!-- Image Preview -->
                                    <img v-if="fileType === 'image'"
                                        :src="uploadedFilePreview"
                                        class="upload_image"
                                        style="width:100%; max-height:300px; object-fit:contain;" />
                                    <!-- PDF Preview -->
                                    <embed v-if="fileType === 'pdf'"
                                        :src="uploadedFilePreview"
                                        type="application/pdf"
                                        width="100%"
                                        height="300px" />

                                    <!-- Other File -->
                                    <div v-if="fileType === 'other'" class="text-center">
                                        <p>Selected File: <strong>{{ fileName }}</strong></p>
                                    </div>

                                </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Swal from "sweetalert2";
import { inject } from 'vue';
import { EmployeeAdvanceList, deleteEmployeeAdvance, updateEmployeeAdvance } from "../../routes.js";
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
    props: {
        data: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            searchBy: "employee_id",
            searchInput: "",
            showResults: false,
            advanceRecords: [],
            isLoading: false,

            editModal: false,
            editForm: {
                id: null,
                emp_auto_id: null,
                adv_purpose_id: '',
                adv_amount: '',
                installes_month: 1,
                adv_date: new Date().toISOString().slice(0, 10),
                adv_remarks: ''
            },
            is_new_file_uploaded: false,
            uploadedFilePreview: '',
            fileType: '',
            fileName: '',
        }
    },

    methods: {
        async searchAdvanceRecords() {
            if (!this.searchInput) {
                toast.error("Please enter search criteria");
                return;
            }

            this.isLoading = true;

            try {
                const response = await axios.post(EmployeeAdvanceList, {
                    search_by: this.searchBy,
                    employee_searching_value: this.searchInput
                });

                if (response.data.success) {
                    this.advanceRecords = response.data.data;
                    this.showResults = true;
                } else {
                    toast.error(response.data.message || "No records found");
                    this.advanceRecords = [];
                    this.showResults = false;
                }
            } catch (error) {
                console.error("Error fetching advance records:", error);
                toast.error("An error occurred while fetching records");
                this.advanceRecords = [];
                this.showResults = false;
            } finally {
                this.isLoading = false;
            }
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },

        formatDateTime(dateTimeString) {
            if (!dateTimeString) return 'N/A';
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateTimeString).toLocaleDateString(undefined, options);
        },

        async confirmDelete(record) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the advance record permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    // Replace with your actual delete endpoint and payload
                    const response = await axios.get(`${deleteEmployeeAdvance}/${record.id}`);

                    if (response.data.success) {
                        // Remove deleted record from list
                        // this.advanceRecords = this.advanceRecords.filter(r => r.id !== record.id);
                        toast.success('Record deleted successfully');
                    } else {
                        // toast.error(response.data.message || "Failed to delete record");
                    }
                } catch (error) {
                    // console.error("Delete error:", error);
                    // toast.error("An error occurred while deleting");
                }

                toast.success('Record deleted successfully');
                this.searchAdvanceRecords()
            }
        },
        getAttachmentUrl(path)
        {

            return (import.meta.env.VITE_AWS_S3_ENDPOINT + path);
        },


        openEditModal(record) {

            this.editModal = true;
            this.editForm = {
                id: record.id,
                emp_auto_id: record.emp_auto_id,
                adv_purpose_id: record.adv_purpose_id,
                adv_amount: record.adv_amount,
                installes_month: record.installes_month,
                adv_date: record.date,
                adv_remarks: record.adv_remarks
            };
            // Set the uploaded file preview and type based on the selected record's advance_paper
            this.uploadedFilePreview = record.advance_paper ? this.getAttachmentUrl(record.advance_paper) : '';
            this.fileType = record.advance_paper ? (record.advance_paper.endsWith('.pdf') ? 'pdf' : 'image') : '';
            this.fileName = record.advance_paper ? record.advance_paper.split('/').pop() : '';

            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.editModal = false;
            // Restore body scrolling
            document.body.style.overflow = '';
        },

        async updateAdvanceRecord() {
            try {
                const formData = new FormData();
                formData.append('id', this.editForm.id);
                formData.append('emp_auto_id', this.editForm.emp_auto_id);
                formData.append('adv_purpose_id', this.editForm.adv_purpose_id);
                formData.append('adv_amount', this.editForm.adv_amount);
                formData.append('installes_month', 1);
                formData.append('adv_date', this.editForm.adv_date);
                formData.append('adv_remarks', this.editForm.adv_remarks);
                // Append the file only if a new file has been uploaded
                if (this.is_new_file_uploaded && this.$refs.fileInput.files[0]) {
                    formData.append('advance_paper', this.$refs.fileInput.files[0]);
                    formData.append
                }

                const response = await axios.post(updateEmployeeAdvance, formData,{
                                    headers: {
                                        'Content-Type': 'multipart/form-data'
                                    }
                                });


                if (response.data.success) {
                    toast.success(response.data.message || "Record updated successfully");
                    this.closeModal();
                    this.searchAdvanceRecords(); // Refresh the list
                } else {
                    toast.error(response.data.message || "Failed to update record");
                }
            } catch (error) {
                console.error("Update error:", error);
                toast.error(error.response?.data?.message || "An error occurred while updating");
            }
        },

         async viewFile(fileName) {
            try {
                // 1. Request the file as a blob
                const response = await axios({
                    url: `api/get-file-preview-url/${fileName}`,
                    method: 'GET',

                    });

                        // 3. Open the URL in a new browser tab
                window.open(response.data.preview_url, '_blank');


            } catch (error) {
                console.error("Could not open file:", error);
                alert("Permission denied or file not found.");
            }
        },
        handleFileUpload(event) {
             console.log("File upload event fired:");
             // if file is selected, set is_new_file_uploaded to true and server will upload the new file, otherwise keep the old file
             this.is_new_file_uploaded = true;
             const file = event.target.files[0];
             if (!file) {
                 this.uploadedFilePreview = '';
                 this.fileType = '';
                 this.fileName = '';
                 return;
             }

                this.fileName = file.name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploadedFilePreview = e.target.result;
                    if (file.type.startsWith('image/')) {
                        this.fileType = 'image';
                    }
                    else if (file.type === 'application/pdf') {
                        this.fileType = 'pdf';
                    }
                    else {
                        this.fileType = 'other';
                    }
                };

                reader.readAsDataURL(file);
        },

    },



    mounted() {
        this.$nextTick(() => {
            this.$refs.searchInput.focus();
        });
    }
}
</script>

<style scoped>
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
    max-width: 400px !important;
    width: 100%;
}
</style>
