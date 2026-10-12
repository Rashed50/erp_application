<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * One physical unit of a serial-tracked item. Shares its status / condition
 * vocabulary with InventoryAsset.
 */
class InventoryItemSerial extends Model
{
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'current_warehouse_id' => 'integer',
            'current_employee_id' => 'integer',
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }

    public function purchaseReceiptItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceiptItem::class);
    }

    public function currentWarehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'current_warehouse_id');
    }

    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id');
    }

    public function asset(): HasOne
    {
        return $this->hasOne(InventoryAsset::class, 'serial_id');
    }

    /**
     * Whether the unit sits in the given store and can be moved out of it.
     */
    public function isAvailableIn(int $warehouseId): bool
    {
        return $this->current_warehouse_id === $warehouseId
            && in_array($this->status, [InventoryAsset::STATUS_IN_STOCK, InventoryAsset::STATUS_DAMAGED], true);
    }
}
