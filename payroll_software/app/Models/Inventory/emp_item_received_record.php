<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\InventoryItemsUnit;

class emp_item_received_record extends Model
{
    use HasFactory;

    protected $casts = [
        'item_unit' => InventoryItemsUnit::class
    ];
}
