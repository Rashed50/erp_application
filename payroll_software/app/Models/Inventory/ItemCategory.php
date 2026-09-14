<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\ItemType;

class ItemCategory extends Model{
    use HasFactory;
    protected $primarykeys = "icatg_id";

    public function itemType(){
      return $this->belongsTo(itemType::class,'itype_id','itype_id');
    }


}
