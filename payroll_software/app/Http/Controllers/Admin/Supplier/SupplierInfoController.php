<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\SupplierDataService;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SupplierInfoController extends Controller
{
    //  public function index(){
    //     return view('admin/supplier.index');
    //  }

     public function storeNewSupplierInformation(Request $request){
       // dd($request->all());
        (new SupplierDataService())->insertNewSupplierInformation($request->supplier_name ,$request->vat_no,$request->supplier_address,$request->mobile_no,1,Auth::user()->id,Carbon::now());
        return redirect()->back();
    }

    public function showSupplierInformationUI(){
        return view('admin.supplier.all');
    }


    public function storeInventorySupplierInfo(Request $request) {
        $request->validate([
            'isupp_name' => 'required|string',
            'isupp_email' => 'nullable|email',
            'isupp_vat_number' => 'required|string',
            'isupp_contact_address' => 'nullable|string',
        ]);

        $insert = (new SupplierDataService())->storeAllRequestedInventorySupplierInfo(
            $request->isupp_name,
            $request->isupp_email,
            $request->isupp_vat_number,
            $request->isupp_contact_address,Auth::user()->id,Carbon::now()
        );

        if ($insert) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully! Data Inserted.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Operation Failed. Maybe Same VAT Number Already Exists.',
            ]);
        }
    }


    public function loadInventorySupplierInformation(){
        $suppliers = (new SupplierDataService())->getInventorySupplierAllRecords();

        return response()->json(
            [
                'success' => true,
                'suppliers' => $suppliers,
                'message' => 'Suppliers All Information',
            ],
            200
        );
    }


    public function changeInventorySupplierStatus($supplierAutoID){
        if ($supplierAutoID) {
            $statusUpdate = (new SupplierDataService())->updateStatusForRequestedSupplierInfo($supplierAutoID);

            if ($statusUpdate) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier status updated successfully.',
                ], 200); // HTTP status 200 OK
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update supplier status.',
                ], 500); // HTTP status 500 Internal Server Error
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Supplier ID.',
            ], 400); // HTTP status 400 Bad Request
        }
    }

}
