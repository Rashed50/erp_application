<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemSubCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'iscatg_id';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'icatg_id' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'icatg_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemName::class, 'iscatg_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_by_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_by_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('iscatg_name', 'like', "%{$search}%")
                ->orWhere('iscatg_code', 'like', "%{$search}%");
        });
    }
}
