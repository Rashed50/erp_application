<?php

namespace App\Http\Controllers\DataServices;

use App\Models\LeaveApplication;
use App\Models\LeaveReason;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;


class LeaveApplicationDataService {

    public function getLeaveTypeRecordsForDropdown(){
        return   LeaveType::get();
    }

    public function getLeaveReasonRecordsForDropdown(){
        return LeaveReason::select('lev_reas_id','lev_reas_name')->get();
    }

    public function getLeaveApplicationStatusForDropdown(){
        return DB::table('leave_status')->select('leav_sta_auto_id', 'status_title')->get();
    }



    public function checkThisRecordAlreadyExist(){
        return true;
    }

    public function insertLeaveApplicationInformation($emp_auto_id,$leave_type_id,$leave_reason_id,$leav_days,$application_date,$start_date,$end_date,$inserted_by,$app_status,$description,$reference_by,$leave_paper){
        if($this->checkThisRecordAlreadyExist()){
            return  LeaveApplication::insertGetId([
                'emp_auto_id' => $emp_auto_id,
                'leave_type_id' => $leave_type_id,
                'leave_reason_id' => $leave_reason_id,
                'leav_days' => $leav_days,
                'appl_date' => $application_date,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'inserted_by' => $inserted_by,
                'appl_status' => $app_status,
                'description' => $description,
                'reference_by'=>$reference_by,
                'leave_paper' => $leave_paper
            ]);
        }
    }




    public function updateLeaveApplicationExitPaperPath($leav_auto_id ,$file_path){

        return  LeaveApplication::where('leav_auto_id',$leav_auto_id)->update([
            'exit_paper' => $file_path ,

        ]);
    }

    public function updateLeaveApplicationInformationByAdmin($leav_auto_id ,$leave_reason_id,$leave_days,$leave_start_date,$end_date,$updated_by,$app_status,$admin_comments){

            return  LeaveApplication::where('leav_auto_id',$leav_auto_id)->update([
                'leav_auto_id' => $leav_auto_id ,
                'leave_reason_id' => $leave_reason_id,
                'leave_start_date' => $leave_start_date,
                'end_date' => $end_date,
                'leav_days' => $leave_days,
                'approve_by' =>$updated_by,
                'updated_by' => $updated_by,
                'appl_status' => $app_status,
                'admin_comments' => $admin_comments,
            ]);
    }

    public function rejectALeaveApplication($leav_auto_id,$updated_by){
        return  LeaveApplication::where('leav_auto_id',$leav_auto_id)->update([
              'appl_status' => 2,
              'updated_by' => $updated_by,
            ]);
    }

    public function getLeaveApplicationDetailsByLeaveAutoId($leav_auto_id){

        return DB::select('CALL getALeaveApplicationRecordDetailsByLeaveAutoId1(?)',array($leav_auto_id));
    }

    public function findAnEmployeeLeaveApplicationInformationByEmpAutoID($emp_auto_id){
        return LeaveApplication::where('emp_auto_id', $emp_auto_id)
        ->whereIn('appl_status', [1,3]) // submitted or processing , but approved
        ->orderBy('leav_auto_id','DESC')->first();
    }

     public function getAnEmployeeLastLeaveApplicationInformationByEmpAutoID($emp_auto_id){
         return LeaveApplication::where('emp_auto_id', $emp_auto_id)
        ->where('appl_status', 4) // completed, taken leave
        ->orderBy('leav_auto_id','DESC')->first();
    }

    public function getLeaveApplicationPendingRecordsForLisView($branch_office_id){
         return DB::select('CALL getLeaveApplicationRecordbyMultipleApplicationStatus1(?,?,?)',array(1,3,$branch_office_id));
    }

    public function getLeaveApplicationApprovedButSalaryPendingRecordsForListView($branch_office_id){
        return LeaveApplication::whereIn('leave_status.status_code', [10,13,15]) // application approved
            ->leftjoin('employee_infos', 'leave_applications.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->leftjoin('leave_reasons', 'leave_applications.leave_reason_id', '=', 'leave_reasons.lev_reas_id')
            ->leftjoin('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.lev_type_id')
            ->leftjoin('leave_status', 'leave_applications.appl_status', '=', 'leave_status.leav_sta_auto_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')


            ->select(
                'leave_applications.*',
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.mobile_no',
                'employee_infos.hourly_employee',
                'employee_categories.catg_name',
                'sponsors.spons_name',
                'project_infos.proj_name',
                'leave_reasons.lev_reas_name',
                'leave_types.lev_type_name',
                'leave_status.status_title'
            )
            ->get();
    }

     public function getLeaveApplicationPendingRecordsForLisViewWithPagination($request,$branch_office_id){

          $query = LeaveApplication::whereIn('appl_status', [1, 3])
            ->leftjoin('employee_infos', 'leave_applications.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->leftjoin('leave_reasons', 'leave_applications.leave_reason_id', '=', 'leave_reasons.lev_reas_id')
            ->leftjoin('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.lev_type_id')
             ->leftjoin('leave_status', 'leave_applications.appl_status', '=', 'leave_status.leav_sta_auto_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->select(
                'leave_applications.*',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.mobile_no',
                'employee_categories.catg_name',
                'sponsors.spons_name',
                'project_infos.proj_name',
                'leave_reasons.lev_reas_name',
                'leave_types.lev_type_name',
                'leave_status.status_title'
            );

        // ✅ Use column names directly
        if ($request->employee_id) {
            $query->where('employee_infos.employee_id', '=', $request->employee_id);
        }

        if ($request->start_date) {
            $query->whereDate('leave_applications.start_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('leave_applications.end_date', '<=', $request->end_date);
        }

        if ($request->application_status) {
            $query->where('leave_applications.appl_status', $request->application_status);
        }
        $query->orderBy('leave_applications.leav_auto_id', 'desc');


        $perPage = $request->per_page ?? 1000;
        $records = $query->paginate($perPage);
        return $records->items();

    }

}
