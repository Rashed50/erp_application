<?php

namespace Modules\Account\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Modules\Account\Models\{AccountTransaction, AccountTransactionDetails};
use Modules\Account\Models\{ChartofaccSalesRecord,ChartofaccSalesRecordDetail, CustomerLedger,CustomerTransaction};
use PhpOption\None;

class SalesService
{
    function getSales()
    {
        return ChartofaccSalesRecord::with([])
            ->where(function ($query) {
                $query->where('branch_office_id', auth()->user()->branch_office_id)
                    ->orWhereNull('branch_office_id');
            })
            ->latest()
            ->get();
    }


    function create(array $data, ?array $attachments = null, ?bool $is_draft = null, ?int $branch_id = null)
    {
        Log::info('Create function called with parameters:', [
            'data' => $data,
            'attachments' => $attachments,
            'is_draft' => $is_draft,
            'branch_id' => $branch_id
        ]);

        if ($is_draft) {
            $find = ChartofaccSalesRecord::where('sr_invoice_no', $data['invoice_no'])->first();
            if ($find) {
                return $this->update($find->sr_auto_id, $data, $attachments, $branch_id);
            }
        }
         $login_id = auth()->user()->id;
        $sale = new ChartofaccSalesRecord();
        $sale->cus_auto_id = $data['customer'];
        $sale->account_debit_id = $data['account_debit_id'];
        $sale->account_credit_id = $data['account_credit_id'];
        $sale->sr_invoice_no = $data['invoice_no'];
        $sale->sr_invoice_description = $data['invoice_desc'] ?? '';
        $sale->sr_tnx_id = 1;
        $sale->sr_issue_date = $data['issue_date'] ?? now();
        $sale->sr_payment_terms = $data['payment_term'];
        $sale->sr_due_date = $data['due_date'] ?? now();
        $sale->sr_supply_date = $data['supply_date'] ?? now();
        $sale->notes = $data['notes'];
        $sale->sr_payment_mean = '';
        $sale->sr_total_amount = $data['total_amount'];
        $sale->sr_discount_amount = 0;
        $sale->sr_vat_amount = $data['vat_amount'];
        $sale->sr_grand_total_amount = $data['total'];
        $sale->is_draft = $data['is_draft'] ?? true;
        $sale->retention_amount = $data['retention'] ?? 0;
        $sale->branch_office_id = $data['branch_office_id'] ?? null;
        $sale->created_by_id =  $login_id ;
        $sale->updated_by_id =  $login_id;
        $sale->project_id = $data['project_id'];
        $sale->save();

        foreach ($data['items'] as $item) {
            $productName = $item['product_data']['spi_name_en'] ?? $item['newProduct'] ?? '';
            $productId = $item['product_data']['spi_auto_id '] ?? null;

            $account = ChartofaccSalesRecordDetail::create([
                'sr_auto_id' => $sale->sr_auto_id ?: $sale->id,
                'srd_description' => '',
                'srd_product_id' => $productId,
                'srd_qty' => $item['qty'] ?? 1,
                'srd_unit_price' => $item['unit_price'] ?? 0,
                'srd_inclusive' => 1,
                'srd_discount' => 0,
                'srd_vat_percent' => $item['vat'] ?? 0,
                'srd_vat_value' => $item['vat_amount'] ?? 0,
                'srd_total_amount' => $item['with_vat'] ?? 0,
                'product_name' => $productName,
            ]);
        }


        $attach = [];
        if ($attachments) {
            if (!file_exists(public_path('sales/' . $sale->sr_auto_id))) {
                mkdir(public_path('sales/' . $sale->sr_auto_id));
            }
            foreach ($attachments as $attachment) {
                $attachment->move(public_path('sales/' . $sale->sr_auto_id), $attachment->getClientOriginalName());
                $attach[] = '/sales/' . $sale->sr_auto_id . '/' . $attachment->getClientOriginalName();
            }
            $sale->attachments = ($attach);
            $sale->save();
        }
        $sale->load(['customer', 'items.product', 'debit', 'credit']);


        // Create Account Transaction
        if ($is_draft == false){
            $saleDate = Carbon::createFromFormat('Y-m-d', $sale->sr_issue_date);
            $saleDateTime = $saleDate->setTime(now()->hour, now()->minute, now()->second);

            try {
                $tr = new AccountTransaction();
                $tr->date = $saleDateTime;
                $tr->tr_no = $sale->sr_invoice_no;
                $tr->sales_id = $sale->sr_auto_id;
                $tr->approve_by =  $login_id;
                $tr->total_amount = $sale->sr_grand_total_amount;
                $tr->general_particular = $sale->notes;
                $tr->save();

                // Credit
                $tr_cr = new AccountTransactionDetails();
                $tr_cr->trd_id = $tr->id;
                $tr_cr->account_no = $sale->account_credit_id;
                $tr_cr->credit = $sale->sr_grand_total_amount;
                $tr_cr->particular = "{$sale->sr_grand_total_amount} amount credit from {$sale->credit->account_id}";
                $tr_cr->save();

                // Debit
                $tr_de = new AccountTransactionDetails();
                $tr_de->trd_id = $tr->id;
                $tr_de->account_no = $sale->account_debit_id;
                $tr_de->debit = $sale->sr_grand_total_amount;
                $tr_de->particular = "{$sale->sr_grand_total_amount} amount debit from {$sale->debit->account_id}";
                $tr_de->save();



                $cus_tran = new CustomerTransaction();
                $cus_tran->customer_id =  $data['customer'];
                $cus_tran->transaction_id = $tr->id;
                $cus_tran->transaction_date =  $saleDate;
                $cus_tran->transaction_type = 'Invoice';
                $cus_tran->invoice_no = $sale->sr_invoice_no;
                $cus_tran->debit = $sale->sr_grand_total_amount;
                $cus_tran->credit = 0;
                $cus_tran->notes =  $sale->notes;
                $cus_tran->created_by = $login_id;
                $cus_tran->branch_office_id = 1;
                $cus_tran->ct_status = 1;
                $cus_tran->save();

                $drcr = CustomerTransaction::select(
                    DB::raw("SUM(debit) as dr_amount"),
                    DB::raw("SUM(credit) as cr_amount"),
                )->where('customer_id', $data['customer'])->first();

                CustomerLedger::where('customer_id', $data['customer'])->update([
                    'current_balance' => $drcr->dr_amount - $drcr->cr_amount,
                // 'updated_at' => $form_data['paid_date'],
                    'updated_by' =>  $login_id,
                ]);

            } catch (\Exception $e) {
                Log::error('Error in creating transaction:', ['error' => $e->getMessage()]);
                // return $e->getMessage();
            }
        }

        return $sale;
    }



