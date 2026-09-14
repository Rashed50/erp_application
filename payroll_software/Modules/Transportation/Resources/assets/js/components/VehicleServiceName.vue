<template>
    <div class="container mt-1">
      <h3 class="mb-1"> Vehicle Maintenance Title</h3>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="border p-4 rounded shadow">
        <div class="row mb-2">
          <label for="service_name" class="col-md-4 col-form-label">Maintenance Title:</label>
          <div class="col-md-8">
            <input type="text" id="service_name" class="form-control" placeholder="Enter Title Name" v-model="formData.service_name" required />
          </div>
        </div>

        <div class="row mb-2">
          <label for="ser_nam_status" class="col-md-4 col-form-label">Status:</label>
          <div class="col-md-8">
            <select id="ser_nam_status" class="form-select" v-model="formData.ser_nam_status">
              <option :value="1">Active</option>
              <option :value="0">Inactive</option>
            </select>
          </div>
        </div>

        <button type="submit" :disabled="isSaveBtnClicked" class="btn btn-primary w-100" >{{ button_title }}</button>
      </form>

      <!-- List -->
      <table class="table table-bordered mt-2">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Service Name</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(service, index) in serviceList" :key="service.ser_nam_auto_id">
            <td>{{ index + 1 }}</td>
            <td>{{ service.service_name }}</td>
            <td>
              <span class="badge" :class="service.ser_nam_status ? 'bg-success' : 'bg-danger'">
                {{ service.ser_nam_status ? "Active" : "Inactive" }}
              </span>
            </td>
            <td>
                <a class="view_btn" @click="editServiceName(index)">
                                        <i class="fa fa-pencil-square-o"></i></a>||


                <a class="delete_btn" @click="deleteItem(service.ser_nam_auto_id)">
                                <i class="fa fa-trash fa-md delete_icon"></i>
                            </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </template>

  <script>
    import axios from 'axios';
    import { ref } from 'vue';
    import { toast } from 'vue3-toastify';
    import 'vue3-toastify/dist/index.css';
    import Swal from 'sweetalert2';
    import { VehicleServicing_URLS, } from "../router.js";


  export default {
    data() {
      return {
        isSaveBtnClicked : false,
        formData: {
          service_name: "",
          ser_nam_status: 1,
        },
        serviceList: this.data_for_form.servicing_names,
        button_title:"Save",
        ser_nam_auto_id:-1,

      };
    },
    props:{
            data_for_form: {
                type: Object,
                required: true
            }
        },
    methods: {

        async submitForm() {
                try {

                    this.isSaveBtnClicked = true;
                    let formData = new FormData();

                    formData.append('service_name',this.formData.service_name);
                    if(this.ser_nam_auto_id>0){
                        formData.append('ser_nam_auto_id',this.ser_nam_auto_id);
                        formData.append('ser_nam_status',this.formData.ser_nam_status);
                    }

                    const response =  await axios.post(VehicleServicing_URLS.StoreVechicleServiceNameAPI, formData);
                    if(response.status == 201){
                        toast.success('Created Successfully');
                        this.resetFormData()
                        this.serviceList = response.data.data;
                    }
                    else if(response.status == 200){
                        toast.success(' Updated Successfully');
                        this.resetFormData()
                        this.serviceList = response.data.data;
                    }
                    else {
                        toast.error("Data not Saved, Try Again");
                    }
                    }catch (error) {
                        toast.error("Operation Failed, Try Again");
                    }finally {
                    this.isSaveBtnClicked = false;
                }
        },
        editServiceName(index) {
            this.button_title = "Update";
            const aservicename = this.serviceList[index];
            this.ser_nam_auto_id = aservicename.ser_nam_auto_id;
            this.formData =  { ...this.serviceList[index] };
        },
        resetFormData(){
            this.formData.service_name = ""
            this.formData.ser_nam_status = 1
            this.button_title ="Save"
            this.ser_nam_auto_id = -1
        },
        async deleteItem(ser_nam_auto_id) {

            // Show confirmation dialog
            const confirmDelete = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            });


            if (!confirmDelete.isConfirmed) return;

             let toastId;
            try {
                // Show loading toast
                toastId = toast.loading('Deleting ...', {
                    position: toast.POSITION.TOP_RIGHT
                });

                const response = await axios.delete(`${VehicleServicing_URLS.deleteVechicleServiceName}/${ser_nam_auto_id}`);

                // Dismiss loading toast before showing success message
                toast.remove(toastId);

                if (response.status == 200) {
                    toast.success('Deleted successfully', {
                        autoClose: 3000,
                        position: toast.POSITION.TOP_RIGHT,
                    });
                    this.serviceList = response.data.data;
                    // Refresh the data after successful deletion
                  //  await this.performSearch();
                } else {
                    toast.error(response.data.message || 'Failed to delete ', {
                        autoClose: 5000,
                        position: toast.POSITION.TOP_RIGHT,
                    });
                }
            } catch (error) {
                console.error('Error deleting :', error);

                // Ensure loading toast is dismissed in case of error
                if (toastId) toast.dismiss(toastId);

                let errorMessage = 'An error occurred while deleting';
                if (error.response) {
                    errorMessage = error.response.data.message || errorMessage;
                } else if (error.request) {
                    errorMessage = 'Network error - please check your connection';
                }

                toast.error(errorMessage, {
                    autoClose: 5000,
                    position: toast.POSITION.TOP_RIGHT,
                });
            }
        },

    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 1000px;
  }
  </style>
