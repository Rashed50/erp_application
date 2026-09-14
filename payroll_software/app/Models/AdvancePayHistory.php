<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayHistory extends Model{
    use HasFactory;
    protected $primaryKey = "aph_id";
    protected $guarded = [];

    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }

    public function advance(){
      return $this->belongsTo('App\Models\AdvancePay','adv_pay_id','adv_pay_id');
    }
}
