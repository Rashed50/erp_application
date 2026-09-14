<template>
    <div>
        <div class="card mt-2 shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="mb-0 font-weight-bold">
                            <i class="fa fa-users mr-2"></i>
                            {{ active_report_form_title }}

                        </h4>
                    </div>
                    <div class="col-md-5 text-right">
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
                        <label class="form-label fw-bold">Vehicle</label>
                        <select v-model="this.selected_vehicles" class="form-select" required>
                            <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                                {{ sc.veh_name }}
                            </option>
                        </select>
                    </div>

                    <!-- <div class="grid-item">
                        <label class="form-label fw-bold">Vehicle</label>
                        <select v-model="this.form_data.veh_auto_id" class="form-select" required>
                            <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                                {{ sc.veh_name }}
                            </option>
                        </select>
                    </div> -->

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
                            <option value="1">Vehicle Maintenance Details</option>
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
                        <i class="fa fa-filter"></i>Generate Report
                    </button>
                    <button @click="resetFilter" class="btn btn-outline-danger">
                        <i class="fa fa-sync"></i> Reset
                    </button>
                </div>
            </div>


              <!-- Details Report UI Section -->
            <div v-if="details_report" class="card-body border-top pb-0">
                <!-- GRID CONTAINER -->
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
                        <label class="form-label fw-bold">Vehicle</label>
                        <select v-model="this.selected_vehicles" class="form-select" required>
                            <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                                {{ sc.veh_name }}
                            </option>
                        </select>
                    </div>

                    <!-- <div class="grid-item">
                        <label class="form-label fw-bold">Vehicle</label>
                        <select v-model="this.form_data.veh_auto_id" class="form-select" required>
                            <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                                {{ sc.veh_name }}
                            </option>
                        </select>
                    </div> -->

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
                            <option value="1">Vehicle Maintenance Details</option>
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
import {toast} from "vue3-toastify";
//import { computed, onMounted, ref } from "vue";

 // import{AttendanceSummaryReportProcessAPI} from "../../routes.js"


