<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'work_order_id' => $this->work_order_id,
            'work_order_no' => $this->whenLoaded('workOrder', fn () => $this->workOrder?->work_order_no),
            'employee_id' => $this->employee_id,
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee ? "{$this->employee->name} ({$this->employee->employee_code})" : null),
            'income_expense_account_id' => $this->income_expense_account_id,
            'account_name' => $this->whenLoaded('account', fn () => $this->account->name),
            'payment_account_id' => $this->payment_account_id,
            'payment_account_name' => $this->whenLoaded('paymentAccount', fn () => $this->paymentAccount->name),
            'amount' => (float) $this->amount,
            'transaction_date' => $this->transaction_date?->toDateString(),
            'reference_no' => $this->reference_no,
            'description' => $this->description,
            'attachment_url' => $this->attachment ? Storage::disk('public')->url($this->attachment) : null,
            'created_by' => $this->created_by,
            'created_by_name' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'approved_by' => $this->approved_by,
            'approved_by_name' => $this->whenLoaded('approver', fn () => $this->approver?->name),
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
