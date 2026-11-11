<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequestItem extends Model
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
            'approved_qty' => 'decimal:3',
        ];
    }

    /**
     * What the request's purchase orders ordered of this item, in SQL.
     */
    private const ORDERED_SQL = '(select coalesce(sum(purchase_order_items.quantity), 0) from purchase_order_items join purchase_orders on purchase_orders.id = purchase_order_items.purchase_order_id where purchase_orders.material_request_id = material_request_items.material_request_id and purchase_order_items.item_id = material_request_items.item_id)';

    /**
     * The approved quantity covered by purchase orders raised against the
     * request (an item appears on one line of a request). From
     * withOrderedQuantity() when the query added it, else queried.
     */
    protected function purchasedQty(): Attribute
    {
        return Attribute::get(function () {
            $ordered = $this->attributes['ordered_qty'] ?? PurchaseOrderItem::query()
                ->where('item_id', $this->item_id)
                ->whereRelation('purchaseOrder', 'material_request_id', $this->material_request_id)
                ->sum('quantity');

            return round(min((float) $this->approved_qty, (float) $ordered), 3);
        });
    }

    public function scopeWithOrderedQuantity(Builder $query): Builder
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select('material_request_items.*');
        }

        return $query->selectRaw(self::ORDERED_SQL.' as ordered_qty');
    }

    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }
}
