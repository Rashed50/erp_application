<template>
  <div>
    <!-- Search Component -->
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <!-- Update Employee Sponsor Card -->
    <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">
      
      <!-- Card Header -->
      <div class="card-header bg-light text-center py-3" style="border-bottom: 1px solid #ededed;">
        <h5 class="mb-0 text-secondary font-weight-bold" style="letter-spacing: 0.5px; font-size: 1.1rem;">
          UPDATE EMPLOYEE SPONSOR
        </h5>
      </div>
      
      <!-- Card Body & Form -->
      <div class="card-body px-5 py-4">
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />
          
          <!-- Optional reactive tracking if you need to pass down previous sponsor values -->
          <input type="hidden" v-model="form.emp_prev_sponsor_id" />

          <!-- 1. Sponsor Name Dropdown Field -->
          <div class="row mb-4 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Sponsor Name: <span class="text-danger">*</span>
            </label>
            <div class="col-sm-6">
              <select class="form-select" v-model="form.sponsor_id" required>
                <option value="">Select Sponsor Name</option>
                <option v-for="sponsor in sponsors_list" :key="sponsor.spons_id" :value="sponsor.spons_id">
                  {{ sponsor.spons_name }}
                </option>
              </select>
            </div>
          </div>

          <hr class="text-muted my-4">

          <!-- Action Button Footer -->
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
import { updateSponserAPI } from '../../routes.js';

export default {
  components: { EmployeeSearchComponent },

  props: {
    sponsors_list: {
      type: Object,
      required: true
    }
  },
  mounted() {

    console.log("Sponsor list in sponsor componentdsfsd", this.sponsors_list)
  },

  data() {
    return {
      searched_employee: null,
      is_saving: false,
        form: {
        emp_auto_id: '',
        sponsor_id:'',
        previous_sponsor_id:'',

      }
    }
  },

  methods: {


    handleSearchingResult(employee) {
      if (employee == null) return;

        this.searched_employee = employee;
        this.form.emp_auto_id = employee.emp_auto_id;
        this.form.previous_sponsor_id = employee.sponsor_id;
        this.form.sponsor_id = employee.sponsor_id; // selected the current sponsor by default in the dropdown
       // console.log('Data Received Successfully', employee);
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
        formData.append('emp_current_sponsor', this.form.sponsor_id);
        formData.append('emp_prev_sponsor_id', this.form.previous_sponsor_id);

        const response = await axios.post(updateSponserAPI, formData, {
        });

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
