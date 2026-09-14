<?php

namespace App\Http\Controllers\DataServices;

use App\Http\Controllers\Admin\DataServices\AuthenticationDataService;
use App\Http\Controllers\Admin\DataServices\EmployeeDataService;
use App\Models\User;
use App\Models\ProjectInfo;
use App\Models\ProjectPlot;
use Carbon\Carbon;
use App\Models\UserProjectAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProjectDataService{


  /*
     ==========================================================================
     ================ Project Information Section =============================
     ==========================================================================
    */
    public $projectInfoTable = 'project_infos';

    public function generateProjectCode()
    {
        $last = ProjectInfo::latest()->first();
        if(is_null($last)){
            return 'ABC-'.substr(date('Y'),-2).'0001';
        }else if($last->proj_id <= 98){
            return  'ABC-'.substr(date('Y'),-2).'00'.($last->proj_id+1);
        }else if($last->proj_id >= 99 && $last->proj_id <= 998){
            return  'ABC-'.substr(date('Y'),-2).'0'.($last->proj_id+1);
        }else
        return  'ABC-'.substr(date('Y'),-2).($last->proj_id+1);

    }

    public function insertNewProjectInformation($proj_name, $starting_date, $address, $proj_code, $proj_budget, $proj_deadling, $proj_description,
    $working_status,$boq_clearance_duration, $contract_paper,$color_code,$branch_office_id,$created_by)
    {
        return ProjectInfo::insert([
            'proj_name' => $proj_name,
            'starting_date' => $starting_date,
            'address' => $address,
            'proj_code' => $proj_code,
            'proj_budget' => $proj_budget,
            'proj_deadling' => $proj_deadling,
            'proj_description' => $proj_description,
            'working_status' => $working_status,
            'boq_clearance_duration' => $boq_clearance_duration,
            'contract_paper' => $contract_paper,
            'color_code'=>$color_code,
            'branch_office_id' =>$branch_office_id,
            'created_by'=>$created_by,
            'created_at' => Carbon::now(),
        ]);
    }

    public function updateProjectInformation($proj_id, $proj_name, $starting_date, $address, $proj_code, $proj_budget, $proj_deadling, $proj_description,$working_status,
    $boq_clearance_duration,$status,$color_code,$updated_by)
    {

        return ProjectInfo::where('proj_id', $proj_id)->update([
            'proj_name' => $proj_name,
            'starting_date' => $starting_date,
            'address' => $address,
            'proj_code' => $proj_code,
            'proj_budget' => $proj_budget,
            'proj_deadling' => $proj_deadling,
            'working_status' => $working_status,
             'status' => $status,
            'boq_clearance_duration' => $boq_clearance_duration,
            'proj_description' => $proj_description,
            'color_code'=>$color_code,
            'updated_by' =>$updated_by,
            'updated_at' => Carbon::now(),

        ]);
    }

    public function countTotalPrjectsInRunning(){

          return ProjectInfo::where('working_status', 1)->count();
    }

     public function countTotalNumberOfRunningPrjects($branch_office_id){
        return ProjectInfo::where('branch_office_id',$branch_office_id)->where('working_status', 1)->count();
    }

    public function getAllProjectsInformationForListView($branch_office_id)
    {
        return  ProjectInfo::orderBy('status', 'DESC')->orderBy('proj_id', 'DESC')->where('branch_office_id',$branch_office_id)->get();
    }

    // will be remove soon 7.7.24
    public function getAllActiveProjectListForDropdown1()
    {
        return DB::table($this->projectInfoTable)->select('proj_id', 'proj_name')
            ->where('status', 1)->orderBy('proj_name', 'ASC')->get();
    }

        public function getAllActiveInActiveProjectListForDropdown($branch_office_id=1)
    {
        return DB::table($this->projectInfoTable)->select('proj_id', 'proj_name')
             ->where('branch_office_id',$branch_office_id)
            ->orderBy('proj_name', 'ASC')->get();
    }
      public function getAllRunningProjectListForDropdown($branch_office_id=1)
    {
        return DB::table($this->projectInfoTable)->select('proj_id', 'proj_name')
            ->where('working_status', 1)->where('branch_office_id',$branch_office_id)
            ->orderBy('proj_name', 'ASC')->get();
    }

    public function getAllActiveProjectListForDropdown($branch_office_id=1)
    {
        return DB::table($this->projectInfoTable)->select('proj_id', 'proj_name')
            ->where('status', 1)->where('branch_office_id',$branch_office_id)
            ->orderBy('proj_name', 'ASC')->get();
    }

    public function getAllProjectInformation()
    {
        return  ProjectInfo::orderBy('status', 'DESC')->get();
    }

    public function getAllProjectColorCodeArray()
    {
         $list =  ProjectInfo::select('proj_name','proj_id','color_code')->orderBy('proj_id', 'ASC')->get();
         $project_color_codes = [];
         foreach($list as $ap){
            $project_color_codes[$ap->proj_id] = $ap->color_code;
         }
       return $project_color_codes;
    }



    public function findAProjectInformation($proj_id)
    {
        return ProjectInfo::where('proj_id', $proj_id)->first();
    }

    public function findAProjectInformationWithSelectedBasicInformation($proj_id)
    {
        return ProjectInfo::select('proj_id','proj_name','status')->where('proj_id', $proj_id)->first();
    }

    public function getProjectNameByProjectId($proj_id)
    {
        return (ProjectInfo::where('proj_id', $proj_id)->first())->proj_name ?? '';
    }

    public function getProjectsInformationReportByWorkingStatus($working_status,$status)
    {
       // dd($working_status,$status);
        $query = ProjectInfo::orderBy('proj_id', 'DESC') ;

        if($status){
              $query->where('status', $status);
        }
        if($working_status){
             $query->where('working_status', $working_status);
        }
        return $query->get();

    }
    // public function getProjectListByMultipleProjectId($project_ids){
    //     return  ProjectInfo::select('proj_id', 'proj_name')->orderBy('proj_name')->whereIn('proj_id',$project_ids)->get();
    // }
    public function getProjectListByMultipleProjectId($project_ids,$branch_office_id=1){
        return  ProjectInfo::select('proj_id', 'proj_name')->orderBy('proj_name')->where('status', 1)->
                    where('branch_office_id',$branch_office_id)->whereIn('proj_id',$project_ids)->get();
    }


    public function getActiveInactiveAllProjectsForAnnualSalaryReportByMultipleProjectId($project_ids,$branch_office_id=1){
        return  ProjectInfo::select('proj_id', 'proj_name')->orderBy('proj_name')->where('branch_office_id',$branch_office_id)->whereIn('proj_id',$project_ids)->get();
    }

   public function getAllActiveProjectIDs()
    {

       return ProjectInfo::where('status',1)->orderBy('proj_name', 'ASC')->pluck('proj_id')->toArray();

    }
    public function getAllActiveInactiveProjectIDs()
    {

       return ProjectInfo::orderBy('proj_name', 'ASC')->pluck('proj_id')->toArray();

    }

    public function getLoginUserAccessPermissionProjectIDsArray($user_id)
    {

       return UserProjectAccess::where('access_status',1)->where('user_auto_id', $user_id)
                ->pluck('proj_id')->toArray();

    }

    public function getAllActiveProjectIDOfABranchOfficeAsArray($branch_office_id)
    {
       return ProjectInfo::where('branch_office_id',$branch_office_id)->where('status',1)->orderBy('proj_name', 'ASC')->pluck('proj_id')->toArray();
    }
     public function getAllRunningProjectIDOfABranchOfficeAsArray($branch_office_id)
    {
       return ProjectInfo::where('branch_office_id',$branch_office_id)->where('working_status', 1)->where('status',1)->orderBy('proj_name', 'ASC')->pluck('proj_id')->toArray();

    }


    public function findLoginUserProject()
    {
        $loginUserId = Auth::user()->id;
        $user = (new AuthenticationDataService())->findUserById($loginUserId);
        $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmail($user->email);

        if ($employee) {
            $project  = $this->findAProjectInformation($employee->project_id);
            if ($project == null) {
                return $this->getAllActiveProjectListForDropdown();
            }
            return [$project];
        } else {
            return $this->getAllActiveProjectListForDropdown();
        }
    }




     /*
     ==========================================================================
     ================ Project Plot Information ================================
     ==========================================================================
    */

    public function insertProjectNewPlotInformations($project_auto_id, $plot_name,$created_by){

         if ($this->checkThisPlotUnderThisProjectIsExist($project_auto_id, $plot_name)) {
             return 0;
         } else {
            return ProjectPlot::insert([
                'proj_auto_id' => $project_auto_id,
                'plo_name' => $plot_name,
                'insert_by' => $created_by,
                'created_at' => Carbon::now(),
            ]);
        }
    }

    public function checkThisPlotUnderThisProjectIsExist($project_auto_id, $plot_name){

        $info = ProjectPlot::where('proj_auto_id',$project_auto_id)->whereRaw('LOWER(plo_name) =?',[$plot_name])->get();
        if(count($info))
             return true;
        return false;
    }

    public function getAllActiveProjectPlotListForDropdown($project_auto_id)
    {
        if($project_auto_id){
            return ProjectPlot::select('plo_name', 'pro_plo_auto_id')->where('proj_auto_id',$project_auto_id)
            ->where('status', 1)->orderBy('plo_name', 'ASC')->get();
        }
        return ProjectPlot::select('plo_name', 'pro_plo_auto_id')
            ->where('status', 1)->orderBy('plo_name', 'ASC')->get();
    }

    public function getAPotRecordByProjectIdForDropdownList($project_auto_id)
    {
            return ProjectPlot::select('plo_name', 'pro_plo_auto_id')->where('proj_auto_id',$project_auto_id)
            ->where('status', 1)->orderBy('plo_name', 'ASC')->get();

    }



    /*
     ==========================================================================
     ================ User Project Access Permission ==========================
     ==========================================================================
    */


     public function getLoginUserAssingedProjectForDropdownList($user_id){
         return UserProjectAccess::select('project_infos.proj_id','project_infos.proj_name')
                            ->leftjoin('users', 'user_project_accesses.user_auto_id', '=', 'users.id')
                            ->leftjoin('project_infos', 'user_project_accesses.proj_id', '=', 'project_infos.proj_id')  ->where('user_project_accesses.access_status',1)
                            ->where('users.id',$user_id)->where('users.status',1)->where('project_infos.status',1)->where('project_infos.working_status',1)
                            ->orderBy('project_infos.proj_name','asc')->get();
    }


     public function getUsersProjectAccessInformaitions($limit){
        return UserProjectAccess::leftjoin('users', 'user_project_accesses.user_auto_id', '=', 'users.id')
                  ->leftjoin('employee_infos', 'users.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                             ->orderBy('user_proj_acc_auto_id', 'DESC')->take($limit)->get();
    }
    public function getActiveUsersProjectAccessInformations(){
        return UserProjectAccess::where('access_status', 1)->orderBy('user_proj_acc_auto_id', 'DESC')->get();
    }


    public function getUsersProjectAccessDetailsInformations($user_id){
        return UserProjectAccess::
         leftjoin('project_infos', 'user_project_accesses.proj_id', '=', 'project_infos.proj_id')
        ->leftjoin('users', 'user_project_accesses.user_auto_id', '=', 'users.id')
        ->leftjoin('employee_infos', 'users.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->where('user_auto_id', $user_id)->orderBy('proj_name','ASC')->get();

    }

    public function getAProjectAccessPermissionUsers($project_id){


        return UserProjectAccess::
         leftjoin('project_infos', 'user_project_accesses.proj_id', '=', 'project_infos.proj_id')
        ->leftjoin('users', 'user_project_accesses.user_auto_id', '=', 'users.id')
        ->leftjoin('employee_infos', 'users.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->where('user_project_accesses.proj_id', $project_id)->orderBy('proj_name','ASC')->get();

    }



    public function checkThisUserProjectAccessPermissionIsExist($user_auto_id, $proj_id){

               return UserProjectAccess::where('user_auto_id', $user_auto_id)->where('proj_id', $proj_id)->count() > 0 ? true : false;

    }
    public function insertUserProjectAccessInformations($user_auto_id, $proj_id,$created_by){

        if ($this->checkThisUserProjectAccessPermissionIsExist($user_auto_id, $proj_id)) {
            return 0;
        } else {
            return UserProjectAccess::insert([
                'user_auto_id' => $user_auto_id,
                'proj_id' => $proj_id,
                'insert_by' => $created_by,
                'created_at' => Carbon::now(),
            ]);
        }
    }
    public function updateUserProjectAccessStatusAsNotAllowed($proAccessId){
        return UserProjectAccess::where('user_proj_acc_auto_id', $proAccessId)->update([
            'access_status' => 0,
             'update_by'=> Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }
    public function updateUserProjectAccessStatusAsAllowed($proAccessId){
        return UserProjectAccess::where('user_proj_acc_auto_id', $proAccessId)->update([
            'access_status' => 1,
             'update_by'=> Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }




}
