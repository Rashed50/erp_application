<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller; // Use Laravel's base controller
use Illuminate\Http\Request;
// use App\Models\DailyExpense;
use Illuminate\Support\Facades\DB;
use Modules\Account\Models\{AccountTransaction, AccountTransactionDetails,PurchaseInvoice, PurchaseInvoiceDetails, PurchaseInvoiceAttachment};
use App\Http\Controllers\DataServices\{ProjectDataService, CompanyDataService,ExpenseDataService};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;
use Modules\Account\Services\FileManager;
use Modules\Account\Services\{SuppliersService,GeneralLedgerService};
use  App\Http\Controllers\Admin\Helper\UploadDownloadController;

class AccountingExpenseController extends Controller
{

    protected SuppliersService $supplierService;
     protected GeneralLedgerService $generalLedgerService;
     private Collection $project_records;
    public function __construct()
    {
        // $this->middleware('permission:read-purchase_invoice')->only('index', 'show', 'listAPI');
        // $this->middleware('permission:create-purchase_invoice')->only('billCreate', 'storePurchase');
        // $this->middleware('permission:update-purchase_invoice')->only('edit', 'updatePurchaseAPI');

        $this->generalLedgerService = new GeneralLedgerService();
        $this->project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);

    }

        public function index(Request $request)
        {
            $expense_types =  (new ExpenseDataService())->getCostTypeHeadForDropdownList();
            $cr_accounts = $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1); // load only asset type account
            return view('account::pages.purchase.daily_expoense', [
                'data' =>[
                    'expense_types' => $expense_types,
                    'projects' => $this->project_records,
                    'cr_accounts'=>$cr_accounts,

                    ]
                ]);
        }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'credit_account_id' => 'required',
            'expense_date' => 'required|date',
            'expense_type_id' => 'required',
            'amount' => 'required|numeric|min:0',
            'emp_auto_id' => 'required|exists:employee_infos,emp_auto_id',
        ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        // Log::info('Request Data:', $request->all());

        // $validator = Validator::make($request->all(), [
        //     'purchase_type' => 'required|string|in:product,service|max:255',
        //     'description' => 'nullable|string|max:255',
        //     'supplier_id' => 'required|exists:suplier_subsidiary_ledger,supplier_id',
        //     'invoice_number' => 'required|string|max:255|unique:purchase_invoice',
        //     'issue_date' => 'required|date',
        //     'purchase_date' => 'required|date',
        //     'note' => 'nullable|string',
        //     'project_id' => 'required',
        //     'total_amount' => 'required|numeric',
        //     'vat_amount' => 'required|numeric',
        //     'discount' => 'numeric',
        //     'net_total' => 'required|numeric',
        //     'items' => 'required|array',
        //     'files' => 'nullable|array',
        //     'files.*' => 'file|max:20480',
        // ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Begin transaction to ensure atomicity
        DB::beginTransaction();
        try {
            // Create a new PurchaseInvoice record
            $purchase = PurchaseInvoice::create([
                'purchase_type' => 'service', // Assuming it's always service for now, adjust as needed
                'expense_type_id' => $request->input('expense_type_id'),
                'description' => $request->input('remarks'),
                'supplier_id' => $request->input('emp_auto_id') ?? 9999,
                'invoice_number' => time(),// $request->input('invoice_number'),
                'issue_date' => $request->input('expense_date'),
                'purchase_date' => $request->input('expense_date'),
                'notes' => $request->input('emp_auto_id'),
                'project_id' => $request->input('project_id'),
                'total_amount' => $request->input('amount'),
                'vat_amount' =>0,// $request->input('vat_amount'),
                'discount_amount' =>0,// $request->input('discount') ?? 0,
                'net_total' => $request->input('amount'),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'branch_id' => auth()->user()->branch_office_id,
                'account_debit_id' =>200,// $request->input('account_debit_id'),
                'account_credit_id' => $request->input('credit_account_id'),
            ]);

            // Create PurchaseInvoiceDetails record

                    PurchaseInvoiceDetails::create([
                        'purchase_id' => $purchase->pur_id,
                        'item_id' => null,
                        'service_name' =>'service',
                        'description' => '',
                        'qty' => 1,
                        'unit_price' => $request->input('amount'),
                        'discount' =>  0,
                        'vat' => 0,
                        'total_amount' =>  $request->input('amount'),
                    ]);


            // Handle file uploads
            if ($request->hasFile('dr_invoice_path')) {
                $files = $request->file('dr_invoice_path');
                foreach ($files as $afile) {
                    $uploadedFile = (new UploadDownloadController())->uploadAccountingPurchaseInvoice($afile,null);
                    PurchaseInvoiceAttachment::create([
                        'p_invoice_id' => $purchase->pur_id,
                        'file_path' => $uploadedFile,
                    ]);
                }

            }

            $is_draft = false;

            // Create Account Transaction

                $purchaseDate = Carbon::createFromFormat('Y-m-d', $purchase->issue_date);
                $purchaseDateTime = $purchaseDate->setTime(now()->hour, now()->minute, now()->second);


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
                    $tr_cr->particular = "daily expense ";
                    $tr_cr->save();

                    // Debit
                    $tr_de = new AccountTransactionDetails();
                    $tr_de->trd_id = $tr->id;
                    $tr_de->account_no = $purchase->account_debit_id;
                    $tr_de->debit = $purchase->net_total;
                    $tr_de->particular = "daily expense ";
                    $tr_de->save();

            // Commit the transaction
            DB::commit();

            return response()->json(['status' => 201, 'message' => 'Saved successfully!', 'purchase' => $purchase], 201);
        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }


    }

    public function search(Request $request)
    {
        //ALTER TABLE `purchase_invoice` ADD `expense_type_id` INT NULL AFTER `purchase_type`;
         $query = PurchaseInvoice::query();

        // if ($request->employee_id) {
        //     $query->where('employee_id', $request->employee_id);
        // }
        // if ($request->start_date) {
        //     $query->whereDate('expense_date', '>=', $request->start_date);
        // }
        // if ($request->end_date) {
        //     $query->whereDate('expense_date', '<=', $request->end_date);
        // }
        // if ($request->expense_type) {
        //     $query->where('expense_type_id', $request->expense_type);
        // }
        $records = $query->get(); // Replace with actual query results

        //  return response()->json([
        //     'status' => 200,
        //     'data' => $records,]);

        // Validate request parameters
        // $validator = Validator::make($request->all(), [
        //     'employee_id' => 'nullable|integer|exists:employee_infos,emp_auto_id',
        //     'start_date' => 'nullable|date|date_format:Y-m-d',
        //     'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
        //     'expense_type' => 'nullable|integer|exists:expense_types,cost_type_id',
        //     'per_page' => 'nullable|integer|min:1|max:100',
        //     'page' => 'nullable|integer|min:1',
        //     'search' => 'nullable|string|max:255',
        //     'sort_by' => 'nullable|string|in:id,expense_date,amount,created_at',
        //     'sort_order' => 'nullable|string|in:asc,desc',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 422,
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        try {
            $query = PurchaseInvoice::with([ 'project'])->where('purchase_type', 'service');


            // Apply filters
            // if ($request->employee_id) {
            //     $query->where('emp_auto_id', $request->employee_id);
            // }

            if ($request->start_date) {
                $query->whereDate('purchase_date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('purchase_date', '<=', $request->end_date);
            }

            if ($request->expense_type) {
                $query->where('expense_type_id', $request->expense_type);
            }




            // Search by employee name or invoice number
            if ($request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('employee', function($emp) use ($search) {
                          $emp->where('employee_name', 'LIKE', "%{$search}%")
                              ->orWhere('employee_id', 'LIKE', "%{$search}%");
                      });
                });
            }




            // Order by
            $sortField = $request->sort_by ?? 'purchase_date';
            $sortOrder = $request->sort_order ?? 'DESC';
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->per_page ?? 100;
            $records = $query->paginate($perPage);

            // Format response
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $records->items(),
                'pagination' => [
                    'current_page' => $records->currentPage(),
                    'last_page' => $records->lastPage(),
                    'per_page' => $records->perPage(),
                    'total' => $records->total(),
                    'has_more_pages' => $records->hasMorePages(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                ],
                'links' => [
                    'first_page_url' => $records->url(1),
                    'last_page_url' => $records->url($records->lastPage()),
                    'next_page_url' => $records->nextPageUrl(),
                    'prev_page_url' => $records->previousPageUrl(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }



    }
}
