<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\{Validator, Log, DB};
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AccountsModule\{ChartOfAccounts, JournalInfo};
use App\Models\{Product, Unit, InventorySupplier};
use App\Models\Inventory\item_name;
use Modules\Account\Models\{PurchaseInvoice, PurchaseInvoiceDetails, PurchaseInvoiceAttachment, SupplierLedger, SupplierTransactions};
use Modules\Account\Services\FileManager;
use Modules\Account\Models\{AccountTransaction, AccountTransactionDetails};
use App\Http\Controllers\DataServices\{ProjectDataService, CompanyDataService};

use Modules\Account\Services\{SuppliersService,GeneralLedgerService};
use Carbon\Carbon;
use PhpOption\None;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;



class PurchaseController extends Controller
{
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
        $this->project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);
    }


    // Purchased Records Searching UI
    public function index()
    {

        $purchase_types = [
            ['name' => 'Product', 'value' => 'product'],
            ['name' => 'Service', 'value' => 'service'],
        ];
        $suppliers = $this->supplierService->getActiveSupplierInformationForDropdown();

        return view('account::pages.purchase.listOfPurchase', [
            'data' => [
                'purchase_types' => $purchase_types,
                'projects'=> $this->project_records,
                'suppliers'=>$suppliers
            ]
        ]);

    }



    public function listAPI(Request $request)
    {
        //? Company Information ----------------------------------------------------------
        $company = (new CompanyDataService())->findCompanryProfile();
        $current_datetime = now()->format('Y M d h:i A');   // 10 Nov 2025 08:30 PM
        $login_name = Auth::user()->name;

        //? Request Information ----------------------------------------------------------
        $perPage     = (int)$request->query('per_page', 10);
        $page        = (int)$request->query('page', 1);
        $search      = $request->query('search', '');
        $type        = $request->query('type', null);
        $project_id  = $request->query('project_id', null);
        $supplier_id = $request->query('supplier_id', null);
        $download    = (int)$request->query('is_download', 0);
        $report_type = $request->query('report_type', null);

        // Get the selected date type and filter values
        $selectedDate = $request->query('selectedDate', null); // Expected values: issue_date, purchase_date, created_at
        $dateFrom     = $request->query('dateFrom', null);
        $dateTO       = $request->query('dateTO', null);

        //? Laravel Eloquent Model ----------------------------------------------------------
        $query = PurchaseInvoice::select(
            'pur_id',
            'created_at',
            'purchase_type',
            'issue_date',
            'purchase_date',
            'invoice_number',
            'total_amount',
            'vat_amount',
            'discount_amount',
            'net_total',
            'notes',
            'branch_id',
            'project_id',
            'supplier_id'
        )
            ->with([
                'project:proj_id,proj_name',
                'supplier:supplier_id,supplier_name,supplier_email,supplier_phone,supplier_address'
            ])
            ->orderBy('created_at', 'desc');

        //? Filter -----------------------------------------------------------------------------
        if (!empty($type)) {
            $query->where('purchase_type', $type);
        }
        if (!empty($project_id)) {
            $query->where('project_id', $project_id);
        }
        if (!empty($supplier_id)) {
            $query->where('supplier_id', $supplier_id);
        }
        if ($selectedDate) {
            if ($dateFrom) {
                // Apply the appropriate date filter based on the selected date type
                switch ($selectedDate) {
                    case 'issue_date':
                        $query->whereDate('issue_date', '>=', $dateFrom);
                        break;
                    case 'purchase_date':
                        $query->whereDate('purchase_date', '>=', $dateFrom);
                        break;
                    case 'created_at':
                        $query->whereDate('created_at', '>=', $dateFrom);
                        break;
                }
            }

            if ($dateTO) {
                switch ($selectedDate) {
                    case 'issue_date':
                        $query->whereDate('issue_date', '<=', $dateTO);
                        break;
                    case 'purchase_date':
                        $query->whereDate('purchase_date', '<=', $dateTO);
                        break;
                    case 'created_at':
                        $query->whereDate('created_at', '<=', $dateTO);
                        break;
                }
            }
        }

        //? Search -----------------------------------------------------------------------------
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('isupp_name', 'like', "%{$search}%")
                            ->orWhere('isupp_email', 'like', "%{$search}%")
                            ->orWhere('isupp_contact_address', 'like', "%{$search}%");
                    });
            });
        }


        //? PDF Reporting ---------------------------------------------------------------------
        $supplier     = null;
        if ($supplier_id) {
            $supplier = SupplierLedger::select(
                'supplier_id',
                'supplier_name',
                'supplier_address',
                'supplier_email',
                'supplier_phone'
            )->where('supplier_id', $supplier_id)->first();
        }
        // Check if the request is to download the data as a PDF
        if ($download === 1 && $report_type == '1') {
            $tableDatas = $query->get();

            // Calculate totals
            $totals = [
                'total_amount'     => $tableDatas->sum('total_amount'),
                'vat_amount'       => $tableDatas->sum('vat_amount'),
                'discount_amount'  => $tableDatas->sum('discount_amount'),
                'net_total'        => $tableDatas->sum('net_total'),
            ];

            // $pdf = PDF::loadView('account::pages.purchase.report.list_pdf_2', [
            $pdf = pdf::loadView('account::pages.purchase.pdf.purchase_report', [
                'company'          => $company,
                'current_datetime' => $current_datetime,
                'login_name'       => $login_name,
                'supplier'         => $supplier,
                'purchases'        => $tableDatas,
                'totals'           => $totals
            ]);

            // Log::info(json_encode($tableDatas));

            // Use Direct Downlode pdf
            // return $pdf->download('purchase_report_' . date('Y-m-d_H-i-s') . '.pdf');

            // Use stream to open in browser
            return $pdf->stream('purchase_report_' . date('Y-m-d_H-i-s') . '.pdf');
        } else if ($download === 1 && $report_type == '2' && $supplier_id != null) {

            // Leravel Query Builder
            $supplier = DB::table('suplier_subsidiary_ledger')
                ->select('supplier_id', 'supplier_name', 'supplier_address', 'supplier_email', 'supplier_phone')
                ->where('supplier_id', $supplier_id)
                ->first();

            $transactions = DB::table('suplier_transactions')
                ->select('transaction_id', 'transaction_type', 'invoice_no', 'debit', 'credit', 'transaction_date', 'notes', 'created_by', 'updated_by', 'branch_office_id')
                ->where('supplier_id', $supplier_id)
                ->get();

            $pdf = pdf::loadView('account::pages.purchase.pdf.supplier_date_wise_report', [
                'company'          => $company,
                'current_datetime' => $current_datetime,
                'login_name'       => $login_name,
                'supplier'         => $supplier,
                'purchases'        => $transactions,
            ]);

            return $pdf->stream('supplyer_report_' . date('Y-m-d_H-i-s') . '.pdf');
        }


        // Paginate the data
        $tableDatas = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json($tableDatas);
    }


  // load new purchase invoice page
    public function billCreate()
    {

        $suppliers = $this->supplierService->getActiveSupplierInformationForDropdown();
        $products = item_name::all();
        $data['suppliers'] = $suppliers;
        $data['products'] = $products;
        $data['projects'] = $this->project_records;// (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $data['dr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')->where('chart_of_acct_name', 'like', '%Inventory%')->get();
        $data['expense_accounts'] = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(5); // load only expense type account
        $data['liability_accounts'] = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(2); // load only liabiltiy type account
        $data['cr_accounts'] = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1); // load only asset type account
        $data['asset_accounts'] = $data['cr_accounts'];

        return view('account::pages.purchase.createBill', [
            'data' => $data
        ]);

    }

    // store new purchase invoice information

    public function storePurchase(Request $request)
    {
       // Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'purchase_type' => 'required|string|in:product,service|max:255',
            'description' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suplier_subsidiary_ledger,supplier_id',
            'invoice_number' => 'required|string|max:255|unique:purchase_invoice',
            'issue_date' => 'required|date',
            'purchase_date' => 'required|date',
            'note' => 'nullable|string',
            'project_id' => 'required',
            'total_amount' => 'required|numeric',
            'vat_amount' => 'required|numeric',
            'discount' => 'numeric',
            'net_total' => 'required|numeric',
            'items' => 'required|array',
            'files' => 'nullable|array',
            'files.*' => 'file|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Begin transaction to ensure atomicity
        DB::beginTransaction();
        try {
            // Create a new PurchaseInvoice record
            $purchase = PurchaseInvoice::create([
                'purchase_type' => $request->input('purchase_type'),
                'description' => $request->input('description'),
                'supplier_id' => $request->input('supplier_id'),
                'invoice_number' => $request->input('invoice_number'),
                'issue_date' => $request->input('issue_date'),
                'purchase_date' => $request->input('purchase_date'),
                'notes' => $request->input('note'),
                'project_id' => $request->input('project_id'),
                'total_amount' => $request->input('total_amount'),
                'vat_amount' => $request->input('vat_amount'),
                'discount_amount' => $request->input('discount') ?? 0,
                'net_total' => $request->input('net_total'),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'branch_id' => auth()->user()->branch_office_id,
                'account_debit_id' => $request->input('account_debit_id'),
                'account_credit_id' => $request->input('account_credit_id'),
            ]);

            // Create PurchaseInvoiceDetails record
            foreach ($request->input('items') as $itemJson) {
                $item = json_decode($itemJson, true);

                // Check if qty and unit_price are set
                if (isset($item['qty'], $item['unit_price'])) {
                    PurchaseInvoiceDetails::create([
                        'purchase_id' => $purchase->pur_id,
                        'item_id' => $item['product_id'] ?? null,
                        'service_name' => $request->input('purchase_type') === 'service' ? $item['service_name'] : null,
                        'description' => '',
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'vat' => $item['vat'],
                        'total_amount' => $item['unit_price'] * $item['qty'],
                    ]);
                }
            }

            // Handle file uploads
            if ($request->hasFile('files')) {
                $uploadedFiles = FileManager::upload('invoices', $request->file('files'));

                foreach ($uploadedFiles as $uploadedFile) {
                    PurchaseInvoiceAttachment::create([
                        'p_invoice_id' => $purchase->pur_id,
                        'file_path' => $uploadedFile,
                    ]);
                }
            }

            $is_draft = false;

            // Create Account Transaction
            if ($is_draft == false){
                $purchaseDate = Carbon::createFromFormat('Y-m-d', $purchase->issue_date);
                $purchaseDateTime = $purchaseDate->setTime(now()->hour, now()->minute, now()->second);

                try {
                    $tr = new AccountTransaction();
                    $tr->date = $purchaseDateTime;
                    $tr->tr_no = $purchase->invoice_number;
                    $tr->purchase_invoice_id = $purchase->pur_id;
                    $tr->approve_by = auth()->user()->id;
                    $tr->total_amount = $purchase->net_total;
                    $tr->general_particular = $purchase->notes;
                    $tr->save();

                    // Credit
                    $tr_cr = new AccountTransactionDetails();
                    $tr_cr->trd_id = $tr->id;
                    $tr_cr->account_no = $purchase->account_credit_id;
                    $tr_cr->credit = $purchase->net_total;
                    $tr_cr->particular = "{$purchase->net_total} cr from {$purchase->credit->account_id}";
                    $tr_cr->save();

                    // Debit
                    $tr_de = new AccountTransactionDetails();
                    $tr_de->trd_id = $tr->id;
                    $tr_de->account_no = $purchase->account_debit_id;
                    $tr_de->debit = $purchase->net_total;
                    $tr_de->particular = "{$purchase->net_total} amount debit from {$purchase->debit->account_id}";
                    $tr_de->save();

                    (new SuppliersService())->storePurchaseSubsidiaryLedgerTransaction(
                          $purchase->supplier_id,$tr->id,$purchaseDateTime,$purchase->invoice_number,$purchase->net_total,$purchase->notes,
                        auth()->user()->id,auth()->user()->branch_office_id
                   );


                } catch (\Exception $e) {
                    Log::error('Error in creating transaction:', ['error' => $e->getMessage()]);
                    // return $e->getMessage();
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Purchase saved successfully!', 'purchase' => $purchase], 201);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $purchase = PurchaseInvoice::with(['supplier', 'details.product', 'attachments'])->find($id);

        if (!$purchase) {
            return redirect()->back()->with('error', 'Invoice not found');
        }
        // Log::info(json_encode($purchase));
        return view('account::pages.purchase.details', compact('purchase'));
    }


    public function generatePurchasePdf($id)
    {
        $purchase = PurchaseInvoice::with(['supplier', 'details', 'attachments'])->findOrFail($id);

        $pdf = Pdf::loadView('account::pages.purchase.report.details_pdf', [
            'purchase' => $purchase
        ]);
        return $pdf->stream('purchase_details_report_'.$purchase->invoice_number.'.pdf');
    }

    public function edit($id)
    {



        $purchase_types = [
            ['name' => 'Product', 'value' => 'product'],
            ['name' => 'Service', 'value' => 'service'],
        ];

        $purchase = PurchaseInvoice::with(['supplier', 'details.product', 'attachments'])->find($id);

        $data['suppliers'] = $this->supplierService->getActiveSupplierInformationForDropdown();
        $data['products'] = item_name::all();
        $data['dr_accounts'] = ChartOfAccounts::select('chart_of_acct_name', 'chart_of_acct_number', 'chart_of_acct_id')->where('chart_of_acct_name', 'like', '%Inventory%')->get();
        $data['expense_accounts'] = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(5); // load only expense type account
        $data['liability_accounts'] = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(2); // load only liabiltiy type account
        $data['cr_accounts'] = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1); // load only asset type account
        $data['asset_accounts'] = $data['cr_accounts'];

        $data['purchase_types'] = $purchase_types;
        $data['purchase'] = $purchase;

        return view('account::pages.purchase.editPurchase', [
            'data' => $data
        ]);

    }


    public function updatePurchaseAPI(Request $request, $id)
    {
        Log::info("Id: ", ['id' => $id]);
        Log::info("Request Data: ", $request->all());

        $validator = Validator::make($request->all(), [
            'purchase_type' => 'required|string|in:product,service|max:255',
            'description' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suplier_subsidiary_ledger,supplier_id',
            'invoice_number' => 'nullable|string|max:255|unique:purchase_invoice,invoice_number,' . $id . ',pur_id',
            'issue_date' => 'required|date',
            'purchase_date' => 'required|date',
            'note' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'vat_amount' => 'required|numeric',
            'discount' => 'numeric',
            'net_total' => 'required|numeric',
            'items' => 'required|array',
            'files' => 'nullable|array',
            'files.*' => 'file|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $purchase = PurchaseInvoice::findOrFail($id);
            $purchase->update([
                'purchase_type' => $request->input('purchase_type'),
                'description' => $request->input('description'),
                'supplier_id' => $request->input('supplier_id'),
                'invoice_number' => $request->input('invoice_number'),
                'issue_date' => $request->input('issue_date'),
                'purchase_date' => $request->input('purchase_date'),
                'notes' => $request->input('note'),
                'total_amount' => $request->input('total_amount'),
                'vat_amount' => $request->input('vat_amount'),
                'discount_amount' => $request->input('discount') ?? 0,
                'net_total' => $request->input('net_total'),
                'updated_by' => auth()->user()->id,
                'branch_id' => auth()->user()->branch_office_id,
            ]);



            PurchaseInvoiceDetails::where('purchase_id', $id)->delete();

            foreach ($request->input('items') as $itemJson) {
                $item = json_decode($itemJson, true);

                // Check if qty and unit_price are set
                if (isset($item['qty'], $item['unit_price'])) {
                    PurchaseInvoiceDetails::create([
                        'purchase_id' => $purchase->pur_id,
                        'item_id' => $item['product_id'] ?? null,
                        'service_name' => $request->input('purchase_type') === 'service' ? $item['service_name'] : null,
                        'description' => '',
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'vat' => $item['vat'],
                        'total_amount' => $item['unit_price'] * $item['qty'],
                    ]);
                }
            }

            // Handle file uploads
            if ($request->hasFile('files')) {
                $uploadedFiles = FileManager::upload('invoices', $request->file('files'));

                foreach ($uploadedFiles as $uploadedFile) {
                    PurchaseInvoiceAttachment::create([
                        'p_invoice_id' => $purchase->pur_id,
                        'file_path' => $uploadedFile,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();
            return response()->json(['message' => 'Purchase updated successfully!', 'purchase' => $purchase], 200);

        }catch (\Exception $e){
            DB::rollBack();
            Log::error("Error updating purchase: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update purchase'], 500);
        }


    }
}
