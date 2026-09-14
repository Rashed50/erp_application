<?php
    namespace App\Http\Controllers\DataServices;

use App\Models\EmployeePromotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeePromotionDataService {

    public function getAnEmployeePromotionInfoByEmpId($emp_auto_id){
        return EmployeePromotion::where('emp_id', $emp_auto_id)->first();
    }
    
    public function getAnEmployeePromotionRecordInfoByPromotionAutoId($emp_prom_id ){
        return EmployeePromotion::where('emp_prom_id', $emp_prom_id )->first();
    }


    public function countEmployeePromotionInfoByRequestedEmpId($emp_auto_id){
        return EmployeePromotion::where('emp_id', $emp_auto_id)->count();
    }
    public function deleteAPromotionRecordThatApprovelPendingByPromotionAutoId($emp_prom_id ){
        return EmployeePromotion::where('emp_prom_id', $emp_prom_id)->where('approval_status', 0)->delete(); // 0 = pending , 1= approved
    }

    public function updateEmployeeUploadedFileDbPath($emp_auto_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  EmployeePromotion::where('emp_id', $emp_auto_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function insertAnEmployeePromotionDetailsInfo(
        $emp_id,
        $designation_id,
        $emplyoeeDesignation,
        $basic_amount,
        $hourly_rent,
        $mobile_allowance,
        $food_allowance,
        $medical_allowance,
        $house_rent,
        $local_travel_allowance,
        $conveyance_allowance,
        $others1,
        $promotion_by,
        $prom_date,
        $prom_remarks,
        $prom_apprv_documents,
        $increment_amount
    ){

        $entered_id = Auth::user()->id;
        return EmployeePromotion::insert([
            'emp_id' => $emp_id,
            'designation_id' => $designation_id,
            'new_designation_id' => $emplyoeeDesignation,
            'entered_id' => $entered_id,
            'basic_amount' => $basic_amount,
            'hourly_rent' => $hourly_rent,
            'house_rent' => $house_rent,
            'mobile_allowance' => $mobile_allowance,
            'food_allowance' => $food_allowance,
            'medical_allowance' => $medical_allowance,
            'local_travel_allowance' => $local_travel_allowance,
            'conveyance_allowance' => $conveyance_allowance,
            'others1' => $others1,
            'prom_by' => $promotion_by,
            'prom_date' => $prom_date,
            'prom_remarks' => $prom_remarks,
            'increment_amount' => $increment_amount,
            'created_at' => Carbon::now(),
            'prom_apprv_documents' => $prom_apprv_documents
          ]);
    }
    
    public function updatePromotedMultipleEmployeePromotionPaper($emp_prom_id_lst,$file_path,$updated_at,$updated_by){ 
        return  EmployeePromotion::whereIn('emp_prom_id', $emp_prom_id_lst)->update([             
            'updated_at' => $updated_at,
            'prom_update_by' =>$updated_by,
            'prom_apprv_documents' =>$file_path,
        ]);

    }

    public function approveAnEmployeePromotionRecord($emp_prom_id,$updated_at,$approve_by){ 
        return  EmployeePromotion::where('emp_prom_id', $emp_prom_id)->update([             
            'approval_status' =>  1 ,          
            'updated_at' => $updated_at,
            'prom_update_by' =>$approve_by,
            'prom_apprv_by' =>$approve_by,
        ]);

    }

    public function updateExistingEmployeePromotionDetailsInfo(
        $emp_id,
        $designation_id,
        $emplyoeeDesignation,
        $basic_amount,
        $hourly_rent,
        $mobile_allowance,
        $food_allowance,
        $medical_allowance,
        $house_rent,
        $local_travel_allowance,
        $conveyance_allowance,
        $others1,
        $promotion_by,
        $prom_date,
        $prom_remarks,
        $increment_value
    ){

        $entered_id = Auth::user()->id;
        return  EmployeePromotion::where('emp_id', $emp_id)->update([
            'emp_id' => $emp_id,
            'designation_id' => $designation_id,
            'new_designation_id' => $emplyoeeDesignation,
            'entered_id' => $entered_id,
            'basic_amount' => $basic_amount,
            'hourly_rent' => $hourly_rent,
            'house_rent' => $house_rent,
            'mobile_allowance' => $mobile_allowance,
            'food_allowance' => $food_allowance,
            'medical_allowance' => $medical_allowance,
            'local_travel_allowance' => $local_travel_allowance,
            'conveyance_allowance' => $conveyance_allowance,
            'others1' => $others1,
            'prom_by' => $promotion_by,
            'prom_date' => $prom_date,
            'prom_remarks' => $prom_remarks,
            'no_of_increament' => $increment_value + 1,
            'created_at' => Carbon::now(),
            'prom_update_by' =>$entered_id
            
          ]);

    }


    // Created date wise employee promotion details info report
    public function getEmployeePromotionDetailsDateToDate($approved_by_id, $from_date, $today){
       
        return  DB::select('CALL getEmployeePromotionRecordsReport(?,?,?,?)',array(0,0,$from_date, $today));
    }
    public function getAnEmployeePromotionDetailsRecords($employee_id){
        return  DB::select('CALL getEmployeePromotionRecordsReport(?,?,?,?)',array($employee_id,0,null,null));
    }
     public function getAnEmployeePromotionLastRecords($employee_id){
         $records =  DB::select('CALL getAnEmployeePromotionLastRecord(?)',array($employee_id));
        if(count($records)>0)
             return $records[0];
        return null;
    }
    
    // public function getEmployeePromotionApprovalWaitingRecords1($from_date, $today){
    //     return  DB::select('CALL getEmployeePromotionRecordsByApprovalStatusAndDateToDate(?,?,?)',array(0,$from_date, $today));
    // }
    public function getEmployeePromotionApprovalWaitingRecords($from_date, $today,$branch_office_id){
        // 0 = approval status pending
        return  DB::select('CALL getEmployeePromotionRecordsByApprovalStatusAndDateToDate1(?,?,?,?)',array(0,$from_date, $today,(int)$branch_office_id)); // 0 waiting for approval
      //   return  DB::select('CALL getEmployeePromotionRecordsByApprovalStatusAndDateToDate(?,?,?)',array(0,$from_date, $today));
      
      
    //   return  EmployeePromotion::
    //          where('approval_status',0)
    //         ->where('employee_infos.branch_office_id',$branch_office_id)
    //       // ->whereBetween('prom_date', [$from_date, $today])
    //         ->leftjoin('employee_infos', 'employee_promotions.emp_id', '=', 'employee_infos.emp_auto_id')
    //         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //         ->leftjoin('users', 'employee_promotions.entered_id', '=', 'users.id')
    //         ->get();

    }

    public function getAnEmployeePromotedAllRecordsAmountRelatedDetailsInformation($emp_auto_id){
        return  DB::select('CALL getAnEmployeePromotionAllRecordsAmountDetailsByemp_auto_id(?)',array($emp_auto_id));
    }



 
    public function getAnEmployeePromotionLastRecords1($employee_auto_id,$branch_office_id)
   {

          return  EmployeePromotion::
                where('emp_id',$employee_auto_id)
               ->where('branch_office_id',$branch_office_id)
               ->orderBy('emp_prom_id','desc')
               ->first(); // get the last promotion record

   }
   
   
    /**
     * Insert imported employee promotion data into table.
     */
    public function insertEmployeePromotionsImportedExcellDataFromTable($model): bool
    {
        return DB::table('import_emp_promotion_records')->insert([
            'emp_id'                => $model->emp_id,
            'prom_by'               => $model->prom_by,
            'prom_date'             => $model->prom_date,
            'new_designation_id'    => $model->new_designation_id,
            'basic_amount'          => $model->basic_amount,
            'house_rent'            => $model->house_rent,
            'hourly_rent'           => $model->hourly_rent,
            'food_allowance'        => $model->food_allowance,
            'medical_allowance'     => $model->medical_allowance,
            'local_travel_allowance'=> $model->local_travel_allowance,
            'conveyance_allowance'  => $model->conveyance_allowance,
            'others1'               => $model->others1,
            'mobile_allowance'      => $model->mobile_allowance,
            'prom_remarks'          => $model->prom_remarks,
            'prom_apprv_documents'  => $model->prom_apprv_documents,
            'entered_id'            => $model->entered_id,
        ]);
    }


   // remove all data from templorary promotion table
    public function deleteEmployeePromotionsImportedExcellDataFromTable(){

        return DB::table('import_emp_promotion_records')->delete();
    }
      public function getAllEmployeesPromotionsImportedExcellDataFromImportedTable(){

        return DB::table('import_emp_promotion_records')->get();
    }


















}
