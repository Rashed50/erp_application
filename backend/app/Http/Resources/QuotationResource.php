<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
            'customer_name' => $this->whenLoaded('customer', fn() => $this->customer->name),
            'invoice_number' => $this->invoice_number,
            'description' => $this->description,
            'issue_date' => $this->issue_date?->toDateString(),
            'total_amount' => (float) $this->total_amount,
            'discount_amount' => (float) $this->discount_amount,
            'vat_amount' => (float) $this->vat_amount,
            'profit_percent' => (float) $this->profit_percent,
            'net_total' => (float) $this->net_total,
            'notes' => $this->notes,
            'file_path' => $this->file_path,
            'items' => QuotationItemResource::collection($this->whenLoaded('items')),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
