<template>
    <div class="row mt-0 pt-0">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="showAttendanceINForm">IN</button>
            <button class="btn btn-primary m-2" @click="showAttendanceOUTForm">
                OUT
            </button>
            <button class="btn btn-primary m-2" @click="showReport" data-target="#report_modal">
                <!-- <i class="fa fa-file-text me-2" aria-hidden="true"></i> -->
                Update
            </button>
            <button class="btn btn-primary m-2" @click="showOvertimeForm">OverTime</button>
            <button class="btn btn-primary m-2" @click="showBioAttendence">Bio Attendance</button>
        </div>
        <hr />
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <h4>Attendance IN</h4>
            <!-- <attendance_in_component
                :projects="form_data.projects"
            ></attendance_in_component> -->

        </div>

        <div v-else-if="showForm == 2">
            <h4>Attendance OUT</h4>
            <!-- <h3>Search Inprogress</h3>
              <attendance-out-component
                :projects="form_data.projects"
            ></attendance-out-component> -->
        </div>

        <div v-else-if="showForm == 3">
            <h4>Attendance Update</h4>

        </div>

        <div v-else-if="showForm == 4">
            <overtime_component :projects="form_data.projects"></overtime_component>
        </div>
        <div v-else-if="showForm == 5">
            <attendence_bio_component :projects="form_data.projects"></attendence_bio_component>
        </div>
    </div>
</template>

<script>
import axios from "axios";

import { toast } from "vue3-toastify";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
// import { AttendanceProcessAPI } from "../../routes.js";
// import AttendanceINComponent from './AttendanceINComponent.vue';
// import AttendanceOUTComponent from './AttendanceOUTComponent.vue';
// import OvertimeComponent from './OvertimeComponent.vue';
//  from "../../../routes";

import AttendanceBioComponent from "./AttendanceBioComponent.vue";

const currentMonth = new Date().getMonth();
const currentYear = new Date().getFullYear();

export default {
    components: {
        // Multiselect,
        // DataTableComponent,
        // AttendanceINComponent,
        // AttendanceOUTComponent,
    },
    data() {
        return {
            // Table state
            table_loading: false,
            tableDatas: [],
            searchQuery: "",
            pageSize: 25,
            currentPage: 1,
            totalPages: 1,
            totalRows: 0,

            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
            attendanceReports: null,

            showForm: 4, // initially show IN form, 2= OUT form, 3=Report, 4=Overtime
            selected_project: null,
            multi_employee_ids: null,
            projects: [],
            month: currentMonth,
            year: currentYear,
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
        };
    },
    props: ["form_data"], // Receives data from index Blade
    computed: {
        projectOptions() {

            this.selected_project = this.projects.map((st) => ({
                label: st.proj_name, // show in dropdown
                value: st.proj_id, // put in v-model
            }));
        },

        startEntry() {
            return (this.currentPage - 1) * this.pageSize + 1;
        },
    },
    mounted() {
        this.projects = this.form_data.projects;
    },

    methods: {
        //  Menu buttons methods
        showAttendanceINForm() {
            this.showForm = 1;
        },
        showAttendanceOUTForm() {
            this.showForm = 2;
        },

        showReport() {
            this.showForm = 3;
        },
        showOvertimeForm() {
            this.showForm = 4;
        },
        showBioAttendence() {
            this.showForm = 5;
        },

        applyFilter() {

            this.fetchData(1);
        },


        resetFilter() {
            // this.month = null;
            // this.year = null;
            // this.selected_project = null;
            // this.fetchData(0);
        },



    },
};
</script>

<style scoped>
.m-2 {
    margin: 1px;
}
</style>
