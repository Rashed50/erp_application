<template>
    <div class="row mt-0 pt-0">
        <div class="col-md-12 text-center">
            <button class="btn btn-primary m-2" @click="processWPSEmpWorkRecord">Process</button>
            <button class="btn btn-warning m-2" @click="searchRecords">
                Search
            </button>
            <button
                class="btn btn-danger m-2"
                @click="showReport"
                data-target="#report_modal"
            >
                <i class="fa fa-file-text me-2" aria-hidden="true"></i>
                Reports
            </button>
        </div>
        <hr />
    </div>
    <div class="row">
        <div v-if="showForm == 1">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row form-group">
                                <label for="" class="col-md-2 text-right"
                                    >Month</label
                                >
                                <select
                                    v-model="this.month"
                                    class="form-select col-md-4"
                                >
                                    <option
                                        v-for="(month, index) in months"
                                        :key="index"
                                        :value="index + 1"
                                    >
                                        {{ month }}
                                    </option>
                                </select>

                                <label for="" class="col-md-2 text-right"
                                    >Year</label
                                >
                                <select
                                    v-model="this.year"
                                    class="form-select col-md-4"
                                >
                                    <option
                                        v-for="year in years"
                                        :key="year"
                                        :value="year"
                                    >
                                        {{ year }}
                                    </option>
                                </select>
                            </div>

                            <div class="row form-group">
                                <label for="" class="col-md-2 text-right"
                                    >Project</label
                                >
                                <select
                                    v-model="this.selected_project"
                                    class="form-select col-md-4"
                                    required
                                >
                                    <option
                                        v-for="(item, index) in this.projects"
                                        :key="index"
                                        :value="item.proj_id" options
                                    >
                                        {{ item.proj_name }}
                                    </option>
                                </select>

                                <label for="" class="col-md-2 text-right"
                                    >Employee IDs</label
                                >
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    id="multi_employee_ids"
                                    name="multi_employee_ids" :value="multi_employee_ids"
                                    multiple
                                />
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <!-- Filter Button -->
                            <div class="d-flex justify-content-end mb-3">
                                <button
                                    class="btn btn-outline-success btn-sm px-3 py-1"
                                    @click="applyFilter"
                                >
                                    <i
                                        class="fa fa-filter"
                                        aria-hidden="true"
                                    ></i>
                                    Filter
                                </button>
                            </div>

                            <!-- Reset Button -->
                            <div class="d-flex justify-content-end mb-3">
                                <button
                                    class="btn btn-outline-danger btn-sm px-3 py-1"
                                    @click="resetFilter"
                                >
                                    <i
                                        class="fa fa-eraser me-2"
                                        aria-hidden="true"
                                    ></i>
                                    Reset
                                </button>
                            </div>

                            <!-- Download Button -->
                            <div class="d-flex justify-content-end mb-3">
                                <button
                                    class="btn btn-outline-info btn-sm px-3 py-1"
                                    @click="downloadData"
                                >
                                    <i
                                        class="fa fa-download me-2"
                                        aria-hidden="true"
                                    ></i>
                                    Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- DataTable -->
                        <DataTableComponent
                            :columns="tableColumns"
                            :data="tableDatas"
                            :loading="table_loading"
                            :current-page="currentPage"
                            :total-pages="totalPages"
                            :total-rows="totalRows"
                            :page-size="pageSize"
                            :search-query="searchQuery"
                            search-placeholder="Search by name, ID, mobile..."
                            debounce-delay="600"
                            @page-change="handlePageChange"
                            @page-size-change="handlePageSizeChange"
                            @search-change="handleSearchChange"
                        >
                            <!-- S/N -->
                            <template #cell-sn="{ index }">
                                {{ startEntry + index }}
                            </template>


                            <!-- Name -->
                            <template #cell-employee_name="{ row }">
                                <div class="text-capitalize">
                                    <strong class="text-primary">{{
                                        row.employee_id
                                    }}</strong>
                                    <br />
                                    {{ row.employee_name }} <br />
                                    {{ row.akama_no }}
                                </div>
                            </template>

                            <!-- designation -->
                            <template #cell-catg_name="{ row }">
                                <div class="small">
                                    <div>
                                        <strong>{{
                                            row.catg_name || "-"
                                        }}</strong>
                                    </div>
                                </div>
                            </template>

                            <!-- Bank Infor -->
                            <template #cell-bank_info="{ row }">
                                <div class="text-capitalize">
                                    <strong class="text-primary">{{
                                        row.acc_iban
                                    }}</strong>
                                    <br />
                                    {{ row.bank_code }} <br />
                                    {{ row.acc_number }}
                                </div>
                            </template>
                            <template #cell-working_project="{ row }">
                                <div class="small">

                                    <div>
                                        <strong>{{
                                            row.last_working_project
                                        }}</strong>
                                    </div>
                                </div>
                            </template>

                            <template #cell-working_hours="{ row }">
                                <div class="medium">
                                    <div>
                                        <strong>Basic- {{
                                            row.basic_hours
                                        }}</strong>
                                        <br />
                                       OT-{{ row.over_time }}
                                    </div>
                                </div>
                            </template>

                            <template #cell-working_days="{ row }">
                                <div class="medium">

                                    <div>
                                       Working-  {{  row.working_days    }} <br />
                                       SL- {{  row.sick_leave    }} <br />
                                       Total-{{ row.present }} <br />
                                       Absent- {{ row.absent }} <br />
                                    </div>
                                </div>
                            </template>

                            <!-- Action Buttons -->
                            <template #cell-action="{ row }">
                                <div class="text-center">
                                    <button
                                        @click="viewSalaryDetails(row)"
                                        class="btn btn-info btn-sm"
                                        title="View Salary"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <!-- <button
                                        @click="editEmployee(row)"
                                        class="btn btn-warning btn-sm ml-1"
                                        title="Edit"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </button> -->
                                </div>
                            </template>
                        </DataTableComponent>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="showForm == 2">

              <h3>Search Inprogress</h3>
        </div>

        <div v-else-if="showForm == 3">
              <attendance_report  :data="form_data"
            ></attendance_report>
        </div>

        <div v-else-if="showForm == 4">
            <!-- <subcontractor-edit
                :subcontractor-id="currentEditId"
                :data_for_form="data_for_subcontractor_form"
            >
            </subcontractor-edit> -->
            <h3>Attendance Report Section</h3>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import DataTableComponent from "../Table/DataTableComponent.vue";

