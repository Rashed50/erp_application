<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryPaymentResource extends JsonResource
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
            'salary_history_id' => $this->salary_history_id,
            'payment_date' => $this->payment_date?->toDateString(),
            'amount' => (float) $this->amount,
            'payment_account_id' => $this->payment_account_id,
            'payment_account_name' => $this->whenLoaded('paymentAccount', fn () => $this->paymentAccount ? "{$this->paymentAccount->account_number} - {$this->paymentAccount->name}" : null),
            'remarks' => $this->remarks,
            'created_by_name' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at,
        ];
    }
}
