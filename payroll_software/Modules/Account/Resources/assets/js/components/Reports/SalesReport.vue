<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <h4>Sales Report</h4>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">

                <!-- Filters -->
                <div class="row">
                    <div class="filter-grid">
                        <div class="mb-3">
                            <label for="">Project</label>
                            <Multiselect
                                v-model="projectFilter"
                                :options="projectOptions"
                                placeholder="Select project"
                                :searchable="true"
                            />
                        </div>

                        <div class="mb-3">
                            <label for="">Status</label>
                            <Multiselect
                                v-model="statusFilter"
                                :options="statusOptions"
                                placeholder="Select status"
                                :searchable="true"
                                :clearable="true"
                            />
                        </div>

                        <div class="mb-3">
                            <label for="">Due Date From</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="dueDateFrom"
                            />
                        </div>

                        <div class="mb-3">
                            <label for="">Due Date To</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="dueDateTo"
                            />
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <!-- Filter Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <button
                                class="btn btn-outline-success btn-sm px-3 py-1"
                                @click="applyFilter"
                            >
                                <i class="fa fa-filter" aria-hidden="true"></i>
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
                                @click="downloadPDF"
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

                <!-- Data Table -->
                <div class="row">
                    <div class="col-12">
                        <DataTable
                            :data="salesReports"
                            :columns="columns"
                            :per-page="perPage"
                            :search="search"
                            @update:perPage="
                                (val) => {
                                    perPage = val;
                                    fetchData();
                                }
                            "
                            @update:search="
                                (val) => {
                                    search = val;
                                    fetchData();
                                }
                            "
                            @page-change="(page) => changePage(page)"
                        >
                            <!-- Custom Action Buttons -->
                            <template #action="{ row }">
                                <button
                                    class="btn btn-info btn-sm"
                                    @click="editReport(row.action)"
                                >
                                    Edit
                                </button>
                                <button
                                    class="btn btn-danger btn-sm"
                                    @click="deleteReport(row.action)"
                                >
                                    Delete
                                </button>
                            </template>

                            <!-- Dynamic Footer -->
                            <template #footer>
                                <tr v-if="footerTotals">
                                    <td colspan="4">Totals</td>
                                    <td>{{  Math.round( footerTotals.total_amount ) }}</td>
                                    <td>{{ Math.round( footerTotals.total_vat ) }}</td>
                                    <td>{{Math.round( footerTotals.total_grand  )}}</td>
                                    <td>{{ Math.round( footerTotals.retention_amount)  }}</td>
                                    <td>
                                        {{ Math.round(footerTotals.receivable_amount) }}
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
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import DataTable from "../../components/DataTable.vue";

import { toast } from "vue3-toastify";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

// Props =================================================================
const props = defineProps({
    projects: {
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
const projectFilter = ref(null);
const statusFilter = ref("");
const dueDateFrom = ref("");
const dueDateTo = ref("");

// Computed ================================================================
// Computed: project_options with proj_name + proj_code
const projectOptions = computed(() =>
    props.projects.map((p) => ({
        label: `${p.proj_name} (${p.proj_code})`,
        value: p.proj_id,
    }))
);

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
    { key: "invoice_no", label: "Inv. No" },
    { key: "project_name", label: "Project" },
    { key: "date", label: "Date" },
    { key: "total_amount", label: "Total" },
    { key: "vat", label: "VAT" },
    { key: "total_vat", label: "Total + VAT" },
    { key: "retention_amount", label: "Retention" },
    { key: "receivable_amount", label: "Receivable" },
    { key: "chart_of_accounts", label: "Credited" },
    // { key: "action", label: "Action" },
];

// Filter Button Action
const applyFilter = () => {
    fetchData();
};

const resetFilter = () => {
    projectFilter.value = null;
    statusFilter.value = null;
    dueDateFrom.value = null;
    dueDateTo.value = null;
    fetchData();
};

const fetchData = async (page = 1) => {
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
                    due_date_from: dueDateFrom.value || null,
                    due_date_to: dueDateTo.value || null,
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
                project_name: `${item.project_details?.proj_name ?? "-"} (${
                    item.project_details?.proj_code ?? "-"
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
    const params = new URLSearchParams({
        project_id: projectFilter.value || "",
        sr_status: statusFilter.value || "",
        due_date_from: dueDateFrom.value || "",
        due_date_to: dueDateTo.value || "",
    });

    window.open(
        `/admin/accounting/reports/sales-report/pdf?${params.toString()}`,
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
