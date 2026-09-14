<template>
    <div class="container">
      <!-- <h5 class="mb-4">Add Subcontractor Service</h5> -->

        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submitForm">

                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label text-right">Subcontractor</label>
                        <div class="col-md-4">
                            <select v-model="form.subcon_auto_id" class="form-select" required>
                                <option value="">Select...</option>
                                <option v-for="sc in data_for_form.subcontractors" :key="sc.subcon_auto_id" :value="sc.subcon_auto_id">
                                    {{ sc.subcon_name }}
                                </option>

                            </select>
                        </div>

                        <label class="col-md-2 col-form-label  text-right">Service Type</label>
                        <div class="col-md-4">
                            <select v-model="form.service_type" @change="changeServiceTypeEvent" class="form-select">
                            <option value="1">Manpower Auto Process</option>
                            <option value="2">Manpower Manual Process</option>
                            <option value="4">Other Service</option>
                            <!-- <option value="10">Reserve</option> -->
                            </select>
                        </div>

                    </div>

                     <div class="row mb-1">
                        <label class="col-md-2 col-form-label  text-right">Invoie No</label>
                        <div class="col-md-4">
                            <input type="text" v-model="this.form.invoice_no" class="form-control" required>
                        </div>

                        <label class="col-md-2 col-form-label  text-right">Invoice Date</label>

                        <div class="col-md-4">
                            <input type="date" class="form-control" v-model="form.invoice_date" >
                        </div>

                    </div>

                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label  text-right">Service Year</label>
                        <div class="col-md-4">
                            <select v-model="form.year" class="form-select">
                                <option v-for="year in years" :key="year" :value="year">
                                {{ year }}
                                </option>
                        </select>
                        </div>

                        <label class="col-md-2 col-form-label  text-right">Service Month</label>
                        <div class="col-md-4">
                            <select v-model="form.month" class="form-select">
                            <option v-for="(month, index) in months" :key="index" :value="index + 1">
                            {{ month }}
                            </option>
                        </select>
                        </div>
                    </div>


                    <div v-if="operation_type == 1">
                        <br>
                        <button type="button" :disabled="isSaveBtnClicked" @click="processSubContractorWorkRecords" class="btn btn-primary" v-if="hasPermission('process_service_of_subcontractor')">Process Work Records</button>
                    </div>
                    <div v-if="operation_type > 1">

                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label  text-right">No. of Units</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.no_of_unit" min="1" disabled class="form-control" required>
                            </div>

                            <label class="col-md-2 col-form-label  text-right">Unit Rate</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.per_unit_rate" @input="calculatePayableAmount" class="form-control" min="1" required>
                            </div>
                        </div>


                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label  text-right">Discount</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.discount" @input="calculatePayableAmount" min="0" class="form-control" required>
                            </div>
                            <label class="col-md-2 col-form-label  text-right">Payable</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.grand_total" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label  text-right">Remarks</label>
                            <div class="col-md-10">
                                <textarea v-model="form.remarks" class="form-control"></textarea>
                            </div>


                        </div>

                         <div class="row mb-1">

                            <label class="col-md-2 col-form-label  text-right"> File</label>
                            <div class="col-md-10">
                                <input type="file" accept="*/*" id="invoice_file" @change="handleFileUpload('service_invoice', $event)"
                                class="form-control col-md-8" />
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" :disabled="isSaveBtnClicked" class="btn btn-primary"  v-if="hasPermission('insert_service_of_subcontractor')">Save</button>
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
    // import Multiselect from '@vueform/multiselect';
    import "@vueform/multiselect/themes/default.css";
    import { Service_URLS } from "../../routes.js"

    const currentMonth =  new Date().getMonth();
    const currentYear = new Date().getFullYear();
    const today = new Date().toISOString().substr(0, 10)



import { inject } from 'vue';
import { useAuth } from "../../../../../../../resources/js/components/useAuth.js";

const auth = inject('auth')

