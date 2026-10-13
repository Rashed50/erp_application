<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class, 'supplier_id'); }

}
