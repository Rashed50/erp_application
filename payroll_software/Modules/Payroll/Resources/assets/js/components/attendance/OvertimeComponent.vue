<template>
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-3">
                <h4 class="mb-0 font-weight-bold">
                    <!-- <i class="fa fa-users mr-2"></i> -->
                    {{ active_form_title }}

                </h4>
            </div>
            <div class="col-md-7 text-right">
            </div>
            <div class="col-md-1 text-right">
                <button
                    @click="showInsertForm(2)"
                    class="btn btn-sm btn-outline-primary"
                >
                    Add new
                    <i
                        :class="
                            details_report
                                ? 'fas fa-caret-up'
                                : 'fas fa-caret-down'
                        "
                    ></i>
                </button>
            </div>
                <div class="col-md-1 text-right">
                <button
                    @click="showSearchForm(1)"
                    class="btn btn-sm btn-outline-primary"
                >
                    Search
                    <i
                        :class="
                            summary_report
                                ? 'fas fa-caret-up'
                                : 'fas fa-caret-down'
                        "
                    ></i>
                </button>
            </div>
        </div>

    <div v-if="showform==1">
        <div class="card mb-4">
            <!-- <div class="card-header">Upload Overtime Sheet</div> -->
            <div class="card-body">
                <form @submit.prevent="uploadFile" enctype="multipart/form-data" class="row">
                    <div class="col-md-4">
                        <label>Project</label>
                        <select v-model="uploadForm.project_id" class="form-select" required>
                            <option value="">Select Project</option>
                            <option v-for="proj in projects" :key="proj.proj_id" :value="proj.proj_id">
                                {{ proj.proj_name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" v-model="uploadForm.ot_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>File (PDF/Image)</label>
                        <input type="file" @change="handleFileUpload" class="form-control" accept=".pdf,.jpg,.png">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mt-4" :disabled="loading">
                            {{ loading ? 'Uploading...' : 'Upload' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div v-if="showform==2">
         <div class="card mb-4">
            <!-- <div class="card-header">Search Records</div> -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <select v-model="searchQuery.project_id" class="form-select">
                            <option value="">Select Project</option>
                            <option v-for="proj in projects" :key="proj.proj_id" :value="proj.proj_id">
                                {{ proj.proj_name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="date" v-model="searchQuery.ot_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button @click="fetchRecords" class="btn btn-success">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>OT Date</th>
                        <th>Project Name</th>
                        <th>Inserted By</th>
                        <th>Created At</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in records" :key="row.ots_auto_id">
                        <td>{{ row.ots_auto_id }}</td>
                        <td>{{ row.ot_date }}</td>
                        <td>{{ row.proj_name }}</td>
                        <td>{{ row.inserted_by_name }}</td>
                        <td>{{ formatDate(row.created_at) }}</td>
                        <td>
                            <a v-if="row.ot_file" :href="getS3Url(row.ot_file)" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i></a>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" @click="deleteRecord(row.ots_auto_id)"> <i class="fa fa-trash"></i> </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-if="showform==3">
    </div>




    </div>
</template>

<script>
import axios from 'axios';
import { toast } from "vue3-toastify";
import { OTSheetStore,OTSheetSearch,OTSheetDelete } from '../../routes';

 const today = new Date().toISOString().substr(0, 10);

export default {
    props: ['projects'], // Projects passed from Laravel controller
    data() {
        return {
            loading: false,
            records: [],
            showform : null,
            active_form_title:'',
            uploadForm: {
                project_id: '',
                ot_date: today,
                ot_file: null
            },
            searchQuery: {
                project_id: '',
                ot_date: today
            },
            // Get S3 Base URL from .env via Vite/Mix
            s3BaseUrl: import.meta.env.VITE_S3_BUCKET_URL
        }
    },
    methods: {
        formatDate(dateStr) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString(undefined, options);
        },
        showInsertForm(formNumber) {
            this.showform = 1;
            this.active_form_title = 'Overtime New File Upload';
        },
        showSearchForm(formNumber) {
            this.showform = 2;
            this.active_form_title = 'Search';
        },
        handleFileUpload(event) {
            this.uploadForm.ot_file = event.target.files[0];
        },
        async uploadFile() {
            this.loading = true;
            let formData = new FormData();
             if (this.uploadForm.project_id == null || this.uploadForm.project_id === '' || this.uploadForm.ot_date === '') {
                    toast.error("Please select a project and date");
                    return;
                }
            else if (this.uploadForm.ot_file == null) {
                    toast.error("Please select a file to upload");
                    return;
                }

            formData.append('project_id', this.uploadForm.project_id);
            formData.append('ot_date', this.uploadForm.ot_date);
            if(this.uploadForm.ot_file) formData.append('ot_file', this.uploadForm.ot_file);

            try {
                await axios.post(OTSheetStore, formData);
                toast.success("File uploaded successfully");
                this.uploadForm = {
                    project_id: '',
                    ot_date: today,
                    ot_file: null
                };

            } catch (error) {
                toast.error("Upload failed");
            } finally {
                this.loading = false;
            }
        },
        async fetchRecords() {
            const params = this.searchQuery;
            const response = await axios.get(OTSheetSearch, { params });
            if (response.data.success) {
                this.records = response.data.data;
            } else {
                toast.error("Failed to fetch records");
            }
        },
        async deleteRecord(id) {
            if (!confirm("Are you sure you want to delete this record?")) return;

            try {
                await axios.delete(`${OTSheetDelete}/${id}`);
                toast.success("Record deleted successfully");
                this.fetchRecords();
            } catch (error) {
                toast.error("Failed to delete record");
            }
        },
        getS3Url(path) {
            return this.s3BaseUrl + path;
        }
    },
    mounted() {
        this.fetchRecords();
    }
}
</script>
