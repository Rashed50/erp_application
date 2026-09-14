<template>
    <div>
        <!-- breadcrumb -->
        <div class="row">
            <div class="col-md-6">
                <h4>Ticket Create</h4>
            </div>
            <div class="col-md-6 text-right"></div>
        </div>
        <!-- body -->
        <div class="card mt-2">
            <form class="card-body" @submit.prevent="submit" enctype="multipart/form-data">

                <div class="row mb-4" id="main">

                    <!-- Left Column -->
                    <div class="col-md-5" id="sub-1">
                        <!-- Ticket For -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Ticket For <span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select v-model="ticket_for" class="form-control" required>
                                    <option disabled value="">Select ticket for</option>
                                    <option v-for="ticket in data.ticketForOptions" :key="ticket" :value="ticket">
                                        {{ ticket.charAt(0).toUpperCase() + ticket.slice(1) }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Employee -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Employee ID<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" v-model="search_employee"
                                    @input="searchEmployee" placeholder="Write Employee ID" required>

                                <div v-if="employeeInfo" class="d-flex" style="flex-direction: column;">

                                    <span><strong>Name: </strong>
                                        {{ employeeInfo.employee_name }}
                                    </span>

                                    <span><strong>Employee ID:</strong>
                                        {{ employeeInfo.employee_id }}
                                    </span>

                                    <span><strong>Passport NO: </strong>
                                        {{ employeeInfo.passfort_no }}
                                    </span>
                                </div>
                                <span v-if="employeeError" class="error-text text-danger">Please Enter An Active
                                    Employee Id</span>
                            </div>
                        </div>

                        <!-- Ticket Type -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Ticket Type <span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select v-model="ticket_type" class="form-select" required>
                                    <option disabled value="" selected>Select item</option>
                                    <option v-for="tk_type in data.ticketTypes" :key="tk_type" :value="tk_type">
                                        {{ tk_type.charAt(0).toUpperCase() + tk_type.slice(1) }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Paid By -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Paid By <span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select v-model="paid_by" class="form-select" required>
                                    <option disabled value="" selected>Select item</option>
                                    <option v-for="p_by in data.paidByOptions" :key="p_by" :value="p_by">
                                        {{ p_by.charAt(0).toUpperCase() + p_by.slice(1) }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Ticket Number -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Ticket Number<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control inpute_height" placeholder="Ticket Number"
                                    v-model="ticket_number" required />
                            </div>
                        </div>

                        <!-- Confirm Date -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Confirm Date<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="datetime-local" class="form-control" v-model="confirm_date" required />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1"></div>

                    <!-- Right Column (if needed) -->
                    <div class="col-md-5" id="sub-2">

                        <!-- Quantity -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Quantity <span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" min="1" class="form-control" v-model="qty" required
                                    @input="totalCalculate" />
                            </div>
                        </div>

                        <!-- Unit Price -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Unit Price<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" step="0.01" class="form-control" v-model="unit_price"
                                    @input="totalCalculate" />
                            </div>
                        </div>

                        <!-- Total Price -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Total Price<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" step="0.01" class="form-control" v-model="total_price" readonly />
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Remarks<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control inpute_height" placeholder="Remarks..." v-model="remarks" />
                            </div>
                        </div>

                        <!-- Reference By -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Reference By<span class="req_star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control inpute_height" placeholder="Reference..."
                                    v-model="reference_by" />
                            </div>
                        </div>

                        <!-- Attachment -->
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Attachment</label>
                            </div>
                            <div class="col-md-9">
                                <input type="file" class="form-control" @change="onFileChange" />
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-success btn-sm mt-2" type="submit" :disabled="uploading">
                    <i v-if="uploading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i>
                    &nbsp; Save
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import Multiselect from '@vueform/multiselect';
import "@vueform/multiselect/themes/default.css";
import { TicketStoreAPI, EmployeeSearchAPI, TicketList } from "../../routes.js";

export default {
    components: {
        Multiselect,
    },

    data() {
        return {
            ticket_for: 'employee', // Default value
            emp_auto_id: null,
            guest_auto_id: null,
            ticket_type: null,
            paid_by: null,
            ticket_number: '',
            confirm_date: null,
            qty: 1,
            unit_price: 0,
            total_price: 0,
            remarks: '',
            reference_by: '',
            attachment: null,
            uploading: false,


            search_employee:null,
            employeeInfo: null,
            multipleEmployees: [],
            employeeError: false
        };
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },
    mounted() {
        // console.log("Data =", this.data);
    },

    computed: {
        employeeList() {
            const list = [{ label: 'Select item', value: null }, ...this.data.employees.map(d => ({ label: d.employee_name, value: d.emp_auto_id }))];
            // console.log('Employee List:', list);
            return list;
        },
    },

    methods: {
        totalCalculate() {
            this.total_price = this.qty * this.unit_price;
        },
        async submit() {
            this.uploading = true;
            const formData = new FormData();
            if (this.employeeInfo) {
                formData.append('emp_auto_id', this.employeeInfo.emp_auto_id);
            }else{
                toast.error('Please Give Valid Employee ID.');
                return;
            }
            formData.append('ticket_for', this.ticket_for);
            formData.append('guest_auto_id', this.guest_auto_id);
            formData.append('ticket_type', this.ticket_type);
            formData.append('paid_by', this.paid_by);
            formData.append('ticket_number', this.ticket_number);
            formData.append('confirm_date', this.confirm_date);
            formData.append('qty', this.qty);
            formData.append('unit_price', this.unit_price);
            formData.append('total_price', this.total_price);
            formData.append('remarks', this.remarks);
            formData.append('reference_by', this.reference_by);
            if (this.attachment) {
                formData.append('attachment', this.attachment);
            }

            // Log the form data to the console
            // for (let [key, value] of formData.entries()) {
            //     console.log(`${key}: ${value}`);
            // }

            try {
                const response = await axios.post(TicketStoreAPI, formData);
                toast.success('Ticket created successfully!');
                setTimeout(() => {
                    window.location = TicketList;
                }, 1000);
                // Reset form fields or handle navigation
            } catch (error) {
                toast.error('Error creating ticket.');
            } finally {
                this.uploading = false;
            }
        },

        onFileChange(event) {
            this.attachment = event.target.files[0];
        },

        async searchEmployee() {
            // console.log("Search Employee Input =", this.search_employee);

            // Reset error and info states
            this.employeeInfo = null;
            this.multipleEmployees = [];
            this.employeeError = false;

            // Define search parameters
            const searchType = "employee_id"; // Adjust based on actual search type if needed
            const searchValue = this.search_employee;

            // Ensure there's input to search for
            if (!searchValue) {
                console.error("Please Enter Employee ID/Iqama/Passport Number");
                return;
            }

            try {
                const response = await axios.post(EmployeeSearchAPI, {
                    search_by: searchType,
                    employee_searching_value: searchValue
                });

                if (response.data.status !== 200) {
                    this.employeeError = true;
                    return;
                }

                const foundEmployees = response.data.findEmployee;
                // console.log("Employee =", foundEmployees)
                if (foundEmployees.length > 1) {
                    this.multipleEmployees = foundEmployees;
                    // console.error("Critical Error Found, Please Contact with Software Support");
                } else if (foundEmployees.length === 1) {
                    this.employeeInfo = foundEmployees[0];
                    // console.log("Employee Found:", this.employeeInfo);
                } else {
                    this.employeeError = true;
                }
            } catch (error) {
                console.error("Error searching employee:", error);
            }
        },

    }
};
</script>

<style scoped>
/* Add any specific styles here */
</style>
