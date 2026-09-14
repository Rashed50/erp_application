<?php

namespace Modules\Account\Http\Controllers\Report;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Controllers\DataServices\{ProjectDataService, ChartOfAccountDataService, CompanyDataService, ReportDataService};
use Modules\Account\Services\{SalesCustomerService, SuppliersService, GeneralLedgerService, LedgerReportService};
use Modules\Account\Models\{SupplierTransactions, CustomerTransaction, SupplierLedger};


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    protected SuppliersService $supplierService;
    protected GeneralLedgerService $generalLedgerService;
    private Collection $project_records;
    public function __construct()
    {
        // $this->middleware('permission:read-purchase_invoice')->only('index', 'show', 'listAPI');
        // $this->middleware('permission:create-purchase_invoice')->only('billCreate', 'storePurchase');
        // $this->middleware('permission:update-purchase_invoice')->only('edit', 'updatePurchaseAPI');
        $this->supplierService = new SuppliersService();
        $this->generalLedgerService = new GeneralLedgerService();
    }

    public function index()
    {

        //  $salesCustomerService = new SalesCustomerService();
        $company = (new CompanyDataService())->findCompanryProfile();
        $data_for_report_form = [
            'projects' => (new ProjectDataService())->getAllActiveProjectListForDropdown(1),
            'company' => $company,
        ];

        return view('report::pages.report.index', compact('data_for_report_form'));
    }

    /**
     * Get all suppliers for dropdown.
     * @return JsonResponse
     */
    public function getSuppliers(): JsonResponse
    {
        try {
            // Load suppliers from suppliers_ledger table
            $suppliers = $this->supplierService->getActiveSupplierInformationForDropdown();

            return response()->json([
                'success' => true,
                'suppliers' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'suppliers' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate Supplier Report.
     * @param Request $request
     * @return JsonResponse
     */
    public function supplierReport(Request $request): JsonResponse
    {
        try {
            // Add your supplier report logic here
            // Example: Query supplier transactions based on date range
            $data = [];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Supplier report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get all customers for dropdown.
     * @return JsonResponse
     */
    public function getCustomers(): JsonResponse
    {
        try {

            $customers = (new SalesCustomerService())->getCustomersForSales(auth()->user()->branch_office_id);


            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'customers' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate Sales Report.
     * @param Request $request
     * @return JsonResponse
     */
    public function salesReport(Request $request): JsonResponse
    {
        try {
            // Add your sales report logic here
            // Example: Query sales transactions based on date range and customer
            $data = [];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Sales report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get all accounts for dropdown.
     * @return JsonResponse
     */
    public function getAccounts(): JsonResponse
    {
        try {

            $accounts = (new ChartOfAccountDataService())->getChartOfAccountsMotherAccountlistForDropdown();
            return response()->json([
                'success' => true,
                'accounts' => $accounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'accounts' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate Cash Transaction Report.
     * @param Request $request
     * @return JsonResponse
     */
    public function cashTransaction(Request $request): JsonResponse
    {
        try {
            // Query account transaction details
            $query = DB::table('account_transaction_details as atd')
                ->join('chart_of_accounts as coa', 'atd.account_no', '=', 'coa.chart_of_acct_id')
                ->select(
                    'atd.created_at as date',
                    'coa.chart_of_acct_name as ledger_name',
                    'atd.debit',
                    'atd.credit'
                )
                ->orderBy('atd.created_at', 'asc')
                ->orderBy('atd.trd_id', 'asc');

            // Filters - Handle multiple account_ids
            if ($request->filled('account_ids') && is_array($request->account_ids) && count($request->account_ids) > 0) {
                $query->whereIn('atd.account_no', $request->account_ids);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('atd.created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('atd.created_at', '<=', $request->end_date);
            }

            // If transaction_type is provided and not empty, filter by type (though removed from UI)
            // Note: account_transaction_details may not have transaction_type, adjust if needed

            $data = $query->get()->map(function ($item) {
                return [
                    'date' => $item->date ? \Carbon\Carbon::parse($item->date)->format('d M, Y') : '-',
                    'ledger_name' => $item->ledger_name ?? '',
                    'debit' => floatval($item->debit ?? 0),
                    'credit' => floatval($item->credit ?? 0),
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Cash transaction report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate Cash Transaction Report PDF.
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function getCashTransactionDownload(Request $request)
    {
        try {
            // Query account transaction details
            $query = DB::table('account_transaction_details as atd')
                ->join('chart_of_accounts as coa', 'atd.account_no', '=', 'coa.chart_of_acct_id')
                ->select(
                    'atd.created_at as date',
                    'coa.chart_of_acct_name as ledger_name',
                    'atd.debit',
                    'atd.credit'
                )
                ->orderBy('atd.created_at', 'asc')
                ->orderBy('atd.trd_id', 'asc');

            // Filters - Handle multiple account_ids
            if ($request->filled('account_ids') && is_array($request->account_ids) && count($request->account_ids) > 0) {
                $query->whereIn('atd.account_no', $request->account_ids);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('atd.created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('atd.created_at', '<=', $request->end_date);
            }

            $data = $query->get()->map(function ($item) {
                return [
                    'date' => $item->date ? \Carbon\Carbon::parse($item->date)->format('d M, Y') : '-',
                    'ledger_name' => $item->ledger_name ?? '',
                    'debit' => floatval($item->debit ?? 0),
                    'credit' => floatval($item->credit ?? 0),
                ];
            })->toArray();

            // Totals
            $totals = [
                'total_debit' => array_sum(array_column($data, 'debit')),
                'total_credit' => array_sum(array_column($data, 'credit')),
            ];

            // Company and datetime
            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y h:i A');

            $pdf = PDF::loadView('report::pages.report.cash_transaction_pdf', [
                'records' => $data,
                'totals' => $totals,
                'filters' => $request->all(),
                'company' => $company,
                'current_datetime' => $current_datetime,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ])->setPaper('a4', 'landscape');

            return $pdf->stream('cash_transaction_report.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }



    /*
    ============================================================================
    ============================= EXPENSE REPORT SECTION =======================
    ============================================================================
    */

    /**
     * Generate Supplier Report PDF.
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function getSupplierReportPdf(Request $request)
    {
        return $this->showSupplierCurrentOutStandingBalance($request);

        try {

            $query =  SupplierTransactions::with('supplier');
            // Handle multiple supplier IDs
            $supplierIds = $request->input('supplier_id', []);
            if (!is_array($supplierIds)) {
                $supplierIds = [$supplierIds];
            }
            $supplierIds = array_filter($supplierIds); // Remove empty values

            // Filters
            if (!empty($supplierIds)) {
                $query->whereIn('supplier_id', $supplierIds);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }

            // --- Calculate previous balance for multiple suppliers ---
            $previousBalance = 0;
            $ledger = null;
            // dd($request->all());

            if (!empty($supplierIds)) {
                // Get supplier ledgers for all selected suppliers
                $ledgers = DB::table('suplier_subsidiary_ledger')
                    ->whereIn('supplier_id', $supplierIds)
                    ->get();
                $openingBalance = $ledgers->sum('opening_balance') ?? 0;

                // Sum of transactions before start_date for all suppliers
                if ($request->filled('start_date')) {
                    $startDate = $request->start_date;

                    $prevTransactions = SupplierTransactions::whereIn('supplier_id', $supplierIds)
                        ->whereDate('transaction_date', '<', $startDate)
                        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                        ->first();

                    $totalDebit = floatval($prevTransactions->total_debit ?? 0);
                    $totalCredit = floatval($prevTransactions->total_credit ?? 0);

                    $previousBalance = $openingBalance + $totalDebit - $totalCredit;
                }
            }
            $rows = $query->orderBy('suptran_id', 'asc')->get();
            $runningBalance = $previousBalance;
            // --- Map current transactions ---
            $records = $rows->map(function ($r) use (&$runningBalance) {
                $debit = floatval($r->debit ?? 0);
                $credit = floatval($r->credit ?? 0);

                // Update running balance
                $runningBalance = $runningBalance + $debit - $credit;

                return [
                    'supplier_name' => $r->supplier ? $r->supplier->supplier_name : 'Unknown',
                    'invoice_no' => $r->invoice_no ?? '',
                    'date' => $r->transaction_date ? \Carbon\Carbon::parse($r->transaction_date)->format('d M, Y') : '-',
                    'transaction_type' => $r->transaction_type ?? '',
                    'amount' => $credit,
                    'paid' => $debit,
                    'due' => $runningBalance,
                ];
            })->toArray();

            // --- Totals ---
            $totals = [
                'total_amount' => array_sum(array_column($records, 'amount')),
                'total_paid'   => array_sum(array_column($records, 'paid')),
                'total_due'    => end($records)['due'] ?? 0, // last row due
            ];


            // Company and datetime
            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y h:i A');

            $pdf = PDF::loadView('report::pages.report.supplier_report_pdf', [
                'records' => $records,
                'totals' => $totals,
                'previous_balance' => $previousBalance,
                'filters' => $request->all(),
                'company' => $company,
                'current_datetime' => $current_datetime,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ])->setPaper('a4', 'landscape');

            return $pdf->stream('supplier_report.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function showSupplierCurrentOutStandingBalance($request)
    {

        //   dd($request->all());
        $records = SupplierLedger::where('active_status', 1)->get();
        // Company and datetime
        $company = (new CompanyDataService())->findCompanryProfile();
        $current_datetime = now()->format('d M Y h:i A');

        return view('report::pages.report.supplier.current_balance_report', compact('records', 'company', 'current_datetime'));
    }

    /**
     * Generate Sales Report PDF.
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function getSalesReportPdf(Request $request)
    {
        try {
            // Use CustomerTransaction model
            $query = CustomerTransaction::with('customer');

            // Handle multiple customer IDs
            $customerIds = $request->input('customer_id', []);
            if (!is_array($customerIds)) {
                $customerIds = [$customerIds];
            }
            $customerIds = array_filter($customerIds); // Remove empty values

            // Filters
            if (!empty($customerIds)) {
                $query->whereIn('customer_id', $customerIds);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }

            // --- Calculate previous balance for multiple customers ---
            $previousBalance = 0;

            if (!empty($customerIds)) {
                // Get customer ledgers for all selected customers
                $ledgers = DB::table('customer_subsidiary_ledger')
                    ->whereIn('customer_id', $customerIds)
                    ->get();
                $openingBalance = $ledgers->sum('opening_balance') ?? 0;

                // Sum of transactions before start_date for all customers
                if ($request->filled('start_date')) {
                    $startDate = $request->start_date;

                    $prevTransactions = CustomerTransaction::whereIn('customer_id', $customerIds)
                        ->whereDate('transaction_date', '<', $startDate)
                        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                        ->first();

                    $totalDebit = floatval($prevTransactions->total_debit ?? 0);
                    $totalCredit = floatval($prevTransactions->total_credit ?? 0);

                    $previousBalance = $openingBalance + $totalDebit - $totalCredit;
                }
            }

            $rows = $query->orderBy('custran_id', 'asc')->get();
            $runningBalance = $previousBalance;

            // --- Map current transactions ---
            $records = $rows->map(function ($r) use (&$runningBalance) {
                $debit = floatval($r->debit ?? 0);
                $credit = floatval($r->credit ?? 0);

                // Update running balance
                $runningBalance = $runningBalance + $debit - $credit;

                return [
                    'customer_name' => $r->customer ? $r->customer->customer_name : 'Unknown',
                    'invoice_no' => $r->invoice_no ?? '',
                    'date' => $r->transaction_date ? \Carbon\Carbon::parse($r->transaction_date)->format('d M, Y') : '-',
                    'transaction_type' => $r->transaction_type ?? '',
                    'amount' => $debit,
                    'paid' => $credit,
                    'due' => $runningBalance,
                ];
            })->toArray();

            // --- Totals ---
            $totals = [
                'total_amount' => array_sum(array_column($records, 'amount')),
                'total_paid'   => array_sum(array_column($records, 'paid')),
                'total_due'    => end($records)['due'] ?? 0, // last row due
            ];

            // Company and datetime
            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y h:i A');

            $pdf = PDF::loadView('report::pages.report.sales_report_pdf', [
                'records' => $records,
                'totals' => $totals,
                'previous_balance' => $previousBalance,
                'filters' => $request->all(),
                'company' => $company,
                'current_datetime' => $current_datetime,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ])->setPaper('a4', 'landscape');

            return $pdf->stream('sales_report.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the Previous Three Month Report.
     * @return Renderable
     */
    public function previousThreeMonthReport()
    {
        // Return the form view
        return view('report::pages.report.previous_three_month_report');
    }

    /**
     * Generate the Previous Three Month Report.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generatePreviousThreeMonthReport(Request $request)
    {
        try {
            $request->validate([
                'selected_date' => 'required|date',
            ]);

            $reportData = (new ReportDataService())->getPreviousThreeMonthSalaryReport($request->selected_date);

            return response()->json([
                'success' => true,
                'reportData' => $reportData,
                'selectedDate' => $request->selected_date,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'reportData' => [],
            ]);
        }
    }

    /**
     * Display the Previous Three Month Subcontractor Report.
     * @return Renderable
     */
    public function previousThreeMonthSubcontractorReport()
    {
        // Return the form view
        return view('report::pages.report.previous_three_month_subcontractor_report');
    }

    /**
     * Generate the Previous Three Month Subcontractor Report.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generatePreviousThreeMonthSubcontractorReport(Request $request)
    {
        try {
            $request->validate([
                'selected_date' => 'required|date',
            ]);

            $reportData = (new ReportDataService())->getPreviousThreeMonthSubcontractorReport($request->selected_date);

            return response()->json([
                'success' => true,
                'reportData' => $reportData,
                'selectedDate' => $request->selected_date,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'reportData' => [],
            ]);
        }
    }









    /*
    ============================================================================
    ============================= EXPENSE REPORT SECTION =======================
    ============================================================================
    */

    /**
     * Generate Cash Transaction Report PDF.
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function getExpenseDetailsReport(Request $request)
    {



        try {
            // Query account transaction details
            $query = DB::table('purchase_invoice as puri')
                ->join('chart_of_accounts as coa', 'puri.account_credit_id', '=', 'coa.chart_of_acct_id')
                ->join('project_infos as pi', 'puri.project_id', '=', 'pi.proj_id')
                ->join('suplier_subsidiary_ledger as sl', 'puri.supplier_id', '=', 'sl.supplier_id')
                ->select(
                    'coa.chart_of_acct_name as ledger_name',
                    'pi.proj_name',
                    'sl.supplier_name',
                    'puri.purchase_type',
                    'puri.invoice_number',
                    'puri.purchase_date',
                    'puri.created_at as inserted_date',
                    'puri.notes',
                    'puri.vat_amount',
                    'puri.net_total',
                )
                ->orderBy('puri.purchase_date', 'asc');


            // Filters - Handle multiple account_ids
            if ($request->filled('account_ids') && is_array($request->account_ids) && count($request->account_ids) > 0) {
                $query->whereIn('puri.account_credit_id', $request->account_ids);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('puri.purchase_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('puri.purchase_date', '<=', $request->end_date);
            }

            $data = $query->get()->toArray();


            // Totals
            $totals = [
                'total_vat' => array_sum(array_column($data, 'vat_amount')),
                'total_net_amount' => array_sum(array_column($data, 'net_total')),
            ];

            // Company and datetime
            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y h:i A');


            return view('report::pages.report.expense.expense_details_pdf', [
                'records' => $data,
                'totals' => $totals,
                'filters' => $request->all(),
                'company' => $company,
                'current_datetime' => $current_datetime,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            $pdf = PDF::loadView('report::pages.report.expense.expense_details_pdf', [
                'records' => $data,
                'totals' => $totals,
                'filters' => $request->all(),
                'company' => $company,
                'current_datetime' => $current_datetime,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ])->setPaper('a4', 'landscape');

            return $pdf->stream('expense_details.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate the Previous Three Month Subcontractor Report.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processGeneralLedgerSaleAndPurchaseSummaryReport(Request $request)
    {

        return 'okk';
        try {
            $request->validate([
                'selected_date' => 'required|date',
            ]);

            //$reportData = (new ReportDataService())->getPreviousThreeMonthSubcontractorReport($request->selected_date);
            return $reportData =  (new LedgerReportService())->processSaleAndPurchaseDateByDateledgerReport($from_date, $to_date);
            // return $reportData =  (new LedgerReportService())->processSaleAndPurchaseDateByDateledgerReport($request->start_date,$request->end_date);
            return response()->json([
                'success' => true,
                'reportData' => $reportData,
                'selectedDate' => $request->selected_date,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'reportData' => [],
            ]);
        }
    }
}
