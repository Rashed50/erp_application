<?php

namespace App\Http\Controllers\Admin\AccountsModule;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Inventory\item_name;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\ItemSubCategory;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\ChartOfAccountDataService;
use App\Http\Controllers\DataServices\PurchaseInvoiceDataService;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\SupplierDataService;


class ChartofaccountPurchaseController extends Controller
{
    public function index(){

        (new PurchaseInvoiceDataService())->removeAddToCartItemsInChartofAccountPuchaseInvoice();
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $item_names = (new InventoryItemSetupDataService())->getInventoryItemNamesAllRecordsForDropDownMenu();
        $purchase_by = (new ChartOfAccountDataService())->getListOfChartOfAccountInformationByAccountTypeId(1); // asset account
        $purchase_from = (new SupplierDataService())->getAllActiveInventorySupplierForDropdown(Auth::user()->branch_office_id);
        $journals =array();// (new ChartOfAccountDataService())->getListOfJournalNameForDropdownMenu();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);

        return view('admin.accounts_module.purchase.index',compact('item_names','journals',  'allType', 'purchase_by', 'purchase_from', 'projects'));

    }

    public function addToCartInventoryItemsInChartOfAccountPurchaseInvoice(Request $request)
    {
        try {

             $item_details_record = (new InventoryItemSetupDataService())->searchAnItemNameInformationsByItemCode($request->item_deta_code);// item_name::where('item_deta_code', $request->item_deta_code)->select('item_deta_name')->firstOrFail();
            // Add the items to the cart
            $cart_items = (new PurchaseInvoiceDataService())->addToCartItemsInChartofAccountPuchaseInvoice(
                $item_details_record->item_deta_code,  $item_details_record->item_deta_name,  $request->p_quantity,$request->p_unit_rate,$request->p_item_total_amount,$request->remarks  );
            if ($cart_items) {
                return response()->json(
                    [
                        'status'=>200,
                        'success' => true,
                        'cart_items' => $cart_items,
                        'message' => 'Cart Items Successfully Added',
                    ]
                );
            }

            // Return an error response if the cart items could not be added
            return response()->json(
                [
                    'status'=>403,
                    'success' => false,
                    'error'=>'error',
                    'message' => 'Failed to add items to the cart',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status'=>403,
                    'success' => false,
                    'error'=>'error',
                    'message' => 'System Operation Failed '. $e->getMessage(),
                ]
            );


        }
    }

    public function storePurchaseInvoiceInformation(Request $request){
        try{
            //   dd($request->all());
            //   response()->json(
            //     [
            //         'status'=>200,
            //         'success' => true,
            //         'data' => $request->all(),
            //         'message' => 'Cart Items Successfully Added',
            //     ]
            // );
        // $d_acc = 1020;
        //  $cr_acc = 1300;
        $cart_items = (new PurchaseInvoiceDataService())->getAddToCartItemsInChartofAccountPuchaseInvoice();
        //dd($cart_items);
        if(count($cart_items)){
            $system_auto_invoice = (new PurchaseInvoiceDataService())->generateSystemGeneratedPurhcaseInvoice(Auth::user()->branch_office_id);
            $invoice_id =   (new PurchaseInvoiceDataService())->storePurchaseInvoiceInformation(120,300, $request->journal_id,$request->supplyer_id,  $system_auto_invoice ,$request->remarks ,$request->invoice_date ,$request->invoice_no ,$request->total_with_out_vat
            ,$request->p_discount ,$request->p_vat_total ,$request->pinv_grand_total ,'cash',1,Auth::user()->id,$request->project_id);

            if($invoice_id){
                foreach($cart_items as $aci){
                    (new PurchaseInvoiceDataService())->savePurchaseCartItems($invoice_id,$aci->id,$aci->qty,$aci->price,$aci->options->item_total_amount);
                 }
                 return response()->json(
                    [
                        'status'=>200,
                        'success' => true,
                        'message' => 'Successfully Saved',
                    ]
                );
            }
        }
        return response()->json(
            [
                'status'=>403,
                'success' => false,
                'error'=>'error',
                'message' => 'Failed to Save. Please Try Again',
            ]
        );

        }catch(Exception $ex){

            return response()->json(
                [
                    'status'=>403,
                    'success' => false,
                    'error'=>'error',
                    'message' => 'System Operation Failed '. $e->getMessage(),
                ]
            );
        }

    }








}
