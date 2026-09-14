<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpContactPerson extends Model
{
    use HasFactory;
    protected $primaryKey = 'ecp_id';

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }

    public function user(){
      return $this->belongsTo('App\Models\User','user_id','id');
    }


}
