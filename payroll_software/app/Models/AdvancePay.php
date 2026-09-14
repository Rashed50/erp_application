<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePay extends Model{
    use HasFactory;

    protected $table = 'advance_pays';
    protected $primaryKey = 'adv_pay_id';

    public function user(){
      return $this->belongsTo('App\Models\User','entered_id','id');
    }

    public function advPurpose(){
      return $this->belongsTo('App\Models\AdvancePurpose','adv_pay_purpose','id');
    }

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }
}
