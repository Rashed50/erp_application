<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <h4>Subcontractor Report</h4>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filters -->
                <div class="row">
                    <div class="filter-grid">
                        <div class="mb-0">
                            <label for="">Report Type</label>
                            <Multiselect v-model="reportType" :options="reportOptions" placeholder="Report Type"
                                :searchable="true" />
                        </div>
                        <div class="mb-0">
                            <label for="">Subcontractor</label>
                            <Multiselect v-model="subcontractorsFilter" :options="subcontractorsOptions"
                                placeholder="Select Subcontractor" :searchable="true" />
                        </div>
                        <div class="mb-0">
                            <label for="">Project</label>
                            <Multiselect mode="multiple" value-prop="value" track-by="label" label="label"
                                v-model="projectFilter" :options="projectOptions" placeholder="Select projects"
                                :searchable="true" :close-on-select="false" :clear-on-select="false"
                                :create-option="false" :hide-selected="false" :caret="true" 
                                class="multiselect-blue"
                                 
                                
                                
                                />
                        </div>

                        <div class="mb-0">
                            <label for="">From</label>
                            <input type="date" class="form-control" v-model="fromDate" />
                        </div>

                        <div class="mb-0">
                            <label for="">To</label>
                            <input type="date" class="form-control" v-model="toDate" />
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <!-- Filter Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-success btn-sm px-3 py-1" @click="applyFilter">
                                <i class="fa fa-filter" aria-hidden="true"></i>
                                Filter
                            </button>
                        </div>

                        <!-- Reset Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-danger btn-sm px-3 py-1" @click="resetFilter">
                                <i class="fa fa-eraser me-2" aria-hidden="true"></i>
                                Reset
                            </button>
                        </div>

                        <!-- Download Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-info btn-sm px-3 py-1" @click="downloadPDF">
                                <i class="fa fa-download me-2" aria-hidden="true"></i>
                                Download
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="row">
                    <div class="col-12">
                        <DataTable :data="salesReports" :columns="columns" :per-page="perPage" :search="search"
                            @update:perPage="
                                (val) => {
                                    perPage = val;
                                    fetchData();
                                }
                            " @update:search="
                                (val) => {
                                    search = val;
                                    fetchData();
                                }
                            " @page-change="(page) => changePage(page)">
                            <template #action="{ row }">
                                <button class="btn btn-info btn-sm" @click="editReport(row.action)">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm" @click="deleteReport(row.action)">
                                    Delete
                                </button>
                            </template>

                            <template #footer>
                                <tr v-if="footerTotals">
                                    <td colspan="4">Totals</td>
                                    <td>{{ footerTotals.total_amount }}</td>
                                    <td>{{ footerTotals.total_vat }}</td>
                                    <td>{{ footerTotals.total_grand }}</td>
                                    <td>{{ footerTotals.retention_amount }}</td>
                                    <td>
                                        {{ footerTotals.receivable_amount }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";
// import DataTable from "../../components/DataTable.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import { toast } from "vue3-toastify";

// Props =================================================================
const props = defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
    subcontractors: {
        type: Array,
        default: () => [],
    },
    status: {
        type: Array,
        default: () => [],
    },
});

// State =================================================================
const salesReports = ref({
    data: [],
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1,
});
const footerTotals = ref(null);

const perPage = ref(10);
const search = ref("");

// Filters

const reportType = ref("");
const projectFilter = ref(null);
const subcontractorsFilter = ref(null);
const statusFilter = ref("");
const fromDate = ref(new Date().toISOString().slice(0, 10));
const toDate = ref(new Date().toISOString().slice(0, 10));

// Computed ================================================================

const reportOptions = ref([
    {
        label: "Multi-Subcon Monthly Work Details",
        value: "1",
    },
    {
        label: "Multi-Subcon Projectbase Salary",
        value: "2",
    },
    {
        label: "Outstanding Statement",
        value: "3", // 3
    },
    {
        label: "Single Subcon Manpower Details",
        value: "4", // 4
    },
    {
        label: "Month by Month Invoice & Payment ",
        value: "5", //5
    },
    {
        label: "Payment Details",
        value: "6", //6
    },
    {
        label: "Active Manpower Report",
        value: "7", //7
    },
    {
        label: "Subcontracts Details",
        value: "8", //8
    },
]);

// Computed: project_options with proj_name + proj_code
const projectOptions = computed(() =>
    props.projects.map((p) => ({
        label: `${p.proj_name} (${p.proj_code})`,
        value: p.proj_id,
    }))
);

const subcontractorsOptions = computed(() =>
    props.subcontractors.map((s) => ({
        label: `${s.subcon_name}`,
        value: s.subcon_auto_id,
    }))
);

