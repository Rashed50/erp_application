<?php

namespace App\Http\Controllers\Admin\UserProjectAssign;

use App\Http\Controllers\Controller;

use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService; 
use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;
use Intervention\Image\Facades\Image;

class UserProjectAssignController extends Controller
{ 
 
  function __construct()
    {
        $this->middleware('permission:user_project_access_permission_edit', ['only' => ['userProjectAccessInformationAsAllowed','userProjectAccessInformationAsNotAllowed','saveAndUpdateTimekeeperAttendanceINOUTPermission']]);
        $this->middleware('permission:user_project_access_permission', ['only' => ['userProjectAccessInformationUI','userProjAccessInfoInsertRequest']]);
    }

  // Ajax Request For Single Employee Project Access Details Informations
  public function employeeProjectAccessInfoDetailsWithAjaxRequest(Request $request){
      
      
      if($request->project_id != ''){
            $findEmpWithProjectAccsDetails = (new ProjectDataService())->getAProjectAccessPermissionUsers($request->project_id);
      }else{

            $findEmp = (new AuthenticationDataService())->findAnUserDetailsByEmployeeId($request->empId);
            if($findEmp == null){
                return json_encode(['success' => false,'status_code' => 404, 'error' => 'No project access granted for the user','da'=>$request->all()]);
            }
            $findEmpWithProjectAccsDetails = (new ProjectDataService())->getUsersProjectAccessDetailsInformations($findEmp->id);
      }

      if (count($findEmpWithProjectAccsDetails) > 0) {
            return json_encode(['success' => true, 'status' => 200, 'data' => $findEmpWithProjectAccsDetails,'error'=>null]);
      } else {
            return json_encode(['success' => false, 'status' => 404, 'error' => 'Project Not Assigned to the user']);
      }
      
  }
 
 // Project Access Permission UI Loading
  public function userProjectAccessInformationUI(){
      
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $users = (new AuthenticationDataService())->getAllActiveUsersForDropdownList();
    $records = array();// (new ProjectDataService())->getUsersProjectAccessInformaitions(20);
    return view('admin.project-info.access-permission.employee_project_access', compact('users', 'projects', 'records'));
  }

  public function userProjAccessInfoInsertRequest(Request $request){
     
        $project_id_list = $request->proj_id;
        if( $project_id_list != null && $request->user_id != null){
              foreach ($project_id_list as $proj_id) {
                   
                  (new ProjectDataService())->insertUserProjectAccessInformations($request->user_id, $proj_id,Auth::user()->id);

              }
              Session::flash('success', 'Successfully! Allowed Project Access  ');
              return redirect()->back();
         }else {
            Session::flash('error', 'Please Select User and Project Name');
            return redirect()->back();
          }
    
    
  }

  public function userProjectAccessInformationAsNotAllowed($projAccessId){
    $DeActive = (new ProjectDataService())->updateUserProjectAccessStatusAsNotAllowed($projAccessId);

    
    if ($DeActive) {
        Session::flash('success', 'Successfully! Deactivated Employee Project Access Informations');
        return $this->userProjectAccessInformationUI() ; 
    } else {
        Session::flash('error', 'Operation Failed ');
        return redirect()->back();
    }
  }

  public function userProjectAccessInformationAsAllowed($projAccessId){
    $Active = (new ProjectDataService())->updateUserProjectAccessStatusAsAllowed($projAccessId);
 
    if ($Active) {
        Session::flash('success', 'Successfully! Completed');
        return  $this->userProjectAccessInformationUI() ;
    } else {
        Session::flash('error', 'Operation Failed ');
        return redirect()->back();
    }
  }
  
      /*
        |--------------------------------------------------------------------------
        |  Timekeeper Attendance IN OUT Permission
        |--------------------------------------------------------------------------
    */

    public function saveAndUpdateTimekeeperAttendanceINOUTPermission(Request $request){

      $aiop_ids = $request->aiop_id;
      $days =(int) $request->allow_days;

       if( $days >0 ){
            foreach ($aiop_ids as $aid) {
                (new EmployeeAttendanceDataService())->updateAttendanceINOUTPermissionDaysValueByAutoId($aid,$days);
            }
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
       }else {
          Session::flash('error', 'Please Input Correct Value');
          return redirect()->back();
        }
    }

 

 
}
