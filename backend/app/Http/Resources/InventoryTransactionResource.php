<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
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
            'transaction_no' => $this->transaction_no,
            'transaction_type' => $this->transaction_type,
            'item_id' => $this->item_id,
            'item_name' => $this->whenLoaded('item', fn () => $this->item?->item_name),
            'item_code' => $this->whenLoaded('item', fn () => $this->item?->item_code),
            'serial_id' => $this->serial_id,
            'serial_number' => $this->whenLoaded('serial', fn () => $this->serial?->serial_number),
            'quantity' => (float) $this->quantity,
            'from_warehouse_id' => $this->from_warehouse_id,
            'from_warehouse_name' => $this->whenLoaded('fromWarehouse', fn () => $this->fromWarehouse?->name),
            'to_warehouse_id' => $this->to_warehouse_id,
            'to_warehouse_name' => $this->whenLoaded('toWarehouse', fn () => $this->toWarehouse?->name),
            'employee_id' => $this->employee_id,
            'employee_code' => $this->whenLoaded('employee', fn () => $this->employee?->employee_code),
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee?->name),
            'reference_type' => $this->reference_type ? class_basename($this->reference_type) : null,
            'reference_id' => $this->reference_id,
            'transaction_date' => $this->transaction_date,
            'performed_by' => $this->performed_by,
            'performed_by_name' => $this->whenLoaded('performer', fn () => $this->performer?->name),
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
        ];
    }
}
