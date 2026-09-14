<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item_name extends Model
{
    use HasFactory;

    protected $table = 'item_names';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'itype_id',
        'icatg_id',
        'iscatg_id',
        'item_deta_name',
        'item_deta_code',
        'item_deta_status',
        'create_by_id',
        'update_by_id',
    ];


    public function itemType(){
      return $this->belongsTo(ItemType::class,'itype_id','itype_id');
    }

    public function itemCatg(){
      return $this->belongsTo(ItemCategory::class,'icatg_id','icatg_id');
    }

    public function itemSubCatg(){
      return $this->belongsTo(ItemSubCategory::class,'iscatg_id','iscatg_id');
    }

    public function itemBrand(){
      return $this->belongsTo(ItemBrand::class,'item_id','item_id');
    }
}