export default {
    setup() {
        const { hasPermission } = useAuth();

        return {
            hasPermission,
        };
    },
        data() {
         return {
             form: {
                    subcon_auto_id:"",
                    service_type: 1,
                    no_of_unit:1,
                    per_unit_rate: 0,
                    discount:0,
                    total_amount:0,
                    grand_total:0,
                    month: currentMonth+1,
                    year:  currentYear,
                    invoice_date:today,
                    invoice_no: currentYear + '' + (currentMonth + 1) + '' + Math.floor(Math.random() * (5))+5,
                    service_invoice:'',
                    remarks: ''

                },
                isSaveBtnClicked:false,
                operation_type:1, // 1 = work record auto process, 2= manpower manual process , 5= other service
                months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
                ],
                years: [currentYear, currentYear - 1] // Last 2 years + current year

            };
        },
        props:{
            data_for_form: {
                type: Object,
                required: true
            }
        },
        methods: {
            handleFileUpload(field, event) {
                //this.form[field] = event.target.files[0];
                const file = event.target.files[0];
                if (file) {
                    this.form.service_invoice = file;
                }

            },
        getLastDateString(month, year) {
            const lastDay = new Date(year, month, 1);
            return lastDay.toISOString().split('T')[0]; // YYYY-MM-DD
        },
        // manual insertion of invoice record
        async submitForm() {
            try {


                    console.log(this.form['service_invoice']);
                    this.isSaveBtnClicked = true;
                    this.form.total_amount = (this.form.per_unit_rate)*(this.form.no_of_unit);
                    this.form.invoice_date = this.getLastDateString(this.form.month, this.form.year); // 2024-06-30
                    console.log('last date of the month ',this.form.invoice_date);


                    // Use FormData for Multipart Upload
                    const formData = new FormData();
                    Object.keys(this.form).forEach(key => {
                        if (key !== 'service_invoice') {
                            formData.append(key, this.form[key]);
                        }
                    });
                    return;

                    if (this.form.service_invoice) {
                        formData.append('service_invoice', this.form.service_invoice);
                    }

                    const response = await axios.post(Service_URLS.StoreServiceAPI, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });

                if(response.status == 201){
                    this.resetForm();
                    toast.success('Created Successfully');
                }else{
                    toast.error('Operation Failed, Try Again');
                }
                    console.log(response);

                } catch (error) {
                        console.error('Error saving data:', error);
                        toast.error('Operation Failed, Try Again');
                }finally{
                    this.isSaveBtnClicked = false;
                }
        },
        resetForm() {
            this.isSaveBtnClicked = false;
            this.form = {
                subcon_auto_id:"",
                no_of_unit: 1,
                per_unit_rate: 0,
                discount: 0,
                total_amount:0,
                grand_total:0,
                month: currentMonth,
                year: currentYear,
                invoice_date:today,
                invoice_no:currentYear + '' + (currentMonth + 1) + '' + Math.floor(Math.random() * (5))+5,
                service_invoice:'',
                service_type: 1,
                remarks: ''
            };
            if(document.getElementById('invoice_file')){
                 document.getElementById('invoice_file').value = null; // Reset file input
            }


        },
        calculatePayableAmount(){

            const discount =  this.form.discount;
            this.form.grand_total =((this.form.per_unit_rate)*(this.form.no_of_unit)) - discount;
         },
         changeServiceTypeEvent(value){

            this.operation_type = this.form.service_type;
         },
        async processSubContractorWorkRecords(){


            if(this.form.subcon_auto_id == null){
                toast.error('Select Subcontractor ');
                return;
            }

            try{

                this.isSaveBtnClicked = true;
                this.form.remarks = "";
                this.form.discount = 0;
                this.form.no_of_unit = 1;
                this.form.per_unit_rate = 1;
                this.form.invoice_date = this.getLastDateString(this.form.month, this.form.year); // 2024-06-30
                console.log('last date of the month ',this.form.invoice_date);


                const response = await axios.post(Service_URLS.WorkSalaryProcessAPI,this.form);


                if(response.status == 201){
                    toast.success('Process Completed Successfully');
                    this.resetForm();
                }else{
                    toast.error('Process Failed, Try Again');
                }
            } catch (error) {
                    console.error('Error saving data:', error);
                    toast.error(error.response?.data?.message || 'Process Failed, Try Again');
            }finally{
            }
            this.isSaveBtnClicked = false;
         },

         getCurrentMonth(){
           const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
                ];
            return months[currentMonth];
        },


        }
    };
  </script>

  <style scoped>
  
  </style>
