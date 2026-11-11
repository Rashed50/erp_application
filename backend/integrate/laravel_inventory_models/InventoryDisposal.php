<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryDisposal extends Model
{
    use HasFactory;
    protected $fillable = [
        'disposal_no',
        'warehouse_id',
        'disposal_date',
        'reason',
        'approved_by',
        'disposed_by',
        'status',
        'remarks'
    ];

    protected $casts = [
        'disposal_date' => 'date',
    ];

    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'approved_by', 'emp_auto_id'); }
    public function disposer(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'disposed_by', 'emp_auto_id'); }
    public function items(): HasMany { return $this->hasMany(InventoryDisposalItem::class, 'disposal_id'); }

}
