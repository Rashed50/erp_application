<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryAssetResource extends JsonResource
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
            'asset_tag' => $this->asset_tag,
            'item_id' => $this->serial?->item_id,
            'item_name' => $this->whenLoaded('item', fn () => $this->item?->item_name),
            'item_code' => $this->whenLoaded('item', fn () => $this->item?->item_code),
            'serial_id' => $this->serial_id,
            'serial_number' => $this->serial?->serial_number,
            'purchase_cost' => (float) $this->purchase_cost,
            'purchase_date' => $this->serial?->purchase_date?->toDateString(),
            'warranty_expiry' => $this->serial?->warranty_expiry?->toDateString(),
            'current_warehouse_id' => $this->serial?->current_warehouse_id,
            'current_warehouse_name' => $this->whenLoaded('currentWarehouse', fn () => $this->currentWarehouse?->name),
            'current_employee_id' => $this->serial?->current_employee_id,
            'current_employee_code' => $this->whenLoaded('currentEmployee', fn () => $this->currentEmployee?->employee_code),
            'current_employee_name' => $this->whenLoaded('currentEmployee', fn () => $this->currentEmployee?->name),
            'status' => $this->serial?->status,
            'condition' => $this->serial?->condition,
            'assignments' => AssetAssignmentResource::collection($this->whenLoaded('assignments')),
            'returns' => AssetReturnResource::collection($this->whenLoaded('returns')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
