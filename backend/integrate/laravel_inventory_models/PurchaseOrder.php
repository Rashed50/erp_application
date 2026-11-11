<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'po_no',
        'supplier_id',
        'material_request_id',
        'purchase_date',
        'expected_date',
        'status',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'remarks',
        'created_by',
        'approved_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class, 'supplier_id'); }
    public function materialRequest(): BelongsTo { return $this->belongsTo(MaterialRequest::class, 'material_request_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'created_by', 'emp_auto_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'approved_by', 'emp_auto_id'); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id'); }
    public function receipts(): HasMany { return $this->hasMany(PurchaseReceipt::class, 'purchase_order_id'); }

}
