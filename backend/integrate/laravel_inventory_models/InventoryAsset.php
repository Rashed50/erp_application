<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryAsset extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'serial_id',
        'asset_tag',
        'purchase_receipt_item_id',
        'purchase_cost',
        'purchase_date',
        'warranty_expiry',
        'current_warehouse_id',
        'current_employee_auto_id',
        'status',
        'condition'
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function serial(): BelongsTo { return $this->belongsTo(InventoryItemSerial::class, 'serial_id'); }
    public function purchaseReceiptItem(): BelongsTo { return $this->belongsTo(PurchaseReceiptItem::class, 'purchase_receipt_item_id'); }
    public function currentWarehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'current_warehouse_id'); }
    public function currentEmployee(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'current_employee_auto_id', 'emp_auto_id'); }
    public function assignments(): HasMany { return $this->hasMany(AssetAssignment::class, 'asset_id'); }
    public function returns(): HasMany { return $this->hasMany(AssetReturn::class, 'asset_id'); }

}
