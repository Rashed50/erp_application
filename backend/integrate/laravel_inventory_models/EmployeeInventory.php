<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmployeeInventory extends Model
{
    use HasFactory;
    protected $table = 'employee_inventory';

    protected $fillable = [
        'employee_auto_id',
        'item_id',
        'serial_id',
        'project_id',
        'warehouse_id',
        'quantity',
        'issue_date',
        'issued_by',
        'status',
        'condition',
        'expected_return_date',
        'remarks'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'issue_date' => 'date',
        'expected_return_date' => 'date',
    ];

    public function employee(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'employee_auto_id', 'emp_auto_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function serial(): BelongsTo { return $this->belongsTo(InventoryItemSerial::class, 'serial_id'); }
    public function project(): BelongsTo { return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id'); }
    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function issuer(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'issued_by', 'emp_auto_id'); }
    public function returns(): HasMany { return $this->hasMany(EmployeeInventoryReturn::class, 'employee_inventory_id'); }

}
