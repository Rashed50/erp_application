<template>
    <div>
        <div class="card p-3 shadow-sm mb-4 border border-light">
            <!-- Row 1: Bonus Type (Sponsor-er Jaygay) -->
            <div class="row align-items-center mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <label class="fw-bold me-2 text-nowrap" style="min-width: 90px; text-align: right;">
                        Bonus Type:
                    </label>
                    <select class="form-select" v-model="form.bonus_type">
                        <option value="1">-- Select Types --</option>
                        <option value="5">Annual Bonus</option>
                        <option value="10">Performance Bonus</option>
                        <option value="15">Festival Bonus</option>
                        <option value="30">Single Air Ticket</option>
                        <option value="35">Return Air Ticket</option>
                        <option value="37">Round Air Ticket</option>
                        <option value="40">One Month Salary</option>
                        <option value="45">Leave Salary</option>
                        <option value="50">Bonus and Air Ticket</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: From Date, To Date, Employee ID Input & Search Button -->
            <div class="row align-items-center g-2">
                <!-- From Date -->
                <div class="col-md-5 d-flex align-items-center">
                    <label class="fw-bold me-2 text-nowrap" style="min-width: 50px; text-align: right;">
                        From:<span class="text-danger">*</span>
                    </label>
                    <input type="date" class="form-control" v-model="form.from_date" />
                </div>

                <!-- To Date -->
                <div class="col-md-5 d-flex align-items-center">
                    <label class="fw-bold me-2 text-nowrap" style="min-width: 35px; text-align: right;">
                        To:<span class="text-danger">*</span>
                    </label>
                    <input type="date" class="form-control" v-model="form.to_date" />
                </div>

           

                <!-- Search Button (Exact Navy Tone matching image) -->
                <div class="col-md-2 text-end">
                    <button type="button" class="btn text-white fw-bold px-4 w-100" style="background-color: #2b3e4e;"
                        @click="processBonusReport" :disabled="is_processing">
                        <i class="fas fa-spinner fa-spin me-1" v-if="is_processing"></i>
                        {{ is_processing ? 'Processing...' : 'Process' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { EmployeeBonusReportAPI } from '../../routes.js';

export default {
    data() {
        const today = new Date().toISOString().split('T')[0];

        return {
            is_processing: false,
            form: {
                employee_id: '',
                bonus_type: '1',
                bonus_report_type: 1,
                from_date: today,
                to_date: today,
            }
        }
    },

    methods: {
        async processBonusReport() {
            if (!this.form.from_date || !this.form.to_date) {
                toast.warning('Please select both From and To dates');
                return;
            }

            this.is_processing = true;

            try {
                const formData = new FormData();
                formData.append('bonus_report_type', this.form.bonus_report_type);
                formData.append('employee_id', this.form.employee_id || '');
                formData.append('bonus_type', this.form.bonus_type || '2');
                formData.append('from_date', this.form.from_date);
                formData.append('to_date', this.form.to_date);

                const response = await axios.get(EmployeeBonusReportAPI, {
                    params: new URLSearchParams(formData)
                });

                if (response.data) {
                    const reportWindow = window.open('', '_blank');
                    reportWindow.document.write(response.data);
                    reportWindow.document.close();
                } else {
                    toast.error('No report data returned from server');
                }

            } catch (error) {
                console.error(error);
                toast.error('Error occurred, Please Try Again: ' + (error.response?.data?.message || error.message));
            } finally {
                this.is_processing = false;
            }
        }
    }
}
</script>