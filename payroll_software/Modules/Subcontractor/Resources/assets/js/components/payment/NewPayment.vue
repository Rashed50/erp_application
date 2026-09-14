<template>
    <div class="container">
        <!-- <h5 class="mb-4">Add Subcontractor Service</h5> -->
        <div class="card">
            <div class="card-body">
                <form
                    @submit.prevent="submitForm"
                    enctype="multipart/form-data"
                >
                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label text-right" >Subcontractor</label>
                        <div class="col-md-10">
                            <select
                                v-model="form.subcon_auto_id"
                                class="form-select"  @change="filterSubcontractor"
                                required
                            >
                                <option   value="">Select...</option>
                                <option
                                    v-for="sc in data_for_form.subcontractors"
                                    :key="sc.subcon_auto_id"
                                    :value="sc.subcon_auto_id"
                                >
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
                                <option
                                    v-for="year in years"
                                    :key="year"
                                    :value="year"
                                >
                                    {{ year }}
                                </option>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label text-right">Month</label>
                        <div class="col-md-4">
                            <select v-model="form.month" class="form-select">
                                <option
                                    v-for="(month, index) in months"
                                    :key="index"
                                    :value="index + 1"
                                >
                                    {{ month }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label  text-right"
                            >Payment Type</label
                        >
                        <div class="col-md-4">
                            <select
                                v-model="form.payment_method"
                                class="form-select"
                            >
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label text-right">Amount</label>
                        <div class="col-md-4">
                            <input
                                type="number"
                                v-model="form.grand_total"
                                class="form-control"
                                required
                            />
                        </div>
                    </div>

                    <div class="row mb-1">
                            <label class="col-md-2 col-form-label text-right">Payment Date</label>
                            <div class="col-md-4">
                                <input type="date" v-model="form.payment_date" class="form-control" required>
                            </div>
                    </div>

                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label  text-right">Remarks</label>
                        <div class="col-md-10">
                            <textarea
                                v-model="form.remarks"
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-md-2  text-right">File</label>
                            <input
                                type="file"
                                @change="
                                    handleFileUpload('payment_file', $event)
                                "
                                class="form-control col-md-10"
                            />
                        </div>
                    </div>

                    <!-- {{ formAction }} -->

                    <div class="text-end mt-3">
                        <button
                            type="submit"
                            class="btn btn-success me-2"
                            @click="formAction = 'save'"
                        >
                            Save & Create Payment Receipt
                        </button>


                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
// import Multiselect from '@vueform/multiselect';
import "@vueform/multiselect/themes/default.css";
import { Payment_URLS } from "../../routes.js";

const currentMonth = new Date().getMonth();
const currentYear = new Date().getFullYear();

export default {
    data() {
        return {
            subcontractor_summary:null,
            is_show_summary:false,
            form: {
                subcon_auto_id: "",
                payment_method: 1,
                discount: 0,
                total_amount: 0,
                grand_total: 0,
                month: currentMonth+1,
                year: currentYear,
                remarks: "",
                payment_date:  new Date().toISOString().substr(0, 10)
            },
            months: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            years: [currentYear, currentYear - 1], // Last 2 years + current year
            formAction: "save",
        };
    },
    props: {
        data_for_form: {
            type: Object,
            required: true,
        },
    },
    methods: {
        handleFileUpload(field, event) {
            this.form[field] = event.target.files[0];
        },
       async filterSubcontractor() {
            // Access the selected ID directly
            const selectedId = this.form.subcon_auto_id;
            this.is_show_summary = false;
            if(selectedId == "")
                 return;
             try {

                    const response = await axios.get(`${Payment_URLS.SummaryProcessAPI}/${selectedId}`);
                    console.log(response.data.data);
                    if(response.status == 200){
                        this.subcontractor_summary = response.data.data;
                        this.is_show_summary = true;
                    }
                } catch (error) {
                     console.log('Operation Failed , Please try again');
                }

        },
        async submitForm2() {


            try {

                if(this.form.subcon_auto_id == "" || this.form.total_amount == "" || this.form.total_amount == "0"){
                    toast.error('Please input valid Data');
                    return;
                }

                this.form.total_amount = this.form.grand_total;
                this.form.discount = 0;

                // let formData = new FormData();
                // for (let key in this.form) {
                //     formData.append(key, this.form[key]);
                // }


                // // CASE 1: Create Invoice → open PDF in new tab

                    // Create a temporary form element to submit traditionally
                //     const tempForm = document.createElement("form");
                //     tempForm.action = Payment_URLS.PaymentInvoiceAPI;
                //     tempForm.method = "POST";
                //     tempForm.target = "_blank"; // open in new tab
                //    // tempForm.enctype = "multipart/form-data";

                //     // Add CSRF token if using Laravel
                //     const csrfToken = document
                //         .querySelector('meta[name="csrf-token"]')
                //         ?.getAttribute("content");
                //     if (csrfToken) {
                //         const csrfInput = document.createElement("input");
                //         csrfInput.type = "hidden";
                //         csrfInput.name = "_token";
                //         csrfInput.value = csrfToken;
                //         tempForm.appendChild(csrfInput);
                //     }

                //     // Append form data fields
                //     for (let [key, value] of formData.entries()) {
                //         const input = document.createElement("input");
                //         input.type = "hidden";
                //         input.name = key;
                //         input.value = value;
                //         tempForm.appendChild(input);
                //     }

                //     document.body.appendChild(tempForm);
                //     tempForm.submit();
                //     document.body.removeChild(tempForm);

                     const queryString = new URLSearchParams({
                        subcon_auto_id:this.form.subcon_auto_id,
                        payment_method:this.form.payment_method ,
                        discount: this.form.discount,
                        total_amount:this.form.total_amount ,
                        month:this.form.month,
                        year:this.form.year,
                        remarks:this.form.remarks,
                        payment_date:this.form.payment_date
                    }).toString();


                    // Open in new tab
                    const url =   `${Payment_URLS.PaymentInvoiceAPI}?${queryString}`;
                    window.open(url, '_blank');




            } catch (error) {
                console.error("Error saving data:", error);
                toast.error("Operation Failed, Try Again");
            }
        },

        submitForm(  ) {

               this.form.total_amount = this.form.grand_total;
                this.form.discount = 0;

                let formData = new FormData();
                for (let key in this.form) {
                    formData.append(key, this.form[key]);
                }


                // 1. Create a hidden form

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = Payment_URLS.PaymentInvoiceAPI; // Your Laravel Route
                form.target = '_blank'; // Key for new tab
                form.enctype = "multipart/form-data";

                form.style.display = 'none';

                // 2. Add CSRF Token (Crucial for Laravel)
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const hiddentToken = document.createElement('input');
                    hiddentToken.type = 'hidden';
                    hiddentToken.name = '_token';
                    hiddentToken.value = csrfToken;
                    form.appendChild(hiddentToken);
                }

                // 3. Append Data
                // IMPORTANT: If 'data' is a standard object, use this.
                // If 'data' is already a FormData object, use the loop below.
                // formData.entries().forEach(key => {
                //     const input = document.createElement('input');
                //     input.type = 'hidden';
                //     input.name = key;

                //     // If the value is an object/array, stringify it
                //     const value = typeof formData[key] === 'object' ? JSON.stringify(formData[key]) : formData[key];
                //     input.value = value;
                //     form.appendChild(input);
                // });

                for (let [key, value] of formData.entries()) {
                        const input = document.createElement("input");
                        input.type = "hidden";
                        input.name = key;
                        input.value = value;
                        form.appendChild(input);
                    }

                // 4. Submit and Cleanup
                document.body.appendChild(form);
                form.submit();



                // Remove from DOM after a short delay
                setTimeout(() => {
                    document.body.removeChild(form);
                }, 100);
            },
        resetForm() {
            this.form = {
                payment_method: 1,
                discount: 0,
                total_amount: 0,
                grand_total: 2330,
                month: currentMonth+1,
                year: currentYear,
                remarks: "",
            };
        },
        calculatePayableAmount() {
            this.total_amount = this.form.grand_total;
        },
    },
};
</script>
