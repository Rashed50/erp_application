<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTransactionResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer_name' => $this->whenLoaded('customer', fn () => $this->customer->name),
            'work_order_id' => $this->work_order_id,
            'work_order_no' => $this->whenLoaded('workOrder', fn () => $this->workOrder?->work_order_no),
            'transaction_type' => $this->transaction_type,
            'invoice_no' => $this->invoice_no,
            'debit' => (float) $this->debit,
            'credit' => (float) $this->credit,
            'transaction_date' => $this->transaction_date?->toDateString(),
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
