<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class EmployeeInventoryReturn extends Model
{
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'return_date' => 'date',
        ];
    }

    public function employeeInventory(): BelongsTo
    {
        return $this->belongsTo(EmployeeInventory::class);
    }

    /**
     * The employee the item was issued to.
     */
    public function employee(): HasOneThrough
    {
        return $this->hasOneThrough(Employee::class, EmployeeInventory::class, 'id', 'id', 'employee_inventory_id', 'employee_id');
    }

    /**
     * The issued item.
     */
    public function item(): HasOneThrough
    {
        return $this->hasOneThrough(ItemName::class, EmployeeInventory::class, 'id', 'item_id', 'employee_inventory_id', 'item_id');
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
