<template>
    <div>
        <div class="card mt-2 shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h4 class="mb-0 font-weight-bold">
                            <i class="fa fa-users mr-2"></i>
                            {{ active_report_form_title }}

                        </h4>
                    </div>
                    <div class="col-md-7 text-right">
                    </div>
                    <div class="col-md-1 text-right">
                        <button
                            @click="showReportForm(2)"
                            class="btn btn-sm btn-outline-primary"
                        >
                            Details
                            <i
                                :class="
                                    details_report
                                        ? 'fas fa-caret-up'
                                        : 'fas fa-caret-down'
                                "
                            ></i>
                        </button>
                    </div>
                     <div class="col-md-1 text-right">
                        <button
                            @click="showReportForm(1)"
                            class="btn btn-sm btn-outline-primary"
                        >
                            Summary
                            <i
                                :class="
                                    summary_report
                                        ? 'fas fa-caret-up'
                                        : 'fas fa-caret-down'
                                "
                            ></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Report UI Section -->
            <div v-if="summary_report" class="card-body border-top pb-0">
                <!-- GRID CONTAINER -->
                <div class="filter-grid">
                    <!-- Project -->
                    <div class="grid-item">
                        <label class="form-label fw-bold">Projects</label>
                        <Multiselect
                            v-model="filterProjects"
                            mode="multiple"
                            :options="projectOptions"
                            value-prop="value"
                            track-by="label"
                            label="label"
                            placeholder="Select Projects"
                            :searchable="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :create-option="false"
                            :hide-selected="false"
                            :caret="true"
                            class="multiselect-blue"
                            id="project_ids"
                        >
                            <template #tag="{ option, handleTagRemove }">
                                <div class="multiselect-tag is-user">
                                    {{ option.label }}
                                    <span
                                        class="multiselect-tag-remove"
                                        @click="handleTagRemove(option, $event)"
                                    >
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                            </template>

                            <template #multiplelabel="{ values }">
                                <div class="multiselect-multiple-label">
                                    {{ values.length }} Projects Selected
                                </div>
                            </template>

                            <template #nooptions>
                                <span class="text-muted"
                                    >No projects available</span
                                >
                            </template>
                        </Multiselect>
                    </div>

                      <div class="grid-item">
                        <label>From</label>
                        <input type="date" v-model="from_date" class="form-control" />
                     </div>


                      <div class="grid-item">
                        <label>To</label>
                        <input type="date" v-model="to_date"  class="form-control" />
                    </div>

                    <div class="grid-item">
                        <label class="form-label fw-bold">Report Category</label>
                         <select v-model="report_type"  id="report_type" class="form-select">
                            <option :value="null">Select Report Category</option>
                            <option value="1">Projects Month by Month</option>
                        </select>
                    </div>
                    <div class="grid-item">
                        <label class="form-label fw-bold">Report Format</label>
                        <select   v-model="report_format" id="report_format"  class="form-select" >
                            <option :value="1">PDF Preview</option>
                            <option :value="2">Excel Download</option>
                        </select>


                    </div>


                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end mt-2">
                    <button @click="processSummaryRport" class="btn btn-outline-primary mr-2">
                        <i class="fa fa-filter"></i> Process Summary
                    </button>
                    <button @click="resetFilter" class="btn btn-outline-danger">
                        <i class="fa fa-sync"></i> Reset
                    </button>
                </div>
            </div>


              <!-- Details Report UI Section -->
            <div v-if="details_report" class="card-body border-top pb-0">
                <!-- GRID CONTAINER -->
                <div class="filter-grid">
                    <!-- Project -->
                    <div class="grid-item">
                        <label class="form-label fw-bold">Projects</label>
                        <Multiselect
                            v-model="filterProjects"
                            mode="multiple"
                            :options="projectOptions"
                            value-prop="value"
                            track-by="label"
                            label="label"
                            placeholder="Select Projects"
                            :searchable="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :create-option="false"
                            :hide-selected="false"
                            :caret="true"
                            class="multiselect-blue"

                        >
                            <template #tag="{ option, handleTagRemove }">
                                <div class="multiselect-tag is-user">
                                    {{ option.label }}
                                    <span
                                        class="multiselect-tag-remove"
                                        @click="handleTagRemove(option, $event)"
                                    >
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                            </template>

                            <template #multiplelabel="{ values }">
                                <div class="multiselect-multiple-label">
                                    {{ values.length }} Projects Selected
                                </div>
                            </template>

                            <template #nooptions>
                                <span class="text-muted"
                                    >No projects available</span
                                >
                            </template>
                        </Multiselect>
                    </div>

                      <div class="grid-item">
                        <label>From</label>
                        <input type="date" v-model="from_date" class="form-control" />
                     </div>


                      <div class="grid-item">
                        <label>To</label>
                        <input type="date" v-model="to_date"  class="form-control" />
                    </div>

                    <div class="grid-item">
                        <label class="form-label fw-bold">Report Category</label>
                         <select v-model="report_type"  id="report_type" class="form-select">
                            <option :value="null">Select Report Category</option>
                            <option value="1">Projects Month by Month</option>
                        </select>
                    </div>
                    <div class="grid-item">
                        <label class="form-label fw-bold">Report Format</label>
                        <select   v-model="report_format" id="report_format"  class="form-select" >
                            <option :value="1">PDF Preview</option>
                            <option :value="2">Excel Download</option>
                        </select>


                    </div>


                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end mt-2">
                    <button @click="applyFilter" class="btn btn-outline-primary mr-2">
                        <i class="fa fa-filter"></i> Process
                    </button>
                    <button @click="resetFilter" class="btn btn-outline-danger">
                        <i class="fa fa-sync"></i> Reset
                    </button>
                </div>
            </div>


        </div>
    </div>
