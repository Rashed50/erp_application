<template>
    <div>
        <EmployeeSearchComponent @searching_result="handleSearchingResult"></EmployeeSearchComponent>

        <div v-if="searched_employee" class="card shadow-sm border rounded-3 mt-4">

            <form class="form-horizontal" @submit.prevent="submitCash">
                <div class="card">

                    <div class="card-body card_form" style="padding-top: 20px;">

                        <div class="row">

                            <div class="col-md-7">

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-4">Employee ID:</label>
                                    <div class="col-md-8">
                                        <input readonly type="text" class="form-control typeahead"
                                            placeholder="Input Employee ID" v-model="form.emp_id">
                                    </div>
                                    <div id="showEmpId"></div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-4">Received Amount:<span
                                            class="req_star">*</span></label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control"
                                            :class="{ 'is-invalid': errors.pay_amount }" placeholder="Input Amount"
                                            v-model.number="form.pay_amount">

                                        <span class="invalid-feedback d-block" v-if="errors.pay_amount">
                                            {{ errors.pay_amount_msg }}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-4">Received Date</label>
                                    <div class="col-md-8">
                                        <input type="date" class="form-control" v-model="form.payment_date">
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-4">Remarks:</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" placeholder="Type Here... "
                                            v-model="form.payment_remarks">
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-4">Attached File:</label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" ref="fileInput"
                                            @change="handleAttachedFileUpload" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">

                                        <span class="invalid-feedback d-block" v-if="errors.attached_file">
                                            {{ errors.attached_file_msg }}
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <!-- Right Side: Attachment Preview Box (col-md-5) -->
                            <div class="col-md-5 d-flex align-items-center justify-content-center">
                                <div class="w-100" v-if="form.attached_file && form.attached_file_url">
                                    <label class="form-label text-muted small fw-bold">Attachment Preview:</label>
                                    <div class="border rounded p-2 bg-light shadow-sm d-flex justify-content-center align-items-center"
                                        style="min-height: 220px; max-height: 260px;">

                                        <!-- Image Preview -->
                                        <img v-if="form.attached_file_type === 1" :src="form.attached_file_url"
                                            class="img-fluid rounded" style="max-height: 240px; object-fit: contain;"
                                            alt="File Preview">

                                        <!-- PDF Preview -->
                                        <iframe v-else-if="form.attached_file_type === 2" :src="form.attached_file_url"
                                            width="100%" height="240px"></iframe>

                                        <!-- Other File Formats (.doc / .docx) -->
                                        <div v-else class="text-muted small text-center">
                                            Preview not supported for this file format ({{ form.attached_file.name }}).
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect" :disabled="is_saving">
                            <span v-if="is_saving">Saving...</span>
                            <span v-else>SAVE</span>
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

import { CashAddEmployeeAPI } from '../../routes.js';
import EmployeeSearchComponent from '../../../../../../../Modules/HrManagement/Resources/assets/js/components/employee/SearchComponent.vue';

export default {
    components: { EmployeeSearchComponent },

    data() {
        return {
            searched_employee: null,
            is_saving: false,

            form: {
                emp_id: '',
                pay_amount: '',
                payment_date: '',
                payment_remarks: '',
                attached_file: null,
                attached_file_type: 0,
                attached_file_url: null,
            },

            errors: {
                pay_amount: false,
                pay_amount_msg: '',
                payment_date: false,
                payment_date_msg: '',
                payment_remarks: false,
                payment_remarks_msg: '',
                attached_file: false,
                attached_file_msg: ''
            }
        }
    },

    mounted() {
        const today = new Date().toISOString().split('T')[0];
        this.form.payment_date = today;
    },

    methods: {
        handleSearchingResult(employee) {
            if (employee == null) return;
            this.searched_employee = employee;
            this.form.emp_id = employee.employee_id;
        },

        validateForm() {
            let isValid = true;
            this.errors.pay_amount = false;

            if (!this.form.pay_amount) {
                this.errors.pay_amount = true;
                this.errors.pay_amount_msg = 'Payment amount is required';
                isValid = false;
            }
            return isValid;
        },

        handleAttachedFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.attached_file = file;
                const name = file.name.toLowerCase();

                // 1. Image Check
                if (file.type.startsWith('image/')) {
                    this.form.attached_file_type = 1;
                }
                // 2. PDF Check
                else if (file.type === 'application/pdf' || name.endsWith('.pdf')) {
                    this.form.attached_file_type = 2;
                }
                // 3. Other files
                else {
                    this.form.attached_file_type = 0;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.attached_file_url = e.target.result;
                };
                reader.readAsDataURL(file);

            } else {
                this.form.attached_file = null;
                this.form.attached_file_url = null;
                this.form.attached_file_type = 0;
            }
        },

        async submitCash() {
            if (!this.validateForm()) {
                return;
            }

            try {
                this.is_saving = true;
                const formData = new FormData();

                formData.append('emp_id', this.form.emp_id || '');
                formData.append('pay_amount', this.form.pay_amount || '');
                formData.append('payment_date', this.form.payment_date || '');
                formData.append('payment_remarks', this.form.payment_remarks || '');

                if (this.form.attached_file) {
                    formData.append('attached_file', this.form.attached_file);
                }

                const response = await axios.post(CashAddEmployeeAPI, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.status === 200 || response.data.success) {
                    toast.success(response.data.message || 'Cash Added Successfully');
                    this.resetForm();
                } else {
                    toast.error('Failed: ' + (response.data.message || 'Error occurred'));
                }
            } catch (error) {
                console.error(error);
                toast.error('Error occurred, Please Try Again: ' + error.message);
            } finally {
                this.is_saving = false;
            }
        },

        resetForm() {
            this.form.pay_amount = '';
            this.form.payment_remarks = '';
            this.form.attached_file = null;
            this.form.attached_file_url = null;
            this.form.attached_file_type = 0;

            const today = new Date().toISOString().split('T')[0];
            this.form.payment_date = today;


        }
    }
}
</script>