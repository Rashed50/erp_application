<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetReturnResource extends JsonResource
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
            'asset_assignment_id' => $this->asset_assignment_id,
            'asset_id' => $this->whenLoaded('assignment', fn () => $this->assignment?->asset_id),
            'asset_tag' => $this->whenLoaded('asset', fn () => $this->asset?->asset_tag),
            'employee_id' => $this->whenLoaded('assignment', fn () => $this->assignment?->employee_id),
            'employee_code' => $this->whenLoaded('employee', fn () => $this->employee?->employee_code),
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee?->name),
            'destination_warehouse_id' => $this->destination_warehouse_id,
            'destination_warehouse_name' => $this->whenLoaded('destinationWarehouse', fn () => $this->destinationWarehouse?->name),
            'return_date' => $this->return_date?->toDateString(),
            'condition' => $this->condition,
            'return_reason' => $this->return_reason,
            'remarks' => $this->remarks,
            'received_by' => $this->received_by,
            'received_by_name' => $this->whenLoaded('receiver', fn () => $this->receiver?->name),
            'created_at' => $this->created_at,
        ];
    }
}
