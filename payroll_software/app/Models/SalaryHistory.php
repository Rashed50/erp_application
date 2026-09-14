<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MonthsEnum;

class SalaryHistory extends Model{
    use HasFactory;
    protected $primaryKey = 'slh_auto_id';


    // protected $casts = [
    //   'slh_month' =>  MonthsEnum::Class,
    //  ];

    public function employee(){
      return $this->belongsTo(EmployeeInfo::class,'emp_auto_id','emp_auto_id');
    }


    public function project(){
      return $this->belongsTo(ProjectInfo::class,'project_id','proj_id');
    }

    public function month(){
      return $this->belongsTo(Month::class,'slh_month','month_id');
    }



}
