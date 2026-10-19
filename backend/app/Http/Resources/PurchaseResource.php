<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'supplier_name' => $this->whenLoaded('supplier', fn () => $this->supplier->name),
            'work_order_id' => $this->work_order_id,
            'work_order_no' => $this->whenLoaded('workOrder', fn () => $this->workOrder?->work_order_no),
            'purchased_by_id' => $this->purchased_by_id,
            'purchased_by_name' => $this->whenLoaded('purchasedBy', fn () => $this->purchasedBy ? "{$this->purchasedBy->name} ({$this->purchasedBy->employee_code})" : null),
            'purchase_type' => $this->purchase_type,
            'invoice_number' => $this->invoice_number,
            'description' => $this->description,
            'issue_date' => $this->issue_date?->toDateString(),
            'purchase_date' => $this->purchase_date?->toDateString(),
            'total_amount' => (float) $this->total_amount,
            'discount_amount' => (float) $this->discount_amount,
            'vat_amount' => (float) $this->vat_amount,
            'overall_discount' => (float) $this->overall_discount,
            'overall_vat' => (float) $this->overall_vat,
            'net_total' => (float) $this->net_total,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => (float) $this->due_amount,
            'notes' => $this->notes,
            'items' => PurchaseItemResource::collection($this->whenLoaded('items')),
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
