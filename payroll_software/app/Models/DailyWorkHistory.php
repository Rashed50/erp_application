<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWorkHistory extends Model
{
    use HasFactory;
    protected $primaryKey = 'day_work_id';
    protected $guarded = [];

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }

    public function monthly(){
      return $this->belongsTo('App\Models\Month','month','month_id');
    }

    public function user(){
      return $this->belongsTo('App\Models\User','entered_id','id');
    }

}
