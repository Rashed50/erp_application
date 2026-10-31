<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }

    public function receiptItems(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class);
    }

    /**
     * quantity x unit price, less the discount, plus the tax.
     */
    protected function total(): Attribute
    {
        return Attribute::get(fn () => round((float) $this->quantity * (float) $this->unit_price - (float) $this->discount + (float) $this->tax, 2));
    }

    /**
     * The sum of the receipt lines: from withReceivedQuantity() when the
     * query added it, else queried.
     */
    protected function receivedQty(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value ?? $this->receiptItems()->sum('quantity')), 3));
    }

    public function scopeWithReceivedQuantity(Builder $query): Builder
    {
        return $query->withSum('receiptItems as received_qty', 'quantity');
    }

    public function remainingQuantity(): float
    {
        return round((float) $this->quantity - $this->received_qty, 3);
    }
}
