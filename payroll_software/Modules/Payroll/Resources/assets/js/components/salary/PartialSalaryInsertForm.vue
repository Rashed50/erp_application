{{-- resources/js/components/payroll/PartialSalaryHistoryForm.vue --}}

<template>
    <employee_search_component @searching_result="handleSearchingResult"></employee_search_component>
    <div class="card-header">
            <h4>Add New Record</h4>
    </div>
    <div class="card">
        <div class="card-body">
            <form @submit.prevent="submit">
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Project *</label>
                            <select
                                v-model="form.project_id"
                                class="form-select"
                                :class="{ 'is-invalid': errors.project_id }"
                                required
                            >
                                <option value="">Select Project</option>
                                <option v-for="project in projects" :key="project.proj_id" :value="project.proj_id">
                                    {{ project.proj_name }}
                                </option>
                            </select>
                            <div v-if="errors.project_id" class="invalid-feedback">
                                {{ errors.project_id[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Month *</label>
                            <select
                                v-model="form.month"
                                class="form-select"
                                :class="{ 'is-invalid': errors.month }"
                                required
                            >
                                <option value="">Select Month</option>
                                <option v-for="(name, num) in months" :key="num" :value="num">
                                    {{ name }}
                                </option>
                            </select>
                            <div v-if="errors.month" class="invalid-feedback">
                                {{ errors.month[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                           <label>Year *</label>
                            <select
                                v-model="form.year"
                                class="form-select"
                                :class="{ 'is-invalid': errors.year }"
                                required
                            >
                                <option value="">Select Years</option>
                                    <option v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                            </select>

                            <div v-if="errors.year" class="invalid-feedback">
                                {{ errors.year[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Amount *</label>
                            <input
                                type="number"
                                v-model="form.amount"
                                class="form-control"
                                :class="{ 'is-invalid': errors.amount }"
                                placeholder="Enter amount"
                                min="1"
                                required
                            >
                            <div v-if="errors.amount" class="invalid-feedback">
                                {{ errors.amount[0] }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Paid Date *</label>
                            <input
                                type="date"
                                v-model="form.paid_at"
                                class="form-control"
                                :class="{ 'is-invalid': errors.paid_at }"
                                required
                            >
                            <div v-if="errors.paid_at" class="invalid-feedback">
                                {{ errors.paid_at[0] }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" :disabled="loading">
                            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                            {{ isEdit ? 'Update' : 'Save' }}
                        </button>
                        <button type="button" class="btn btn-secondary ml-2" @click="cancel">
                            Cancel
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
import {PartialSalaryStoreAPI} from '../../routes.js';

const today = new Date();
const currentYear = today.getFullYear();
const currentMonth = today.getMonth() + 1; // Months are zero-indexed

export default {
    // setup() {
    //     const { hasPermission } = useAuth();
    //     return {
    //         hasPermission,
    //     };
    // },
    props: {
        projects: {
            type: Object,
            required: true
        },
        authUserId: {
            type: Number,
            required: true
        },
        record: Object,
        isEdit: {
            type: Boolean,
            default: false
        },

        months: {
            type: Object,
            required: true
        },
        years: {
            type: Array,
            required: true
        }

    },
    data() {
        return {
            searched_employee: null,
            form: {
                emp_auto_id: '',
                project_id: '',
                month: currentMonth,
                year: currentYear,
                amount: 0,
                paid_at: today.toISOString().substr(0, 10), // Format as YYYY-MM-DD,
                inserted_by: '',
                updated_by: null
            },
            errors: {},
            loading: false,
        }
    },
    mounted() {

        if (this.isEdit && this.record) {
            console.log('editing record found: ', this.record);
            this.form = {
                emp_auto_id: this.record.emp_auto_id,
                project_id: this.record.project_id,
                month: this.record.month,
                year: this.record.year,
                amount: this.record.amount,
                paid_at: this.record.paid_at,
                inserted_by: this.record.inserted_by,
                updated_by: this.authUserId
            };
        }
    },
    methods: {
         handleSearchingResult(employee) {
            if(employee == null){
                // Employee Not found
                return;
            }
            console.log('receiving searching employee object ');
            this.searched_employee = employee;
            console.log(this.searched_employee);
            this.form.emp_auto_id = this.searched_employee.emp_auto_id;
        },
        async submit() {
            this.loading = true;
            this.errors = {};
            this.form.inserted_by =  this.authUserId;
            if(this.form.emp_auto_id === '') {
                toast.error('Please search an employee before submitting the form');
                this.loading = false;
                return;
             }
            try {
                const url = this.isEdit
                    ? `${PartialSalaryStoreAPI}/${this.record.psh_auto_id}`
                    : PartialSalaryStoreAPI;

                const method = this.isEdit ? 'put' : 'post';

                const response = await axios[method](url, this.form);

                    if (response.status === 201 || response.status === 200) {
                        this.searched_employee = null;
                        this.$emit('saved', response.data);
                        this.resetForm();

                    }else {
                        toast.error('An error occurred. Please try again.');
                    }

            } catch (error) {
                     toast.error('An error occurred. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
                emp_auto_id: '',
                project_id: '',
                month: '',
                year: '',
                amount: '',
                paid_at: '',
                inserted_by: this.authUserId,
                updated_by: null
            };
            this.errors = {};
        },
        cancel() {
            this.$emit('cancel');
            this.resetForm();
        }
    }
}
</script>
