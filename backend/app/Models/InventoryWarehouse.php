<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryWarehouse extends Model
{
    use HasFactory;

    public const TYPE_CENTER = 'center';

    public const TYPE_ZONAL = 'zonal';

    public const TYPE_PROJECT_STORE = 'project_store';

    /**
     * @var array<string, string>
     */
    public const TYPES = [
        self::TYPE_CENTER => 'Center Warehouse',
        self::TYPE_ZONAL => 'Zonal Warehouse',
        self::TYPE_PROJECT_STORE => 'Project Store',
    ];

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'work_order_id' => 'integer',
            'manager_employee_id' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class, 'warehouse_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(InventoryAsset::class, 'current_warehouse_id');
    }

    public function getTypeNameAttribute(): ?string
    {
        return self::TYPES[$this->warehouse_type] ?? null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('warehouse_code', 'like', "%{$search}%");
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
