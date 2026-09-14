<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMultiProjectWorkHistory extends Model
{
    use HasFactory;

    public $table = "emp_multi_proj_work_hist";

    public function projectName(){
      return $this->belongsTo('App\Models\ProjectInfo','project_id','proj_id');
    }

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }



}
