<?php

namespace App\Http\Controllers\DataServices;

use App\Models\EmployeeActivity;


Class EmpActivityDataService{


    public function getAnEmployeeLastActivity($emp_auto_id){
        return EmployeeActivity::where('emp_auto_id',$emp_auto_id)
                        ->orderby('emp_act_auto_id','DESC')
                        ->first();
    }
    
     public function getAnEmployeeLastActivityComments($emp_auto_id){
        $act = EmployeeActivity::where('emp_auto_id',$emp_auto_id)
                        ->orderby('emp_act_auto_id','DESC')
                        ->first();
        if($act == null)
          return '';
        else 
          return $act->remarks;
    }

}