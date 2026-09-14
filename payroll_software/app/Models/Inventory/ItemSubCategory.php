<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\ItemType;
use App\Models\Inventory\ItemCategory;

class ItemSubCategory extends Model
{
    use HasFactory;
    public function itemType(){
      return $this->belongsTo(ItemType::class,'itype_id','itype_id');
    }

    public function itemCatg(){
      return $this->belongsTo(ItemCategory::class,'icatg_id','icatg_id');
    }
}
