<?php

namespace Modules\Account\Http\Controllers\Report;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use Modules\Account\Services\LedgerReportService;
//! Model Import
use Modules\Account\Models\ChartofaccSalesRecord;
use App\Models\ProjectInfo;

class SalesReportController extends Controller
{

    public function index()
    {

        //  $salesCustomerService = new SalesCustomerService();
        $company = (new CompanyDataService())->findCompanryProfile();
        $data_for_report_form = [
            'projects' => (new ProjectDataService())->getAllActiveProjectListForDropdown(1),
            'company' => $company,
        ];
        return view('account::pages.reports.report', compact('data_for_report_form'));
    }
    public function salesReport()
    {
        // $projects = ProjectInfo::select('proj_id', 'proj_name', 'proj_code')
        //     ->where('status', 1)
        //     ->orderBy('proj_name', 'asc')
        //     ->get();

        $projects = DB::table('project_infos')
            ->select('proj_id', 'proj_name', 'proj_code')
            ->where('status', 1)
            ->orderBy('proj_name', 'asc')
            ->get();

        $status = [
            ['id' => 1,  'name' => 'Active'],
            ['id' => 0,  'name' => 'Inactive'],
        ];

        return view('account::pages.reports.sales_report', compact('projects', 'status'));
    }

    public function salesReportListAPI(Request $request)
    {
        $query = ChartofaccSalesRecord::with([
            'customer',
            'items',
            'debit',
            'credit',
            'ProjectDetails',
        ]);

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sr_invoice_no', 'like', "%{$search}%")
                    ->orWhere('sr_invoice_description', 'like', "%{$search}%")
                    ->orWhere('sr_payment_terms', 'like', "%{$search}%")
                    ->orWhere('sr_payment_mean', 'like', "%{$search}%");
            });
        }


        // 🔹 Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('sr_status')) {
            $query->where('sr_status', $request->sr_status);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('sr_due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('sr_due_date', '<=', $request->due_date_to);
        }

        // 🔢 Per Page (default 10)
        $perPage = $request->get('per_page', 10);

        // 📄 Pagination
        // $records = $query->orderBy('sr_auto_id', 'desc')->paginate($perPage);
        $records = (clone $query)->orderBy('sr_auto_id', 'desc')->paginate($perPage);

        // 🔹 Footer totals calculation
        $totals = [
            'total_amount'      => (clone $query)->sum('sr_total_amount'),
            'total_vat'         => (clone $query)->sum('sr_vat_amount'),
            'total_grand'       => (clone $query)->sum('sr_grand_total_amount'),
            'retention_amount'  => (clone $query)->sum('retention_amount'),
            'receivable_amount' => (clone $query)->sum('sr_grand_total_amount') - (clone $query)->sum('retention_amount'),
        ];

        return response()->json([
            'records' => $records,
            'totals'  => $totals,
        ]);
    }

    public function downloadPDF(Request $request)
    {
        // temporary redirect to summary report
        return $this->processSalesAndPurchaseSummaryReport($request);

        $query = ChartofaccSalesRecord::with([
            'customer',
            'items',
            'debit',
            'credit',
            'ProjectDetails',
        ]);

        // 🔹 Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('sr_status')) {
            $query->where('sr_status', $request->sr_status);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('sr_due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('sr_due_date', '<=', $request->due_date_to);
        }

        $records = $query->orderBy('sr_auto_id', 'desc')->get();

        // 🔹 Totals
        $totals = [
            'total_amount'      => (clone $query)->sum('sr_total_amount'),
            'total_vat'         => (clone $query)->sum('sr_vat_amount'),
            'total_grand'       => (clone $query)->sum('sr_grand_total_amount'),
            'retention_amount'  => (clone $query)->sum('retention_amount'),
            'receivable_amount' => (clone $query)->sum('sr_grand_total_amount') - (clone $query)->sum('retention_amount'),
        ];

        $company = (new CompanyDataService())->findCompanryProfile();
        $current_datetime = now()->format('d M Y h:i A');

        $pdf = Pdf::loadView('account::pages.reports.purchase.sales_report', [
            'records' => $records,
            'totals'  => $totals,
            'filters' => $request->all(),
            'company' => $company,
            'current_datetime' => $current_datetime,
        ])->setPaper('a4', 'landscape');

        //! Direct download PDF
        // return $pdf->download('sales_report.pdf');

        //! Open PDF in browser
        return $pdf->stream('sales_report.pdf');
    }


    public function expensePDF(Request $request)
    {
        $company = (new CompanyDataService())->findCompanryProfile();
        $current_datetime = now()->format('d M Y h:i A');

        $pdf = Pdf::loadView('account::pages.reports.purchase.expense_pdf', [
            'company' => $company,
            'current_datetime' => $current_datetime,
        ])->setPaper('a4');

        //! Open PDF in browser
        return $pdf->stream('expense.pdf');
    }







    public function processSalesAndPurchaseSummaryReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $reportData = (new LedgerReportService())->processSaleAndPurchaseDateByDateledgerReport($from_date, $to_date);
        $company = (new CompanyDataService())->findCompanryProfile();
        return view('account::pages.reports.sales_purchase_summary_report', [
            'records' => $reportData,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'company' => $company,
            'current_datetime' => now()->format('d M Y h:i A'),
        ]);
    }







}
