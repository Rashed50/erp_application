<template>
    <div class="container">
        <h4 class="text-center fw-bold" style="color:blue">Update Service Invoice Information</h4>
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row mb-1">
                        <label class="col-md-2 col-form-label text-right">Subcontractor</label>
                        <div class="col-md-4">
                            <select v-model="form.subcon_auto_id" class="form-select" required disabled>
                                <option v-for="sc in data_for_form.subcontractors" :key="sc.subcon_auto_id"
                                    :value="sc.subcon_auto_id" :selected="sc.subcon_auto_id === form.subcon_auto_id">
                                    {{ sc.subcon_name }}
                                </option>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label text-right">Service Type</label>
                        <div class="col-md-4">
                            <select v-model="this.form.service_type" @change="changeServiceTypeEvent" class="form-select"
                                 >
                                <option value="1">Manpower Auto Process</option>
                                <option value="2">Manpower Manual Process</option>
                                <option value="4">Other</option>
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
                        <label class="col-md-2 col-form-label text-right">Year</label>
                        <div class="col-md-4">
                            <select v-model="form.year" class="form-select">
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label text-right">Month</label>
                        <div class="col-md-4">
                            <select v-model="form.month" class="form-select">
                                <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                    {{ month }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div v-if="form.service_type == 1">
                        <br>
                        <!-- <button type="button" :disabled="isSaveBtnClicked" @click="processSubContractorWorkRecords"
                            class="btn btn-primary">Process Work Records</button> -->
                    </div>

                    <div v-if="form.service_type > 1">
                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label text-right">No. of Units</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.no_of_unit" min="1" class="form-control" required>
                            </div>

                            <label class="col-md-2 col-form-label text-right">Unit Rate</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.per_unit_rate" @input="calculatePayableAmount"
                                    class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label text-right">Discount</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.discount" @input="calculatePayableAmount" min="0"
                                    class="form-control" required>
                            </div>
                            <label class="col-md-2 col-form-label text-right">Payable</label>
                            <div class="col-md-4">
                                <input type="number" v-model="form.grand_total" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label text-right">Remarks</label>
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
                            <button type="submit" :disabled="isSaveBtnClicked"  v-if="hasPermission('update_service_of_subcontractor')" class="btn btn-primary">Update</button>
                            <button type="button" @click="$emit('cancel')"
                                class="btn btn-secondary ms-2">Cancel</button>
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
import { Service_URLS } from "../../routes.js";



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
    props: {
        serviceId: {
            type: [Number, String],
            required: true
        },
        data_for_form: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            form: {
                subcon_service_auto_id: null,
                subcon_auto_id: '',
                service_type: '',
                no_of_unit: 1,
                per_unit_rate: 0,
                discount: 0,
                total_amount: 0,
                grand_total: 0,
                month: new Date().getMonth() + 1,
                year: new Date().getFullYear(),
                remarks: '',
                srv_status: 1
            },
            isSaveBtnClicked: false,
            loading: false,
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            years: [new Date().getFullYear(), new Date().getFullYear() - 1]
        };
    },

    created() {
        this.fetchServiceData();
    },

    methods: {

        handleFileUpload(field, event) {
             const file = event.target.files[0];
            if (file) {
                this.form.service_invoice = file;
            }

        },

        async fetchServiceData() {
            this.loading = true;
            try {
                const response = await axios.get(`${Service_URLS.GetServiceAPI}/${this.serviceId}`);
                const serviceData = response.data.data.service;

                // Map API response to form fields
                this.form = {
                    subcon_service_auto_id: serviceData.subcon_service_auto_id,
                    subcon_auto_id: serviceData.subcon_auto_id,
                    service_type: serviceData.service_type, // Ensure string type for select
                    no_of_unit: serviceData.no_of_unit,
                    per_unit_rate: serviceData.per_unit_rate,
                    discount: serviceData.discount,
                    total_amount: serviceData.total_amount,
                    grand_total: serviceData.grand_total,
                    month: serviceData.month,
                    year: serviceData.year,
                    remarks: serviceData.remarks,
                    srv_status: serviceData.srv_status,
                    invoice_date:serviceData.invoice_date,
                    invoice_no:serviceData.invoice_no

                };

            } catch (error) {
                console.error('Error fetching service data:', error);
                toast.error('Failed to load service data');
            } finally {
                this.loading = false;
            }
        },


        async submitForm() {
            try {
                this.isSaveBtnClicked = true;
                this.calculatePayableAmount();

                // Create FormData object for proper PUT request handling
                const formData = new FormData();
                Object.keys(this.form).forEach(key => {
                    formData.append(key, this.form[key]);
                });

                // Add Laravel's method spoofing for PUT request
                formData.append('_method', 'PUT');

                const response = await axios.post(
                    `${Service_URLS.UpdateServiceAPI}/${this.serviceId}`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );

                if (response.data.success) {
                    toast.success(response.data.message || 'Service updated successfully');
                    this.$emit('updated', response.data.data);
                } else {
                    toast.error(response.data.message || 'Update failed, please try again');
                }
            } catch (error) {
                console.error('Error updating service:', error);
                const errorMsg = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Update failed';
                toast.error(errorMsg);

                // Handle validation errors
                if (error.response?.status === 422 && error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                }
            } finally {
                this.isSaveBtnClicked = false;
            }
        },

        calculatePayableAmount() {
            this.form.total_amount = this.form.no_of_unit * this.form.per_unit_rate;
            this.form.grand_total = this.form.total_amount - this.form.discount;
        },

        changeServiceTypeEvent(event) {
            // Disabled in edit mode as service type shouldn't change
           //  this.operation_type =
              this.form.service_type = event.target.value;
        },

        async processSubContractorWorkRecords() {
            if (!this.form.subcon_auto_id) {
                toast.error('Select Subcontractor');
                return;
            }

            this.isSaveBtnClicked = true;
            try {
                const response = await axios.post(Service_URLS.WorkSalaryProcessAPI, {
                    subcon_auto_id: this.form.subcon_auto_id,
                    month: this.form.month,
                    year: this.form.year
                });

                if (response.status === 201) {
                    // Update form with processed data
                    this.form.no_of_unit = response.data.no_of_unit || 1;
                    this.form.per_unit_rate = response.data.per_unit_rate || 0;
                    this.form.discount = response.data.discount || 0;
                    this.calculatePayableAmount();
                    toast.success('Process completed successfully');
                } else {
                    toast.error('Process failed, please try again');
                }
            } catch (error) {
                console.error('Error processing work records:', error);
                toast.error(error.response?.data?.message || 'Process failed');
            } finally {
                this.isSaveBtnClicked = false;
            }
        }
    },

    watch: {
        serviceId(newVal) {
            if (newVal) {
                this.fetchServiceData();
            }
        }
    }
};
</script>

<style scoped>
.container {
    max-width: 800px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
</style>
