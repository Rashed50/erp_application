<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartOfAccountResource extends JsonResource
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
            'name' => $this->name,
            'account_number' => $this->account_number,
            'account_type_id' => $this->account_type_id,
            'account_type' => $this->whenLoaded('accountType', fn () => $this->accountType->name),
            'parent_id' => $this->parent_id,
            'parent_name' => $this->whenLoaded('parent', fn () => $this->parent?->name),
            'sibling_level' => $this->sibling_level,
            'balance' => (float) $this->balance,
            'opening_date' => $this->opening_date?->toDateString(),
            'active_status' => $this->active_status,
            'is_transaction' => $this->is_transaction,
            'is_predefined' => $this->is_predefined,
            'is_closed' => $this->is_closed,
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
