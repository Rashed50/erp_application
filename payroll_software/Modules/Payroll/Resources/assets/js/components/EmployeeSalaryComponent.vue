<template>
    <div>
        <div class="card mt-2 shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0 font-weight-bold">
                            <i class="fa fa-users mr-2"></i>
                            Payroll
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button @click="openPayslipModal" class="btn btn-sm btn-success mr-2"> <i
                                class="fa fa-envelope mr-1"></i>
                            Send Payslip
                        </button>
                        <button @click="this.changeSelectedMenu(2)" class="btn btn-sm btn-outline-primary"> WPS Salary
                        </button>
                        <button @click="this.changeSelectedMenu(1)" class="btn btn-sm btn-outline-primary"> Filter <i
                                :class="isFilter ? 'fas fa-caret-up' : 'fas fa-caret-down'"></i>
                        </button>

                        <button @click="this.changeSelectedMenu(3)" class="btn btn-sm btn-outline-primary"> Leave
                            Approved
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div v-if="this.checkSelectedMenu == 1" class="card-body border-top pb-0">
                <!--  Projects, Sponsors, Banks, Month -->
                <div class="row g-3">
                    <!-- Projects -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Projects</label>
                        <Multiselect v-model="filterProjects" mode="multiple" :options="projectOptions"
                            value-prop="value" track-by="label" label="label" placeholder="Select Projects"
                            :searchable="true" :close-on-select="false" :clear-on-select="false" :create-option="false"
                            :hide-selected="false" :caret="true" class="multiselect-blue">
                            <template #tag="{ option, handleTagRemove }">
                                <div class="multiselect-tag is-user">
                                    {{ option.label }}
                                    <span class="multiselect-tag-remove" @click="handleTagRemove(option, $event)">
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
                                <span class="text-muted">No projects available</span>
                            </template>
                        </Multiselect>
                    </div>

                    <!-- Sponsors -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Sponsors</label>
                        <Multiselect v-model="filterSponsors" mode="multiple" :options="sponsorOptions"
                            value-prop="value" track-by="label" label="label" placeholder="Select Sponsors"
                            :searchable="true" :close-on-select="false" :clear-on-select="false" :create-option="false"
                            :hide-selected="false" :caret="true" class="multiselect-blue">
                            <template #tag="{ option, handleTagRemove }">
                                <div class="multiselect-tag is-user">
                                    {{ option.label }}
                                    <span class="multiselect-tag-remove" @click="handleTagRemove(option, $event)">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                            </template>

                            <template #multiplelabel="{ values }">
                                <div class="multiselect-multiple-label">
                                    {{ values.length }} Sponsors Selected
                                </div>
                            </template>

                            <template #nooptions>
                                <span class="text-muted">No sponsors available</span>
                            </template>
                        </Multiselect>
                    </div>

                    <!-- Banks -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Banks</label>
                        <Multiselect v-model="filterBanks" :options="bankOptions" placeholder="Select Banks"
                            :searchable="true" mode="tags" />
                    </div>

                    <!-- Month -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Month</label>
                        <select v-model="filterMonth" class="form-select">
                            <option :value="null">Select Month</option>
                            <option v-for="m in 12" :key="m" :value="m">
                                {{ getMonthName(m) }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- SECOND ROW: Year, Report Format, Report Type & Action Buttons -->
                <div class="row g-3 align-items-end mt-1 mb-3">
                    <!-- Year -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Year</label>
                        <select v-model="filterYear" class="form-select">
                            <option :value="null">Select Year</option>
                            <option v-for="year in yearOptions" :key="year" :value="year">
                                {{ year }}
                            </option>
                        </select>
                    </div>

                    <!-- Report Format -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Report Format</label>
                        <select v-model="reportFormat" class="form-select">
                            <option value="1">PDF</option>
                            <option value="2">Excel</option>
                        </select>
                    </div>

                    <!-- Report Type -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Report Type</label>
                        <select v-model="reportType" class="form-select">
                            <option value="1">WPS Salary Report</option>
                            <option value="2">Salary Summary</option>
                            <option value="3">Detailed Salary Report</option>
                            <option value="4">Salary Process not yet Completed</option>
                        </select>
                    </div>

                    <!-- Action Buttons aligned with bottom right -->
                    <div class="col-md-3 d-flex align-items-center justify-content-start gap-2">
                        <button @click="applyFilter" class="btn btn-outline-primary me-1">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        <button @click="resetFilter" class="btn btn-outline-danger me-1">
                            <i class="fa fa-sync"></i> Reset
                        </button>
                        <button @click="downloadReport" class="btn btn-outline-primary">
                            <i class="fa fa-download"></i> Download
                        </button>
                    </div>
                </div>

                <!-- DataTable Component -->
                <div class="row">
                    <div class="col-12">
                        <DataTableComponent :columns="tableColumns" :data="tableDatas" :loading="table_loading"
                            :current-page="currentPage" :total-pages="totalPages" :total-rows="totalRows"
                            :page-size="pageSize" :search-query="searchQuery"
                            search-placeholder="Search by name, ID, mobile..." debounce-delay="600"
                            @page-change="handlePageChange" @page-size-change="handlePageSizeChange"
                            @search-change="handleSearchChange">

                            <!-- S/N -->
                            <template #cell-sn="{ index }">
                                {{ startEntry + index }}
                            </template>

                            <!-- Employee ID -->
                            <template #cell-employee_id="{ row }">
                                <strong class="text-primary">{{ row.employee_id }}</strong>
                            </template>

                            <!-- Employee Info. -->
                            <template #cell-employee_name="{ row }">
                                <div class="text-capitalize">{{ row.employee_name }}</div>
                                <div class="small">
                                    <strong>{{ row.designation }}</strong>
                                </div>
                            </template>

                            <!-- Akama -->
                            <template #cell-passport_iqama="{ row }">
                                <strong>{{ row.akama_no }}</strong>
                            </template>

                            <!-- Bank Info. -->
                            <template #cell-bank_info="{ row }">
                                <strong>{{ row.iban }}</strong>
                                <div class="small">
                                    <strong>{{ row.bank_code }}</strong>
                                </div>
                                <div class="small">
                                    <strong>{{ row.account_number }}</strong>
                                </div>
                            </template>

                            <!-- Basic & House -->
                            <template #cell-basic_n_house="{ row }">
                                <strong>{{ row.basic_amount }}</strong>
                                <div class="small">
                                    <strong>{{ row.house_rent }}</strong>
                                </div>
                            </template>

                            <!-- Salary -->
                            <template #cell-salary_info="{ row }">
                                <strong>{{ row.slh_total_salary }}</strong>
                            </template>

                            <!-- Month, Year -->
                            <template #cell-month_n_year="{ row }">
                                <div class="text-center">
                                    <div class="font-weight-bold text-primary">
                                        {{ getMonthName(row.slh_month) }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ row.slh_year }}
                                    </div>
                                </div>
                            </template>

                            <!-- Remarks -->
                            <template #cell-remarks="{ row }">
                                <div class="text-left">
                                    <span v-if="isIqamaExpired(row)" class="text-danger fw-bold">Iqama
                                        Expired!</span><br v-if="isIqamaExpired(row)" />
                                    <span v-if="isInvalidIban(row)" class="text-danger fw-bold">Invalid IBAN!</span><br
                                        v-if="isInvalidIban(row)" />
                                    <span v-if="isInvalidSalary(row)" class="text-danger fw-bold">Invalid
                                        Salary!</span><br v-if="isInvalidSalary(row)" />
                                    <span v-if="!isIqamaExpired(row) && !isInvalidIban(row) && !isInvalidSalary(row)"
                                        class="badge badge-success">OK</span>
                                </div>
                            </template>

                        </DataTableComponent>
                    </div>
                </div>
            </div>
            <div v-else-if="this.checkSelectedMenu == 2" class="text-center mt-0">
                <salary_process :projects="this.data.projects"></salary_process>
            </div>
        </div>

        <!-- Send Payslip Modal -->
        <div v-if="showPayslipModal" class="modal-backdrop" @click.self="closePayslipModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white text-dark">
                        <h5 class="modal-title">
                            <i class="fa fa-envelope mr-2"></i>
                            Send Payslip via Email
                        </h5>
                        <button type="button" class="btn-close btn-close-black" @click="closePayslipModal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="sendPayslipEmails">
                            <!-- Month & Year Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Salary Month <span
                                            class="text-danger">*</span></label>
                                    <select v-model="payslipForm.month" class="form-select" required>
                                        <option :value="null">Select Month</option>
                                        <option v-for="m in 12" :key="m" :value="m">
                                            {{ getMonthName(m) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                                    <select v-model="payslipForm.year" class="form-select" required>
                                        <option :value="null">Select Year</option>
                                        <option v-for="year in yearOptions" :key="year" :value="year">
                                            {{ year }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Project Selection -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Projects</label>
                                <Multiselect v-model="payslipForm.projectIds" mode="multiple" :options="projectOptions"
                                    value-prop="value" track-by="label" label="label"
                                    placeholder="Select Projects (leave empty for all)" :searchable="true"
                                    :close-on-select="false" :clear-on-select="false" :create-option="false"
                                    :hide-selected="false" :caret="true" class="multiselect-blue"
                                    @change="onProjectChange">
                                    <template #tag="{ option, handleTagRemove }">
                                        <div class="multiselect-tag is-user">
                                            {{ option.label }}
                                            <span class="multiselect-tag-remove"
                                                @click="handleTagRemove(option, $event)">
                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                    </template>
                                    <template #multiplelabel="{ values }">
                                        <div class="multiselect-multiple-label">
                                            {{ values.length }} Projects Selected
                                        </div>
                                    </template>
                                </Multiselect>
                            </div>

                            <!-- Employee Selection -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee IDs</label>
                                <input type="text" v-model="this.payslipForm.employeeIds" class="form-control"
                                    placeholder="Multiple Employee IDs" />

                            </div>

                            <!-- Info Alert -->
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle mr-2"></i>
                                <strong>Note:</strong> Payslips will be sent as PDF attachments via email to the
                                employees who
                                have valid email addresses.
                                The process runs in the background and may take a few minutes for the large numbers of
                                employees.
                            </div>

                            <!-- Action Buttons -->
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" @click="closePayslipModal">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-success"
                                    :disabled="sendingPayslips || !payslipForm.month || !payslipForm.year">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    <span v-if="sendingPayslips">Sending...</span>
                                    <span v-else>Send Payslips</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="this.checkSelectedMenu == 3" class="text-center mt-0">
            <LeaveApprovedSalaryPendingComponent :projects="this.data.projects" :leaveReasons="this.data.leave_reasons"
                :applicationStatuses="this.data.application_status">
            </LeaveApprovedSalaryPendingComponent>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import DataTableComponent from "./Table/DataTableComponent.vue";
import { toast } from "vue3-toastify";
import LeaveApprovedSalaryPendingComponent from './LeaveApprovedSalaryPendingComponent.vue';



export default {
    components: {
        DataTableComponent,
        Multiselect,
        LeaveApprovedSalaryPendingComponent,
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
            isFilter: false,
            checkSelectedMenu: 0, // 0 = default value not show eny form, 1 = Filtered, 2= salary processed

            // Filter values
            filterProjects: [],
            filterSponsors: [],
            filterBanks: [],
            filterMonth: today.getMonth(),
            filterYear: today.getFullYear(),
            reportFormat: 1, // 1 = PDF, 2 = Excel
            reportType: 1,   // Report type option

            // Table state
            table_loading: false,
            tableDatas: [],
            searchQuery: "",
            pageSize: 25,
            currentPage: 1,
            totalPages: 1,
            totalRows: 0,

            // Payslip Modal State
            showPayslipModal: false,
            sendingPayslips: false,
            loadingEmployees: false,
            payslipForm: {
                month: today.getMonth() + 1,
                year: today.getFullYear() - 1,
                projectIds: [],
                employeeIds: "",
            },

            // Table columns definition
            tableColumns: [
                { field: "sn", title: "S/N", width: "3%" },
                { field: "employee_id", title: "ID", width: "5%" },
                {
                    field: "employee_name",
                    title: "Employee Details",
                    width: "15%",
                },
                {
                    field: "passport_iqama",
                    title: "Iqama",
                    width: "10%",
                },
                {
                    field: "bank_info",
                    title: "Bank Info.",
                    width: "18%",
                },
                {
                    field: "basic_n_house",
                    title: "Basic & House",
                    width: "10%",
                },
                {
                    field: "salary_info",
                    title: "Salary",
                    width: "10%",
                },
                {
                    field: "month_n_year",
                    title: "Month",
                    width: "5%",
                    bodyClass: "text-center",
                },
                {
                    field: "remarks",
                    title: "Remarks",
                    width: "10%",
                },
                // {
                //     field: "action",
                //     title: "Action",
                //     width: "10%",
                //     bodyClass: "text-center",
                // },
            ],
        };
    },

    computed: {
        startEntry() {
            return (this.currentPage - 1) * this.pageSize + 1;
        },

        projectOptions() {
            if (!this.data.projects || !Array.isArray(this.data.projects))
                return [];
            return this.data.projects.map((proj) => ({
                label: proj.proj_name,
                value: proj.proj_id,
            }));
        },

        sponsorOptions() {
            if (!this.data.sponsors || !Array.isArray(this.data.sponsors))
                return [];
            return this.data.sponsors.map((spons) => ({
                label: spons.spons_name,
                value: spons.spons_id,
            }));
        },

        bankOptions() {
            if (!this.data.bank_names || !Array.isArray(this.data.bank_names))
                return [];
            return this.data.bank_names.map((bank) => ({
                label: bank.bn_name,
                value: bank.bn_auto_id,
            }));
        },

        yearOptions() {
            const current = new Date().getFullYear();
            const years = [];
            for (let y = current; y >= current - 4; y--) {
                years.push(y);
            }
            return years;
        },


    },

    methods: {
        changeSelectedMenu(value) {
            this.checkSelectedMenu = value;
        },
        async fetchData() {
            this.table_loading = true;
            // console.log(this.filterProjects,this.filterBanks);
            try {

                if (this.filterMonth == null) {
                    toast.error('Please Select Salary Month');
                    return;
                } else if (this.filterYear == null) {
                    toast.error('Please Select Salary Year');
                    return;
                }


                const response = await axios.get("/admin/payroll/api/salary", {
                    params: {
                        page: this.currentPage,
                        per_page: this.pageSize,
                        search: this.searchQuery || undefined,

                        // Multiple Projects → array of IDs
                        project_id: this.filterProjects,
                        sponsor_id: this.filterSponsors,

                        // Multiple Banks → array of IDs
                        bank_id:
                            this.filterBanks.length > 0
                                ? this.filterBanks.map((b) => b) // [1, 3, 9]
                                : undefined,
                        month: this.filterMonth || undefined,
                        year: this.filterYear || undefined,
                        report_format: 1,
                    },
                });

                const res = response.data;

                this.tableDatas = res.data || [];
                this.totalRows = res.pagination.total;
                this.totalPages = res.pagination.last_page;
                this.currentPage = res.pagination.current_page;
            } catch (error) {
                console.error("Failed to fetch salary data:", error);
                this.tableDatas = [];
                this.$toast?.error?.("Failed to load data") ||
                    alert("Error loading data");
            } finally {
                this.table_loading = false;
            }
        },
        downloadReport() {
            try {
                if (this.filterMonth == null) {
                    toast.error('Please Select Salary Month');
                    return;
                }
                if (this.filterYear == null) {
                    toast.error('Please Select Salary Year');
                    return;
                }

                const params = new URLSearchParams();

                if (this.filterProjects && this.filterProjects.length > 0) {
                    this.filterProjects.forEach((id) => {
                        params.append('project_id[]', id);
                    });
                }
                if (this.filterSponsors && this.filterSponsors.length > 0) {
                    this.filterSponsors.forEach((id) => {
                        params.append('sponsor_id[]', id);
                    });
                }
                if (this.filterBanks && this.filterBanks.length > 0) {
                    this.filterBanks.forEach((id) => {
                        params.append('bank_id[]', id);
                    });
                }
                if (this.searchQuery) {
                    params.append('search', this.searchQuery);
                }
                params.append('month', this.filterMonth);
                params.append('year', this.filterYear);
                params.append('report_format', this.reportFormat);
                params.append('report_type', this.reportType);
                params.append('export', this.reportFormat == 1 ? 'pdf' : 'excel');

                const url = `/admin/payroll/api/salary?${params.toString()}`;

                if (this.reportFormat == 1) {
                    // PDF - open in new tab
                    window.open(url, '_blank');
                } else {
                    // Excel - force download
                    window.location.href = url;
                }
            } catch (error) {
                console.log(error);
                toast.error('Failed to download report');
            }
        },

        //!========================== [ Filter ]============================
        applyFilter() {
            this.currentPage = 1;
            this.fetchData();
        },

        resetFilter() {
            this.filterProjects = [];
            this.filterSponsors = [];
            this.filterBanks = [];
            this.filterMonth = null;
            this.filterYear = new Date().getFullYear();
            this.reportFormat = 1;
            this.reportType = 1;
            this.currentPage = 1;
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

        isIqamaExpired(row) {
            if (!row.akama_expire_date) return true;
            return (
                new Date(row.akama_expire_date) <
                new Date().setHours(0, 0, 0, 0)
            );
        },

        isInvalidIban(row) {
            const iban = (row.iban || "").trim().toUpperCase();
            return !(iban.startsWith("SA") && iban.length === 24);
        },

        isInvalidSalary(row) {
            const salary = parseFloat(row.slh_total_salary);
            return !salary || salary <= 0;
        },

        viewSalaryDetails(row) {
            alert(
                `Salary details for: ${row.employee_name} (ID: ${row.employee_id})`
            );
        },

        editEmployee(row) {
            alert(`Edit employee: ${row.employee_name}`);
        },

        exportWPSFile() {
            alert(
                "WPS File Export feature coming soon! (Emirates NBD / ADCB format)"
            );
        },

        //====================== [ Payslip Email Methods ] =========================
        openPayslipModal() {
            this.showPayslipModal = true;
            this.payslipForm = {
                month: this.filterMonth || new Date().getMonth() + 1,
                year: this.filterYear - 1 || new Date().getFullYear() - 1,
                projectIds: [...this.filterProjects],
                employeeIds: "",
            };
            if (this.payslipForm.projectIds.length > 0) {
                this.fetchEmployeesForPayslip();
            }
        },

        closePayslipModal() {
            this.showPayslipModal = false;
            this.payslipForm = {
                month: null,
                year: null,
                projectIds: [],
                employeeIds: [],
            };


        },

        async onProjectChange() {

        },



        async sendPayslipEmails() {
            if (!this.payslipForm.month || !this.payslipForm.year) {
                toast.error("Please select month and year");
                return;
            }

            if (this.payslipForm.projectIds.length === 0 && this.payslipForm.employeeIds.length === 0) {
                toast.error("Please select at least one project or employee");
                return;
            }

            this.sendingPayslips = true;
            try {
                const response = await axios.post("/admin/payroll/api/payslip/send-emails", {
                    month: this.payslipForm.month,
                    year: this.payslipForm.year,
                    project_ids: this.payslipForm.projectIds,
                    employee_ids: this.payslipForm.employeeIds,
                });

                if (response.data.success) {
                    toast.success(response.data.message);
                    this.closePayslipModal();
                } else {
                    toast.error(response.data.message || "Failed to send payslips");
                }
            } catch (error) {
                console.error("Error sending payslips:", error);
                const errorMessage = error.response?.data?.message || "Failed to send payslips";
                toast.error(errorMessage);
            } finally {
                this.sendingPayslips = false;
            }
        },
        //==========================================================================

        //====================== [ Pagination & Search ] =========================
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
        //==========================================================================
    },

    mounted() {
        // this.fetchData();
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
    --ms-tag-bg: #3490dc;
    --ms-tag-color: #fff;
    --ms-tag-radius: 6px;
    --ms-ring-color: #3490dc40;
}

.multiselect-tag {
    background: #3490dc !important;
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
    color: #3490dc;
}

.filter-grid {
    display: grid;
    gap: 10px;
    grid-template-columns: repeat(4, 1fr);
    /* First row: 4 columns */
}

/* Second row: Report Format & Report Type only (2 columns on left) */
.filter-grid-row2 {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.filter-row-2 {
    padding-top: 4px;
}

/* 992px - Tablet (3 columns for first row) */
@media (max-width: 992px) {
    .filter-grid:not(.filter-grid-row2) {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* 768px - Mobile (2 columns for first row) */
@media (max-width: 768px) {
    .filter-grid:not(.filter-grid-row2) {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* 576px - Small Mobile (1 column for first row) */
@media (max-width: 576px) {
    .filter-grid:not(.filter-grid-row2) {
        grid-template-columns: 1fr;
    }

    .filter-grid-row2 {
        grid-template-columns: 1fr;
    }
}

/* Make actions vertically aligned nicely */
.actions {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

/* Modal Styles */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

.modal-dialog {
    background: white;
    border-radius: 8px;
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content {
    border: none;
}

.modal-header {
    border-radius: 8px 8px 0 0;
    padding: 1rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.btn-close-white {
    filter: invert(1);
}
</style>
