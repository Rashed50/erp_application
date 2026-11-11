<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssetAssignment extends Model
{
    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_RETURNED = 'returned';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'asset_id' => 'integer',
            'employee_id' => 'integer',
            'warehouse_id' => 'integer',
            'work_order_id' => 'integer',
            'assigned_date' => 'date',
            'expected_return_date' => 'date',
            'returned_date' => 'date',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(InventoryAsset::class, 'asset_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assetReturn(): HasOne
    {
        return $this->hasOne(AssetReturn::class);
    }
}
