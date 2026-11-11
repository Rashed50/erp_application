<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryTransferItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_transfer_id',
        'item_id',
        'requested_qty',
        'approved_qty',
        'sent_qty',
        'received_qty'
    ];

    protected $casts = [
        'requested_qty' => 'decimal:3',
        'approved_qty' => 'decimal:3',
        'sent_qty' => 'decimal:3',
        'received_qty' => 'decimal:3',
    ];

    public function transfer(): BelongsTo { return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }

}
