<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerInfo extends Model
{
    use HasFactory;
    protected $primaryKey = 'ban_id';

    public function user(){
      return $this->belongsTo('App\Models\User','entered_id','id');
    }
}