    public function update(int $id, array $data, ?array $attachments = null, ?bool $is_draft = false, ?int $branch_id = null)
    {
        // Log::info('Create function called with parameters:', [
        //     'data' => $data,
        //     'attachments' => $attachments,
        //     'is_draft' => $is_draft,
        //     'branch_id' => $branch_id
        // ]);

        // return null;

        try {
            // Start database transaction to ensure atomicity
            DB::beginTransaction();
            // Log::error("Sale ID: $id");

            // Find the sale record
            $sale = ChartofaccSalesRecord::find($id);
            if (!$sale) {
                Log::error("Sale record not found with ID: $id");
                return null;
            }

            // Log::info("From Service, Draft =" . ($is_draft ? 'true' : 'false'));

            // Update sale details
            $sale->cus_auto_id = $data['customer'];
            $sale->account_debit_id = $data['account_debit_id'];
            $sale->account_credit_id = $data['account_credit_id'];
            $sale->sr_invoice_no = $data['invoice_no'];
            $sale->sr_invoice_description = $data['invoice_desc'] ?? '';
            $sale->sr_tnx_id = 1; // Assuming transaction ID is constant for now
            $sale->sr_issue_date = $data['issue_date'] ?? now();
            $sale->sr_payment_terms = $data['payment_term'];
            $sale->sr_due_date = $data['due_date'] ?? now();
            $sale->sr_supply_date = $data['supply_date'] ?? now();
            $sale->notes = $data['notes'];
            $sale->sr_total_amount = $data['total_amount'];
            $sale->sr_vat_amount = $data['vat_amount'];
            $sale->sr_grand_total_amount = $data['total'];
            $sale->is_draft = $is_draft;
            $sale->retention_amount = $data['retention'] ?? 0;
            $sale->project_id = $data['project_id'];
            $sale->branch_office_id = $branch_id;
            $sale->updated_by_id = auth()->user()->id;

            $sale->save();

            // Update or create items (Sales Detail)
            foreach ($data['items'] as $item) {
                $productName = $item['product_data']['name'] ?? $item['newProduct'] ?? '';
                $productId = $item['srd_product_id'] ?? null;

                // Log::info("Product ID =". $productId);

                if (isset($item['srd_id'])) {
                    $sales_details = ChartofaccSalesRecordDetail::find($item['srd_id']);
                    if ($sales_details) {
                        // Update sales detail if found
                        $sales_details->srd_qty = $item['qty'] ?? $sales_details->srd_qty;
                        $sales_details->srd_unit_price = $item['unit_price'] ?? $sales_details->srd_unit_price;
                        $sales_details->srd_discount = $item['discount'] ?? $sales_details->srd_discount;
                        $sales_details->srd_vat_percent = $item['vat'] ?? $sales_details->srd_vat_percent;
                        $sales_details->srd_vat_value = $item['vat_amount'] ?? $sales_details->srd_vat_value;
                        $sales_details->srd_total_amount = $item['with_vat'] ?? $sales_details->srd_total_amount;
                        $sales_details->product_name = $item['newProduct'] ?? $sales_details->product_name;
                        $sales_details->srd_product_id = $item['srd_product_id'] ?? $sales_details->srd_product_id;
                        $sales_details->save();
                    }
                } else {
                    // Create new sales detail record
                    ChartofaccSalesRecordDetail::create([
                        'sr_auto_id' => $sale->sr_auto_id,
                        'srd_description' => '',
                        'srd_qty' => $item['qty'] ?? 1,
                        'srd_unit_price' => $item['unit_price'] ?? 0,
                        'srd_discount' => $item['discount'] ?? 0,
                        'srd_vat_percent' => $item['vat'] ?? 0,
                        'srd_vat_value' => $item['vat_amount'] ?? 0,
                        'srd_total_amount' => $item['with_vat'] ?? 0,
                        'srd_inclusive' => 1,
                        'srd_product_id' => $productId,
                        'product_name' => $productName,
                    ]);
                }
            }

            // Handle attachments if provided
            if ($attachments) {
                $directory = public_path('sales/' . $sale->sr_auto_id);
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                $attach = [];
                foreach ($attachments as $attachment) {
                    $attachment->move($directory, $attachment->getClientOriginalName());
                    $attach[] = '/sales/' . $sale->sr_auto_id . '/' . $attachment->getClientOriginalName();
                }
                $sale->attachments = $attach;
                $sale->save();
            }

            // Create Account Transaction if not a draft
            if ($is_draft == 0) {
                $saleDateTime = Carbon::parse($sale->sr_issue_date)->setTime(now()->hour, now()->minute, now()->second);

                // Create new account transaction
                $tr = new AccountTransaction();
                $tr->date = $saleDateTime;
                $tr->tr_no = $sale->sr_invoice_no;
                $tr->sales_id = $sale->sr_auto_id;
                $tr->approve_by = auth()->user()->id;
                $tr->total_amount = $sale->sr_grand_total_amount;
                $tr->general_particular = $sale->notes;
                $tr->save();

                // Credit transaction details
                AccountTransactionDetails::create([
                    'trd_id' => $tr->id,
                    'account_no' => $sale->account_credit_id,
                    'credit' => $sale->sr_grand_total_amount,
                    'particular' => "{$sale->sr_grand_total_amount} credited from {$sale->credit->account_id}",
                ]);

                // Debit transaction details
                AccountTransactionDetails::create([
                    'trd_id' => $tr->id,
                    'account_no' => $sale->account_debit_id,
                    'debit' => $sale->sr_grand_total_amount,
                    'particular' => "{$sale->sr_grand_total_amount} debited from {$sale->debit->account_id}",
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Load related data after update
            $sale->load(['customer', 'items.product', 'debit', 'credit']);

            return $sale;

        } catch (\Exception $e) {
            // Rollback transaction if something goes wrong
            DB::rollBack();
            Log::error("Error in updating sale: {$e->getMessage()}", ['exception' => $e]);
            return null;
        }
    }
    public function storeSalesPaymentReceivedInformation($form_data){
       // try {

            // Start database transaction to ensure atomicity
            DB::beginTransaction();
            // Log::error("Sale ID: $id");

            // Find the sale record
            $sale_record = ChartofaccSalesRecord::where('sr_auto_id',$form_data['sr_auto_id'])->first();
            if (!$sale_record) {
                Log::error("Sale record not found with ID: $sale_record");
                return null;
            }

            // Log::info("From Service, Draft =" . ($is_draft ? 'true' : 'false'));

            // Update sale details
            $sale_record->cus_auto_id = $form_data['cus_auto_id'];
           // $sale_record->account_debit_id = $data['account_debit_id'];
           // $sale_record->account_credit_id = $data['account_credit_id'];
          //  $sale_record->invoice_no = $data['invoice_no'];
            $sale_record->paid_date = $form_data['paid_date'];
           // $sale_record->sr_tnx_id = 1; // Assuming transaction ID is constant for now
          //  $sale_record->remarks = $data['remarks'] ?? "";
            $sale_record->paid_amount = $form_data['paid_amount'];
            $sale_record->paid_type = $form_data['paid_amount'] == $sale_record->sr_grand_total_amount ? 10:5;
            $login_id =  auth()->user()->id;

            ChartofaccSalesRecord::where('sr_auto_id',$form_data['sr_auto_id'])->update([
               // 'paid_amount'=> $form_data['paid_amount'],
                'paid_date'=> $form_data['paid_date'],
                'paid_type'=> $form_data['paid_amount'] == $sale_record->sr_grand_total_amount ? 10:5,
                'updated_by_id'=> $login_id,
               // 'paid_amount'=>$form_data['paid_date'],
            ]);
            // Handle attachments if provided
            // if ($attachments) {
            //     $directory = public_path('sales/' . $sale->sr_auto_id);
            //     if (!file_exists($directory)) {
            //         mkdir($directory, 0755, true);
            //     }
            //     $attach = [];
            //     foreach ($attachments as $attachment) {
            //         $attachment->move($directory, $attachment->getClientOriginalName());
            //         $attach[] = '/sales/' . $sale->sr_auto_id . '/' . $attachment->getClientOriginalName();
            //     }
            //     $sale->attachments = $attach;
            //     $sale->save();
            // }

            // Create Account Transaction if not a draft

                // Create new account transaction
                $tr = new AccountTransaction();
                $tr->date = Carbon::parse($form_data['paid_date'])->setTime(now()->hour, now()->minute, now()->second);
                $tr->tr_no = $sale_record->sr_invoice_no;
                $tr->sales_id = $sale_record->sr_auto_id;
                $tr->approve_by =$login_id;
                $tr->total_amount = $form_data['paid_amount'];
                $tr->general_particular = $form_data['remarks'];
                $tr->save();

                // Credit transaction details
                AccountTransactionDetails::create([
                    'trd_id' => $tr->id,
                    'account_no' => $form_data['account_credit_id'],
                    'credit' =>  $tr->total_amount,
                    'particular' => "{ $tr->total_amount} cr {$form_data['account_credit_id']}",
                ]);

                // Debit transaction details
                AccountTransactionDetails::create([
                    'trd_id' => $tr->id,
                    'account_no' => $form_data['account_debit_id'] ,
                    'debit' => $tr->total_amount,
                    'particular' => "{$tr->total_amount} dr  {$form_data['account_debit_id']}",
                ]);

                $cus_tran = new CustomerTransaction();
                $cus_tran->customer_id = $form_data['cus_auto_id'];
                $cus_tran->transaction_id = $tr->id;
                $cus_tran->transaction_date = $form_data['paid_date'];
                $cus_tran->transaction_type = 'payment Received';
                $cus_tran->invoice_no = $sale_record->sr_invoice_no;
                $cus_tran->debit = 0;
                $cus_tran->credit = $form_data['paid_amount'];
                $cus_tran->notes = $form_data['remarks'] ;
                $cus_tran->created_by = $login_id;
                $cus_tran->branch_office_id = 1;
                $cus_tran->ct_status = 1;
                $cus_tran->save();

                $drcr = CustomerTransaction::select(
                    DB::raw("SUM(debit) as dr_amount"),
                    DB::raw("SUM(credit) as cr_amount"),
                )->where('customer_id', $form_data['cus_auto_id'])->first();

                CustomerLedger::where('customer_id', $form_data['cus_auto_id'])->update([
                    'current_balance' => $drcr->dr_amount - $drcr->cr_amount,
                    'updated_by' =>  $login_id,
                ]);




            // Commit the transaction
            DB::commit();
            return true;

        // } catch (\Exception $e) {
        //     // Rollback transaction if something goes wrong
        //     DB::rollBack();
        //     Log::error("Error in updating sale: {$e->getMessage()}", ['exception' => $e]);
        //     return false;
        // }
    }


    public function searchSalesPaymentReceivedRecords($customer_id,$from_date,$to_date){

        if($customer_id == null){
            return  CustomerTransaction:: where('customer_transactions.debit', 0) // payment made credit should be greater than 0
                ->whereBetween('customer_transactions.transaction_date', [$from_date, $to_date])
                ->leftJoin('users', 'customer_transactions.created_by', '=', 'users.id')
                ->leftjoin('customer_subsidiary_ledger', 'customer_transactions.customer_id', '=', 'customer_subsidiary_ledger.customer_id')
                ->select('customer_transactions.*', 'users.name as created_by_name','customer_subsidiary_ledger.customer_name')
                ->get();
        }else{
            return  CustomerTransaction::where('customer_transactions.customer_id', $customer_id)
                ->whereBetween('customer_transactions.transaction_date', [$from_date, $to_date])
                ->leftJoin('users', 'customer_transactions.created_by', '=', 'users.id')
                ->where('customer_transactions.debit', 0) // payment made credit should be greater than 0
                ->leftjoin('customer_subsidiary_ledger', 'customer_transactions.customer_id', '=', 'customer_subsidiary_ledger.customer_id')
                ->select('customer_transactions.*', 'users.name as created_by_name','customer_subsidiary_ledger.customer_name')
                ->get();
        }




    // protected $table = 'account_transaction_details';

    // protected $fillable = [
    //     'trd_id',
    //     'account_no',
    //     'debit',
    //     'credit',
    //     'particular'
    // ];

    }

     /*
     ==========================================================================
     =================== Customer Subsidiary Leadger Transaction Module =======
     ==========================================================================
    */






     



}
