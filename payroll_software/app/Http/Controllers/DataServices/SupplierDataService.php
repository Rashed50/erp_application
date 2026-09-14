<?php

namespace App\Http\Controllers\DataServices;


use App\Models\InventorySupplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class SupplierDataService{

    /*
     ==========================================================================
     ==================== Inventory Item supplier Section =====================
     ==========================================================================
    */
    public function getSupplierDetailsInformationByID($supplierID){
        return InventorySupplier::where('isupp_auto_id', $supplierID)->first();
    }

    public function updateStatusForRequestedSupplierInfo($supplierID){
        $supplier = $this->getSupplierDetailsInformationByID($supplierID);
        if ($supplier) {
            // Update the supplier's status using the ternary operator correctly
            InventorySupplier::where('isupp_auto_id', $supplierID)->update([
                'isupp_status' => $supplier->isupp_status == 0 ? 1 : 0,
            ]);
            return true; // Indicate success
        }
        return false; // Indicate failure
    }



    public function getInventorySupplierAllRecords(){
        return InventorySupplier::select('isupp_auto_id', 'isupp_name', 'isupp_email', 'isupp_vat_number', 'isupp_contact_address', 'branch_office_id', 'isupp_status')->get();
    }

    public function getAllActiveInventorySupplierForDropdown($branch_office_id){
        return InventorySupplier::where('isupp_status', 1)->select('isupp_auto_id', 'isupp_name')->get();
    }

    public function storeAllRequestedInventorySupplierInfo($isupp_name, $isupp_email, $isupp_vat_number, $isupp_contact_address,$insert_by,$insert_date) {
        if ($this->checkPurchaseVatNumberIsExist($isupp_vat_number)) {
            return 0;
        } else {
            return InventorySupplier::create([
                'isupp_name' => $isupp_name,
                'isupp_email' => $isupp_email,
                'isupp_vat_number' => $isupp_vat_number,
                'isupp_contact_address' => $isupp_contact_address,
                'isupp_create_by_id' => $insert_by,
                'created_at' => $insert_date
            ]);
        }
    }

    public function checkPurchaseVatNumberIsExist($vat_number) {
        // Check if a supplier with the given VAT number already exists
        return InventorySupplier::where('isupp_vat_number', $vat_number)->exists();
    }


}
