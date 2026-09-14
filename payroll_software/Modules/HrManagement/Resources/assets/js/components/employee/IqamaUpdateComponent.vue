<template>
  <div>
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

      <!-- Header Section -->
      <div class="card-header bg-light d-flex align-items-center justify-content-between py-3"
        style="border-bottom: 1px solid #ededed;">
        <h5 class="mb-0 fw-bold text-uppercase">
          Iqama & Passport Update
        </h5>

        <button type="submit" form="iqamaPassportForm" :disabled="is_saving" class="btn text-white px-5 py-2"
          style="background-color: #2c3e50; border-radius: 4px; font-weight: 500;">
          <i v-if="is_saving" class="fas fa-spinner fa-spin me-2"></i>
          {{ is_saving ? 'Updating...' : 'Update' }}
        </button>
      </div>

      <div class="card-body px-4 py-4">
        <form id="iqamaPassportForm" @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />

          <!-- Main Layout Split into 2 Columns as per Sketch -->
          <div class="row">

            <!-- LEFT COLUMN: IQAMA SECTION -->
            <div class="col-md-6 pe-md-4 border-end">
              <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Iqama Details</h6>

              <div class="mb-3">
                <label class="form-label font-weight-bold text-dark">
                  Iqama No: <span class="text-danger">*</span>
                </label>
                <input type="text" v-model="form.akama_no_up" placeholder="Input Iqama Number Here" required
                  class="form-control" minlength="10" maxlength="10" />
              </div>

              <div class="mb-3">
                <label class="form-label font-weight-bold text-dark">
                  Iqama Expire Date: <span class="text-danger">*</span>
                </label>
                <input type="date" v-model="form.akama_expire" required class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label font-weight-bold text-dark">
                  Attachment (Upload Iqama):
                </label>
                <input type="file" @change="handleIqamaFile"
                  accept="image/*, application/pdf, .doc, .docx, .xls, .xlsx, .csv" class="form-control" />
              </div>

              <!-- Iqama Preview Box -->
              <div class="mt-3" v-if="akama_photo">
                <label class="form-label text-muted small">Attachment Preview:</label>
                <div class="border rounded p-2 bg-light shadow-sm d-flex justify-content-center align-items-center"
                  style="min-height: 250px;">
                  <img v-if="iqama_file_type === 1" :src="iqama_url" class="img-fluid rounded"
                    style="max-height: 280px; object-fit: contain;" alt="Iqama Preview">
                  <iframe v-else-if="iqama_file_type === 2" :src="iqama_url" width="100%" height="280px"></iframe>
                </div>
              </div>
            </div>

            <!-- RIGHT COLUMN: PASSPORT SECTION -->
            <div class="col-md-6 ps-md-4">
              <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Passport Details</h6>

              <div class="mb-3">
                <label class="form-label font-weight-bold text-dark">
                  Passport No:
                </label>
                <input type="text" v-model="form.passport_no_up" placeholder="Input Passport Number"
                  class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label font-weight-bold text-dark">
                  Passport Expire Date:
                </label>
                <input type="date" v-model="form.pass_expire" class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label font-weight-bold text-dark">
                  Attachment (Upload Passport):
                </label>
                <input type="file" @change="handlePassportFile"
                  accept="image/*, application/pdf, .doc, .docx, .xls, .xlsx, .csv" class="form-control" />
              </div>

              <!-- Passport Preview Box -->
              <div class="mt-3" v-if="passport_photo">
                <label class="form-label text-muted small">Attachment Preview:</label>
                <div class="border rounded p-2 bg-light shadow-sm d-flex justify-content-center align-items-center"
                  style="min-height: 250px;">
                  <img v-if="passport_file_type === 1" :src="passport_url" class="img-fluid rounded"
                    style="max-height: 280px; object-fit: contain;" alt="Passport Preview">
                  <iframe v-else-if="passport_file_type === 2" :src="passport_url" width="100%" height="280px"></iframe>
                </div>
              </div>
            </div>

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
import { IqamaAndPassportUpdateAPI } from '../../routes.js';

export default {
  components: { EmployeeSearchComponent },

  data() {
    return {
      searched_employee: null,
      is_saving: false,

      akama_photo: null,
      passport_photo: null,

      passport_file_type: 0, // 1 = image , 2 = pdf
      passport_url: null,

      iqama_file_type: 0, // 1 = image , 2 = pdf
      iqama_url: null,

      form: {
        emp_auto_id: '',
        akama_no_up: '',
        akama_expire: '',
        passport_no_up: '',
        pass_expire: '',
      }
    }
  },

  methods: {
    handlePassportFile(e) {
      const file = e.target.files[0];
      if (file) {
        this.passport_photo = file;
        const name = file.name.toLowerCase();

        if (file.type.startsWith('image/')) {
          this.passport_file_type = 1;
        } else if (file.type === 'application/pdf' || name.endsWith('.pdf')) {
          this.passport_file_type = 2;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
          this.passport_url = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    handleIqamaFile(e) {
      const file = e.target.files[0];
      if (file) {
        this.akama_photo = file;

        const name = file.name.toLowerCase();

        if (file.type.startsWith('image/')) {
          this.iqama_file_type = 1;
        } else if (file.type === 'application/pdf' || name.endsWith('.pdf')) {
          this.iqama_file_type = 2;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
          this.iqama_url = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    handleSearchingResult(employee) {
      if (employee == null) return;

      this.searched_employee = employee;
      this.form.emp_auto_id = employee.emp_auto_id;
      this.form.akama_no_up = employee.akama_no ? employee.akama_no : '';
      this.form.akama_expire = employee.akama_expire ? employee.akama_expire : '';
      this.form.passport_no_up = employee.passfort_no ? employee.passfort_no : '';
      this.form.pass_expire = employee.pass_expire ? employee.pass_expire : '';

      console.log('Data Received Successfully', employee);
    },

    async submitForm() {
      try {
        if (!this.form.emp_auto_id) {
          toast.error("Please Search an Employee then Try to update");
          return;
        }

        this.is_saving = true;
        let formData = new FormData();

        if (this.akama_photo) {
          formData.append('akama_photo', this.akama_photo);
        }
        if (this.passport_photo) {
          formData.append('passport_file', this.passport_photo);
        }

        formData.append('emp_auto_id', this.form.emp_auto_id);
        formData.append('akama_no_up', this.form.akama_no_up);
        formData.append('akama_expire', this.form.akama_expire);
        formData.append('passport_no_up', this.form.passport_no_up);
        formData.append('pass_expire', this.form.pass_expire);

        const response = await axios.post(IqamaAndPassportUpdateAPI, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.status === 200) {
          toast.success(response.data.message || 'Updated successfully!');
          this.akama_photo = null;
          this.passport_photo = null;
          this.iqama_url = null;
          this.passport_url = null;
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