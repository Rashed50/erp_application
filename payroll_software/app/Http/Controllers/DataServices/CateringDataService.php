<?php

namespace App\Http\Controllers\DataServices;

use App\Models\CateringMonthlyRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CateringDataService{

    public function insertAnEmployeeMonthlyCateringRecord($emp_auto_id,$month,$year,$total_days,$amount,$inserted_by,$approved_by,$remarks,$project_id=null){
        
         return CateringMonthlyRecord::insertGetId([
            'emp_auto_id'=>$emp_auto_id ,
            'month'=>$month,
            'year'=>$year,
            'total_days'=>$total_days ,
            'amount' =>$amount,
            'inserted_by'=>$inserted_by ,
            'approved_by'=>$approved_by ,
            'remarks'=>$remarks ,
             'project_id' => $project_id
         ]);

    }
    public function updateAnEmployeeMonthlyCateringRecord($emcr_auto_id,$month,$year,$total_days,$amount,$inserted_by,$approved_by,$remarks,$project_id){
        
        return CateringMonthlyRecord::where('emcr_auto_id',$emcr_auto_id)->update([         
           'month'=>$month,
           'year'=>$year,
           'total_days'=>$total_days ,
           'amount' =>$amount,
           'inserted_by'=>$inserted_by ,
           'approved_by'=>$approved_by ,
           'remarks'=>$remarks ,
            'project_id' => $project_id
        ]);

   }

    public function checkAnEmployeeCateringMonthRecordAlreadyExist($emp_auto_id,$month,$year){
        return (CateringMonthlyRecord::where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->count()) > 0 ? true:false;
    }
    public function searchingAnEmployeeCateringMonthRecordByAutoId($emcr_auto_id){
        return CateringMonthlyRecord::where('emcr_auto_id',$emcr_auto_id)
                ->leftjoin('employee_infos', 'catering_monthly_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('project_infos', 'catering_monthly_records.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->first();
    }
    
    public function getAnEmployeeAMonthCateringRecordForSalaryProcessingByEmployeeAutoId($emp_auto_id,$month,$year){
        return  CateringMonthlyRecord::select('emcr_auto_id','emp_auto_id','year','month','total_days','amount','status')->where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->first();
    } 

    public function deleteAnEmployeeCateringMonthRecordByAutoId($emcr_auto_id){
           return CateringMonthlyRecord::where('emcr_auto_id',$emcr_auto_id)->delete();
   }

    public function searcAnEmployeeCateringRecordForListView($empoyee_id,$month,$year){
        
        return DB::select('call getAnEmployeeCateringRecordsForListView1(?,?,?)',array($empoyee_id,$month,$year));
   }
   public function getAnEmployeeCateringServiceReport($empoyee_id,$month,$year){
      
    return DB::select('call getAnEmployeeCateringServiceReport1(?,?)',array($empoyee_id,$year));
   }
   public function getCateringServiceRecordByMonthAndYearReport($month,$year){ 
    
    return DB::select('call getEmployeeCateringServiceRecordByMonthAndYearForReport1(?,?)',array($month,$year));
   }

    public function insertCateringMonthlyImportedRecordInTemporaryTable($emp_auto_id,$month,$year,$days,$amount,$inserted_by){
       return DB::insert('insert into catering_monthly_records_upload(emp_auto_id,month,year,total_days,amount,inserted_by) values (?,?,?,?,?,?)', [$emp_auto_id,$month,$year,$days,$amount,$inserted_by]);
    }
    
    public function deleteCateringImportedTemporaryTableRecordsForExcelUpload(){
        return DB::select('call removeAllRecordsFromCateringUploadTemporaryTable1()');
     }

    public function getCateringImportedTemporaryTableRecordsForFinalUpload(){
        return DB::select('call getAllRecordsFromCateringUploadTemporaryTable1()');
     }
     
     
   public function getCateringServiceRecordByProjectMonthAndYearReport($project_ids,$month,$year){

        return CateringMonthlyRecord::select('catering_monthly_records.emcr_auto_id',
        'catering_monthly_records.month',
        'catering_monthly_records.year',
        'catering_monthly_records.total_days',
        'catering_monthly_records.amount',
        'catering_monthly_records.inserted_by',
        'catering_monthly_records.remarks',
        'employee_infos.emp_auto_id',
        'employee_infos.employee_id',
        'employee_infos.employee_name',
        'employee_infos.akama_no',
        'employee_infos.mobile_no',
        'employee_infos.hourly_employee',
        'employee_infos.akama_photo',
        'employee_infos.pasfort_photo',
        'employee_categories.catg_name',
        'sponsors.spons_name',
        'project_infos.proj_name',
        'months.month_name',
        'users.name as inserted_by')
        ->whereIn('catering_monthly_records.project_id',$project_ids)
        ->where('month',$month)
        ->where('year',$year)
        ->leftjoin('employee_infos', 'catering_monthly_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->leftjoin('project_infos', 'catering_monthly_records.project_id', '=', 'project_infos.proj_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        ->leftjoin('months', 'catering_monthly_records.month', '=', 'months.month_id')
        ->leftjoin('users', 'catering_monthly_records.inserted_by', '=', 'users.id')
        ->get(); 

   }


   
   
    
}

