<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryWarehouseResource extends JsonResource
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
            'warehouse_code' => $this->warehouse_code,
            'name' => $this->name,
            'warehouse_type' => $this->warehouse_type,
            'warehouse_type_name' => $this->type_name,
            'parent_id' => $this->parent_id,
            'parent_name' => $this->whenLoaded('parent', fn () => $this->parent?->name),
            'work_order_id' => $this->work_order_id,
            'work_order_no' => $this->whenLoaded('workOrder', fn () => $this->workOrder?->work_order_no),
            'manager_employee_id' => $this->manager_employee_id,
            'manager_employee_code' => $this->whenLoaded('manager', fn () => $this->manager?->employee_code),
            'manager_employee_name' => $this->whenLoaded('manager', fn () => $this->manager?->name),
            'address' => $this->address,
            'status' => $this->status,
            'stocks_count' => $this->whenCounted('stocks'),
            'created_by_name' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
