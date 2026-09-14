<template>
    <div class="container">
        <h4 class="text-center fw-bold" style="color:blue">Edit Subcontractor Payment</h4>
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label text-right">Subcontractor</label>
                        <div class="col-md-10">
                            <select v-model="form.subcon_auto_id" class="form-select" required disabled>
                                <option v-for="sc in data_for_form.subcontractors" :key="sc.subcon_auto_id"
                                    :value="sc.subcon_auto_id">
                                    {{ sc.subcon_name }}
                                </option>

                            </select>
                             <div v-if="is_show_summary == true">
                                <br>
                                <label for="for_invoice_amount">Total Invoice Amount: {{ subcontractor_summary['total_invoice'] }} </label>
                                <br>
                                <label for="for_payment_amount">Total Payment: {{ subcontractor_summary['total_payment'] }}  </label>
                                <br>
                                <label for="for_payment_amount">Balance : {{ subcontractor_summary['total_invoice'] - subcontractor_summary['total_payment']  }}  </label>

                            </div>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label  text-right">Year</label>
                        <div class="col-md-4">
                            <select v-model="form.year" class="form-select">
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label  text-right">Month</label>
                        <div class="col-md-4">
                            <select v-model="form.month" class="form-select">
                                <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                    {{ month }}
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label  text-right">Payment Type</label>
                        <div class="col-md-4">
                            <select v-model="form.payment_method" class="form-select">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                            </select>
                        </div>


                        <label class="col-md-2 col-form-label text-right">Amount</label>
                        <div class="col-md-4">
                            <input type="number" v-model="form.grand_total" class="form-control" required>
                        </div>

                    </div>
                      <div class="row mb-1">
                            <label class="col-md-2 col-form-label text-right">Payment At</label>
                            <div class="col-md-4">
                                <input type="date" v-model="form.payment_date" class="form-control" required>
                            </div>
                        <label class="col-md-2 col-form-label text-right">Status</label>
                        <div class="col-md-4">
                            <select v-model="form.act_status" @change="filterPaymentStatus" class="form-select" required>
                                <option value="">Select Payment Status</option>
                                <option value="10">Payment Confirmation</option>
                                <option value="3">Change Paid Amount</option>
                                <!-- <option value="0">Payment Initiated</option> -->

                            </select>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label text-right">Remarks</label>
                        <div class="col-md-10">
                            <textarea v-model="form.remarks" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row mb-1">

                        <div class="col-md-12 d-flex align-items-center text-right">
                            <label class="col-md-2  text-right">File</label>
                            <input type="file" @change="handleFileUpload('payment_file', $event)"
                                class="form-control col-md-10" />

                        </div>
                    </div>

                    <div class="text-end">
                        <div v-if="final_update == 1">
                            <button type="submit"   class="btn btn-primary">
                                <span v-if="isUpdating">   <i class="fas fa-spinner fa-spin"></i> Updating... </span>
                                <span v-else>Payment Confirm</span>
                            </button>
                        </div>
                        <div v-if="final_update == 0">
                            <button type="submit"  class="btn btn-primary"> 
                                <span v-if="isUpdating">   <i class="fas fa-spinner fa-spin"></i> Updating... </span>
                                <span v-else>Review Amount</span>
                            </button>
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
import "@vueform/multiselect/themes/default.css";
import { Payment_URLS } from "../../routes.js"

const currentMonth = new Date().getMonth();
const currentYear = new Date().getFullYear();

