<template>
    <div class="container mt-0">
      <div class="card shadow">
        <div class="card-header bg-secondary text-white text-center mt-0 pb-0 pt-0">
          <h4>Vehicle Maintenance Information</h4>
        </div>
        <div class="card-body">
          <form @submit.prevent="submitForm" enctype="multipart/form-data">

            <div class="row col-md-12 mb-1 mt-0">
                <label class="col-md-2 col-form-label text-right">Vehicle Name:</label>
                <div class="col-md-4">
                    <select v-model="form.veh_auto_id" class="form-select" required>
                        <option value="">Select One</option>
                        <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                            {{ sc.veh_name }}
                        </option>
                    </select>
                </div>
                <!-- Amount Input -->
                <label class="col-md-2 col-form-label text-right">Current Mileage:</label>
                <div class="col-md-4">
                    <input v-model="form.current_mileage" type="number"   step="1"  class="form-control" placeholder="Input Vehicle Current Mileage" />
                </div>
             </div>

            <div class="row mt-3">
                <form @submit.prevent="addToCart">
                    <div class="row mb-1">
                        <!-- Service Dropdown -->
                        <div class="col-md-2">
                            <label class="form-label">Service Name</label>
                            <select v-model="selectedService" class="form-select">
                                    <option value="">Select One</option>
                                <option v-for="service in data_for_form.servicing_names" :key="service.ser_nam_auto_id" :value="service.ser_nam_auto_id">
                                {{ service.service_name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select v-model="this.selected_service_type" class="form-select">
                                <option :value="'Repair'">Repair</option>
                                <option :value="'New'">New</option>
                            </select>
                        </div>

                         <!-- Qty Input -->
                        <div class="col-md-1">
                            <label class="form-label">Qty</label>
                            <input v-model="qty" type="number" class="form-control" placeholder="Qty" step="0.01" @input="calculateSubtotalAmount" />
                        </div>
                         <!-- Amount Input -->
                         <div class="col-md-2">
                            <label class="form-label">U.Rate</label>
                            <input v-model="unit_rate" type="number" @input="calculateSubtotalAmount" step="0.01" class="form-control" placeholder="Unit Price" />
                        </div>

                         <!-- Total Input -->
                         <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input v-model="total_amount" type="number" class="form-control" step="0.01" placeholder="Total Amount" />
                        </div>


                        <!-- Remarks Input -->
                        <div class="col-md-2">
                            <label class="form-label ">Remarks</label>
                            <input v-model="remarks" type="text" class="form-control" placeholder="Remarks" />
                        </div>

                        <!-- Add to Cart Button -->
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-40">Add<i class="bi bi-cart-fill"></i></button>
                        </div>
                    </div>
                </form>
                     <!-- List of Added Records -->
                    <div v-if="servicing_list.length > 0">
                        <hr>
                        <h5 class="mt-1">Added Services</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>S.N</th>
                                    <th>Service Name</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Total</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in servicing_list" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.service_name }}</td>
                                <td>{{ item.service_type }}</td>
                                <td>{{ item.qty }}</td>
                                <td>{{ item.unit_rate }}</td>
                                <td>{{ item.total_amount }}</td>
                                <td>{{ item.remarks }}</td>
                                <td>
                                    <a class="view_btn" @click="removeFromCart(index)">
                                        <i class="fa fa-trash fa-md delete_icon"></i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
            </div>

            <div class="row mt-2">
                <!-- Left Column -->
                <div class="col-md-6">

                    <div class="mb-2 row">
                    <label class="col-sm-4 col-form-label  text-right">Subtotal Amount:</label>
                    <div class="col-sm-8">
                        <input v-model="form.grand_total_amount" type="number" @input="calculateGrandTotalAmount" min="0" step="0.01" class="form-control" required />
                    </div>
                    </div>

                    <div class="mb-2 row">
                    <label class="col-sm-4 col-form-label  text-right">Discount:</label>
                    <div class="col-sm-8">
                        <input v-model="form.discount" type="number" @input="calculateGrandTotalAmount" value="0" min="0" step="0.01" class="form-control" />
                    </div>
                    </div>

                    <div class="mb-2 row">
                    <label class="col-sm-4 col-form-label  text-right">Payable Amount:</label>
                    <div class="col-sm-8">
                        <input v-model="form.payable_amount" type="number" class="form-control" min="0" step="0.01" required />
                    </div>
                    </div>

                    <div class="mb-2 row">
                    <label class="col-sm-4 col-form-label  text-right">Payment Method:</label>
                    <div class="col-sm-8">
                        <select v-model="form.payment_method" class="form-select">
                        <option value="1">Cash</option>
                        <option value="2">Bank</option>
                        </select>
                    </div>
                    </div>
                    <div class="mb-2 row">
                        <label class="col-sm-4 col-form-label  text-right">Invoice No.:</label>
                        <div class="col-sm-8">
                            <input v-model="form.invoice_no" type="text" class="form-control" required />
                         </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="mb-2 row">
                        <label class="col-sm-4 col-form-label  text-right">Date:</label>
                        <div class="col-sm-8">
                            <input v-model="form.start_date" type="date" class="form-control" required />
                            <input v-model="form.end_date" type="date" hidden class="form-control" />
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label class="col-sm-4 col-form-label  text-right">Maintenance By:</label>
                        <div class="col-sm-8">
                            <input v-model="this.form.service_by" type="number"   @keyup="searchEmployeeInformation" placeholder="Enter Employee ID" class="form-control" required />
                            <!-- <label for="service_by_info" v-bind:[value]="service_by_info" id="service_by_info" @input="searchEmployeeInformation" > Md Rashedul hoque</label> -->
                            <p><strong><span>{{ this.service_by_info }}</span>  </strong> </p>

                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label class="col-sm-4 col-form-label  text-right">Invoice File:</label>
                        <div class="col-sm-8">
                            <input type="file" @change="handleFileUpload('servicing_invoice_file', $event)" class="form-control" />
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label class="col-sm-4 col-form-label  text-right">Remarks:</label>
                        <div class="col-sm-8">
                            <textarea v-model="form.remarks" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button (Centered) -->
            <div class="text-center mt-4">
              <button type="submit" :disabled="isSaveBtnClicked" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>

  <script>

    import axios from 'axios';
    import { ref } from 'vue';

    import { toast } from 'vue3-toastify';
    import Multiselect from '@vueform/multiselect';
    import "@vueform/multiselect/themes/default.css";
    import { VehicleServicing_URLS, } from "../router.js";

  export default {
    components: {
        Multiselect,
    },
    data() {
        return {
            selectedService: "",
            qty: '',
            unit_rate:'',
            total_amount: '',
            remarks: "",
            servicing_list: [],
            selected_service_type:"Repair",
            service_by_info:'',
            isSaveBtnClicked:false,
            form:{
                veh_auto_id: '',
                grand_total_amount: '',
                discount: '0',
                payable_amount: '',
                payment_method: 1,
                start_date: this.getCurrentDate(),
                end_date: this.getCurrentDate(),
                service_by: '',
                servicing_invoice_file: null,
                invoice_no:'',
                remarks: '',
                current_mileage:0,

            }
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
                    if(this.servicing_list.length == 0){
                        toast.error("Please add One or More Maintenance Name");
                        return;
                    }

                    this.isSaveBtnClicked = true;
                    let formData = new FormData();

                    formData.append('veh_auto_id',this.form.veh_auto_id);
                    formData.append('grand_total_amount',this.form.grand_total_amount);
                    formData.append('discount',this.form.discount);
                    formData.append('payable_amount',this.form.payable_amount);
                    formData.append('payment_method',this.form.payment_method);
                    formData.append('service_by',this.form.service_by);
                    formData.append('start_date',this.form.start_date);
                    formData.append('end_date',this.form.end_date);
                    formData.append('remarks',this.form.remarks);
                    formData.append('current_mileage',this.form.current_mileage);
                    formData.append('invoice_no',this.form.invoice_no);


                    formData.append('servicing_invoice_file',this.form['servicing_invoice_file'])

                    this.servicing_list.forEach((service, index) => {

                        formData.append(`services[${index}][id]`, service.id);
                        formData.append(`services[${index}][service_name]`, service.service_name);
                        formData.append(`services[${index}][service_type]`, service.service_type === null ? "": service.service_type);
                        formData.append(`services[${index}][qty]`, service.qty);
                        formData.append(`services[${index}][unit_rate]`, service.unit_rate);
                        formData.append(`services[${index}][total_amount]`, service.total_amount);
                        formData.append(`services[${index}][remarks]`, service.remarks);
                    });
                    const response =  await axios.post(VehicleServicing_URLS.StoreVechicleServicingAPI, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                    if(response.status == 201){
                        toast.success('Created Successfully');
                        this.resetForm();
                    }else {
                        toast.error("Data not Saved, Try Again");
                    }
                }catch (error) {
                    toast.error("Operation Failed, Try Again");
                }finally {
                    this.isSaveBtnClicked = false;
                }
        },
        addToCart() {
            if (!this.selectedService) {
                toast.error("Please Select Service Name");
                return;
            }else if (!this.selected_service_type) {
                toast.error("Please Select a Service Type");
                return;
            }
            else if (!this.qty) {
                toast.error("Please input Quantity");
                return;
            }
            else if (!this.unit_rate) {
                toast.error("Please input Unit Rate");
                return;
            }
            else if (!this.total_amount) {
                toast.error("Please input Service total Amount");
                return;
            }

            const sname = this.findAServiceNameById(this.selectedService);

            this.servicing_list.push({id:this.selectedService,service_name: sname,service_type:this.selected_service_type,qty:this.qty,unit_rate:this.unit_rate,total_amount:this.total_amount, remarks: this.remarks });
            this.calculateGrandTotalAmount();
            this.selectedService = '';
            this.qty ='';
            this.unit_rate='';
            this.total_amount='';
            this.remarks = "";
        },
        removeFromCart(index) {
            this.servicing_list.splice(index, 1);
            this.calculateGrandTotalAmount();
        },
        getServiceNameById(index){

               let serviceName = null; // Variable to store the found item
               const alst = this.data_for_form.servicing_names;
               serviceName = alst[index];
               alst.forEach((value, index) => {
                    console.log(value);
                    console.log(index);
                });
            return serviceName; // Return the found item or null if not found
        },
        findAServiceNameById(sid) {

            const foundService = this.data_for_form.servicing_names.find(
                (aservice) => aservice.ser_nam_auto_id === sid
            );
           // console.log(foundService.ser_nam_auto_id);
            return foundService ? foundService.service_name : null;
        },
        resetForm(){
          this.isSaveBtnClicked = false;
          this.form.veh_auto_id = "",
          this.form.grand_total_amount = "",
          this.form.discount = "0",
          this.form.payable_amount = "",
          this.form.payment_method = 1 , // Format: YYYY-MM-DD
          this.form.start_date = this.getCurrentDate(),
          this.form.end_date = this.getCurrentDate(),
          this.form.service_by = "",
          this.form.servicing_invoice_file = '',
          this.form.invoice_no='';
          this.form.remarks = '',
          this.form.current_mileage = 0;
          this.servicing_list = [];

         },
        handleFileUpload(field, event) {
            this.form[field] = event.target.files[0];
        },
        getCurrentDate() {
            const today = new Date();
            return today.toISOString().split("T")[0]; // Format: YYYY-MM-DD
        },
        calculateSubtotalAmount(){
            let total = this.qty*this.unit_rate;
            this.total_amount =total.toFixed(2)
        },
        calculateGrandTotalAmount(){

            let grand_total_amount = 0;
            this.servicing_list.forEach((service, index) => {
                grand_total_amount += service.qty * service.unit_rate;
            });
            this.form.grand_total_amount = grand_total_amount.toFixed(2);
            let discount = this.form.discount;
            this.form.payable_amount = (grand_total_amount - discount).toFixed(2);
        },
        async searchEmployeeInformation(){
            try{

                let empid = this.form.service_by;
                const empidString = String(empid);
                if(empidString.length < 3){
                    this.service_by_info = '';
                    return;
                }

                    const response = await axios.get(`${VehicleServicing_URLS.SearchEmployee}/${empid}`);

                    if(response.status == 200){
                        this.service_by_info = response.data.data.employee_name;
                    }else {
                        //this.form.service_by = '';
                         this.service_by_info = '';
                    }

            }catch(error){
                 console.log(error);
                 toast.error("Please input Valid Information");

            }
        }
    }
  };
  </script>

