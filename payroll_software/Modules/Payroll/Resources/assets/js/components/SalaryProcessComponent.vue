<template>
    <div>
        <h3>WPS Employees Salary</h3>
    </div>
    <div class="card">
        <div class="card-body">
            <form  id="wps_salary_from_excel_form" @submit.prevent="submitForm" class="form-horizontal" enctype="multipart/form-data"    >
<!-- @submit.prevent="submitForm" -->
                <div class="row">

                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Month</label>
                                <select v-model="selectedMonth" class="form-select">
                                        <option :value="null">Select Month</option>
                                        <option v-for="m in 12" :key="m" :value="m">
                                            {{ getMonthName(m) }}
                                        </option>
                                    </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Year</label>
                            <select v-model="selectedYear" class="form-select col-md-8" @change="changeYearDropdown" required>
                                <!-- <option value="" disabled>Select...</option> -->
                                <option :value="null">Select Year</option>
                                <option v-for="year in yearOptions" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="col-md-4 text-right">Project</label>
                            <select v-model="this.selectedProject" class="form-select col-md-8" @change="changeProjectDropdown" required>
                                    <option :value="null">Select Project</option>
                                <option v-for="project in projects" :key="project.proj_id"
                                    :value="project.proj_id">
                                    {{ project.proj_name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                                <label class="col-md-4 text-right">File Upload</label>
                            <!-- <input type="file" class="form-control col-md-8" @change="handleFileUpload" required> -->
                            <input type="file" ref="excelFile" @change="handleFileUpload" accept=".xlsx, .xls" required>

                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 d-flex align-items-center">

                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <!-- <button type="submit"   class="btn btn-primary">Generate WPS Salary</button> -->
                            <button @click="showWPSSalaryUsingExcel" class="btn btn-primary" :disabled="is_processing_started" >{{ is_processing_started ? 'Uploading...' : 'Salary Preview' }} </button>
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                             <button @click="downloadS3File( this.file_download_url)"  class="btn btn-primary" v-show="this.is_processing_done">
                                {{ loading ? 'Downloading...' : 'Download File' }}
                            </button>
                        </div>


                    </div>


                </div>
            </form>
            <div>
                <input type="text" v-model="file_download_url" hidden class="form-control">
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import { WPSSalaryPreviewUsingExcelfileAPI } from "../routes.js";

export default {
    
    data() {
        return {
            selectedMonth: null,
            currentDate: new Date,
            selectedYear: new Date().getFullYear(),
            yearOptions: Array.from({ length: 3 }, (_, i) => new Date().getFullYear() - i),
            selectedProject: null,
            file_download_url: '',
            is_processing_started: false,
            is_processing_done: false,
             file: null,
             file_downloading: false,
        };
    },
     props: { // Receives data from Blade
        projects: {
            type: Object,
            required: true
        }
    },
    methods: {
        changeProjectDropdown() {
            // Logic to handle project change
        },
        getMonthName(monthNumber) {
            const date = new Date();
            date.setMonth(monthNumber - 1);
            return date.toLocaleString('default', { month: 'long' });
        },
        handleFileUpload(event) {
            this.file = event.target.files[0];
        },
        async showWPSSalaryUsingExcel() {

                try {
                        if(this.selectedMonth == '' || this.selectedYear == ''  ||   this.selectedProject == '' )
                        {
                            toast.error('Input Require Field Data');
                            return;
                        }
                        else if (!this.file) {
                            toast.error("Please select a file to upload.");
                            return;
                        }
                        this.is_processing_started = true;
                        const formData = new FormData();

                        formData.append('project_id', this.selectedProject);
                        formData.append('month', this.selectedMonth);
                        formData.append('year', this.selectedYear);
                        formData.append('operation_type_id', 1);
                        formData.append('excel_file', this.file);

                        // Send POST request
                        const response = await axios.post(WPSSalaryPreviewUsingExcelfileAPI, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });


                        if (response.data.success) {
                            this.is_processing_done = true;
                            this.file_download_url = new URL(response.data.file_path);
                            toast.success(response.data.message || "Operation completed successfully!");
                           // this.resetForm();

                        } else {
                            toast.error(response.data.message || "Failed to Download WPS salary");
                        }
                         this.is_processing_started = false;
                } catch (error) {
                    toast.error('Operation Failed , Please try again');
                    this.is_processing_started = false;
                }
        },


        async downloadS3File(url) {

            try {

                    this.file_downloading = true;
                    const response = await axios({
                        url: url, // <--- Here the value is explicitly assigned
                        method: 'GET',
                        responseType: 'blob',
                    });
                    console.log('File download response:', response.data);
                    const blob = new Blob([response.data], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });

                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');

                    link.href = downloadUrl;
                    link.setAttribute('download', 'salary_report_' + this.selectedMonth + '_' + this.selectedYear + '.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    // Cleanup
                    link.remove();
                    window.URL.revokeObjectURL(downloadUrl);


                // const response = await axios({
                //     url: url,
                //     method: 'GET',
                //     responseType: 'blob', // Important for binary data
                // });

                // debugger;
                // console.log('Response Blob:', response.data);
                //  console.log('Blob URL:', blobUrl);

                // // Create a local URL for the binary data
                // const blobUrl = window.URL.createObjectURL(new Blob([response.data]));

                // // Create a hidden link and click it
                // const link = document.createElement('a');
                // link.href = blobUrl;
                // link.setAttribute('download', "Manpower_Data.xlsx"); // Name the file here
                // document.body.appendChild(link);
                // link.click();

                // // Cleanup
                // document.body.removeChild(link);
                // window.URL.revokeObjectURL(blobUrl);


            } catch (error) {
                console.log('Download failed:', error);
                toast.error('File download failed, please try again.');
            } finally {
                this.file_downloading = false;
            }
        },



    },
};

</script>
