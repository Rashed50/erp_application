<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmployeeInventoryReturn extends Model
{
    use HasFactory;
    protected $table = 'employee_inventory_returns';

    protected $fillable = [
        'employee_inventory_id',
        'employee_auto_id',
        'item_id',
        'serial_id',
        'quantity',
        'return_date',
        'return_condition',
        'return_reason',
        'received_by',
        'destination_warehouse_id',
        'remarks'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'return_date' => 'date',
    ];

    public function employeeInventory(): BelongsTo { return $this->belongsTo(EmployeeInventory::class, 'employee_inventory_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'employee_auto_id', 'emp_auto_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function serial(): BelongsTo { return $this->belongsTo(InventoryItemSerial::class, 'serial_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'received_by', 'emp_auto_id'); }
    public function destinationWarehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'destination_warehouse_id'); }

}
