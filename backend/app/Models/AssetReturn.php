<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class AssetReturn extends Model
{
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'return_date' => 'date',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AssetAssignment::class, 'asset_assignment_id');
    }

    /**
     * The assignment's asset.
     */
    public function asset(): HasOneThrough
    {
        return $this->hasOneThrough(InventoryAsset::class, AssetAssignment::class, 'id', 'id', 'asset_assignment_id', 'asset_id');
    }

    /**
     * The employee the asset was assigned to.
     */
    public function employee(): HasOneThrough
    {
        return $this->hasOneThrough(Employee::class, AssetAssignment::class, 'id', 'id', 'asset_assignment_id', 'employee_id');
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'destination_warehouse_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
