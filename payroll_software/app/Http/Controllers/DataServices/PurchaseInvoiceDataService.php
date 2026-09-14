<?php

namespace App\Http\Controllers\DataServices;

use Carbon\Carbon;
use App\Models\AccountsModule\ChartofaccountPurchaseInfo;
 use App\Models\AccountsModule\ChartofaccountPurchaseItemDetails;
use Gloudemans\Shoppingcart\Facades\Cart;


class PurchaseInvoiceDataService{


    public function generateSystemGeneratedPurhcaseInvoice($branch_office_id){
         $today = now()->format('Y-m-d');
        return  $today.'-'.ChartofaccountPurchaseInfo::count();

    }

    public function addToCartItemsInChartofAccountPuchaseInvoice($item_code,$item_name,$quantity,$unit_rate,$item_total_amount,$remarks)
    {
        Cart::instance('puchaseinvoice_item_details')->add([
            'id' => $item_code,
            'name' => $item_name,
            'qty' => $quantity ?? 1,
            'price' => $unit_rate,
            'weight' => 1,
            'options' => [
                'item_total_amount'=> $item_total_amount,
            ],
        ]);
      return Cart::instance('puchaseinvoice_item_details')->content();
    }

   public function getAddToCartItemsInChartofAccountPuchaseInvoice()
   {
        return Cart::instance('puchaseinvoice_item_details')->content();
   }

   public function removeAddToCartItemsInChartofAccountPuchaseInvoice()
   {
        Cart::instance('puchaseinvoice_item_details')->destroy();
   }


   public function storePurchaseInvoiceInformation($de_acc, $cr_acc, $jou_id, $supplier_auto_id, $cpi_invoice_no, $inv_descrip, $inv_date, $supl_inv_no, $itm_amount, $discount, $vat_amnt, $ground_total, $payterm, $pay_status,$created_by, $project_id,
    ){

           return ChartofaccountPurchaseInfo::insertGetId([
                        'cpi_debit_account_id'=>$de_acc ,
                        'cpi_credit_account_id'=>$cr_acc ,
                        'cpi_journal_id'=>$jou_id ,
                        'supplyer_auto_id'=>$supplier_auto_id,
                        'cpi_project_id'=>$project_id,
                        'cpi_invoice_no'=>$cpi_invoice_no,
                        'cpi_inv_remarks'=>$inv_descrip,
                        'cpi_purchase_date'=>$inv_date ,
                        'cpi_payment_terms'=>$payterm,
                        'supplyer_invoice_no'=>$supl_inv_no ,
                        'cpi_total_amount'=>$itm_amount ,
                        'cpi_discount_amount'=>$discount ,
                        'cpi_vat_amount'=>$vat_amnt ,
                        'cpi_grand_total_amount'=>$ground_total ,
                        'cpi_payment_status'=> $pay_status ,
                        'created_by_id'=>$created_by ,
                    ]);


   }

   public function savePurchaseCartItems($ca_purch_auto_id,$item_deta_id,$cpid_qty,$cpid_unit_price,$cpid_paid_amount){

             ChartofaccountPurchaseItemDetails::insertGetId([

                'ca_purch_auto_id'=>$ca_purch_auto_id ,
                'cpid_paid_amount'=>$cpid_paid_amount ,
                'cpid_unit_price'=>$cpid_unit_price ,
                'cpid_qty'=>$cpid_qty ,
                'item_deta_id'=>$item_deta_id ,
                'cpid_description'=> '' ,
            ]);
   }




}
