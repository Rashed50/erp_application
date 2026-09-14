<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AccountsModule\ChartOfAccounts;
use App\Models\AccountsModule\JournalInfo;
use Modules\Account\Models\SalesProductInfo;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Models\ChartofaccSalesRecord;
use Modules\Account\Services\SalesCustomerService;
use Modules\Account\Services\SalesProductInfoService;
use Modules\Account\Services\SalesService;
use App\Http\Controllers\DataServices\ProjectDataService;
use Modules\Account\Services\{GeneralLedgerService};
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $salesService = new SalesService();
        return view('account::pages.sales.index', [
            'data' => [
                'sales' => $salesService->getSales(),
            ]
        ]);
    }


    function show($id, Request $request)
    {
        $sale = ChartofaccSalesRecord::with([
            'customer',
            'items.product',
            'debit',
            'credit'
        ])
        ->where('sr_auto_id', $id)->firstOrFail();

        abort_if($sale->branch_office_id && $sale->branch_office_id != auth()->user()->branch_office_id, 403);
        return view('account::pages.sales.show', [
            'sale' => $sale
        ]);
    }

    public function salesPDF($id, Request $request)
    {
        $sale = ChartofaccSalesRecord::with([
            'customer',
            'items.product',
            'debit',
            'credit'
        ])
        ->where('sr_auto_id', $id)
        ->firstOrFail();

        abort_if($sale->branch_office_id && $sale->branch_office_id != auth()->user()->branch_office_id, 403);

        // Load the view into the PDF generator
        $pdf = Pdf::loadView('account::pages.sales.salesPDF', [
            'sale' => $sale
        ]);

        // PDF Direct Downlode
        // return $pdf->download('sale_report_'.$sale->sr_invoice_no.'.pdf');

        // PDF Open in New tab
        return $pdf->stream('sale_report_'.$sale->sr_invoice_no.'.pdf');
    }


    // load sale UI
    public function create()
    {
        $salesCustomerService = new SalesCustomerService();
        $salesProductInfoService = new SalesProductInfoService();


        $data['units'] = Unit::current_branch()->get();
        $data['customers'] = $salesCustomerService->getCustomersForSales(auth()->user()->branch_office_id);
        $data['products'] = $salesProductInfoService->getProductsForSales(auth()->user()->branch_office_id);
      //  $data['journals'] = JournalInfo::select('jour_name', 'jour_id')->where('jour_type_id', 1)->get();
        $data['cr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')->where('chart_of_acct_name', 'like', '%Sales Revenue%')->get();
        $data['payment_received_dr_accounts'] = $mother_accounts = (new GeneralLedgerService())->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1); // load only asset type account

        $asset_acc_type_id = (new GeneralLedgerService())->getAssetAccountTypeId();
        $receiable_account = (new GeneralLedgerService())->getGeneralLedgerReceiableAssetTypeAccountRecord($asset_acc_type_id);
        $data['dr_accounts'] =[ $receiable_account];

        $data['projects'] = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);

        $data['unpaid_sales'] = ChartofaccSalesRecord::select('sr_auto_id','sr_invoice_no','sr_grand_total_amount')->whereIn('paid_type',[0,5])
                                //->where('cus_auto_id',$customer_id)
                                ->get();
        return view('account::pages.sales.create', [
            'data' => $data
        ]);
    }


    // store sale UI information
    function store(Request $request)
    {
        // ALTER TABLE `chartofacc_sales_records` ADD `project_id` INT NOT NULL AFTER `updated_by_id`;
        Log::info("Request data received for Create:", $request->all());
        if ($request->is_draft != 1) {
            $request->validate([
                'invoice_no' => 'required|unique:' . ChartofaccSalesRecord::getTableName() . ',sr_invoice_no',
            ]);
        }
        $data = $request->except('attachments');
        $data['items'] = json_decode($data['items'], true);
        $data['branch_office_id'] = auth()->user()->branch_office_id;

        $salesService = new SalesService();
        $sale = $salesService->create(
            $data,
            null,
            $request->is_draft == 1,
            auth()->user()->branch_office_id
        );

        return $sale;
    }


    public function edit($id)
    {
        $salesCustomerService = new SalesCustomerService();
        $data['units'] = Unit::current_branch()->get();
        $data['customers'] = $salesCustomerService->getCustomersForSales(auth()->user()->branch_office_id);// CustomerLedger::get();
        $data['products'] = SalesProductInfo::get();
        $data['journals'] = JournalInfo::select('jour_name', 'jour_id')->where('jour_type_id', 1)->get();
        $data['projects'] = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);

      //  dd($data['products']);
        // $data['cr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')
        //                             ->where('acct_type_id', 4)
        //                             ->get();

        $data['cr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')->get();

        // $data['dr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')
        //                             ->where('acct_type_id', 4)
        //                             ->get();

        $data['dr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')->get();

        $data['sale'] = ChartofaccSalesRecord::with([
            'customer',
            'items.product',
            'debit',
            'credit'
        ])->where('sr_auto_id', $id)->firstOrFail();

        //  dd($data['sale']);

        abort_if($data['sale']->branch_office_id && $data['sale']->branch_office_id != auth()->user()->branch_office_id, 403);
        return view('account::pages.sales.edit', [
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {

        // Log::info("Request data received for update:", $request->all());
        if ($request->is_draft != 1) {
            $request->validate([
                'invoice_no' => 'required|unique:' . ChartofaccSalesRecord::getTableName() . ',sr_invoice_no,' . $id . ',sr_auto_id',
            ]);
        }

        $data = $request->except('attachments');
        $data['items'] = json_decode($data['items'], true);
        $data['branch_office_id'] = auth()->user()->branch_office_id;

        // Log::info("Force Save = " . $request->force_save);
        // Log::info("Is Draft = " . $request->is_draft);


        $is_draft = 0;
        if($request->is_draft === true || $request->is_draft === "true"){
            $is_draft = 1;
        }else if($request->is_draft === false || $request->is_draft === "false"){
            $is_draft = 0;
        }

        $salesService = new SalesService();
        $sale = $salesService->update(
            $id,
            $data,
            null,
            $is_draft,
            auth()->user()->branch_office_id
        );

        return $sale;
    }

    ################################################################################
    ############################ Sales Payment Received ############################
    ################################################################################

    public function getACustomerUnpiadSalesRecordsAPI($customer_id){

       // ALTER TABLE `chartofacc_sales_records` ADD `paid_type` TINYINT NOT NULL DEFAULT '0' AFTER `sr_status`, ADD `paid_date` DATE NULL DEFAULT NULL AFTER `paid_type`;
        $data = ChartofaccSalesRecord:://whereIn('paid_type',[0,5])
                        where('cus_auto_id',$customer_id)
                        ->get();
        return response()->json(['success'=>true,'data'=> $data,'customer'=>$customer_id],200);
    }
    public function salesPaymentReceived(Request $request){


        $is_success = (new SalesService())->storeSalesPaymentReceivedInformation($request->all());
        if($is_success){
            return response()->json(['success'=>true,'data'=>  $request->all(),'status'=>200]);
        }else{
            return response()->json(['success'=>false,'status'=>403,'data'=>  $request->all(),'message'=>'System Operation Failed']);
        }
    }




}
