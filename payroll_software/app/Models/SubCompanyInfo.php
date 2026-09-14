<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCompanyInfo extends Model
{
    use HasFactory;
    protected $primaryKey = 'sb_comp_id';

    public function user(){
      return $this->belongsTo('App\Models\User','entered_id','id');
    }

    public function company(){
      return $this->belongsTo('App\Models\CompanyProfile','company_id','comp_id');
    }
}