export default {
    data() {
        return {
            apiData:[],
            isUpdating:false,
            subcontractor_summary:null,
            is_show_summary:false,
            final_update:0,
            form: {
                subcon_auto_id: '',
                payment_method: 1,
                discount: 0,
                total_amount: 0,
                grand_total: 0,
                month: currentMonth + 1,
                year: currentYear,
                remarks: '',
                payment_status:10,
                payment_date:new Date().toISOString().substr(0, 10),

            },
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            years: [currentYear, currentYear - 1] // Last 2 years + current year

        };
    },

    props: {
        payment_id: {
            type: [Number, String],
            required: true
        },
        data_for_form: {
            type: Object,
            required: true
        }
    },

    methods: {

        filterPaymentStatus(){
            const paymentStaus = this.form.act_status;
            if(paymentStaus ==3 || paymentStaus == 0){
                this.final_update = 0;
            }
            else {
                this.final_update = 1;
            }
        },

        async fetchPaymentData() {
            console.log("Fatching Data .....")
            try {
                const response = await axios.get(`${Payment_URLS.DetailsPaymentAPI}/${this.payment_id}`);
                const paymentData = response.data.data.payment;
                const amonth_summary = response.data.data.amonth_summary;
                console.log("Payment Data =", paymentData);
                this.apiData = paymentData;

                this.form = {
                    subcon_auto_id: paymentData.subcon_auto_id,
                    payment_method: paymentData.payment_method.toString(),
                    payment_date : paymentData.payment_date,
                    act_status:paymentData.act_status,
                    discount: paymentData.discount,
                    total_amount: paymentData.total_amount,
                    grand_total: paymentData.grand_total,
                    month: paymentData.month,
                    year: paymentData.year,
                    remarks: paymentData.remarks,
                };
                console.log(amonth_summary);
                this.is_show_summary = true;
                this.subcontractor_summary = amonth_summary;


            //      "amonth_summary": {
            // "total_invoice": "160132",
            // "total_payment": 0
           // }


            } catch (error) {
                console.error('Error fetching service data:', error);
                toast.error('Failed to load service data');
            } finally {
            }
        },

        handleFileUpload(field, event) {
            this.form[field] = event.target.files[0];
        },

        async submitForm() {
            try {

                this.isUpdating = true;

                let formData = new FormData();

                // Append all form data
                formData.append('_method', 'PUT');
                //formData.append('payment_id',this.payment_id);
                formData.append('subcon_auto_id', this.form.subcon_auto_id);
                formData.append('payment_method', this.form.payment_method);
                formData.append('discount', this.form.discount);
                formData.append('total_amount', this.form.total_amount);
                formData.append('grand_total', this.form.grand_total);
                formData.append('month', this.form.month);
                formData.append('payment_date', this.form.payment_date);
                formData.append('act_status', this.form.act_status);
              //  formData.append('month', this.form.month);

                formData.append('year', this.form.year);
                formData.append('remarks', this.form.remarks);

                // Append file if exists
                if (this.form.payment_file instanceof File) {
                    formData.append('payment_file', this.form.payment_file);
                }

                // Make the PUT request to update the payment
                const response = await axios.post(
                    `${Payment_URLS.UpdatePaymentAPI}/${this.payment_id}`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );

                // Handle success response
                if (response.data.success) {
                    toast.success(response.data.message);

                    // Optionally emit an event to parent or redirect
                    // this.$emit('payment-updated', response.data.data.payment);

                    // Or reset form if needed
                    // this.resetForm();
                } else {
                    toast.error(response.data.message || 'Update failed');
                }
                 

            } catch (error) {
                console.error('Error updating payment:', error);

                // Handle validation errors
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    for (const field in errors) {
                        toast.error(`${field}: ${errors[field][0]}`);
                    }
                }
                // Handle other errors
                else {
                    toast.error(error.response?.data?.message || 'Failed to update payment');
                }
            } finally {
                // Any cleanup or loading state change
               // this.isLoading = false; not used it
                this.isUpdating = false;
            }
        },

        resetForm() {

            this.form = {
                payment_method: 1,
                discount: 0,
                total_amount: 0,
                grand_total: 2330,
                month: currentMonth+1,
                year: currentYear,
                remarks: '',
                act_status:10,
                payment_date:new Date().toISOString().substr(0, 10),
            };
        },

        calculatePayableAmount() {
            this.total_amount = this.form.grand_total;
        },
    },

    mounted() {
        if (this.payment_id) {
            this.fetchPaymentData();
        }
    },

    watch: {
        payment_id(newVal) {
            if (newVal) {
                this.fetchPaymentData();
            }
        }
    },
};
</script>
