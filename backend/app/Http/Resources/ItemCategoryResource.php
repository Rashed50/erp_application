<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'icatg_id' => $this->icatg_id,
            'icatg_name' => $this->icatg_name,
            'icatg_code' => $this->icatg_code,
            'status' => $this->status,
            'sub_categories_count' => $this->whenCounted('subCategories'),
            'create_by_id' => $this->create_by_id,
            'update_by_id' => $this->update_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
