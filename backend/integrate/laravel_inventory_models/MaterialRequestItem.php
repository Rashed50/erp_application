<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MaterialRequestItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'material_request_id',
        'item_id',
        'requested_qty',
        'approved_qty',
        'purchased_qty',
        'issued_qty',
        'remarks'
    ];

    protected $casts = [
        'requested_qty' => 'decimal:3',
        'approved_qty' => 'decimal:3',
        'purchased_qty' => 'decimal:3',
        'issued_qty' => 'decimal:3',
    ];

    public function materialRequest(): BelongsTo { return $this->belongsTo(MaterialRequest::class, 'material_request_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }

}
