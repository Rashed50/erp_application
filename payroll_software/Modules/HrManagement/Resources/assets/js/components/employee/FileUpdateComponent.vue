<template>
    <EmployeeSearchComponent ref="employeeSearchComponent" @searching_result="handleSearchingResult">
    </EmployeeSearchComponent>

    <div class="card" v-if="searched_employee">
        <div class="card-header">
            <h3>Update Employee Files</h3>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Upload File</th>
                        <th>Preview</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(file, index) in files" :key="index">

                        <!-- ✅ Checkbox + Title -->
                        <td>
                            <input type="checkbox" v-model="file.checked" />
                            {{ file.label }} <br />

                            <span :style="file.old == null ? 'color: #ff6b6b;' : 'color: #51cf66;'"> {{ file.old == null
                                ? 'File Not Found' : 'File Exists' }} </span>
                        </td>

                        <!-- ✅ File Input -->
                        <td>
                            <input type="file" @change="handleFileUpload($event, index)" :disabled="!file.checked"
                                class="form-control" />
                        </td>

                        <!-- ✅ Preview -->
                        <td>
                            <!-- New Preview -->
                            <!-- <div v-if="file.preview">
                                <img v-if="file.isImage"
                                    :src="file.preview"
                                    style="width:80px; height:auto;" />

                                <span v-else>{{ file.fileName }}</span>
                            </div> -->

                            <!-- Old File -->
                            <!-- <div v-else>
                                <img v-if="file.old"
                                    :src="file.old"
                                    style="width:80px;" />
                            </div> -->


                            <div v-if="file.isImage && file.preview" class="image-preview">
                                <img :src="file.preview" alt="Preview" style="max-width: 200px; max-height: 200px;">
                                <p>{{ file.fileName }}</p>
                            </div>

                            <!-- PDF Preview -->
                            <div v-else-if="file.isPdf && file.preview" class="pdf-preview">
                                <embed :src="file.preview" type="application/pdf" width="200" height="200"
                                    style="border: 1px solid #ccc;">
                                <p>{{ file.fileName }}</p>
                            </div>

                            <!-- Other File Types -->
                            <div v-else class="other-file">
                                <div class="file-icon">
                                    📄 <!-- Generic file icon -->
                                </div>
                                <p>{{ file.fileName }}</p>
                            </div>

                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer text-center">
            <button class="btn btn-primary" :disabled="this.isUploading" @click="submitForm">
                <i v-if="this.isUploading" class="fas fa-spinner fa-spin me-2"></i>
                            {{ is_saving ? 'Uploading...' : 'Submit' }}
            </button>
        </div>
    </div>



</template>


<script>
import axios from 'axios';
import { inject } from 'vue';
import { toast } from 'vue3-toastify';
import { FileUploadAPI } from "../../routes.js";
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";
const auth = inject('auth')
import EmployeeSearchComponent from './SearchComponent.vue';

