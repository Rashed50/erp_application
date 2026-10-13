<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_no',
        'item_id',
        'serial_id',
        'transaction_type',
        'quantity',
        'from_location_id',
        'to_location_id',
        'reference_type',
        'reference_id',
        'transaction_date',
        'performed_by',
        'remarks'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'transaction_date' => 'datetime',
    ];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function serial(): BelongsTo { return $this->belongsTo(InventoryItemSerial::class, 'serial_id'); }
    public function fromLocation(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'from_location_id'); }
    public function toLocation(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'to_location_id'); }
    public function performedBy(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'performed_by', 'emp_auto_id'); }
    public function reference(): MorphTo { return $this->morphTo(); }

}