</template>

<script>
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import DataTableComponent from "../Table/DataTableComponent.vue";
import{AttendanceSummaryReportProcessAPI} from "../../routes.js"

export default {
    components: {
        DataTableComponent,
        Multiselect,
    },

    props: {
        data: {
            type: Object,
            required: true,
            default: () => ({}),
        },
    },

    data() {
        const today = new Date();
        return {
            summary_report: false,
            details_report: false,
            // Filter values
            filterProjects: [],
            from_date:today,
            to_date:today,
            report_type:null,
            report_format:1,
            active_report_form_title : ''

        };
    },

    computed: {

        projectOptions() {
            if (!this.data.projects || !Array.isArray(this.data.projects))
                return [];
            return this.data.projects.map((proj) => ({
                label: proj.proj_name,
                value: proj.proj_id,
            }));
        },



    },

    methods: {

        showReportForm(type =1){

            if(type == 1){
                // summary report ui
                this.summary_report= !this.summary_report;
                this.details_report= false;
                this.active_report_form_title = 'Attendance Summary Report';

            }else if(type == 2){
                this.summary_report= false;
                this.details_report= !this.details_report;
                this.active_report_form_title = 'Attendance Details Report';


            }
        },

        async processSummaryRport() {
            try {
                //this.multi_employee_ids =   document.getElementById("multi_employee_ids").value;
                // if (this.selected_project == null || this.multi_employee_ids == null) {
                //     toast.error("Please select a project or enter employee ids");
                //     return;
                // }
                 var  project_ids =
                            this.filterProjects.length > 0
                                ? this.filterProjects.map((p) => p.value) // [58, 62, 55]
                                : undefined;
                // project_ids = document.getElementById('project_ids').value;
                // debugger;

                // console.log(project_ids);

                const params = new URLSearchParams({

                    project_ids: this.filterProjects || null,
                    from_date: this.from_date || null,
                    to_date: this.to_date || null,
                    report_category:1,//this.report_category,
                    report_format: this.report_format, // 1 = pdf preview, 2= download excel

                });

                window.open(
                    `/admin/payroll/api/attendance/summary-report?${params.toString()}`,
                    "_blank"
                );

            } catch (error) {
                console.log(error);
                 this.tableDatas = [];
                this.$toast?.error?.("Failed to load data")
            } finally {

             }
        },

        //!========================== [ Filter ]============================
        applyFilter() {

        },

        resetFilter() {
            this.filterProjects = [];
            this.report_type = null;
         },
        //===================================================================

        formatDate(date) {
            if (!date) return "-";
            return new Date(date).toLocaleDateString("en-GB"); // 28/11/2025
        },

        formatMonthYear(row) {
            if (!row.slh_month || !row.slh_year) return "-";
            const monthNames = [
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
            ];
            return `${monthNames[row.slh_month - 1]} ${row.slh_year}`;
        },

        getMonthName(monthNumber) {
            const months = [
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
            ];
            return months[monthNumber - 1] || "Unknown";
        },


     },

    mounted() {

    },
};
</script>

<style scoped>
.text-capitalize {
    text-transform: capitalize;
}
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.multiselect-blue {
    --ms-tag-bg: #4361ee;
    --ms-tag-color: #fff;
    --ms-tag-radius: 6px;
    --ms-ring-color: #4361ee40;
}

.multiselect-tag {
    background: #4361ee !important;
    color: white !important;
    padding: 4px 8px !important;
    border-radius: 6px !important;
    margin: 2px !important;
    font-size: 0.85rem;
}

.multiselect-tag-remove {
    margin-left: 6px !important;
    cursor: pointer;
}

.multiselect-multiple-label {
    padding: 8px 12px;
    font-weight: 500;
    color: #4361ee;
}

.filter-grid {
    display: grid;
    gap: 10px;
    grid-template-columns: repeat(4, 1fr); /* Desktop - 4 columns */
}

/* 992px - Tablet (3 columns) */
@media (max-width: 992px) {
    .filter-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* 768px - Mobile (2 columns) */
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* 576px - Small Mobile (1 column) */
@media (max-width: 576px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}

/* Make actions vertically aligned nicely */
.actions {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}
</style>
