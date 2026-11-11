<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDisposalItem extends Model
{
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'serial_id' => 'integer',
            'quantity' => 'decimal:3',
        ];
    }

    public function disposal(): BelongsTo
    {
        return $this->belongsTo(InventoryDisposal::class, 'disposal_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(InventoryItemSerial::class, 'serial_id');
    }
}
