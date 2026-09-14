<?php

namespace Modules\Account\Services;

use App\Models\{InventorySupplier};
use Modules\Account\Models\{SupplierLedger,SupplierTransactions};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuppliersService
{
    public function create(array $data)
    {
        DB::beginTransaction();
        try {


            $supplier = new SupplierLedger([
                'supplier_name' => $data['isupp_name'],
                'supplier_email' => $data['isupp_email'],
                'supplier_phone' => $data['supplier_phone'],
                'vat_no' => $data['isupp_vat_number'],
                'supplier_address' => $data['isupp_contact_address'],
                'payment_term' => 1,
                'contact_person' => '',
                'contact_person_phone' => '',
                'contact_person_email' => '',
                'country' => '',
                'opening_date' => '',
                'opening_balance' => $data['initial_balance'],
                'current_balance' => $data['initial_balance'],
                'branch_office_id' => $data['branch_office_id'],
                'created_by' => Auth::id(),
                'updated_by' => null,
                'active_status' => $data['active_status'] ?? 0,
            ]);
            $supplier->save();

            DB::commit();

            return $supplier;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Inventory Supplier: ' . $e->getMessage());
            throw $e;
        }
    }


    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try {

            SupplierLedger::where('supplier_id', $id)->update([
            'supplier_name' => $data['isupp_name'],
            'supplier_email' =>  $data['isupp_email'],
            'vat_no' =>  $data['isupp_vat_number'],
            'supplier_address' =>  $data['isupp_contact_address'],
        //    'supplier_phone' =>  $data['supplier_phone'],
          //  'opening_balance' =>  $data['opening_balance'],
            'active_status' => $data['active_status'] ?? 0,
            'updated_by' =>  Auth::id(),
            'updated_at' => Carbon::now()
        ]);

            DB::commit();
            return SupplierLedger::where('supplier_id', $id)->first();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Inventory Supplier: ' . $e->getMessage());
            throw $e;
        }
    }


    public function getActiveSupplierInformationForDropdown(){
        return  SupplierLedger::select('supplier_id','supplier_name')->get();
    }








    /*
     ==========================================================================
     =================== Supplier Subsidiary Leadger Transaction Module =======
     ==========================================================================
    */
     public function storePurchaseSubsidiaryLedgerTransaction($supplier_id,$transaction_id,$purchaseDateTime,$invoice_number,$cr_amount,$notes,$crated_by,$branch_office_id){

            $sup_tran = new SupplierTransactions();
            $sup_tran->supplier_id = $supplier_id;
            $sup_tran->transaction_id = $transaction_id;
            $sup_tran->transaction_date = $purchaseDateTime;
            $sup_tran->transaction_type = 'purchase';
            $sup_tran->invoice_no = $invoice_number;
            $sup_tran->debit = 0;
            $sup_tran->credit = $cr_amount;
            $sup_tran->notes = $notes ;
            $sup_tran->created_by = $crated_by;
            $sup_tran->branch_office_id = $branch_office_id;
            $sup_tran->save();

            $drcr = SupplierTransactions::select(
                DB::raw("SUM(debit) as dr_amount"),
                 DB::raw("SUM(credit) as cr_amount"),
            )->where('supplier_id', $supplier_id)->first();

            $asup = SupplierLedger::where('supplier_id', $supplier_id)->first();

            SupplierLedger::where('supplier_id', $supplier_id)->update([
                'current_balance' => $asup->opening_balance + ($drcr->cr_amount - $drcr->dr_amount),
                'updated_at' => $purchaseDateTime,
                'updated_by' =>  $crated_by,
            ]);

     }

    public function saveSupplierPaymentSubsidiaryLedgerTransaction($supplier_id,$transaction_id,$purchaseDateTime,$invoice_number,$dr_amount,$notes,$crated_by,$branch_office_id){

            $sup_tran = new SupplierTransactions();
            $sup_tran->supplier_id = $supplier_id;
            $sup_tran->transaction_id = $transaction_id;
            $sup_tran->transaction_date = $purchaseDateTime;
            $sup_tran->transaction_type = 'bill payment';
            $sup_tran->invoice_no = $invoice_number;
            $sup_tran->debit = $dr_amount;
            $sup_tran->credit = 0;
            $sup_tran->notes = $notes ;
            $sup_tran->created_by = $crated_by;
            $sup_tran->branch_office_id = $branch_office_id;
            $sup_tran->save();

            $drcr = SupplierTransactions::select(
                DB::raw("SUM(debit) as dr_amount"),
                 DB::raw("SUM(credit) as cr_amount"),
            )->where('supplier_id', $supplier_id)->first();

            $asup = SupplierLedger::where('supplier_id', $supplier_id)->first();

            SupplierLedger::where('supplier_id', $supplier_id)->update([
                'current_balance' => $asup->opening_balance + ($drcr->cr_amount - $drcr->dr_amount),
                'updated_at' => $purchaseDateTime,
                'updated_by' =>  $crated_by,
            ]);

    }

}
