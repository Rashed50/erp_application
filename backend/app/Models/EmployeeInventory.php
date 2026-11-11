<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * The sum of the returns: from withReturnedQuantity() when the query
     * added it, else from the loaded or queried returns.
     */
    protected function returnedQty(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value
            ?? ($this->relationLoaded('returns') ? $this->returns->sum('quantity') : $this->returns()->sum('quantity'))), 3));
    }

    /**
     * Issued until the whole quantity is back.
     */
    protected function status(): Attribute
    {
        return Attribute::get(fn () => $this->outstandingQuantity() > 0 ? self::STATUS_ISSUED : self::STATUS_RETURNED);
    }

    public function scopeWithReturnedQuantity(Builder $query): Builder
    {
        return $query->withSum('returns as returned_qty', 'quantity');
    }

    public function scopeInStatus(Builder $query, string $status): Builder
    {
        $returned = '(select coalesce(sum(employee_inventory_returns.quantity), 0) from employee_inventory_returns where employee_inventory_returns.employee_inventory_id = employee_inventory.id)';

        return $query->whereRaw('employee_inventory.quantity '.($status === self::STATUS_RETURNED ? '<=' : '>').' '.$returned);
    }

    public function outstandingQuantity(): float
    {
        return round((float) $this->quantity - $this->returned_qty, 3);
    }
}
