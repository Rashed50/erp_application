<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
  use HasFactory;
  protected $primaryKey = "district_id";

  public function country(){
    return $this->belongsTo('App\Models\Country','country_id','id');
  }

  public function division(){
    return $this->belongsTo('App\Models\Division','division_id','division_id');
  }
}
