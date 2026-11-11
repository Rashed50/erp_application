<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryUnit extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'short_name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function items(): HasMany { return $this->hasMany(InventoryItem::class, 'unit_id'); }

}
