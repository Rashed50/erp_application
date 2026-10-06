<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            'item_status' => 'boolean',
        ];
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
