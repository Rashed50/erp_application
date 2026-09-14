<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyWorkHistory extends Model
{
    use HasFactory;
    protected $primaryKey = 'month_work_id';
    protected $guarded = [];

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }

    public function month(){
      return $this->belongsTo('App\Models\Month','month_id','month_id');
    }

}
