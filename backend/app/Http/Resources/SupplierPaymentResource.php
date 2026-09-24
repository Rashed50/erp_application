<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SupplierPaymentResource extends JsonResource
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
            'supplier_id' => $this->supplier_id,
            'supplier_name' => $this->whenLoaded('supplier', fn () => $this->supplier?->name),
            'purchase_id' => $this->purchase_id,
            'purchase_invoice_number' => $this->whenLoaded('purchase', fn () => $this->purchase?->invoice_number),
            'payment_account_id' => $this->payment_account_id,
            'payment_account_name' => $this->whenLoaded('paymentAccount', fn () => $this->paymentAccount?->name),
            'payment_account_number' => $this->whenLoaded('paymentAccount', fn () => $this->paymentAccount?->account_number),
            'invoice_no' => $this->invoice_no,
            'payment_date' => $this->payment_date?->toDateString(),
            'bill_amount' => (float) $this->bill_amount,
            'bank_charge' => (float) $this->bank_charge,
            'total_amount' => (float) $this->total_amount,
            'remarks' => $this->remarks,
            'attachment_url' => $this->attachment ? Storage::disk('public')->url($this->attachment) : null,
            'created_by' => $this->created_by,
            'created_by_name' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
