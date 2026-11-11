<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryStockResource extends JsonResource
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
            'warehouse_id' => $this->warehouse_id,
            'warehouse_name' => $this->whenLoaded('warehouse', fn () => $this->warehouse?->name),
            'item_id' => $this->item_id,
            'item_name' => $this->whenLoaded('item', fn () => $this->item?->item_name),
            'item_code' => $this->whenLoaded('item', fn () => $this->item?->item_code),
            'unit_name' => $this->whenLoaded('item', fn () => $this->item?->unit?->short_name),
            'minimum_stock' => $this->whenLoaded('item', fn () => (float) $this->item?->minimum_stock),
            'quantity' => (float) $this->quantity,
            'reserved_quantity' => (float) $this->reserved_quantity,
            'available_quantity' => $this->resource->availableQuantity(),
            'updated_at' => $this->updated_at,
        ];
    }
}
