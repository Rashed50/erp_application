<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PARTIALLY_RECEIVED = 'partially_received';

    public const STATUS_RECEIVED = 'received';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var array<int, string>
     */
    public const STATUSES = [
        self::STATUS_SUBMITTED,
        self::STATUS_APPROVED,
        self::STATUS_PARTIALLY_RECEIVED,
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED,
    ];

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'material_request_id' => 'integer',
            'purchase_date' => 'date',
            'expected_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * The lines' sums: from withLineTotals() when the query added them, else
     * from the loaded or queried lines.
     *
     * @return array{subtotal: float, discount: float, tax: float}
     */
    public function lineSums(): array
    {
        if (array_key_exists('line_subtotal', $this->attributes)) {
            return [
                'subtotal' => (float) $this->attributes['line_subtotal'],
                'discount' => (float) $this->attributes['line_discount'],
                'tax' => (float) $this->attributes['line_tax'],
            ];
        }

        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get(['quantity', 'unit_price', 'discount', 'tax']);

        return [
            'subtotal' => (float) $items->sum(fn (PurchaseOrderItem $item) => round((float) $item->quantity * (float) $item->unit_price, 2)),
            'discount' => (float) $items->sum('discount'),
            'tax' => (float) $items->sum('tax'),
        ];
    }

    public function scopeWithLineTotals(Builder $query): Builder
    {
        $sum = fn (string $expression) => "(select coalesce(sum({$expression}), 0) from purchase_order_items where purchase_order_items.purchase_order_id = purchase_orders.id)";

        if (is_null($query->getQuery()->columns)) {
            $query->select('purchase_orders.*');
        }

        return $query->selectRaw($sum('round(quantity * unit_price, 2)').' as line_subtotal, '.$sum('discount').' as line_discount, '.$sum('tax').' as line_tax');
    }

    protected function subtotal(): Attribute
    {
        return Attribute::get(fn () => round($this->lineSums()['subtotal'], 2));
    }

    protected function discount(): Attribute
    {
        return Attribute::get(fn () => round($this->lineSums()['discount'], 2));
    }

    protected function tax(): Attribute
    {
        return Attribute::get(fn () => round($this->lineSums()['tax'], 2));
    }

    protected function grandTotal(): Attribute
    {
        return Attribute::get(function () {
            $sums = $this->lineSums();

            return round($sums['subtotal'] - $sums['discount'] + $sums['tax'], 2);
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function isReceivable(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_RECEIVED], true);
    }
}
