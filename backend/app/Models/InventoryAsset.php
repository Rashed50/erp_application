<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Asset register entry: one per serial of an asset item, created when the
 * unit is received against a purchase order. Only what is particular to an
 * asset (tag and cost) is kept here; the item, store, custodian, status and
 * condition belong to the serial.
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
            'serial_id' => 'integer',
            'purchase_cost' => 'decimal:2',
        ];
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(InventoryItemSerial::class, 'serial_id');
    }

    /**
     * The serial's item.
     */
    public function item(): HasOneThrough
    {
        return $this->hasOneThrough(ItemName::class, InventoryItemSerial::class, 'id', 'item_id', 'serial_id', 'item_id');
    }

    /**
     * The store the serial is in.
     */
    public function currentWarehouse(): HasOneThrough
    {
        return $this->hasOneThrough(InventoryWarehouse::class, InventoryItemSerial::class, 'id', 'id', 'serial_id', 'current_warehouse_id');
    }

    /**
     * The employee holding the serial.
     */
    public function currentEmployee(): HasOneThrough
    {
        return $this->hasOneThrough(Employee::class, InventoryItemSerial::class, 'id', 'id', 'serial_id', 'current_employee_id');
    }

    /**
     * Assets whose serial matches the constraint (status, store, custodian...).
     */
    public function scopeWhereSerial(Builder $query, string $column, mixed $value): Builder
    {
        return $query->whereHas('serial', fn (Builder $query) => $query->where($column, $value));
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    /**
     * Returns of this asset, through its assignments.
     */
    public function returns(): HasManyThrough
    {
        return $this->hasManyThrough(AssetReturn::class, AssetAssignment::class, 'asset_id', 'asset_assignment_id');
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
