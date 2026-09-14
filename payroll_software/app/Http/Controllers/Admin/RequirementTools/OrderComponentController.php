<?php

namespace App\Http\Controllers\Admin\RequirementTools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\RequirementTools\ItemTypeController;
use Illuminate\Http\Request;
use App\Models\EmployeeInfo;
use App\Models\ItemPurchase;
use App\Models\PurchaseRecord;
use App\Models\ItemSubCategory;
use Carbon\Carbon;
use Session;
use Auth;
use Cart;

class OrderComponentController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function addToCart(Request $request){
      $id = uniqid();
      Cart::add([
        'id' => $id,
        'name' => $request->iscatg_id,
        'qty' => $request->quantity,
        'price' => $request->stock_amount,
        'weight' => 1,
        'options' => [
          'itype_id' => $request->itype_id,
          'icatg_id' => $request->icatg_id,
          'itype_name' => $request->itype_name,
          'icatg_name' => $request->icatg_name,
          'iscatg_name' => $request->iscatg_name,
          ]
        ]);
        return response()->json(['success' => 'Successfully Added Cart On Item']);
  }

  public function metarialListView(){
    $carts = Cart::content();
    $cartQty = Cart::count();
    $cartTotal = Cart::total();
    return response()->json(array(
      'carts' => $carts,
      'cartQty' => $cartQty,
      'cartTotal' => $cartTotal,
    ));
  }

  public function metarialSingleListRemove($rowId){
    Cart::remove($rowId);
    return response()->json(['success' => 'Successfully Remove Item This List']);
  }

  public function orderComplete(Request $request){
    /* Others Amount */
    $othersAmount = $request->other_amount;
    $net_amount = $request->net_amount;
    $total_amount = $request->total_amount;
    $emp_id = $request->emp_id;

    $findEmployee = EmployeeInfo::where('employee_id',$emp_id)->first();
    $employee_id = $findEmployee->emp_auto_id;

    if($othersAmount == ""){
      $extraAmount = 0;
      $totalPay = $net_amount;
    }else{
      $extraAmount = $othersAmount;
      $totalPay = $total_amount;
    }
    /* creator id */
    $createBy = Auth::user()->id;
    /* insert data in item purchase */
    if($findEmployee){
      $insert = ItemPurchase::insertGetId([
        'date' => Carbon::now(),
        'other_cost' => $extraAmount,
        'total_amount' => $totalPay,
        'paid_total_amount' => $request->paid_amount,
        'purchase_by_id' => $employee_id,
        'create_by_id' => $createBy,
        'created_at' => Carbon::now(),
      ]);


    }else{
      Session::flash('not_match_employee','value');
      return redirect()->back();
    }

    /* insert data in item purchase recode */

    $cartContent = Cart::content();
    if($insert){
      foreach ($cartContent as $cart) {

          PurchaseRecord::insert([
            'itype_id' => $cart->options->itype_id,
            'icatg_id' => $cart->options->icatg_id,
            'iscatg_id' => $cart->name,
            'quantity' => $cart->qty,
            'rate' => $cart->price,
            'amount' => $cart->subtotal,
            'item_purchase_id' => $insert,
            'create_by_id' => $createBy,
            'created_at' => Carbon::now(),
          ]);

          $updateStockAmount = PurchaseRecord::where('iscatg_id',$cart->name)->first();

          $find = ItemSubCategory::where('iscatg_id',$updateStockAmount->iscatg_id)->first();
          $quantity = ($find->stock_amount + $cart->qty);

          $update = ItemSubCategory::where('iscatg_id',$updateStockAmount->iscatg_id)->update([
            'stock_amount' => $quantity,
            'updated_at' => Carbon::now(),
          ]);
      }
    }
    Cart::destroy();
    /* redirect back */
    if($insert){
      Session::flash('success','value');
      return redirect()->back();
    }else{
      Session::flash('error','value');
      return redirect()->back();
    }

  }







  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */







  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

  public function index(){
    /* ==== call itemType controller ==== */
    $itemType = new ItemTypeController();
    $allType = $itemType->getAll();
    /* ==== all Cart Data ==== */
    $carts = Cart::content();
    $cartQty = Cart::count();
    $cartTotal = Cart::total();
    return view('admin.metarials-tools.order.all',compact('allType','carts','cartQty','cartTotal'));
  }

}
