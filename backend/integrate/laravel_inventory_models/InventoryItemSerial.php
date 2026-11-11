<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryItemSerial extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'serial_number',
        'asset_tag',
        'barcode',
        'purchase_receipt_item_id',
        'current_location_id',
        'current_employee_auto_id',
        'status',
        'condition',
        'purchase_date',
        'warranty_expiry'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function purchaseReceiptItem(): BelongsTo { return $this->belongsTo(PurchaseReceiptItem::class, 'purchase_receipt_item_id'); }
    public function currentLocation(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'current_location_id'); }
    public function currentEmployee(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'current_employee_auto_id', 'emp_auto_id'); }
    public function asset(): HasOne { return $this->hasOne(InventoryAsset::class, 'serial_id'); }

}
