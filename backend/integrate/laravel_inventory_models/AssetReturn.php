<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetReturn extends Model
{
    use HasFactory;
    protected $table = 'asset_returns';

    protected $fillable = [
        'asset_id',
        'employee_auto_id',
        'project_id',
        'return_date',
        'condition',
        'return_reason',
        'received_by',
        'destination_warehouse_id',
        'remarks'
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function asset(): BelongsTo { return $this->belongsTo(InventoryAsset::class, 'asset_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'employee_auto_id', 'emp_auto_id'); }
    public function project(): BelongsTo { return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'received_by', 'emp_auto_id'); }
    public function destinationWarehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'destination_warehouse_id'); }

}