// data_for_form

const statusOptions = computed(() =>
    props.status.map((st) => ({
        label: st.name, // show in dropdown
        value: st.id, // put in v-model
    }))
);

// Methods =================================================================
// Column Config
const columns = [
    { key: "sn", label: "S/N" },
    { key: "invoice_no", label: "Invoice No" },
    { key: "project_name", label: "Project Name" },
    { key: "date", label: "Date" },
    { key: "total_amount", label: "Total Amount" },
    { key: "vat", label: "VAT" },
    { key: "total_vat", label: "Total + VAT" },
    { key: "retention_amount", label: "Retention Amount" },
    { key: "receivable_amount", label: "Receivable Amount" },
    { key: "chart_of_accounts", label: "Chart of Accounts" },
    // { key: "action", label: "Action" },
];

// Filter Button Action
const applyFilter = () => {
    fetchData();
};

const resetFilter = () => {
    projectFilter.value = null;
    subcontractorsFilter.value = null;
    statusFilter.value = null;
    fromDate.value = new Date().toISOString().slice(0, 10);
    toDate.value = new Date().toISOString().slice(0, 10);
    fetchData();
};

const fetchData = async (page = 1) => {
    console.log("Fetching data...");
    try {
        const response = await axios.get(
            "/admin/accounting/reports/sales-report/list",
            {
                params: {
                    page,
                    per_page: perPage.value || 10,
                    search: search.value || null,
                    project_id: projectFilter.value || null,
                    sr_status: statusFilter.value || null,
                    due_date_from: fromDate.value || null,
                    due_date_to: toDate.value || null,
                },
            }
        );

        const res = response.data;

        // Pagination Data
        salesReports.value = {
            ...res.records,
            data: res.records.data.map((item, index) => ({
                sn: (res.records.from ?? 1) + index,
                invoice_no: item.sr_invoice_no,
                project_name: `${item.project_details?.proj_name ?? "-"} (${item.project_details?.proj_code ?? "-"
                    })`,
                date: item.sr_issue_date,
                total_amount: item.sr_total_amount,
                vat: item.sr_vat_amount,
                total_vat: item.sr_grand_total_amount,
                retention_amount: item.retention_amount,
                receivable_amount:
                    (item.sr_grand_total_amount || 0) -
                    (item.retention_amount || 0),
                chart_of_accounts: item.debit?.chart_of_acct_name ?? "-",
                action: item.sr_auto_id,
            })),
        };

        // Footer Totals
        footerTotals.value = res.totals || null;
    } catch (error) {
        console.error("Error fetching sales reports:", error);
    }
};

const downloadPDF = () => {
    console.log("Download PDF");
    // Manpower payment report shows all suppliers — no subcontractor selection needed
    const reportsRequiringSubcontractor = ['single-report', 'report-3', 'summary-report'];
    if (reportsRequiringSubcontractor.includes(reportType.value) && !subcontractorsFilter.value) {
        toast.error("Please select a subcontractor");
        return;
    }
    const params = new URLSearchParams({
        reportType: reportType.value || "",
        project_id: projectFilter.value || "",
        subcontractor_id: subcontractorsFilter.value || "",
        service_type: statusFilter.value || "",
        from_date: fromDate.value || new Date().toISOString().slice(0, 10),
        to_date: toDate.value || new Date().toISOString().slice(0, 10),
    });

    window.open(
        `/admin/subcontractor/report/single-month-service-payment/pdf?${params.toString()}`,
        "_blank"
    );
};

const changePage = (page) => fetchData(page);
const editReport = (id) => console.log("Edit report:", id);
const deleteReport = (id) => console.log("Delete report:", id);

// Initial Load
onMounted(() => fetchData());
</script>

<style scoped>
.btn_width {
    min-width: 50px;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    padding: 10px;
}

.filter-grid .mb-3 {
    min-width: 300px;
}

.input_h {
    height: 43px;
}

.flex-container {
    display: flex;
    flex-direction: row;
    justify-content: start;
    gap: 10px;
}

.flex-container a {
    font-size: 17px;
    text-align: center;
    cursor: pointer;
}

.view_btn:hover i {
    color: rgb(205, 11, 235);
}

.edit_btn:hover i {
    color: rgb(207, 46, 21);
}

.form-control {
    border: 2px solid #ccc;
    border-radius: 3px;
    padding: 8px;
    transition: border-color 0.3s;
    min-width: 300px;
}

.sky-border {
    border-color: skyblue;
    box-shadow: 0 0 5px rgba(135, 206, 250, 0.5);
}

.form-control:focus {
    border-color: dodgerblue;
    outline: none;
    box-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
}
</style>
