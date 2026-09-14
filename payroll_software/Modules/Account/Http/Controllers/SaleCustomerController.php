<?php

namespace Modules\Account\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Services\{SalesCustomerService,SuppliersService,GeneralLedgerService};

use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth,Log,DB};
// use Illuminate\Support\Facades\DB;


 class SaleCustomerController extends Controller
{
    public function index()
    {
        $salesCustomerService = new SalesCustomerService();
        return view('account::pages.customers.index', [
            'customers' => $salesCustomerService->getCustomersForSales(auth()->user()->branch_office_id)
        ]);
    }

    public function store(Request $request)
    {


        try {

             $data = $request->validate([
                    'customer_name' => 'required|string|max:255',
                    'customer_email' => 'nullable|email|max:255|unique:customer_subsidiary_ledger,customer_email',
                    'customer_phone' => 'nullable|string|max:20|unique:customer_subsidiary_ledger,customer_phone',
                   // 'customer_address' => 'nullable|string|max:500',
                    'vat_no' => 'nullable|string|max:50|unique:customer_subsidiary_ledger,vat_no',
                  //  'payment_term' => 'nullable|integer|min:0|max:365',
                    'contact_person' => 'nullable|string|max:255',
                    'contact_person_phone' => 'nullable|string|max:20',
                    'contact_person_email' => 'nullable|email|max:255',
                    'country' => 'nullable|string|max:100',
                    'opening_date' => 'nullable|date|before_or_equal:today',
                    'current_balance' => 'nullable|numeric|between:-999999999.99,999999999.99',
                   // 'active_status' => 'nullable|boolean',
                   // 'branch_office_id' => 'required|integer|exists:branch_offices,id',
             ]);

             $data['customer_address'] = $request->cus_billing_address.', '. $request->cus_billing_zip.','.$request->cus_billing_zip;

            // Set created_by if not provided and user is authenticated
            if (!isset($data['created_by'])) {
                $data['created_by'] =Auth::user()->id;
            }

            // Set opening date to today if not provided
            if (empty($data['opening_date'])) {
                $data['opening_date'] = now()->toDateString();
            }

            // Ensure current_balance is set
            if (!isset($data['current_balance'])) {
                $data['current_balance'] = 0;
            }

            // Set opening_balance equal to current_balance for new customers
            $data['opening_balance'] = $data['current_balance'];

            $data['branch_office_id'] = auth()->user()->branch_office_id;

            DB::beginTransaction();
            $customer =  (new SalesCustomerService())->saveNewCustomerInformation($data);

            $asset_acc_type_id = (new GeneralLedgerService())->getAssetAccountTypeId();
            $receiable_account = (new GeneralLedgerService())->getGeneralLedgerReceiableAssetTypeAccountRecord($asset_acc_type_id);

            if($request->current_balance >0 && $receiable_account){

                    $dr_account = $receiable_account->chart_of_acct_id;
                     $cr_account = (new GeneralLedgerService())->getGeneralLedgerOwnerEquityCapitalAccountRecord()->chart_of_acct_id;

                     (new GeneralLedgerService())->initialGeneralLedgerAccountBalanceSetup(date('Y-m-d'),
                "",3000,Auth::user()->id,$request->current_balance,"initialize Customer Sales account",$dr_account,$cr_account);
            }


            // Log the creation
            // Log::info('Customer created', [
            //     'customer_id' => $customer->customer_id,
            //     'customer_name' => $customer->customer_name,
            //     'created_by' => $data['created_by'] ?? null,
            // ]);

            DB::commit();
           // return $customer;

            return response()->json([
                'success' => true,
                'message' => 'Saved Successfully.',
                'data' => $customer
            ], 201);



        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                 'message' => 'System Operation Failed',
                'error' => $e.message()
            ], 403);

        }

    }

    public function update(Request $request)
    {
        $salesCustomerService = new SalesCustomerService();

        $data = $request->validate([
                    'customer_name' => 'required|string|max:255',
                    'customer_email' => 'nullable|email|max:255',
                    'customer_phone' => 'nullable|string|max:20',
                   // 'customer_address' => 'nullable|string|max:500',
                    'vat_no' => 'nullable|string|max:50',
                  //  'payment_term' => 'nullable|integer|min:0|max:365',
                    'contact_person' => 'nullable|string|max:255',
                    'contact_person_phone' => 'nullable|string|max:20',
                    'contact_person_email' => 'nullable|email|max:255',
                    'country' => 'nullable|string|max:100',
                    'opening_date' => 'nullable|date|before_or_equal:today',
                    'current_balance' => 'nullable|numeric|between:-999999999.99,999999999.99',
                   // 'active_status' => 'nullable|boolean',
                   // 'branch_office_id' => 'required|integer|exists:branch_offices,id',
             ]);

            $data['customer_address'] = $request->cus_billing_address.', '. $request->cus_billing_zip.','.$request->cus_billing_zip;

        return $salesCustomerService->update($request->customer_id, $data, auth()->user()->branch_office_id);
    }

    public function delete(Request $request)
    {
        $salesCustomerService = new SalesCustomerService();
        $salesCustomerService->delete($request->customer_id, auth()->user()->branch_office_id);
    }





    /**
     * Create a new customer
     */
    public function create($request)
    {

    }

    /**
     * Update existing customer
     */
    // public function update(int $customerId, array $data)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $customer = $this->findById($customerId);

    //         // Set updated_by if user is authenticated
    //         if (auth()->check() && !isset($data['updated_by'])) {
    //             $data['updated_by'] = auth()->id();
    //         }

    //         // Store old data for logging
    //         $oldData = $customer->toArray();

    //         // Update customer
    //         $customer->update($data);

    //         // Log the update
    //         Log::info('Customer updated', [
    //             'customer_id' => $customerId,
    //             'changes' => $this->getChanges($oldData, $customer->toArray()),
    //             'updated_by' => $data['updated_by'] ?? null,
    //         ]);

    //         DB::commit();
    //         return $customer->refresh();

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Failed to update customer', [
    //             'customer_id' => $customerId,
    //             'error' => $e->getMessage(),
    //             'data' => $data
    //         ]);
    //         throw $e;
    //     }
    // }








}
