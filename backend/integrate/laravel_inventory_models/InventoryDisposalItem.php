<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryDisposalItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'disposal_id',
        'item_id',
        'serial_id',
        'quantity',
        'reason'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function disposal(): BelongsTo { return $this->belongsTo(InventoryDisposal::class, 'disposal_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function serial(): BelongsTo { return $this->belongsTo(InventoryItemSerial::class, 'serial_id'); }

}
