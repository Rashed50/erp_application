<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_code',
        'name',
        'category_id',
        'unit_id',
        'item_type',
        'track_type',
        'minimum_stock',
        'description',
        'status'
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:3',
        'status' => 'boolean',
    ];

    public function category(): BelongsTo { return $this->belongsTo(InventoryCategory::class, 'category_id'); }
    public function unit(): BelongsTo { return $this->belongsTo(InventoryUnit::class, 'unit_id'); }
    public function serials(): HasMany { return $this->hasMany(InventoryItemSerial::class, 'item_id'); }
    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class, 'item_id'); }
    public function materialRequestItems(): HasMany { return $this->hasMany(MaterialRequestItem::class, 'item_id'); }
    public function purchaseOrderItems(): HasMany { return $this->hasMany(PurchaseOrderItem::class, 'item_id'); }
    public function purchaseReceiptItems(): HasMany { return $this->hasMany(PurchaseReceiptItem::class, 'item_id'); }
    public function transferItems(): HasMany { return $this->hasMany(InventoryTransferItem::class, 'item_id'); }
    public function transactions(): HasMany { return $this->hasMany(InventoryTransaction::class, 'item_id'); }
    public function employeeInventory(): HasMany { return $this->hasMany(EmployeeInventory::class, 'item_id'); }
    public function employeeReturns(): HasMany { return $this->hasMany(EmployeeInventoryReturn::class, 'item_id'); }
    public function assets(): HasMany { return $this->hasMany(InventoryAsset::class, 'item_id'); }
    public function disposalItems(): HasMany { return $this->hasMany(InventoryDisposalItem::class, 'item_id'); }

}
