<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceInfo extends Model
{
    use HasFactory;
    protected $table = 'advance_infos';

    public function user(){
        return $this->belongsTo('App\Models\User','create_by','id');
      }
  
      public function advPurpose(){
        return $this->belongsTo('App\Models\AdvancePurpose','adv_purpose_id','id');
      }
  
      public function employee(){
        return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
      }
}
