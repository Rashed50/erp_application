<template>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center fw-bold" style="color: blue">Cash Transaction Report</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="account_id">Account</label>
                            <Multiselect
                                v-model="form.account_ids"
                                mode="multiple"
                                :options="accountOptions"
                                value-prop="value"
                                track-by="label"
                                label="label"
                                placeholder="Select Accounts"
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
                                        {{ values.length }} Accounts Selected
                                    </div>
                                </template>

                                <template #nooptions>
                                    <span class="text-muted">No accounts available</span>
                                </template>
                            </Multiselect>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="report_type">Report Type</label>
                            <select v-model="form.report_type" class="form-select" id="report_type">
                                <option value="Search">Search</option>
                                <option value="PDF">PDF</option>
                                <option value="Excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" v-model="form.start_date" class="form-control" id="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" v-model="form.end_date" class="form-control" id="end_date" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-warning" @click="handleAction" :disabled="loading && form.report_type === 'Search'">
                            <span v-if="loading && form.report_type === 'Search'">
                                <i class="fa fa-spinner fa-spin"></i> Generating...
                            </span>
                            <span v-else>
                                <i :class="buttonIcon + ' me-2'"></i> {{ buttonText }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Report Results Table -->
            <div class="table-responsive mt-4" v-if="reportData.length > 0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Date</th>
                            <th>Ledger Name</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in reportData" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.date }}</td>
                            <td>{{ item.ledger_name }}</td>
                            <td>{{ item.debit }}</td>
                            <td>{{ item.credit }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th>{{ totalDebit }}</th>
                            <th>{{ totalCredit }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import Multiselect from "@vueform/multiselect";
var today = new Date().toISOString().slice(0, 10);

export default {
    name: "CashTransaction",
    components: {
        Multiselect,
    },
    props: {
        data_for_form: {
            type: Object,
            required: false,
            default: () => ({}),
        },
    },
    data() {
        return {
            loading: false,
            form: {
                account_ids: [],
                start_date: today,
                end_date: today,
                report_type: "Search",
            },
            accounts: [],
            reportData: [],
        };
    },
    computed: {
        totalDebit() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.debit || 0), 0);
        },
        totalCredit() {
            return this.reportData.reduce((sum, item) => sum + parseFloat(item.credit || 0), 0);
        },
        accountOptions() {
            if (!this.accounts || !Array.isArray(this.accounts)) return [];
            return this.accounts.map(account => ({
                label: account.chart_of_acct_name,
                value: account.chart_of_acct_id
            }));
        },
        selectedAccountName() {
            if (!this.form.account_ids || this.form.account_ids.length === 0) {
                return 'All Accounts';
            }
            if (this.form.account_ids.length === 1) {
                const account = this.accounts.find(a => a.chart_of_acct_id === this.form.account_ids[0]);
                return account ? account.chart_of_acct_name : 'All Accounts';
            }
            return `${this.form.account_ids.length} Accounts Selected`;
        },
        companyName() {
            return this.data_for_form?.company?.comp_name_en || 'Company Name';
        },
        companyAddress() {
            return this.data_for_form?.company?.comp_address || 'Company Address';
        },
        companyLogo() {
            const logo = this.data_for_form?.company?.com_logo;
            return logo ? `/${logo}` : '/images/company_logo.png';
        },
        currentDate() {
            const now = new Date();
            return now.toLocaleDateString('en-GB');
        },
        buttonText() {
            switch (this.form.report_type) {
                case 'Search': return 'Generate Report';
                case 'PDF': return 'View PDF';
                case 'Excel': return 'Download Excel';
                default: return 'Generate Report';
            }
        },
        buttonIcon() {
            switch (this.form.report_type) {
                case 'Search': return 'fa-file-pdf-o';
                case 'PDF': return 'fa-file-pdf-o';
                case 'Excel': return 'fa-file-excel-o';
                default: return 'fa-file-pdf-o';
            }
        },
    },
    mounted() {
        this.fetchAccounts();
    },
    methods: {
        async fetchAccounts() {
            try {
                const response = await axios.get("/admin/report/accounts");
                this.accounts = response.data.accounts || [];
            } catch (error) {
                console.error("Failed to fetch accounts", error);
            }
        },
        async handleAction() {
            if (this.form.report_type === 'Search') {
                this.loading = true;
                await this.generateReport();
                this.loading = false;
            } else if (this.form.report_type === 'PDF') {
                this.loading = true;
                await this.generatePdfInNewTab();
                this.loading = false;
            } else {
                this.downloadReport();
            }
        },
        async generateReport() {
            try {
                const response = await axios.post("/admin/report/cash-transaction", this.form);
                if (response.data.success) {
                    this.reportData = response.data.data || [];
                    toast.success("Report generated successfully!");
                }
            } catch (error) {
                toast.error("Failed to generate report. Please try again.");
                console.error(error);
            }
        },
        downloadReport() {
            if (this.form.report_type === 'PDF') {
                const url = `/admin/report/cash-transaction-pdf?` + new URLSearchParams(this.form).toString();
                window.open(url, '_blank');
            } else if (this.form.report_type === 'Excel') {
                toast.info('Coming soon!');
            }
        },
        async generatePdfInNewTab() {
            try {
                const response = await axios.post("/admin/report/cash-transaction", this.form);
                if (response.data.success) {
                    const reportData = response.data.data || [];
                    if (reportData.length === 0) {
                        toast.warning("No data found for the selected criteria.");
                        return;
                    }
                    this.openPdfInNewTab(reportData);
                }
            } catch (error) {
                toast.error("Failed to generate report. Please try again.");
                console.error(error);
            }
        },
        openPdfInNewTab(reportData) {
            // Calculate running balance
            let runningBalance = 0;
            const dataWithBalance = reportData.map(item => {
                runningBalance += parseFloat(item.debit || 0) - parseFloat(item.credit || 0);
                return { ...item, balance: runningBalance };
            });

            // Calculate totals
            const totalDebit = reportData.reduce((sum, item) => sum + parseFloat(item.debit || 0), 0);
            const totalCredit = reportData.reduce((sum, item) => sum + parseFloat(item.credit || 0), 0);
            const netBalance = totalDebit - totalCredit;

            // Generate table rows
            let tableRows = '';
            dataWithBalance.forEach((item, index) => {
                const balanceClass = item.balance < 0 ? 'style="color: #dc3545;"' : '';
                tableRows += `
                    <tr>
                        <td class="td-sn">${index + 1}</td>
                        <td class="td-date">${item.date}</td>
                        <td class="td-ledger">${item.ledger_name}</td>
                        <td class="td-amount">${this.formatCurrency(item.debit)}</td>
                        <td class="td-amount">${this.formatCurrency(item.credit)}</td>
                        <td class="td-balance" ${balanceClass}>${this.formatCurrency(item.balance)}</td>
                    </tr>
                `;
            });

            const htmlContent = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Transaction Report</title>
    <style>
        * { margin: 0; padding: 0; outline: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }

        @media print {
            @page { size: A4 portrait; margin: 5mm 5mm 10mm 5mm; }
            .no-print { display: none !important; }
            .main-wrap { margin: 0; }
            .report-table th {
                background: rgb(40, 120, 185) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .total-row td {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            table { page-break-after: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            td { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }

        .main-wrap { width: 96%; margin: 10px auto; }

        /* Header - matching subcontract_overall_summary.blade.php */
        .header-table { width: 100%; margin-bottom: 12px; border-collapse: collapse; padding: 18px; border-radius: 3px; margin: 0; }
        .header-table td { vertical-align: middle; }
        .company-title { font-size: 14px; font-weight: bold; margin-bottom: 4px; color: #222; }
        .subtitle { font-size: 11px; color: #666; }
        .top-hr { border: 0; border-top: 3px solid lightblue; margin: 0; }

        /* Title */
        .report-title { text-align: center; font-size: 14px; font-weight: bold; padding: 10px 0; margin: 0; color: #000; }
        .report-subtitle { text-align: center; font-size: 11px; margin-bottom: 15px; color: #333; }

        /* Table */
        .report-table { width: 100%; border-collapse: collapse; margin: 0; padding: 0; }
        .report-table th, .report-table td { border: 0.5px solid gray; padding: 3px; font-size: 12px; }
        .report-table th { background: rgb(40, 120, 185); color: #000; font-weight: bold; text-align: center; }
        .report-table td { text-align: center; height: 20px; }
        .th-sn, .td-sn { width: 30px; text-align: center; }
        .th-date, .td-date { width: 80px; text-align: center; }
        .th-ledger, .td-ledger { text-align: left; padding-left: 5px; }
        .th-amount, .td-amount { width: 90px; text-align: right; padding-right: 5px; }
        .th-balance, .td-balance { width: 100px; text-align: right; padding-right: 5px; }
        .total-row td { font-weight: bold; background: #f8f9fa; }

        /* Signature */
        .signature-section { display: flex; justify-content: space-between; padding-top: 60px; font-size: 9px; }
        .signature-box { text-align: center; }
        .signature-line { margin-bottom: 5px; }

        /* Print Button */
        .print-button-container { text-align: right; margin-bottom: 15px; }
        .btn-print { background-color: #007bff; color: white; border: none; padding: 8px 16px; cursor: pointer; border-radius: 4px; font-size: 14px; }
        .btn-print:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="main-wrap">
        <!-- Print Button -->
        <div class="print-button-container no-print">
            <button onclick="window.print()" class="btn-print">🖨️ Print</button>
        </div>

        <!-- Header - matching subcontract_overall_summary.blade.php -->
        <table class="header-table">
            <tr>
                <td style="width: 65%;">
                    <img src="${this.companyLogo}" alt="" width="184px" height="80px" onerror="this.style.display='none'">
                </td>
                <td style="width: 35%; text-align: end;">
                    <div class="company-title">${this.companyName}</div>
                    <div class="subtitle">${this.companyAddress}</div>
                </td>
            </tr>
        </table>
        <hr class="top-hr">

        <!-- Title -->
        <h4 class="report-title">Cash Transaction Report</h4>
        <p class="report-subtitle">Period: ${this.formatDate(this.form.start_date)} to ${this.formatDate(this.form.end_date)} | Account: ${this.selectedAccountName}</p>

        <!-- Table -->
        <section class="table-section">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="th-sn">S.N</th>
                        <th class="th-date">Date</th>
                        <th class="th-ledger">Ledger Name</th>
                        <th class="th-amount">Debit</th>
                        <th class="th-amount">Credit</th>
                        <th class="th-balance">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td class="td-amount"><strong>${this.formatCurrency(totalDebit)}</strong></td>
                        <td class="td-amount"><strong>${this.formatCurrency(totalCredit)}</strong></td>
                        <td class="td-balance"><strong>${this.formatCurrency(netBalance)}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <!-- Signature -->
        <section class="signature-section">
            <div class="signature-box">
                <p class="signature-line">________________</p>
                <p class="signature-label">Prepared By</p>
            </div>
            <div class="signature-box">
                <p class="signature-line">________________</p>
                <p class="signature-label">Checked By</p>
            </div>
            <div class="signature-box">
                <p class="signature-line">________________</p>
                <p class="signature-label">Approved By</p>
            </div>
        </section>
    </div>
</body>
</html>`;

            // Open in new tab
            const newTab = window.open('', '_blank');
            newTab.document.write(htmlContent);
            newTab.document.close();
        },
        formatCurrency(value) {
            const num = parseFloat(value || 0);
            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB');
        },
    },
};
</script>

<style scoped>
/* Card Styles */
.card {
    margin: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.card-header {
    background-color: #f8f9fa;
}
</style>
