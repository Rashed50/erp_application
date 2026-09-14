<?php

namespace App\Models\Inventory;

use App\Models\Inventory\SubStoreInfo;
use App\Models\SubCompanyInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemDetails extends Model
{
    use HasFactory;

    protected $table = "item_details";

    protected $fillable = ['item_deta_id ', 'icatg_id', 'iscatg_id', 'item_name_auto_id', 'model_no', 'serial_no', 'quantity', 'created_at'];


    public function itemType(){
      return $this->belongsTo(ItemType::class,'itype_id','itype_id');
    }

    public function itemCatg(){
      return $this->belongsTo(ItemCategory::class,'icatg_id','icatg_id');
    }

    public function itemSubCatg(){
      return $this->belongsTo(ItemSubCategory::class,'iscatg_id','iscatg_id');
    }

    public function itemNameCode(){
      return $this->belongsTo(item_name::class,'item_name_auto_id','item_id');
    }

    public function itemCompanyName(){
      return $this->belongsTo(SubCompanyInfo::class,'item_comp_id','sb_comp_id');
    }

    public function itemBrandName(){
      return $this->belongsTo(ItemBrand::class,'ibrand_id','ibrand_id');
    }

    public function subStore(){
      return $this->belongsTo(SubStoreInfo::class,'store_id','sub_store_id' );
    }

}
