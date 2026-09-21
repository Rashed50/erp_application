<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeExpenseTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'income_expense_account_id' => $this->income_expense_account_id,
            'account_name' => $this->whenLoaded('account', fn () => $this->account->name),
            'payment_account_id' => $this->payment_account_id,
            'payment_account_name' => $this->whenLoaded('paymentAccount', fn () => $this->paymentAccount->name),
            'amount' => (float) $this->amount,
            'transaction_date' => $this->transaction_date?->toDateString(),
            'reference_no' => $this->reference_no,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
