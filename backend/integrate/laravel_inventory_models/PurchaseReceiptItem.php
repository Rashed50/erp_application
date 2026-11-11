<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchaseReceiptItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_receipt_id',
        'purchase_order_item_id',
        'item_id',
        'quantity',
        'unit_cost',
        'batch_no',
        'expiry_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function receipt(): BelongsTo { return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id'); }
    public function purchaseOrderItem(): BelongsTo { return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function serials(): HasMany { return $this->hasMany(InventoryItemSerial::class, 'purchase_receipt_item_id'); }
    public function assets(): HasMany { return $this->hasMany(InventoryAsset::class, 'purchase_receipt_item_id'); }

}