export default {
    setup() {
        const { hasPermission } = useAuth();
        return {
            hasPermission,
        };
    },
    components: {
        EmployeeSearchComponent,
    },
    emits: ['uploadEmployeeFilesCompletedCallBack'],
    props: {
        data: {
            type: Object,
            required: true
        },
        searched_employee_id: {
            type: Number,
            default: null
        }
    },
    mounted() {

        console.log('FileUploadComponent mounted with data:', this.searched_employee_id);
        if (this.searched_employee_id) {
            this.$nextTick(() => {
                this.$refs.employeeSearchComponent.searchBy = "employee_id";
                this.$refs.employeeSearchComponent.searchInpute = this.searched_employee_id;
                this.$refs.employeeSearchComponent.searchEmployee();
            });

        }
    },

    data() {
        return {
            searched_employee: null,
            isUploading: false,
            uploadedFilePreview: '',
            fileType: '',
            fileName: '',
            files: [
                { key: 'profile_photo', label: 'Profile Photo', checked: false, file: null, preview: null, old: '' },
                { key: 'passport_photo', label: 'Passport File', checked: false, file: null, preview: null, old: '' },
                { key: 'akama_photo', label: 'Iqama File', checked: false, file: null, preview: null, old: '' },
                { key: 'medical_report', label: 'Medical Report', checked: false, file: null, preview: null, old: '' },
                { key: 'covid_certificate', label: 'Covid Certificate', checked: false, file: null, preview: null, old: '' },
                { key: 'appoint_letter', label: 'Appointment Letter', checked: false, file: null, preview: null, old: '' },
                { key: 'blood_group_paper', label: 'Blood Group', checked: false, file: null, preview: null, old: '' },
                { key: 'educational_papers', label: 'Eduacation Certificate', checked: false, file: null, preview: null, old: '' },
                { key: 'ajeer_file', label: 'AJEER Paper', checked: false, file: null, preview: null, old: '' },
                { key: 'signature_file', label: 'Digital Signature', checked: false, file: null, preview: null, old: '' },

            ]
        }
    },

    computed: {

    },

    methods: {

        assignOldFiles() {
            this.files.forEach(f => {
                if (f.key == 'profile_photo') {
                    f.old = this.searched_employee.profile_photo || null;
                } else if (f.key == 'passport_photo') {
                    f.old = this.searched_employee.passport_photo || null;
                } else if (f.key == 'akama_photo') {
                    f.old = this.searched_employee.akama_photo || null;
                } else if (f.key == 'medical_report') {
                    f.old = this.searched_employee.medical_report || null;
                } else if (f.key == 'covid_certificate') {
                    f.old = this.searched_employee.covid_certificate || null;
                } else if (f.key == 'appoint_letter') {
                    f.old = this.searched_employee.employee_appoint_latter || null;
                } else if (f.key == 'blood_group_paper') {
                    f.old = this.searched_employee.blood_group_paper || null;
                } else if (f.key == 'educational_papers') {
                    f.old = this.searched_employee.educational_papers || null;
                }
                else if (f.key == 'ajeer_file') {
                    f.old = this.searched_employee.ajeer_file || null;
                } else if (f.key == 'signature_file') {
                    f.old = this.searched_employee.signature_file || null;
                } else {
                    f.old = this.searched_employee[f.key] || null;
                }
            });
        },

        handleSearchingResult(employee) {
            console.log('receiving searching data ');
            this.searched_employee = employee;
            console.log(this.searched_employee);
            this.emp_auto_id = this.searched_employee.emp_auto_id;
            this.assignOldFiles();

        },
        resetForm() {
            this.files = [
                { key: 'profile_photo', label: 'Profile Photo', checked: false, file: null, preview: null, old: '' },
                { key: 'passport_photo', label: 'Passport File', checked: false, file: null, preview: null, old: '' },
                { key: 'akama_photo', label: 'Iqama File', checked: false, file: null, preview: null, old: '' },
                { key: 'medical_report', label: 'Medical Report', checked: false, file: null, preview: null, old: '' },
                { key: 'covid_certificate', label: 'Covid Certificate', checked: false, file: null, preview: null, old: '' },
                { key: 'appoint_letter', label: 'Appointment Letter', checked: false, file: null, preview: null, old: '' },
                { key: 'blood_group_paper', label: 'Blood Group', checked: false, file: null, preview: null, old: '' },
                { key: 'educational_papers', label: 'Eduacation Certificate', checked: false, file: null, preview: null, old: '' },
                { key: 'ajeer_file', label: 'AJEER Paper', checked: false, file: null, preview: null, old: '' },
                { key: 'signature_file', label: 'Digital Signature', checked: false, file: null, preview: null, old: '' },
            ]
            this.searched_employee = null;

        },

        handleFileUpload(event, index) {
            const file = event.target.files[0];
            if (!file) return;

            this.files[index].file = file;
            this.files[index].fileName = file.name;

            // // preview
            // if (file.type.startsWith('image/')) {
            //     this.files[index].isImage = true;
            //     this.files[index].preview = URL.createObjectURL(file);
            // } else {
            //     this.files[index].isImage = false;
            //     this.files[index].preview = true;

            // }
            if (file.type.startsWith('image/')) {
                // Handle image files
                this.files[index].isImage = true;
                this.files[index].isPdf = false;
                this.files[index].preview = URL.createObjectURL(file);
            } else if (file.type === 'application/pdf') {
                // Handle PDF files
                this.files[index].isImage = false;
                this.files[index].isPdf = true;
                this.files[index].preview = URL.createObjectURL(file);
            } else {
                // Handle other file types
                this.files[index].isImage = false;
                this.files[index].isPdf = false;
                this.files[index].preview = null;
            }
        },
        async submitForm() {

            try {

                if (this.searched_employee == null) {
                    toast.error('Employee Not Found');
                    this.isUploading = false;
                    return;
                }
                this.isUploading = true;
                console.log(this.searched_employee.employee_id);
                let formData = new FormData();
                formData.append('emp_auto_id', this.searched_employee.emp_auto_id);

                this.files.forEach(file => {
                    if (file.checked && file.file) {
                        formData.append(file.key, file.file);
                    }
                });

                const response = await axios.post(FileUploadAPI, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                if(response.data.success == true){
                    toast.success(response.data.message);
                    this.isUploading = false;

                }else {
                    toast.error(response.data.message);
                    this.isUploading = false;
                    return;
                }
                this.$emit('uploadEmployeeFilesCompletedCallBack', true);
                this.resetForm();
            } catch (error) {
                 this.isUploading = false;
                console.error(error);
                toast.error('Upload Failed, Reload the page and try again');
            }
        },
    },


}
</script>

<style></style>