export default {
    components: {
        Multiselect,
    },


    props: {
        data_for_form: {
            type: Object,
            required: true
        }
    },

    data() {
         return {
            summary_report: false,
            details_report: false,
            // Filter values
            filterProjects: [],
            selected_vehicles:'',
            from_date: new Date().toISOString().slice(0, 10),
            to_date: new Date().toISOString().slice(0, 10),
            report_type:null,
            report_format:1,
            active_report_form_title : 'Transportation Reports'

        };
    },

    computed: {

        projectOptions() {
            if (!this.data_for_form.projects || !Array.isArray(this.data_for_form.projects))
                return [];
            return this.data_for_form.projects.map((proj) => ({
                label: proj.proj_name,
                value: proj.proj_id,
            }));
        },

         vehicleOptions() {
            if (!this.data_for_form.vehicles || !Array.isArray(this.data_for_form.vehicles))
                return [];
            return this.data_for_form.vehicles.map((vh) => ({
                label: vh.veh_name,
                value: vh.veh_id,
            }));
        },





    },

    methods: {

        showReportForm(type =1){

            if(type == 1){
                // summary report ui
                this.summary_report= !this.summary_report;
                this.details_report= false;
                this.active_report_form_title = 'Transportation Summary Report';

            }else if(type == 2){
                this.summary_report= false;
                this.details_report= !this.details_report;
                this.active_report_form_title = 'Transportation Details Report';


            }
        },

        async processSummaryRport() {
            try {
                // Validation Logic
                const hasProjects = this.filterProjects.length > 0;
                const hasVehicle = this.selected_vehicles && this.selected_vehicles !== '';

                // Case 1: If projects are selected, vehicle is optional
                if (hasProjects) {
                    console.log('Projects selected:', this.filterProjects);
                } else if (!hasProjects && !hasVehicle) {
                    // Case 2: If no projects selected, user MUST select a vehicle
                    toast.error("Please select either a Project or a Vehicle");
                    return;
                } else if (!hasProjects && hasVehicle) {
                    // Case 3: No projects, but vehicle selected - OK
                    console.log('Vehicle selected:', this.selected_vehicles);
                }

                // Build query parameters
                const params = new URLSearchParams();

                if (hasProjects) {
                    // When projects are selected: send project_ids, ignore vehicle selection
                    const projectIds = this.filterProjects
                        .map((project) => (project && typeof project === 'object' ? project.value : project))
                        .filter((projectId) => projectId !== null && projectId !== undefined && projectId !== '');

                    params.append('project_ids', projectIds.join(','));
                    params.append('has_projects', 1);
                } else if (hasVehicle) {
                    // When only vehicle is selected: send vehicle_ids
                    params.append('vehicle_ids', this.selected_vehicles);
                    params.append('has_projects', 0);
                }

                // Add date filters
                if (this.from_date) {
                    params.append('from_date', this.from_date);
                }
                if (this.to_date) {
                    params.append('to_date', this.to_date);
                }

                params.append('report_category', 1);
                params.append('report_format', this.report_format);

                window.open(
                    `/admin/transportation/reports/api/reports-generate?${params.toString()}`,
                    "_blank"
                );

            } catch (error) {
                console.log(error);
                this.$toast?.error?.("Failed to generate report")
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

         getAttachmentUrl(path) {
            debugger;
            const baseUrl = `${window.location.origin}/storage/`;
            return `${baseUrl}${path}`;
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



<!-- <template>
    <div class="p-4 border rounded-lg bg-white">

        <div class="row mb-1">
            <label class="col-md-2 col-form-label">Vehicle Name</label>
            <div class="col-md-3">
                <select v-model="form.veh_auto_id" class="form-select" required>
                    <option v-for="sc in data_for_form.vehicles" :key="sc.veh_id" :value="sc.veh_id">
                        {{ sc.veh_name }}
                    </option>
                </select>
            </div>

            <label class="col-md-1 col-form-label">Year</label>
            <div class="col-md-2">
                <select v-model="form.year" class="form-select">
                    <option v-for="year in years" :key="year" :value="year">
                        {{ year }}
                    </option>
                </select>
            </div>

            <label class="col-md-1 col-form-label">Month</label>
            <div class="col-md-2">
                <select v-model="form.month" class="form-select">
                    <option v-for="(month, index) in months" :key="index" :value="index + 1">
                        {{ month }}
                    </option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" @click="searchServicesForListView" class="btn btn-primary">Search</button>
            </div>

        </div>
    </div>
    <hr>




</template>

<script>
import axios from "axios";
import { toast } from 'vue3-toastify';
import { VehicleServicing_URLS } from "../router.js";
import Swal from "sweetalert2";

const currentMonth = new Date().getMonth;
const currentYear = new Date().getFullYear();

export default {
    data() {
        return {
            services: [],
            searchQuery: "",
            form: {
                veh_auto_id: '',
                month: currentMonth,
                year: new Date().getFullYear(),
            },
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            years: [currentYear, currentYear - 1], // Last 2 years + current year
            tableData: { data: [], total: 0, per_page: 10, current_page: 1, last_page: 1 },
            perPage: 10,
        };
    },
    props: {
        data_for_form: {
            type: Object,
            required: true
        }
    },
    computed: {
        totalPages() {
            return Array.from({ length: this.tableData.last_page }, (_, i) => i + 1);
        }
    },

    mounted() {

    },
    methods: {

        async searchServicesForListView() {
            try {
                const formData = new FormData();
                formData['veh_auto_id'] = this.form.veh_auto_id;
                formData['month'] = this.form.month;
                formData['year'] = this.form.year;
                const response1 = await axios.get(VehicleServicing_URLS.SearchServicingAPI, formData);
                this.tableData.data = response1.data.data;
                console.log("List Data =", response1.data);

            } catch (Error) {

                console.log('error');
                toast.error("Operation Failed , Reload Again")

            }
        },




        formatDateTime(date) {
            if (!date) return '';

            // Format the date as DD/MM/YYYY
            const formattedDate = new Intl.DateTimeFormat('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(new Date(date));

            // Format the time as hh:mm AM/PM
            const formattedTime = new Intl.DateTimeFormat('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            }).format(new Date(date));

            return { formattedDate, formattedTime };
        },

        capitalize(value) {
            if (!value) return '';
            return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        }

    },

};
</script>



<style scoped>
.row {
    margin-top: 10px;
}

.edit_btn {
    cursor: pointer
}

.delete_btn {
    cursor: pointer
}
</style> -->
