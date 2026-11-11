<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetAssignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_id',
        'employee_auto_id',
        'project_id',
        'warehouse_id',
        'assigned_date',
        'expected_return_date',
        'returned_date',
        'assigned_by',
        'status',
        'remarks'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'expected_return_date' => 'date',
        'returned_date' => 'date',
    ];

    public function asset(): BelongsTo { return $this->belongsTo(InventoryAsset::class, 'asset_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'employee_auto_id', 'emp_auto_id'); }
    public function project(): BelongsTo { return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id'); }
    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function assigner(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'assigned_by', 'emp_auto_id'); }

}
