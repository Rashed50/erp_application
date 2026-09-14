<template>
  <div>
    <!-- Search Component -->
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <!-- Update Employee Accommodation Info Card -->
    <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

      <!-- Card Header -->
      <div class="card-header bg-light text-center py-3" style="border-bottom: 1px solid #ededed;">
        <h5 class="mb-0 text-secondary font-weight-bold" style="letter-spacing: 0.5px; font-size: 1.1rem;">
          UPDATE EMPLOYEE ACCOMMODATION INFO
        </h5>
      </div>

      <!-- Card Body & Form -->
      <div class="card-body px-5 py-4">
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />

          <!-- 1. Email Address Field -->
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Email:
            </label>
            <div class="col-sm-6">
              <input type="email" placeholder="Input Email Address" class="form-control" v-model="form.email">
            </div>
          </div>

          <!-- 2. Absher Mobile Number Field (Required) -->
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Absher Mobile No: <span class="text-danger">*</span>
            </label>
            <div class="col-sm-6">
              <!-- input type changed to "tel" for better browser and country code support -->
              <input type="tel" placeholder="Input Phone Number Here" required class="form-control"
                v-model="form.mobile_no_up">
            </div>
          </div>

          <!-- 3. Secondary Mobile Number Field -->
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Mobile Number 2:
            </label>
            <div class="col-sm-6">
              <input type="tel" placeholder="Input Alternative Mobile Number" class="form-control"
                v-model="form.phone_no_up">
            </div>
          </div>

          <!-- 4. Home Country Contact Field -->
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Contact No (Home):
            </label>
            <div class="col-sm-6">
              <input type="tel" placeholder="Input Home Country Mobile Number" class="form-control"
                v-model="form.country_phone_no">
            </div>
          </div>

          <!-- 5. Villa/Accommodation Selection Field -->
          <div class="row mb-4 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Villa Name:
            </label>
            <div class="col-sm-6">
              <select class="form-select" v-model="form.living_building_id">
                <option value="">Select Villa Name</option>
                <option v-for="accommodation in accommodations" :key="accommodation.ofb_id"
                  :value="accommodation.ofb_id">
                  {{ accommodation.ofb_name }}
                </option>
              </select>
            </div>
          </div>

          <hr class="text-muted my-4">

          <!-- Action Button Footer -->
          <div class="d-flex justify-content-end me-sm-5 pe-md-4">
            <button type="submit" :disabled="is_saving" class="btn text-white px-4 py-2"
              style="background-color: #2c3e50; border-radius: 4px; font-weight: 500;">
              <i v-if="is_saving" class="fas fa-spinner fa-spin me-2"></i>
              {{ is_saving ? 'Updating...' : 'Update Information' }}
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
import { AccommodationUpdateAPI } from '../../routes.js';

export default {
  components: { EmployeeSearchComponent },
  props: {
    accommodations: {
      type: Object,
      requried: true
    }
  },

  data() {
    return {
      searched_employee: null,
      is_saving: false,

      form: {
        emp_auto_id: '',
        email: '',
        mobile_no_up: '',
        phone_no_up: '',
        living_building_id: '',
        country_phone_no: '',

      }
    }
  },

  methods: {


    handleSearchingResult(employee) {
      if (employee == null) return;



      this.searched_employee = employee;
      this.form.emp_auto_id = employee.emp_auto_id;
      this.form.mobile_no_up = employee.mobile_no;
      this.form.phone_no_up = employee.phone_no;
      this.form.email = employee.email;
      this.form.country_phone_no = employee.country_phone_no;
      this.form.living_building_id = employee.accomd_ofb_id;


      console.log('Data Received Successfully', employee);
    },

    async submitForm() {
      try {
        if (!this.form.emp_auto_id) {
          toast.error("Please Search an Employee then Try to update ");
          return;
        }
        this.is_saving = true;
        let formData = new FormData();

        formData.append('emp_auto_id', this.form.emp_auto_id);
        formData.append('living_building_id', this.form.living_building_id);
        formData.append('country_phone_no', this.form.country_phone_no);
        formData.append('phone_no_up', this.form.phone_no_up);
        formData.append('mobile_no_up', this.form.mobile_no_up);
        formData.append('email', this.form.email);


        const response = await axios.post(AccommodationUpdateAPI, formData, {
          // headers: { 'content-type': 'multipart/form-data' }

        });

        debugger;
        if (response.data.status === 200) {
          toast.success(response.data.message || 'Updated successfully!');
        } else {
          toast.error('Update failed: ' + response.data.message);
        }
      } catch (error) {
        console.error(error);
        toast.error('Error occurred , Please Try Again: ' + error.message);
      } finally {
        this.is_saving = false;
      }
    },

  }
}
</script>