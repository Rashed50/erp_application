<template>
    <div class="container mt-0">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white text-center mt-0 pb-0 pt-0">
                <h4>Vehicle Maintenance Edit Page</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitForm" enctype="multipart/form-data">

                    <div class="row col-md-12 mb-1 mt-0">
                        <label class="col-md-2 col-form-label">Vehicle Name:</label>
                        <div class="col-md-10">
                            <select v-model="form.veh_auto_id" class="form-select" required>
                                <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                                    {{ sc.veh_name }}
                                </option>

                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div v-if="servicing_list.length > 0">
                            <hr>
                            <div class="d-flex align-items-end mt-2 mb-3">
                                <h5 class="">Added Services</h5>
                                <button class="btn btn-success w-40 ms-3" @click.prevent="addNewService">
                                    Add New <i class="bi bi-cart-fill"></i>
                                </button>
                            </div>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">S.N</th>
                                        <th width="22%">Service Name</th>
                                        <th width="13%">Type</th>
                                        <th width="10%">Qty</th>
                                        <th width="10%">Rate</th>
                                        <th width="10%">Total</th>
                                        <th width="25%">Remarks</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in servicing_list" :key="index">

                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <!-- Service Name Input -->
                                            <select v-model="item.service_name.ser_nam_auto_id" class="form-select"
                                                required>
                                                <option v-for="service in data_for_form.servicing_names"
                                                    :key="service.ser_nam_auto_id" :value="service.ser_nam_auto_id">
                                                    {{ service.service_name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <!-- Service Type Input -->
                                            <!-- <select v-model="item.service_type" class="form-select">
                                                <option value="repair">Repair</option>
                                                <option value="new">New</option>
                                            </select> -->
                                             <select v-model="item.service_type" class="form-select"
                                                required>
                                                <option v-for="service in ['Repair','New']"
                                                    :key="service" :value="service">
                                                    {{ service }}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <!-- Qty Input -->
                                            <input v-model="item.qty" type="number" class="form-control" step="0.01"
                                                min="0.01" @input="calculateSubtotalAmount(index)" required />
                                        </td>
                                        <td>
                                            <!-- Unit Rate Input -->
                                            <input v-model="item.unit_rate" type="number" class="form-control"
                                                step="0.01" min="0.01" @input="calculateSubtotalAmount(index)"
                                                required />
                                        </td>
                                        <td>
                                            <!-- Total Input -->
                                            <input v-model="item.total_amount" type="number" class="form-control"
                                                step="0.01" required disabled />
                                        </td>
                                        <td>
                                            <!-- Remarks Input -->
                                            <input v-model="item.remarks" type="text" class="form-control" />
                                        </td>
                                        <td class="text-center">
                                            <!-- Delete Button -->
                                            <a class="delete_btn" @click="removeFromCart(index)">
                                                <i class="fa fa-trash fa-md delete_icon"></i>
                                            </a>
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
                                <label class="col-sm-4 col-form-label">Subtotal Amount:</label>
                                <div class="col-sm-8">
                                    <input v-model="form.grand_total_amount" type="number"
                                        @input="calculateGrandTotalAmount" min="0" step="0.01" class="form-control"
                                        required disabled />
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label">Discount:</label>
                                <div class="col-sm-8">
                                    <input v-model="form.discount" type="number" @input="calculateGrandTotalAmount"
                                        value="0" min="0" step="0.01" class="form-control" />
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label">Payable Amount:</label>
                                <div class="col-sm-8">
                                    <input v-model="form.payable_amount" type="number" class="form-control" min="0"
                                        step="0.01" required />
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label">Payment Method:</label>
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
                                <label class="col-sm-4 col-form-label">Date:</label>
                                <div class="col-sm-8">
                                    <input v-model="form.start_date" type="date" class="form-control" required />
                                    <input v-model="form.end_date" type="date" hidden class="form-control" />
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label">Maintenance By:</label>
                                <div class="col-sm-8">
                                    <input v-model="form.service_by" type="number"   @keyup="searchEmployeeInformation" placeholder="Enter Employee ID" class="form-control" required />
                                    <p><strong><span>{{ this.service_by_info }}</span> </strong> </p>

                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label">Invoice File:</label>
                                <div class="col-sm-8">
                                    <input type="file" @change="handleFileUpload('servicing_invoice_file', $event)"
                                        class="form-control" />
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label">Remarks:</label>
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
            vehicleDetails: [],
            loading: false,

            selectedService: "",
            selected_service_type: "",
            qty: '',
            unit_rate: '',
            total_amount: '',
            remarks: "",

            servicing_list: [],
            service_by_info: '',
            isSaveBtnClicked: false,

            form: {
                veh_auto_id: '',
                grand_total_amount: '',
                discount: '0',
                payable_amount: '',
                payment_method: 1,
                start_date: this.getCurrentDate(),
                end_date: this.getCurrentDate(),
                service_by: '',
                servicing_invoice_file: null,
                remarks: '',
                invoice_no:'',

            }
        };
    },

    props: {
        data_for_form: {
            type: Object,
            required: true
        },
        vehicleId: {
            type: [Number, String],
            required: true
        },
    },

    methods: {

        async fetchVehicleDetails() {
            this.loading = true;
            try {
                 console.log("Details Data =", this.vehicleId);
                debugger;
                const response = await axios.get(`${VehicleServicing_URLS.detailsVechicleServiceName}/${this.vehicleId}`);
                const detailsData = response.data.data;

                this.vehicleDetails = detailsData;

                this.form = {
                    veh_auto_id: detailsData.servicing.vehicle.id,
                    grand_total_amount: detailsData.servicing.grand_total_amount,
                    discount: detailsData.servicing.discount,
                    payable_amount: detailsData.servicing.payable_amount,
                    payment_method: detailsData.servicing.payment_method === 'Cash' ? 1 : 2,
                    start_date: detailsData.servicing.start_date,
                    end_date: detailsData.servicing.end_date,
                    service_by: detailsData.servicing.service_by.id, // this is employee_id
                    // servicing_invoice_file : detailsData.servicing.,
                    invoice_no:detailsData.servicing.invoice_no,
                    remarks: detailsData.servicing.remarks,
                }
                this.service_by_info = detailsData.servicing.service_by.name;

                this.servicing_list = detailsData.service_items;

            } catch (error) {
                console.error('Error fetching service data:', error);
                toast.error('Failed to load service data');
            } finally {
                this.loading = false;
            }
        },

        async ServiceEdit(id) {
            console.log("Service ID", id);


        },

        addNewService() {
            this.servicing_list.push({
                service_name: { ser_nam_auto_id: null, service_name: '' },
                service_type: 'repair',
                qty: 1,
                unit_rate: 0,
                total_amount: 0,
                remarks: ''
            });
        },
        removeFromCart(index) {
            this.servicing_list.splice(index, 1);
            this.calculateGrandTotalAmount();
        },
        getServiceNameById(index) {

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


        async submitForm() {
            this.isSaveBtnClicked = true;
            try {
                if (!this.form.veh_auto_id || !this.form.payable_amount || !this.form.start_date || !this.form.service_by || !this.form.invoice_no) {
                    toast.error('Please fill all required fields');
                    this.isSaveBtnClicked = false;
                    return;
                }

                if (this.servicing_list.length === 0) {
                    toast.error('Please add at least one service item');
                    this.isSaveBtnClicked = false;
                    return;
                }
                debugger;

                // Prepare the final data object
                const payload = {
                    ...this.form,
                    servicing_list: this.servicing_list.map(item => ({
                        ...item,
                        ...(item.id && { id: item.id }),
                        service_name: {
                            ser_nam_auto_id: item.service_name.ser_nam_auto_id,
                            service_name: this.findServiceNameById(item.service_name.ser_nam_auto_id) || ''
                        }
                    }))
                };

                console.log("Formatted Payload:", payload);

                // Make API request
                const response = await axios.put(
                    `${VehicleServicing_URLS.updateVechicleServiceName}/${this.vehicleId}` , payload
                    //,
                    // {
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    //     }
                    // }
                );

                toast.success('Maintenance record updated successfully!');
                // this.fetchVehicleDetails()
                // Optionally redirect or reset form
                // this.$router.push({ name: 'vehicle-maintenance-list' });

            } catch (error) {
                console.error('Error updating maintenance:', error);
                toast.error(error.response?.data?.message || 'Failed to update maintenance');
            } finally {
                this.isSaveBtnClicked = false;
            }
        },

        // Helper method to find service name by ID
        findServiceNameById(id) {
            const service = this.data_for_form.servicing_names.find(
                s => s.ser_nam_auto_id === id
            );
            return service ? service.service_name : '';
        },

        resetForm() {
            this.isSaveBtnClicked = false;
            this.form.veh_auto_id = "",
                this.form.grand_total_amount = "",
                this.form.discount = "0",
                this.form.payable_amount = "",
                this.form.payment_method = 1, // Format: YYYY-MM-DD
                this.form.start_date = this.getCurrentDate(),
                this.form.end_date = this.getCurrentDate(),
                this.form.service_by = "",
                this.form.servicing_invoice_file = '',
                this.form.remarks = '',
                this.servicing_list = [];
                this.service_by_info = '';
        },

        handleFileUpload(field, event) {
            this.form[field] = event.target.files[0];
        },

        getCurrentDate() {
            const today = new Date();
            return today.toISOString().split("T")[0]; // Format: YYYY-MM-DD
        },

        calculatePayableAmount() {
            const discount = parseFloat(this.form.discount) || 0;
            this.form.payable_amount = this.form.grand_total_amount - discount;
        },
        calculateSubtotalAmount(index) {
            const item = this.servicing_list[index];
            const qty = parseFloat(item.qty) || 0;
            const rate = parseFloat(item.unit_rate) || 0;
            item.total_amount = parseFloat((qty * rate).toFixed(2));
            this.calculateGrandTotalAmount();
        },

        calculateGrandTotalAmount() {
            const grandTotal = this.servicing_list.reduce((total, item) => {
                return total + (parseFloat(item.total_amount) || 0);
            }, 0);

            this.form.grand_total_amount = grandTotal;
            this.calculatePayableAmount();
        },

        async searchEmployeeInformation() {
            try {

                 this.service_by_info = '';
                let empid = this.form.service_by;
                if(empid.length < 3){

                    return;
                }

                    const response = await axios.get(`${VehicleServicing_URLS.SearchEmployee}/${empid}`);

                    if(response.status == 200){
                        this.service_by_info = response.data.data.employee_name;
                    }else {
                        //this.form.service_by = '';
                         this.service_by_info = '';
                    }



             } catch (error) {
                console.log(error);
            }
        }
    },

    computed: {

    },

    created() {
        this.fetchVehicleDetails()
    },

    watch: {
        vehicleId(newVal) {
            debugger;
            if (newVal) {
                this.fetchVehicleDetails()
            }
        },

        'form.discount'(newVal) {
            this.calculateGrandTotalAmount();
        },
    }
};
</script>



<style scoped>
.edit_btn {
    cursor: pointer
}

.delete_btn {
    cursor: pointer
}

.w-40 {
    width: 10rem;
}

.w-60 {
    width: 15rem;
}
</style>
