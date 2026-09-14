<?php

namespace App\Models;

use App\Models\Inventory\SubStoreInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPurchaseRecord extends Model
{
    use HasFactory;
    protected $table = "inventory_purchase_records";

    protected $fillable = ['id', 'store_id', 'invoice_no', 'invoice_date', 'received_date', 'purchase_by', 'purchase_from', 'chalan_no', 'created_at', 'updated_at'];


    public function storeName(){
        return $this->belongsTo(SubStoreInfo::class, 'store_id', 'sub_store_id');
    }

    public function purchaseBy(){
        return $this->belongsTo(ItemPurchaseBy::class, 'purchase_by', 'id');
    }

    public function purchaseFrom(){
        return $this->belongsTo(InventorySupplier::class, 'purchase_from', 'isupp_auto_id');
    }

    public function createBy(){
        return $this->belongsTo(User::class, 'create_by_id', 'id');
    }

}
