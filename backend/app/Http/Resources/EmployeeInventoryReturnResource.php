<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeInventoryReturnResource extends JsonResource
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
            'employee_inventory_id' => $this->employee_inventory_id,
            'employee_id' => $this->whenLoaded('employeeInventory', fn () => $this->employeeInventory?->employee_id),
            'employee_code' => $this->whenLoaded('employee', fn () => $this->employee?->employee_code),
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee?->name),
            'item_id' => $this->whenLoaded('employeeInventory', fn () => $this->employeeInventory?->item_id),
            'item_name' => $this->whenLoaded('item', fn () => $this->item?->item_name),
            'item_code' => $this->whenLoaded('item', fn () => $this->item?->item_code),
            'serial_id' => $this->whenLoaded('employeeInventory', fn () => $this->employeeInventory?->serial_id),
            'destination_warehouse_id' => $this->destination_warehouse_id,
            'destination_warehouse_name' => $this->whenLoaded('destinationWarehouse', fn () => $this->destinationWarehouse?->name),
            'quantity' => (float) $this->quantity,
            'return_date' => $this->return_date?->toDateString(),
            'return_condition' => $this->return_condition,
            'return_reason' => $this->return_reason,
            'remarks' => $this->remarks,
            'received_by' => $this->received_by,
            'received_by_name' => $this->whenLoaded('receiver', fn () => $this->receiver?->name),
            'created_at' => $this->created_at,
        ];
    }
}
