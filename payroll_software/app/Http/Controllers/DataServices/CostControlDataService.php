<?php

namespace App\Http\Controllers\DataServices;

use App\Models\CostControl\ActivityElement;
use App\Models\CostControl\ActivityInfo;
use App\Models\CostControl\ActivityDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CostControlDataService
{


    
     /*
     ==========================================================================
     ================ Activity Element ========================================
     ==========================================================================
    */

    public function createActivityElementCode(){
        $nore = ActivityElement::count();
        return $nore > 0 ? (100*1)+($nore + 1) : 101 ;
    }

    public function insertNewActivityElementInformation($act_ele_name,$created_by){
 
        $act_code = $this->createActivityElementCode();
         if ($this->checkThisActivityElementIsExist($act_ele_name)) {
             return 0;
         } else {
            return ActivityElement::insert([
                'act_ele_name' => $act_ele_name,
                'act_ele_code' => $act_code,
                'insert_by' => $created_by
            ]);
        }
    }

    public function checkThisActivityElementIsExist($act_ele_name){
       return ActivityElement::whereRaw('LOWER(act_ele_name) =?',[$act_ele_name])->count()  > 0 ? true:false; 
    }
    public function getAllActiveActivityElemenListForDropdown()
    {        
        return ActivityElement::select('act_ele_name', 'act_ele_auto_id')
            ->where('status', 1)->orderBy('act_ele_name', 'ASC')->get();
    }

     
     /*
     ==========================================================================
     ================ Activity Name ===========================================
     ==========================================================================
    */


    public function createActivityNameCode(){
        $nore = ActivityInfo::count();
        return $nore > 0 ? (100*1)+($nore + 1) : 101 ;
    }

    public function insertNewActivityName($act_name,$created_by){
 
        $act_code = $this->createActivityNameCode();
        if ($this->checkThisActivityElementIsExist($act_name)) {
             return 0;
         } else {
            return ActivityInfo::insert([
                'act_name' => $act_name,
                'act_code' => $act_code,
                'insert_by' => $created_by
            ]);
        }
    }

    public function checkThisActivityNameIsExist($act_name){
               
       return ActivityInfo::whereRaw('LOWER(act_name) =?',[$act_name])->count()  > 0 ? true:false; 

    }

    public function getAllActiveActivityNameListForDropdown()
    {        
        return ActivityInfo::select('act_auto_id', 'act_name')
            ->where('status', 1)->orderBy('act_name', 'ASC')->get();
    }
    
  

     /*
     ==========================================================================
     ================ Activity Details ========================================
     ==========================================================================
    */

    public function insertNewActivityDetailsInformation($proj_name,
    $plot_name, $activity_element, $activity_name, $emp_auto_id,$total_emp, $working_hours,$total_hours, $working_shift, $working_date, $remarks, $created_by){
  
            return ActivityDetails::insert([
                'proj_auto_id' => $proj_name,
                'pro_plo_auto_id' => $plot_name,
                'act_ele_auto_id' => $activity_element,
                'act_nam_auto_id' => $activity_name,
                'working_shift' => $working_shift,
                'total_worker' => $total_emp,
                'daily_hours' => $working_hours,
                'total_working_hours' => $total_hours,
                'emp_auto_id' => $emp_auto_id,     
                'insert_by' => $created_by,
                'working_date'=>$working_date,
                'remarks'=>$remarks
            ]);
         
    }

    public function getActivityDetailsRecords($pageLimit = 20){
       return ActivityDetails::select('act_det_rec_auto_id','proj_name','plo_name','act_ele_name','act_name','total_worker','daily_hours',
       'total_working_hours','working_date','working_shift','employee_name','employee_id','remarks') 
                     ->leftjoin('employee_infos', 'activity_details_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                     ->leftjoin('project_infos', 'activity_details_records.proj_auto_id', '=', 'project_infos.proj_id')
                    ->leftjoin('project_plots', 'activity_details_records.pro_plo_auto_id', '=', 'project_plots.pro_plo_auto_id')
                    ->leftjoin('activity_elements', 'activity_details_records.act_ele_auto_id', '=', 'activity_elements.act_ele_auto_id')
                    ->leftjoin('activity_info', 'activity_details_records.act_nam_auto_id', '=', 'activity_info.act_auto_id')
                    // ->leftjoin('project_infos', 'activity_details_records.proj_auto_id', '=', 'project_infos.proj_id')                    
                    ->take($pageLimit)->get();
    } 
     public function deleteAnActivityDetailsRecords($act_det_rec_auto_id){
        return ActivityDetails::where('act_det_rec_auto_id',$act_det_rec_auto_id)->delete();
     }




   public function  getMultipleEmployeeWiseActivityDetailsReport($emp_id_list){
    return ActivityDetails::select('act_det_rec_auto_id','proj_name','plo_name','act_ele_name','act_name','total_worker','daily_hours',
    'total_working_hours','working_date','working_shift','employee_name','employee_id','remarks') 
                  ->leftjoin('employee_infos', 'activity_details_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                  ->leftjoin('project_infos', 'activity_details_records.proj_auto_id', '=', 'project_infos.proj_id')
                 ->leftjoin('project_plots', 'activity_details_records.pro_plo_auto_id', '=', 'project_plots.pro_plo_auto_id')
                 ->leftjoin('activity_elements', 'activity_details_records.act_ele_auto_id', '=', 'activity_elements.act_ele_auto_id')
                 ->leftjoin('activity_info', 'activity_details_records.act_nam_auto_id', '=', 'activity_info.act_auto_id')
                 ->whereIn('employee_infos.employee_id',$emp_id_list)                
                  ->get();
   }

   public function  getProjectPlotActivityElementWiseActivityDetailsReport($project_ids,$plot_ids,$activity_elements,$act_names){
    return ActivityDetails::select('act_det_rec_auto_id','proj_name','plo_name','act_ele_name','act_name','total_worker','daily_hours',
    'total_working_hours','working_date','working_shift','employee_name','employee_id','remarks') 
                  ->leftjoin('employee_infos', 'activity_details_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                  ->leftjoin('project_infos', 'activity_details_records.proj_auto_id', '=', 'project_infos.proj_id')
                 ->leftjoin('project_plots', 'activity_details_records.pro_plo_auto_id', '=', 'project_plots.pro_plo_auto_id')
                 ->leftjoin('activity_elements', 'activity_details_records.act_ele_auto_id', '=', 'activity_elements.act_ele_auto_id')
                 ->leftjoin('activity_info', 'activity_details_records.act_nam_auto_id', '=', 'activity_info.act_auto_id')
                 ->whereIn('activity_details_records.proj_auto_id',$project_ids)    
                 ->whereIn('activity_details_records.pro_plo_auto_id',$plot_ids)  
                 ->whereIn('activity_details_records.act_ele_auto_id',$activity_elements)  
                 ->whereIn('activity_details_records.act_nam_auto_id',$act_names)              
                  ->get();
   }


}

 