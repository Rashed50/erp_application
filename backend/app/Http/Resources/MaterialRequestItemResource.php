<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialRequestItemResource extends JsonResource
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
            'material_request_id' => $this->material_request_id,
            'item_id' => $this->item_id,
            'item_name' => $this->whenLoaded('item', fn () => $this->item?->item_name),
            'item_code' => $this->whenLoaded('item', fn () => $this->item?->item_code),
            'requested_qty' => (float) $this->requested_qty,
            'approved_qty' => (float) $this->approved_qty,
            'purchased_qty' => (float) $this->purchased_qty,
            'remarks' => $this->remarks,
        ];
    }
}
