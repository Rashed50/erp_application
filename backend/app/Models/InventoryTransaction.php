<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Append-only stock movement ledger. Written only by InventoryStockService.
 */
class InventoryTransaction extends Model
{
    public const TYPE_PURCHASE = 'purchase';

    public const TYPE_TRANSFER_OUT = 'transfer_out';

    public const TYPE_TRANSFER_IN = 'transfer_in';

    public const TYPE_EMPLOYEE_ISSUE = 'employee_issue';

    public const TYPE_EMPLOYEE_RETURN = 'employee_return';

    public const TYPE_DISPOSAL = 'disposal';

    /**
     * @var array<int, string>
     */
    public const TYPES = [
        self::TYPE_PURCHASE,
        self::TYPE_TRANSFER_OUT,
        self::TYPE_TRANSFER_IN,
        self::TYPE_EMPLOYEE_ISSUE,
        self::TYPE_EMPLOYEE_RETURN,
        self::TYPE_DISPOSAL,
    ];

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'serial_id' => 'integer',
            'from_warehouse_id' => 'integer',
            'to_warehouse_id' => 'integer',
            'employee_id' => 'integer',
            'quantity' => 'decimal:3',
            'transaction_date' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemName::class, 'item_id');
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(InventoryItemSerial::class, 'serial_id');
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'to_warehouse_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
