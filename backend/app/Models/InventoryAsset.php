<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Asset register entry: one per serial of an asset item, created when the
 * unit is received against a purchase order.
 */
class InventoryAsset extends Model
{
    public const STATUS_IN_STOCK = 'in_stock';

    public const STATUS_IN_TRANSIT = 'in_transit';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_DAMAGED = 'damaged';

    public const STATUS_DISPOSED = 'disposed';

    /**
     * @var array<int, string>
     */
    public const STATUSES = [
        self::STATUS_IN_STOCK,
        self::STATUS_IN_TRANSIT,
        self::STATUS_ASSIGNED,
        self::STATUS_DAMAGED,
        self::STATUS_DISPOSED,
    ];

    public const CONDITION_BEYOND_REPAIR = 'beyond_repair';

    /**
     * @var array<int, string>
     */
    public const CONDITIONS = ['new', 'good', 'fair', 'damaged', self::CONDITION_BEYOND_REPAIR];

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'serial_id' => 'integer',
            'current_warehouse_id' => 'integer',
            'current_employee_id' => 'integer',
            'purchase_cost' => 'decimal:2',
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(InventoryItemSerial::class, 'serial_id');
    }

    public function currentWarehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'current_warehouse_id');
    }

    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(AssetReturn::class, 'asset_id');
    }

    /**
     * The status a returned unit takes on, given the condition it came back in.
     */
    public static function statusForCondition(string $condition): string
    {
        return $condition === self::CONDITION_BEYOND_REPAIR ? self::STATUS_DAMAGED : self::STATUS_IN_STOCK;
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('asset_tag', 'like', "%{$search}%")
                ->orWhereHas('serial', fn (Builder $query) => $query->where('serial_number', 'like', "%{$search}%"))
                ->orWhereHas('item', fn (Builder $query) => $query->where('item_name', 'like', "%{$search}%"));
        });
    }
}
