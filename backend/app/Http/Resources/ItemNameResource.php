<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_id' => $this->item_id,
            'icatg_id' => $this->icatg_id,
            'icatg_name' => $this->whenLoaded('category', fn () => $this->category?->icatg_name),
            'iscatg_id' => $this->iscatg_id,
            'iscatg_name' => $this->whenLoaded('subCategory', fn () => $this->subCategory?->iscatg_name),
            'itype_id' => $this->itype_id,
            'itype_name' => $this->type_name,
            'item_name' => $this->item_name,
            'item_title' => $this->item_title,
            'item_code' => $this->item_code,
            'item_status' => $this->item_status,
            'create_by_id' => $this->create_by_id,
            'update_by_id' => $this->update_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
