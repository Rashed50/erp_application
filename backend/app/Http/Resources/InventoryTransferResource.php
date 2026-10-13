<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransferResource extends JsonResource
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
            'transfer_no' => $this->transfer_no,
            'from_warehouse_id' => $this->from_warehouse_id,
            'from_warehouse_name' => $this->whenLoaded('fromWarehouse', fn () => $this->fromWarehouse?->name),
            'to_warehouse_id' => $this->to_warehouse_id,
            'to_warehouse_name' => $this->whenLoaded('toWarehouse', fn () => $this->toWarehouse?->name),
            'transfer_date' => $this->transfer_date?->toDateString(),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'requested_by' => $this->requested_by,
            'requested_by_name' => $this->whenLoaded('requester', fn () => $this->requester?->name),
            'approved_by' => $this->approved_by,
            'approved_by_name' => $this->whenLoaded('approver', fn () => $this->approver?->name),
            'dispatched_by' => $this->dispatched_by,
            'dispatched_by_name' => $this->whenLoaded('dispatcher', fn () => $this->dispatcher?->name),
            'received_by' => $this->received_by,
            'received_by_name' => $this->whenLoaded('receiver', fn () => $this->receiver?->name),
            'items' => InventoryTransferItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
