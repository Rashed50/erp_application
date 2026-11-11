<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeInventoryResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'employee_code' => $this->whenLoaded('employee', fn () => $this->employee?->employee_code),
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee?->name),
            'item_id' => $this->item_id,
            'item_name' => $this->whenLoaded('item', fn () => $this->item?->item_name),
            'item_code' => $this->whenLoaded('item', fn () => $this->item?->item_code),
            'serial_id' => $this->serial_id,
            'serial_number' => $this->whenLoaded('serial', fn () => $this->serial?->serial_number),
            'warehouse_id' => $this->warehouse_id,
            'warehouse_name' => $this->whenLoaded('warehouse', fn () => $this->warehouse?->name),
            'work_order_id' => $this->work_order_id,
            'work_order_no' => $this->whenLoaded('workOrder', fn () => $this->workOrder?->work_order_no),
            'quantity' => (float) $this->quantity,
            'returned_qty' => (float) $this->returned_qty,
            'outstanding_qty' => $this->outstandingQuantity(),
            'issue_date' => $this->issue_date?->toDateString(),
            'expected_return_date' => $this->expected_return_date?->toDateString(),
            'status' => $this->status,
            'condition' => $this->condition,
            'remarks' => $this->remarks,
            'issued_by' => $this->issued_by,
            'issued_by_name' => $this->whenLoaded('issuer', fn () => $this->issuer?->name),
            'returns' => EmployeeInventoryReturnResource::collection($this->whenLoaded('returns')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
