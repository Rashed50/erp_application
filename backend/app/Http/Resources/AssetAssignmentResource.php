<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetAssignmentResource extends JsonResource
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
            'asset_id' => $this->asset_id,
            'asset_tag' => $this->whenLoaded('asset', fn () => $this->asset?->asset_tag),
            'employee_id' => $this->employee_id,
            'employee_code' => $this->whenLoaded('employee', fn () => $this->employee?->employee_code),
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee?->name),
            'warehouse_id' => $this->warehouse_id,
            'warehouse_name' => $this->whenLoaded('warehouse', fn () => $this->warehouse?->name),
            'work_order_id' => $this->work_order_id,
            'work_order_no' => $this->whenLoaded('workOrder', fn () => $this->workOrder?->work_order_no),
            'assigned_date' => $this->assigned_date?->toDateString(),
            'expected_return_date' => $this->expected_return_date?->toDateString(),
            'returned_date' => $this->returned_date?->toDateString(),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'assigned_by' => $this->assigned_by,
            'assigned_by_name' => $this->whenLoaded('assigner', fn () => $this->assigner?->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
