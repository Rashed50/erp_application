<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
  use HasFactory;

  protected $table      = 'divisions';
  protected $primaryKey = "division_id";

  public function country()
  {
    return $this->belongsTo('App\Models\Country', 'country_id', 'id');
  }
}
