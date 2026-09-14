<template>

    <!-- Advace Processing -->
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"></div>
                    <form @submit.prevent="processAdvanceSummaryReport"      class="d-flex">
                        <div class="card-body card_form">
                            <h5 style="text-align: center; color: red;">1. Advance Summary Report</h5>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >Emp. IDs:</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="employee_ids" v-model="employee_ids" placeholder="Enter Employee IDs Separated by Comma(,)" >
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  > </label>
                                <div class="col-md-9">
                                    <label style="color: blue; font-size: 15px;">OR</label>
                                 </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-md-3 control-label"> Porject:<span class="req_star">*</span></label>
                                <div class="col-md-9">
                                <select class="form-select" name="report_type_id" v-model="selectedProject"
                                        @change="searchProjectWiseEmployeeList">
                                        <option value="">Select Project</option>
                                        <option v-for="project in data.projects" :key="project.proj_id"
                                            :value="project.proj_id">
                                            {{ project.proj_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >Salary Month:</label>
                                <div class="col-md-9">
                                    <select class="form-select" name="month" v-model="selectedMonth" @change="selectedMonthName">
                                        <option value="">Select Month</option>
                                        <option v-for="amonth,index in months" :key="index" :value="index+1"> {{ amonth }} </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >Salary Year:</label>
                                <div class="col-md-9">
                                    <select class="form-select" name="year" id="year" v-model="selectedYear" >
                                        <option v-for="selectedYear in years" :key="selectedYear" :value="selectedYear"> {{ selectedYear }} </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >Employee Type:</label>
                                <div class="col-md-9">
                                    <select class="form-select" name="employee_type" v-model="selectEmployeeType">
                                        <option value="">Select Employee Type</option>
                                         <option :value="0">Direct Hourly</option>
                                         <option :value="1">Direct Basic</option>
                                         <option :value="2">Indirect Employee</option>
                                         <option :value="3">Direct & Indirect Basic</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >Report type:</label>
                                <div class="col-md-9">
                                    <select class="form-select" name="report_type" v-model="selectedReportType">
                                        <option value="">Select Report Type</option>
                                         <option :value="1">Selected Month Deduction base Summary</option>
                                         <option :value="2">Iqama & Advance Summary </option>
                                          <option :value="3">Current Deduction Setting </option>
                                        <option :value="4">Only Advance Summary </option>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer card_footer_button text-center">
                                <button type="submit" class="btn btn-primary ms-2">Advance Summary</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        <div class="col-md-2"></div>
    </div>
</template>

<script>
    // import axios from 'axios';
    // import { toast } from 'vue3-toastify';
    // import {AdvanceReportPage} from "../../rotue.js"
    // import { data } from 'jquery';

import axios from 'axios';
import { toast } from 'vue3-toastify';
import { AdvanceReportAPI } from "../../routes.js";


const currentYear = new Date().getFullYear();

export default{
    props:{
        data:{
            type:Object,
            required:true
        }
    },
    data() {
        return {
            months : [ 'January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December'],
            selectedMonth:'',
            years :[currentYear,currentYear -1,currentYear -2],
            selectedYear : currentYear,
            selectedProject:"",
            selectEmployeeType :"",
            selectedReportType :"",
            employee_ids:"",
        };
    },
    computed:{
        selectedMonthName() {
            if (this.selectedMonth) {
                return this.months[this.selectedMonth - 1];
            }
            return '';
        },
        changedYear(){
            return this.years[0];
        }
    } ,

    methods: {

            processAdvanceSummaryReport() {

                try {
                        // if(this.selectedMonth == '' || this.selectedYear == '' || this.selectedReportType == '' || (this.employee_ids == '' && this.selectedProject == '') )
                        // {
                        //     toast.error('Input Require Field Data');
                        //     return;
                        // }
                        // const formData = new FormData();
                        // formData.append('project_id', this.selectedProject);
                        // formData.append('month', this.selectedMonth);
                        // formData.append('year', this.selectedYear);
                        // formData.append('employee_type', this.selectEmployeeType);
                        // formData.append('report_type_id', this.selectedReportType);

                    const queryString = new URLSearchParams({
                        employee_ids:this.employee_ids,
                        project_id:this.selectedProject,
                        month:this.selectedMonth,
                        year:this.selectedYear,
                        employee_type:this.selectEmployeeType,
                        report_type:this.selectedReportType,
                    }).toString();

                    // Open in new tab
                    const url =   `${AdvanceReportAPI}?${queryString}`;
                    window.open(url, '_blank');
                } catch (error) {
                     toast.error('Operation Failed , Please try again');
                }
            },
        }

}


</script>
