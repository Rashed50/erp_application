<?php

namespace Modules\Account\Services;

use Modules\Account\Models\ChartofaccSalesCustomer;
use Modules\Account\Models\CustomerTransaction;
use Modules\Account\Models\CustomerLedger;


class SalesCustomerService
{
    // get customer for Sale add or edit panel
    public function getCustomersForSales(?int $branch_id = null)
    {
         return CustomerLedger::query()->where(function ($query) use ($branch_id) {
            if ($branch_id) {
                $query->where('branch_office_id', $branch_id)
                    ->orWhereNull('branch_office_id');
            }
        })->get();
    }

    function create($data)
    {
        $customer = new ChartofaccSalesCustomer();
        $customer->cus_name = $data['name'];
        $customer->cus_email = $data['email'];
        $customer->cus_phone = $data['phone'];
        $customer->cus_tax_number = $data['tax'];
        $customer->cus_org_name = $data['cus_org_name'];
        $customer->cus_billing_address = $data['cus_billing_address'];
        $customer->cus_billing_city = $data['cus_billing_city'];
        $customer->cus_billing_state = $data['cus_billing_state'];
        $customer->cus_billing_country = $data['cus_billing_country'];
        $customer->cus_billing_zip = $data['cus_billing_zip'];
        $customer->branch_office_id = $data['branch_office_id'] ?? null;
        $customer->cus_status = 1;
        $customer->created_by_id = auth()->id();
        $customer->save();
        return $customer;
    }






/*
     ==========================================================================
     =================== Customer Subsidiary Leadger Transaction Module =======
     ==========================================================================
    */

     public function saveNewCustomerInformation(array $data){
        return CustomerLedger::create($data);
     }

    function update($id, $data, $user_branch_id)
    {
        $customer = CustomerLedger::find($id);
        if (!$customer) return null;
        abort_if($customer->branch_office_id && $customer->branch_office_id != auth()->user()->branch_office_id, 403);
        $customer->update($data);
        return $customer;
    }

    function delete($id)
    {
      // return $id;
        $customer = CustomerLedger::find($id);
        if (!$customer) return null;
        abort_if($customer->branch_office_id && $customer->branch_office_id != auth()->user()->branch_office_id, 403);
        $customer->active_status = 0;
        $customer->update($customer);
        return $customer;
    }


    public function saveCustomerNewTransaction(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customer_subsidiary_ledger,customer_id',
            'transaction_type' => 'required|string|max:100',
            'invoice_no' => 'nullable|string|max:100',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'branch_office_id' => 'nullable|integer|exists:branch_offices,id',
            'ct_status' => 'nullable|boolean',
        ]);

        // Ensure only debit OR credit is provided
        if (($validated['debit'] ?? 0) > 0 && ($validated['credit'] ?? 0) > 0) {
            return response()->json([
                'message' => 'Transaction cannot have both debit and credit amounts.'
            ], 422);
        }

        // Ensure at least one amount is provided
        if (($validated['debit'] ?? 0) == 0 && ($validated['credit'] ?? 0) == 0) {
            return response()->json([
                'message' => 'Transaction must have either debit or credit amount.'
            ], 422);
        }

        $transaction = CustomerTransaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully.',
            'data' => $transaction->load('customer')
        ], 201);
    }

    // public function update(Request $request, CustomerTransaction $transaction)
    // {
    //     if (!$transaction->can_edit) {
    //         return response()->json([
    //             'message' => 'This transaction cannot be edited.'
    //         ], 403);
    //     }

    //     $validated = $request->validate([
    //         'transaction_type' => 'sometimes|required|string|max:100',
    //         'invoice_no' => 'nullable|string|max:100',
    //         'debit' => 'nullable|numeric|min:0',
    //         'credit' => 'nullable|numeric|min:0',
    //         'transaction_date' => 'sometimes|required|date',
    //         'notes' => 'nullable|string',
    //         'ct_status' => 'nullable|boolean',
    //     ]);

    //     $transaction->update($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Transaction updated successfully.',
    //         'data' => $transaction
    //     ]);
    // }
}
