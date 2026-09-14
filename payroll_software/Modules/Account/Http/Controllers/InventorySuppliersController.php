<?php

namespace Modules\Account\Http\Controllers;

use App\Models\BranchOffice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Services\{SuppliersService,GeneralLedgerService};
use Modules\Account\Models\{SupplierLedger};
use Carbon\Carbon;

class InventorySuppliersController extends Controller
{
    protected SuppliersService $suppliersService;

    public function __construct()
    {

        $this->suppliersService = new SuppliersService();
    }

    public function index()
    {

        $branchs = BranchOffice::all();
        return view('account::pages.suppliers.list', [
            'data' => [
                'branchs' => $branchs,
            ]
        ]);
    }

    public function listAPI(Request $request)
    {
        $perPage = (int)$request->query('per_page', 10);
        $page = (int)$request->query('page', 1);
        $search = $request->query('search', '');
        $branch = $request->query('branch', null);
        $active = $request->query('active', null);

        $query = SupplierLedger::with('branch')->orderBy('created_at', 'desc');


        // if (!empty($search)) {

        //     $query->where(function ($q) use ($search) {
        //         $q->where('isupp_name', 'like', "%{$search}%")
        //             ->orWhere('isupp_email', 'like', "%{$search}%")
        //             ->orWhere('isupp_contact_address', 'like', "%{$search}%");
        //     });
        // }

        if ($branch !== null) {
            $query->where('branch_office_id', $branch);
        }

        if ($active !== null) {
            $query->where('isupp_status', $active);
        }

        $tableDatas = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($tableDatas);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'branch_office_id' => 'required|integer',
            'isupp_name' => 'required|string|max:255',
            'isupp_email' => 'required|string|email|max:255',
            'supplier_phone' => 'required',
            'isupp_vat_number' => 'required|string|max:255',
            'isupp_contact_address' => 'nullable|string',
            'active_status' => 'nullable|boolean',
        ]);

            try {
                $supplier = $this->suppliersService->create($request->all());

                $liability_acc_type_id = (new GeneralLedgerService())->getLiabilityAccountTypeId();
                $payable_account = (new GeneralLedgerService())->getGeneralLedgerAccountPayableAccountRecord($liability_acc_type_id);

             if($request->initial_balance >0 && $payable_account){

                    $cr_account = $payable_account->chart_of_acct_id; // 3 = Equity
                    $dr_account = (new GeneralLedgerService())->getGeneralLedgerMontherAccountWhichParentIdNull(1)->chart_of_acct_id; // 1 = Asset
                 (new GeneralLedgerService())->initialGeneralLedgerAccountBalanceSetup(date('Y-m-d'),
                "",2000,Auth::user()->id,$request->initial_balance,"initialize supplier account",$dr_account,$cr_account);
            }


            return response()->json([
                'success' => true,
                'message' => 'Inventory Supplier created successfully.',
                'data' => $supplier,
            ], 201);

        } catch (Exception $e) {
            Log::error('Error storing Inventory Supplier: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Inventory Supplier.',
            ], 500);
        }

    }


    public function create()
    {
        $branchs = BranchOffice::all();
        // Log::info($branchs);
        return view('account::pages.suppliers.create', [
            'data' => [
                'branchs' => $branchs,
            ]
        ]);
    }

    public function show($id)
    {
        return view('account::show');
    }

    public function edit($id)
    {
        $branchs = BranchOffice::all();
        // Log::info($branchs);
        // Log::info("Id =". $id);

        try {
            $supplier = SupplierLedger::where('supplier_id', $id)->first(); // SupplierLedger::find($id);
          //  dd($supplier);

            return view('account::pages.suppliers.update', [
                'data' => [
                    'branchs' => $branchs,
                    'supplier' => $supplier,
                ]
            ]);

        } catch (\Exception $e) {
            // Log::error("Error fetching supplier: ". $e->getMessage());
            return view('account::pages.suppliers.update', [
                'data' => [
                    'branchs' => $branchs,
                    'error' => $e->getMessage(),
                ]
            ]);
        }
    }

 

    public function updateAPI(Request $request, $id): JsonResponse
    {

        $validated = $request->validate([
            'branch_office_id' => 'required|integer',
            'isupp_name' => 'required|string|max:255',
            'isupp_email' => 'required|string|email|max:255',
            'isupp_vat_number' => 'required|string|max:255',
            'isupp_contact_address' => 'nullable|string',
            'active_status' => 'required|boolean',
        ]);

       // try {
            $supplier = $this->suppliersService->update($validated, $id);

            return response()->json([
                'success' => true,
                'message' => 'Inventory Supplier updated successfully.',
                'data' => $supplier
            ], 200);

        // } catch (\Exception $e) {
        //     Log::error('Error updating Inventory Supplier: ' . $e->getMessage());

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'An error occurred: ' . $e->getMessage()
        //     ], 500);
        // }
    }


    public function destroy($id): JsonResponse
    {
        try {
            SupplierLedger::where('supplier_id', $id)->update([
                'active_status' => 0,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier. ' . $e->getMessage(),
            ], 500);
        }
    }
}
