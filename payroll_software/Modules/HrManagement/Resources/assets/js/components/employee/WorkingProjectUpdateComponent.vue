<template>
  <div>
    <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

    <div v-if="searched_employee">
      <div class="card-header bg-light text-center py-3" style="border-bottom: 1px solid #ededed;">
        <h5>
          Update Employee Working Project

        </h5>
      </div>
      <div class="card-body px-5 py-4">

        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="form.emp_auto_id" />


          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Project Assigned Date :</label>
            <div class="col-sm-5">
              <input type="date" class="form-control" v-model="form.date" required />
            </div>

          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Project Name :</label>
            <div class="col-sm-5">
              <input type="hidden" v-model="emp_auto_id" value="" id="input_emp_auto_id" />
              <select class="form-select" v-model="form.projectStatus" required>
                <option value="">Select Here</option>
                <option v-for="project in project_list" :key="project.proj_id" :value="project.proj_id">
                  {{ project.proj_name }}
                </option>
              </select>
            </div>

          </div>



          <hr class="text-muted my-4">

          <div class="d-flex justify-content-end">
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
import { updateWorkingProjectAPI } from '../../routes.js';

export default {
  components: { EmployeeSearchComponent },
  props: {
    project_list: {
      type: Object,
      required: true
    }
  },
  mounted() {

  },

  data() {
    return {
      searched_employee: null,
      is_saving: false,
      projects: [],

      form: {
        emp_auto_id: '',
        projectStatus: '',
        date: '',


      }
    }
  },

  methods: {


    handleSearchingResult(employee) {
      if (employee == null) return;



      this.searched_employee = employee;
      this.form.emp_auto_id = employee.emp_auto_id;
      this.form.date = employee.date;
      this.form.projectStatus =  employee.proj_id;


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
        formData.append('projectStatus', this.form.projectStatus);
        formData.append('date', this.form.date);


        const response = await axios.post(updateWorkingProjectAPI, formData, {
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
