<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
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
            'customer_name' => $this->whenLoaded('customer', fn () => $this->customer?->name),
            'work_title' => $this->work_title,
            'work_order_no' => $this->work_order_no,
            'issue_date' => $this->issue_date?->toDateString(),
            'total_amount' => (float) $this->total_amount,
            'retention_percent' => (float) $this->retention_percent,
            'retention_amount' => $this->retention_amount,
            'paid_amount' => $this->whenHas('paid_amount', fn () => (float) $this->paid_amount),
            'outstanding_amount' => $this->whenHas('paid_amount', fn () => round((float) $this->total_amount - (float) $this->paid_amount, 2)),
            'payments_count' => $this->whenHas('payments_count'),
            'deliver_date' => $this->deliver_date?->toDateString(),
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