import { toast } from "vue3-toastify";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import { AttendanceProcessAPI } from "../../routes.js";
//  from "../../../routes";

const currentMonth = new Date().getMonth();
const currentYear = new Date().getFullYear();

export default {
    components: {
        Multiselect,
        DataTableComponent,
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

            // Table columns definition
            tableColumns: [
                { field: "sn", title: "S/N", width: "5%" },
                // { field: "employee_id", title: "ID", width: "5%" },
                {
                    field: "employee_name",
                    title: "Name",
                    width: "20%",
                },
                {
                    field: "catg_name",
                    title: "Designation",
                    width: "5%",
                },
                {
                    field: "bank_info",
                    title: "Bank Details",
                    width: "20%",
                },

                {
                    field: "working_project",
                    title: "Project",
                    width: "15%",
                },

                {
                    field: "working_days",
                    title: "Days",
                    width: "15%",
                },
                {
                    field: "working_hours",
                    title: "Hours",
                    width: "15%",
                },
                {
                    field: "action",
                    title: "Action",
                    width: "10%",
                    bodyClass: "text-center",
                },
            ],

            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
            attendanceReports: null,

            showForm: 1, // Initially 1 = new , 2= search, 3 = report, 4 = edit
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
        processWPSEmpWorkRecord() {
            this.showForm = 1;
        },

        searchRecords() {
            this.showForm = 2;
        },
        showReport() {
            this.showForm = 3;
        },

        applyFilter() {

            this.fetchData(1);
        },
        downloadData() {
            this.fetchData(2);
                console.log("Download excel");
                this.multi_employee_ids =   document.getElementById("multi_employee_ids").value;
                if (this.selected_project == null || this.multi_employee_ids == null) {
                    toast.error("Please select a project or enter employee ids");
                    return;
                }

                const params = new URLSearchParams({

                    project_id: this.selected_project || null,
                    month: this.month || null,
                    year: this.year || null,
                    employee_ids: this.multi_employee_ids,
                    operation_type: 2, // 1 = fetch, 2= download excel

                });

                window.open(
                    `/admin/payroll/api/attendance/?${params.toString()}`,
                    "_blank"
                );
        },

        resetFilter() {
            // this.month = null;
            // this.year = null;
            // this.selected_project = null;
           // this.fetchData(0);
        },


        async fetchData(operation_type = 1) {
            try {


                this.selected_project = this.selected_project || "";
                this.multi_employee_ids =   document.getElementById("multi_employee_ids").value;

                if( this.selected_project == null && this.multi_employee_ids == null){
                    toast.error("Please select a project or enter employee ids");
                    return;
                }
             //  this.month = 5;
              //  this.year = 2025;
               //
                this.table_loading = true;
                const response = await axios.get(AttendanceProcessAPI, {
                    params: {
                        page: this.currentPage,
                        per_page: this.pageSize,
                        search: this.searchQuery || undefined,

                        project_id: this.selected_project || null,
                        month: this.month || null,
                        year: this.year || null,
                        employee_ids: this.multi_employee_ids,
                        operation_type: operation_type, // 1 = fetch, 2= download
                    },
                });

                const res = response.data;
                //console.log("output ", res.data);

                if (res.success === false) {
                    toast.error(res.message || "Failed to load data");
                    this.tableDatas = [];
                    return;
                }

                this.tableDatas = res.data || [];
                this.totalRows = res.pagination.total;
                this.totalPages = res.pagination.last_page;
                this.currentPage = res.pagination.current_page;
                this.loading = false;
            } catch (error) {
                console.error("Failed to fetch salary data:", error);
                this.tableDatas = [];
                this.loading = false;
            } finally {
                this.table_loading = false;
            }
        },

        handlePageChange(page) {
            if (
                page >= 1 &&
                page <= this.totalPages &&
                page !== this.currentPage
            ) {
                this.currentPage = page;
                this.fetchData();
            }
        },

        handlePageSizeChange(size) {
            this.pageSize = parseInt(size);
            this.currentPage = 1;
            this.fetchData();
        },

        handleSearchChange(query) {
            this.searchQuery = query.trim();
            this.currentPage = 1;
            this.fetchData();
        },

        formatDate(date) {
            if (!date) return "-";
            return new Date(date).toLocaleDateString("en-GB"); // 28/11/2025
        },

        isExpiringSoon(date) {
            if (!date) return false;
            const diffDays =
                (new Date(date) - new Date()) / (1000 * 60 * 60 * 24);
            return diffDays <= 30 && diffDays >= 0;
        },

        viewSalaryDetails(row) {
            // পরে Modal বা Page এ নিয়ে যাবে
            alert(
                `Salary details for: ${row.employee_name} (ID: ${row.employee_id})`
            );
        },

        editEmployee(row) {
            // পরে Edit Page এ নিয়ে যাবে
            alert(`Edit employee: ${row.employee_name}`);
        },

        exportWPSFile() {
            alert(
                "WPS File Export feature coming soon! (Emirates NBD / ADCB format)"
            );
            // পরে .txt ফাইল জেনারেট করব
        },
    },
};
</script>

<style scoped>
.m-2 {
    margin: 1px;
}
</style>
