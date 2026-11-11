<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchaseReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'receipt_no',
        'purchase_order_id',
        'warehouse_id',
        'received_date',
        'received_by',
        'status',
        'remarks'
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id'); }
    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'received_by', 'emp_auto_id'); }
    public function items(): HasMany { return $this->hasMany(PurchaseReceiptItem::class, 'purchase_receipt_id'); }

}
