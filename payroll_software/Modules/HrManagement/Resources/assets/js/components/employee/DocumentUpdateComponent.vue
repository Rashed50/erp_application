<template>
  <div>
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">
      
      <div class="card-header bg-light text-center py-3" style="border-bottom: 1px solid #ededed;">
        <h5 class="mb-0 text-secondary font-weight-bold">
          EMPLOYEE AJEER DOCUMENT UPDATE
        </h5>
      </div>
      
      <div class="card-body px-5 py-4">
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />
          <input type="hidden" v-model="form.operation_type" />

          <div class="row mb-4 align-items-center">
            
            <label class="col-sm-3 text-end font-weight-bold text-dark pe-3">
              AJEER File:
            </label>
            
            <div class="col-sm-6">
              <input type="file" @change="handleAjeerFile" accept="image/*, application/pdf, .doc, .docx" class="form-control" id="ajeer_file_input" />
            </div>
            
            <div class="col-sm-3 mt-3 mt-sm-0 text-center">
              <div v-if="fileType" class="position-relative d-inline-block border rounded p-2 bg-light shadow-sm w-100" style="min-height: 118px; max-width: 140px;">
                
                <img v-if="fileType.includes('image/')" :src="filePreviewUrl" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: contain;" alt="AJEER Preview">

                <div v-else-if="fileType === 'application/pdf'" class="d-flex flex-column align-items-center justify-content-center p-1" style="height: 100px;">
                  <i class="fas fa-file-pdf text-danger fa-3x mb-1"></i>
                  <a :href="filePreviewUrl" target="_blank" class="btn btn-xs btn-outline-danger py-0" style="font-size: 10px;">View PDF</a>
                </div>

                <div v-else-if="fileType.includes('word') || fileType.includes('msword') || fileType.includes('officedocument')" class="d-flex flex-column align-items-center justify-content-center p-1" style="height: 100px;">
                  <i class="fas fa-file-word text-primary fa-3x mb-1"></i>
                  <span class="text-muted d-block text-truncate w-100" style="font-size: 9px;">{{ fileName }}</span>
                </div>

                <button type="button" @click="removeAjeerFile" class="btn btn-danger btn-sm position-absolute rounded-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; padding: 0; top: -8px; right: -8px; border: 2px solid white; font-size: 12px; font-weight: bold;">
                  &times;
                </button>
              </div>
            </div>

          </div>

          <hr class="text-muted my-4">

          <div class="d-flex justify-content-end me-sm-5 pe-md-4">
            <button type="submit" :disabled="is_saving" class="btn text-white px-4 py-2" style="background-color: #2c3e50; border-radius: 4px; font-weight: 500;">
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
import { AjeerDocumentUpdateAPI } from '../../routes.js';

export default {
  components: { EmployeeSearchComponent },
 
  data() {
    return {
      searched_employee: null,
      is_saving: false,
      
      ajeer_raw_file: null,      // Holds the raw native Javascript file stream binary
      filePreviewUrl: null,      // Holds temporary physical blob string mapping inside virtual memory
      fileType: null,            // Caches uploaded asset file type meta content
      fileName: '',              // Caches physical asset title explicitly for system fallback rendering

      form: {
        emp_auto_id: '',
        operation_type: ''
      }
    }
  },

  methods: {
    // Pipeline that hooks file upload stream dynamically to reactive layout
    handleAjeerFile(e) {
      const file = e.target.files[0];
      if (file) {
        this.ajeer_raw_file = file;
        this.fileType = file.type;
        this.fileName = file.name;
        
        // Evict outdated runtime string references safely from memory mapping
        if (this.filePreviewUrl) {
          URL.revokeObjectURL(this.filePreviewUrl);
        }
        
        // Formulate internal reference string directly from native memory
        this.filePreviewUrl = URL.createObjectURL(file);
      }
    },

    // Completely purges physical assets and nullifies tracked UI structures
    removeAjeerFile() {
      if (this.filePreviewUrl) {
        URL.revokeObjectURL(this.filePreviewUrl);
      }

      this.ajeer_raw_file = null;
      this.fileType = null;
      this.fileName = '';
      this.filePreviewUrl = null;
      
      // Wipe the structural HTML element attribute manually to synchronize changes
      const input = document.getElementById('ajeer_file_input');
      if (input) input.value = "";
    },

    // Resolves incoming event responses passed directly by Parent queries
    handleSearchingResult(employee) {
      if (employee == null) return;
      this.searched_employee = employee;
      this.form.emp_auto_id = employee.emp_auto_id;
      console.log('Data Received Successfully', employee);
    },

    // Encapsulates dataset explicitly and posts context straight to Backend endpoints
    async submitForm() {
      try {
        if (!this.form.emp_auto_id) {
          toast.error("Please Search an Employee then Try to update ");
          return;
        }
        
        this.is_saving = true;
        let formData = new FormData();
        
        formData.append('emp_auto_id', this.form.emp_auto_id);
        
        // Only append payload stream chunk if asset context was bound cleanly
        if (this.ajeer_raw_file) {
          formData.append('ajeer_file', this.ajeer_raw_file);
        }

        const response = await axios.post(AjeerDocumentUpdateAPI, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.status === 200) {
          toast.success(response.data.message || 'Updated successfully!');
          this.removeAjeerFile(); 
        } else {
          toast.error('Update failed: ' + response.data.message);
        }
      } catch (error) {
        console.error(error);
        toast.error('Error occurred, Please Try Again: ' + error.message);
      } finally {
        this.is_saving = false;
      }
    },
  }
}
</script>