<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBook extends Model{
    use HasFactory;

    protected $primaryKey = 'lgb_id';
    protected $guarded = [];
    public function vehicle(){
      return $this->belongsTo('App\Models\Vehicle','veh_id','veh_id');
    }
    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }
}
