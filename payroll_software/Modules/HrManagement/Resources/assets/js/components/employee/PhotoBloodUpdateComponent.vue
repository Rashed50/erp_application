<template>
  <div>
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

      <div class="card-header bg-light text-center py-3" style="border-bottom: 1px solid #ededed;">
        <h5 class="mb-0 text-secondary font-weight-bold" style="letter-spacing: 0.5px; font-size: 1.1rem;">
          UPDATE PHOTO & BLOOD GROUP
        </h5>
      </div>

      <div class="card-body px-5 py-4">
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />
          <input type="hidden" v-model="form.operation_type" />

          <div class="row mb-3 align-items-center">
            <label class="col-sm-3 text-end font-weight-bold text-dark pe-3">
              Blood Group: <span class="text-danger">*</span>
            </label>
            <div class="col-sm-6">
              <select class="form-select" v-model="form.blood_group" required>
                <option value="">Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
              </select>
            </div>
          </div>

          <div class="row mb-3 align-items-center">
            <label class="col-sm-3 text-end font-weight-bold text-dark pe-3">
              Blood Group Paper:
            </label>
            <div class="col-sm-6">
              <input type="file" @change="handleBloodGroupPaperFile"
                accept="image/*, application/pdf, .doc, .docx, .xls, .xlsx, .csv" class="form-control"
                id="blood_group_paper" />
            </div>
            <div class="col-sm-3 text-center">
              <div v-if="bloodPaperFileType" class="border rounded p-1 bg-light shadow-sm w-100"
                style="min-height: 118px; max-width: 140px; display: inline-block;">

                <img v-if="bloodPaperFileType.includes('image/')" :src="filePreviewBloodPaper" class="img-thumbnail"
                  style="width: 100px; height: 100px; object-fit: contain;" alt="Blood Paper Preview">

                <div v-else-if="bloodPaperFileType === 'application/pdf'"
                  class="d-flex flex-column align-items-center justify-content-center p-1" style="height: 100px;">
                  <i class="fas fa-file-pdf text-danger fa-3x mb-1"></i>
                  <a :href="filePreviewBloodPaper" target="_blank" class="btn btn-xs btn-outline-danger py-0"
                    style="font-size: 10px;">View PDF</a>
                </div>

                <div
                  v-else-if="bloodPaperFileType.includes('excel') || bloodPaperFileType.includes('spreadsheetml') || bloodPaperFileType.includes('csv')"
                  class="d-flex flex-column align-items-center justify-content-center p-1" style="height: 100px;">
                  <i class="fas fa-file-excel text-success fa-3x mb-1"></i>
                  <span class="text-muted d-block text-truncate w-100" style="font-size: 9px;">{{ bloodPaperFileName
                    }}</span>
                </div>

                <div
                  v-else-if="bloodPaperFileType.includes('word') || bloodPaperFileType.includes('msword') || bloodPaperFileType.includes('officedocument.wordprocessingml')"
                  class="d-flex flex-column align-items-center justify-content-center p-1" style="height: 100px;">
                  <i class="fas fa-file-word text-primary fa-3x mb-1"></i>
                  <span class="text-muted d-block text-truncate w-100" style="font-size: 9px;">{{ bloodPaperFileName
                    }}</span>
                </div>

                <div v-else class="d-flex flex-column align-items-center justify-content-center p-1"
                  style="height: 100px;">
                  <i class="fas fa-file-alt text-secondary fa-3x mb-1"></i>
                  <span class="text-muted d-block text-truncate w-100" style="font-size: 9px;">{{ bloodPaperFileName
                    }}</span>
                </div>

              </div>
            </div>
          </div>

       

          <hr class="text-muted my-4">

          <div class="d-flex justify-content-end me-sm-5 pe-md-4">
            <button type="submit" :disabled="is_saving" class="btn text-white px-4 py-2"
              style="background-color: #2c3e50; border-radius: 4px; font-weight: 500;">
              <i v-if="is_saving" class="fas fa-spinner fa-spin me-2"></i>
              {{ is_saving ? 'Updating...' : 'Update' }}
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import EmployeeSearchComponent from './SearchComponent.vue';
import { BloodGroupUpdateAPI } from '../../routes.js';

export default {
  components: { EmployeeSearchComponent },

  data() {
    return {
      searched_employee: null,
      is_saving: false,

      // Raw stream object references
      blood_group_paper: null,
      employee_photo: null,

      // Reactive window blob objects mapping
      filePreviewBloodPaper: null,
      imagePrevieEmployeePhoto: null,

      // Metadata properties tracked for handling advanced multi-format documents
      bloodPaperFileType: null,
      bloodPaperFileName: '',

      form: {
        emp_auto_id: '',
        operation_type: '',
        blood_group: ''
      }
    }
  },

  methods: {
    // Captures the file payload and isolates its unique MIME properties
    handleBloodGroupPaperFile(e) {
      const file = e.target.files[0];
      if (file) {
        this.blood_group_paper = file;
        this.bloodPaperFileType = file.type;
        this.bloodPaperFileName = file.name;

        if (this.filePreviewBloodPaper) {
          URL.revokeObjectURL(this.filePreviewBloodPaper);
        }
        this.filePreviewBloodPaper = URL.createObjectURL(file);
      }
    },



    // Handles incoming lookup triggers passed straight by Parent component instances
    handleSearchingResult(employee) {
      if (employee == null) return;
      this.searched_employee = employee;
      this.form.emp_auto_id = employee.emp_auto_id;
      this.form.blood_group = employee.blood_group || '';
      console.log('Data Received Successfully', employee);
    },

    // Packaging form values inside multipart context payload wrappers
    async submitForm() {
      try {
        if (!this.form.emp_auto_id) {
          toast.error("Please Search an Employee then Try to update");
          return;
        }

        this.is_saving = true;
        let formData = new FormData();

        formData.append('emp_auto_id', this.form.emp_auto_id);
        formData.append('blood_group', this.form.blood_group);
        formData.append('operation_type', this.form.operation_type);

        if (this.blood_group_paper) {
          formData.append('blood_group_paper', this.blood_group_paper);
        }
       

        const response = await axios.post(BloodGroupUpdateAPI, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.status === 200) {
          toast.success(response.data.message || 'Updated successfully!');

          // Clear active physical storage references safely from RAM 
          if (this.filePreviewBloodPaper) URL.revokeObjectURL(this.filePreviewBloodPaper);
          if (this.imagePrevieEmployeePhoto) URL.revokeObjectURL(this.imagePrevieEmployeePhoto);

          // Clear file input DOM views natively on success
          this.blood_group_paper = null;
          this.bloodPaperFileType = null;
          this.bloodPaperFileName = '';
          this.filePreviewBloodPaper = null;
          this.employee_photo = null;
          this.imagePrevieEmployeePhoto = null;

          document.getElementById('blood_group_paper').value = "";
        } else {
          toast.error('Update failed: ' + response.data.message);
        }
      } catch (error) {
        console.error(error);
        toast.error('Error occurred, Please Try Again: ' + error.message);
      } finally {
        this.is_saving = false;
      }
    }
  }
}
</script>