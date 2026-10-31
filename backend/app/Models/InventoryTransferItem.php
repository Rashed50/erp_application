<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransferItem extends Model
{
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'requested_qty' => 'decimal:3',
        ];
    }

    /**
     * A transfer moves every requested unit at once: all of it is sent on
     * dispatch and received on receipt.
     */
    protected function sentQty(): Attribute
    {
        return Attribute::get(fn () => in_array($this->transfer?->status, [InventoryTransfer::STATUS_IN_TRANSIT, InventoryTransfer::STATUS_RECEIVED], true)
            ? (float) $this->requested_qty
            : 0.0);
    }

    protected function receivedQty(): Attribute
    {
        return Attribute::get(fn () => $this->transfer?->status === InventoryTransfer::STATUS_RECEIVED ? (float) $this->requested_qty : 0.0);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }
}
