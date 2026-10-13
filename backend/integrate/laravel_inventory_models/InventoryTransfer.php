<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'transfer_no',
        'from_warehouse_id',
        'to_warehouse_id',
        'transfer_date',
        'requested_by',
        'approved_by',
        'received_by',
        'status',
        'remarks'
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fromWarehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'from_warehouse_id'); }
    public function toWarehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'to_warehouse_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'requested_by', 'emp_auto_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'approved_by', 'emp_auto_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'received_by', 'emp_auto_id'); }
    public function items(): HasMany { return $this->hasMany(InventoryTransferItem::class, 'inventory_transfer_id'); }

}
