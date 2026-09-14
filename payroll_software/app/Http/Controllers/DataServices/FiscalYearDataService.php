<?php

namespace App\Http\Controllers\DataServices;

use App\Models\EmployeeFiscalYearDuration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FiscalYearDataService{


    /*
     =====================================================================================
     ======================= Employee Fiscal Year Section ================================
     =====================================================================================
    */

     public function checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id){

        return EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)->count() >= 1 ? true : false;
     }

     public function checkAnEmployeeRunningFiscalYearIsAlreadyExist($emp_auto_id){
         return EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)->where('closing_status',false)->count() >= 1 ? true : false;
     }



      public function checkAnEmployeeFiscalYearRecordExistAfterThisDate($emp_auto_id,$start_date,$end_date){

         return EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                        ->whereBetween('start_date', [$start_date, $end_date])
                    //     ->whereDate('start_date','>=',$start_date)
                    //    ->whereDate('end_date', '<=', $end_date)
                        // ->where('closing_status',$closing_status)
                        ->get();
                       // ->count()  >= 1 ? true:false;

     }

     public function deleteAnEmployeeFiscalYearRecordByFiscalYearAutoId($efcr_auto_id){
        return EmployeeFiscalYearDuration::where('efcr_auto_id',$efcr_auto_id)->delete();
    }

     public function getAnEmployeeOpenCloseFiscalYearAllRecords($employee_id,$branch_office_id){
         return EmployeeFiscalYearDuration::select('employee_fiscal_closing_records.*','employee_infos.emp_auto_id','employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','users.name as updated_by_name')
                         ->where('employee_infos.employee_id',$employee_id)
                         ->where('employee_infos.branch_office_id',$branch_office_id)
                         ->leftjoin('employee_infos', 'employee_fiscal_closing_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                         ->leftjoin('users', 'employee_fiscal_closing_records.updated_by', '=', 'users.id')
                         ->get();

     }
       // will be delete soon30.7.26
    //  public function getAnEmployeeFiscalYearLastRecord($emp_auto_id){

    //      if(!$this->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)){

    //         $this->setAnEmployeeFiscalYearDuration($emp_auto_id,1,2021,'2021-12-31',0,2);
    //      }
    //      $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
    //                      ->where('closing_status',false)
    //                      ->latest('efcr_auto_id')
    //                      ->first();

    //      if($record->end_year == null){
    //          $record->end_year = (int) Carbon::now()->format('Y');
    //      }
    //      if($record->end_month == null){
    //          $record->end_month = (int) Carbon::now()->format('m');
    //      }
    //      if($record->end_date == null){
    //          $record->end_date =  Carbon::now()->format('Y-m-d');
    //      }
    //      //  dd($record);
    //      return $record;

    //  }

     public function getAnEmployeeRunningFiscalYearRecord($emp_auto_id){

         if(!$this->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)){

            $this->setAnEmployeeFiscalYearDuration($emp_auto_id,1,2021,'2021-12-31',0,2);
         }

         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                         ->where('closing_status',false)
                         ->latest('efcr_auto_id')
                         ->first();

         if(is_null($record)){
             $record = EmployeeFiscalYearDuration::make();   // create object
             $record->balance_amount = 0.0;

             $record->start_year = (int) Carbon::now()->format('Y');
             $record->start_month = (int) Carbon::now()->format('m');
             $record->start_date =  Carbon::now()->format('Y-m-d');
         }
         $record->end_year = (int) Carbon::now()->format('Y');
         $record->end_month = (int) Carbon::now()->format('m');
         $record->end_date =  Carbon::now()->format('Y-m-d');
         return $record;
     }

     public function getAnEmployeeLastClosingFiscalYearRecord($emp_auto_id){

         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                         ->where('closing_status',true)
                         ->latest('efcr_auto_id')
                         ->first();

         if(is_null($record)){
             $record = EmployeeFiscalYearDuration::make();   // create object
             $record->balance_amount = 0.0;
         }
         return $record;
     }

     // will be delete soon
     public function getAnEmployeeLastFiscalYearRecord($emp_auto_id){

         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                       //  ->where('closing_status',false)
                         ->latest('efcr_auto_id')
                         ->first();
         if(is_null($record)){
             $record = EmployeeFiscalYearDuration::make();   // create object
             $record->balance_amount = 0.0;
         }
         return $record;

     }

     public function getAnEmployeeFiscalYearRecordByFiscalYearAutoId($efcr_auto_id){
         return EmployeeFiscalYearDuration::where('efcr_auto_id',$efcr_auto_id)->first();
     }

    public function updateAnEmployeeFiscalYear($emp_fis_year_auto_id,$emp_auto_id,$end_month,$end_year,$end_date,$balance_amount,$closing_status ,$remarks,$updated_by){

      return  EmployeeFiscalYearDuration::where('efcr_auto_id',$emp_fis_year_auto_id)->where('emp_auto_id',$emp_auto_id)->update([
             'end_month' =>$end_month,
             'end_year' =>$end_year,
             'end_date' =>$end_date,
             'balance_amount' =>$balance_amount,
             'closing_status' => $closing_status ,
             'remarks' =>$remarks,
             'updated_by' =>$updated_by,
         ]);
     }

     public function updateAnEmployeeFiscalYearClosingBalanceOnly($emp_fis_year_auto_id,$balance_amount){

         return  EmployeeFiscalYearDuration::where('efcr_auto_id',$emp_fis_year_auto_id)->update([
                'balance_amount' =>$balance_amount
            ]);
    }

    public function updateAnEmployeeFiscalYearClosingFilePath($emp_fis_year_auto_id,$closing_file){

         return  EmployeeFiscalYearDuration::where('efcr_auto_id',$emp_fis_year_auto_id)->update([
                'closing_file' =>$closing_file
            ]);
    }



     public function setAnEmployeeFiscalYearDuration($emp_auto_id,$start_month,$start_year,$start_date,$balance_amount,$created_by){

         return   EmployeeFiscalYearDuration::insertGetId([
             'emp_auto_id' =>$emp_auto_id,
             'start_month' =>$start_month,
             'start_year' =>$start_year,
             'start_date' =>$start_date,
             'balance_amount' =>$balance_amount,
             'created_by' =>$created_by,
         ]);

     }

     public function checkThisOperationIsAllowInTheRunningFiscalYear($emp_auto_id,$operation_date){

         if(!$this->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)){
            $this->setAnEmployeeFiscalYearDuration($emp_auto_id,1,2021,'2021-12-31',0,2);
         }
         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                         ->where('closing_status',false)
                         ->latest('efcr_auto_id')
                         ->first();
         if($record == null){
             return false;
         }
         if( $record->start_date <= $operation_date)
              return true;
         else
            return false;
      }

    public function deleteExistingOpenedFiscalRecordAndReopenAlreadyClosedFiscalYear($reopened_fical_record_id,$emp_auto_id,$updated_by){
          $records = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)->where('closing_status',false)->orderBy('efcr_auto_id','desc')->get();
          foreach($records as $ar){
            EmployeeFiscalYearDuration::where('efcr_auto_id',$ar->efcr_auto_id)->delete();
          }
          EmployeeFiscalYearDuration::where('efcr_auto_id',$reopened_fical_record_id)->where('emp_auto_id',$emp_auto_id)->update([
             'closing_status' => false ,
             'updated_by' =>$updated_by,
         ]);

     }

     // ==================================================================================
     // ======================= Fiscal Year Related Report ===============================
     // ==================================================================================


      public function salaryClosingEmployeeListDateToDateReport($from_date,$to_date,$branch_office_id){
            return  DB::select('CALL salaryClosingEmployeelistDateToDateReport1(?,?,?)', array($from_date,$to_date,$branch_office_id));
      }


    public function salaryClosingDateToDateSummaryReport($month,$year,$branch_office_id){
        return  DB::select('CALL getSalaryClosingMonthlySummaryReportByMonthAndYear1(?,?,?)', array($month,$year,$branch_office_id));
    }






}
