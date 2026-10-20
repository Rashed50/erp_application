<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'item_name',
        'description',
        'unit_id',
        'count',
        'weight',
        'qty',
        'unit_price',
        'discount',
        'vat',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'count' => 'decimal:2',
            'weight' => 'decimal:2',
            'qty' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'vat' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class, 'unit_id');
    }
}
