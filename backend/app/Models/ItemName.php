<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemName extends Model
{
    use HasFactory;

    public const TYPE_ASSET = 1;

    public const TYPE_NON_ASSET = 2;

    /**
     * @var array<int, string>
     */
    public const TYPES = [
        self::TYPE_ASSET => 'Asset',
        self::TYPE_NON_ASSET => 'Non-Asset',
    ];

    public const TRACK_QUANTITY = 'quantity';

    public const TRACK_SERIAL = 'serial';

    /**
     * @var array<int, string>
     */
    public const TRACK_TYPES = [self::TRACK_QUANTITY, self::TRACK_SERIAL];

    protected $primaryKey = 'item_id';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'icatg_id' => 'integer',
            'iscatg_id' => 'integer',
            'itype_id' => 'integer',
            'unit_id' => 'integer',
            'minimum_stock' => 'decimal:3',
            'item_status' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class, 'unit_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class, 'item_id');
    }

    public function serials(): HasMany
    {
        return $this->hasMany(InventoryItemSerial::class, 'item_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(InventoryAsset::class, 'item_id');
    }

    public function isAsset(): bool
    {
        return $this->itype_id === self::TYPE_ASSET;
    }

    public function isSerialTracked(): bool
    {
        return $this->track_type === self::TRACK_SERIAL;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'icatg_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(ItemSubCategory::class, 'iscatg_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_by_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_by_id');
    }

    public function getTypeNameAttribute(): ?string
    {
        return self::TYPES[$this->itype_id] ?? null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('item_status', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('item_name', 'like', "%{$search}%")
                ->orWhere('item_title', 'like', "%{$search}%")
                ->orWhere('item_code', 'like', "%{$search}%");
        });
    }
}
