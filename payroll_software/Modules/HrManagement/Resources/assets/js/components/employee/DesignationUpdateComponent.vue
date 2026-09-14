<template>
  <div>
    <!-- Search Component -->
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <!-- Update Employee Trade/Designation Card -->
    <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

      <!-- Card Header -->
      <div class="card-header bg-light text-center py-3" style="border-bottom: 1px solid #ededed;">
        <h5>
          UPDATE EMPLOYEE TRADE/DESIGNATION
        </h5>
      </div>

      <!-- Card Body & Form -->
      <div class="card-body px-5 py-4">
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />

          <!-- 1. Assign Trade Field -->
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Assign Trade: <span class="text-danger">*</span>
            </label>
            <div class="col-sm-6">
              <select class="form-select" v-model="form.emplyoeeDesignation" required>
                <option value="">Select Designation</option>
                <option v-for="designation in designations" :key="designation.catg_id" :value="designation.catg_id">
                  {{ designation.catg_name }}
                </option>
              </select>
            </div>
          </div>

          <!-- 2. Work Performance Rating Field -->
          <div class="row mb-3 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Work Performance: <span class="text-danger">*</span>
            </label>
            <div class="col-sm-6">
              <!-- Replaced native name attribute with Vue v-model binding -->
              <select class="form-select" v-model="form.empWorkActivityRating" required>
                <option value="">Select Rating</option>
                <option value="0">Undefined</option>
                <option value="5">Low</option>
                <option value="10">Medium</option>
                <option value="15">Good</option>
                <option value="20">Best</option>
              </select>
            </div>
          </div>

          <!-- 3. Multi Expert Trade Field -->
          <div class="row mb-4 align-items-center">
            <label class="col-sm-4 text-end font-weight-bold text-dark pe-3">
              Multi Expert Trade:
            </label>
            <div class="col-sm-6">
              <!-- Replaced selectpicker with standard multiple select configured for Vue v-model array -->
              <!-- <select class="form-select" v-model="form.empMultiExptDesignation" multiple style="min-height: 100px;">
                <option v-for="designation in designations" :key="'multi-' + designation.catg_id"
                  :value="designation.catg_id">
                  {{ designation.catg_name }}
                </option>
              </select> -->
              <small class="text-muted d-block mt-1">Hold Ctrl (or Cmd on Mac) to select multiple items.</small>

              <Multiselect v-model="form.empMultiExptDesignation" mode="multiple" :options="projectOptions"
                value-prop="value" track-by="label" label="label" placeholder="Select Projects" :searchable="true"
                :close-on-select="false" :clear-on-select="false" :create-option="false" :hide-selected="false"
                :caret="true" class="multiselect-blue">
                <template #tag="{ option, handleTagRemove }">
                  <div class="multiselect-tag is-user">
                    {{ option.label }}
                    <span class="multiselect-tag-remove" @click="handleTagRemove(option, $event)">
                      <i class="fa fa-times"></i>
                    </span>
                  </div>
                </template>

                <template #multiplelabel="{ values }">
                  <div class="multiselect-multiple-label">
                    {{ values.length }} Projects Selected
                  </div>
                </template>

                <template #nooptions>
                  <span class="text-muted">No projects available</span>
                </template>
              </Multiselect>
            </div>
          </div>




          <hr class="text-muted my-4">

          <!-- Action Button Footer -->
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
import Multiselect from "@vueform/multiselect";
import EmployeeSearchComponent from './SearchComponent.vue';
import { updateDesignationAPI } from '../../routes.js';

export default {
  components: {
    EmployeeSearchComponent,
    Multiselect
  },
  props: {
    designations: {
      type: Object,
      required: true
    }
  },

  computed: {
    projectOptions() {
      if (!this.designations || !Array.isArray(this.designations))
        return [];
      console.log(this.designations);
      return this.designations.map((designation) => ({
        label: designation.catg_name,
        value: designation.catg_id,
      }));
    },
  },

  data() {
    return {
      searched_employee: null,
      is_saving: false,

      form: {
        emp_auto_id: '',
        designation_id: '',
        emplyoeeDesignation: '',
        empMultiExptDesignation: [],
        empWorkActivityRating: '',


      }
    }
  },

  methods: {


    handleSearchingResult(employee) {
      if (employee == null) return;



      this.searched_employee = employee;
      this.form.emp_auto_id = employee.emp_auto_id;
      this.form.emplyoeeDesignation = employee.emplyoeeDesignation;
      this.form.empMultiExptDesignation = employee.empMultiExptDesignation;
      this.form.empWorkActivityRating = employee.empWorkActivityRating;


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
        formData.append('empWorkActivityRating', this.form.empWorkActivityRating);
        formData.append('empMultiExptDesignation', this.form.empMultiExptDesignation);
        formData.append('emplyoeeDesignation', this.form.emplyoeeDesignation);


        const response = await axios.post(updateDesignationAPI, formData, {
          // headers: { 'content-type': 'multipart/form-data' }

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