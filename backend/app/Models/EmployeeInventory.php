<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A non-asset item (consumable or tool) handed to an employee from a store.
 */
class EmployeeInventory extends Model
{
    public const STATUS_ISSUED = 'issued';

    public const STATUS_RETURNED = 'returned';

    protected $table = 'employee_inventory';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'item_id' => 'integer',
            'serial_id' => 'integer',
            'warehouse_id' => 'integer',
            'work_order_id' => 'integer',
            'quantity' => 'decimal:3',
            'returned_qty' => 'decimal:3',
            'issue_date' => 'date',
            'expected_return_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(InventoryItemSerial::class, 'serial_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(EmployeeInventoryReturn::class);
    }

    public function outstandingQuantity(): float
    {
        return (float) $this->quantity - (float) $this->returned_qty;
    }
}
