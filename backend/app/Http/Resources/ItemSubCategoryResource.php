<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemSubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'iscatg_id' => $this->iscatg_id,
            'icatg_id' => $this->icatg_id,
            'icatg_name' => $this->whenLoaded('category', fn () => $this->category?->icatg_name),
            'iscatg_name' => $this->iscatg_name,
            'iscatg_code' => $this->iscatg_code,
            'status' => $this->status,
            'items_count' => $this->whenCounted('items'),
            'create_by_id' => $this->create_by_id,
            'update_by_id' => $this->update_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
