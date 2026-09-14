<?php

namespace Modules\HrManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\{LeaveApplicationDataService,EmployeeDataService,CompanyDataService};
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Session;
use DateTime;
use Auth;

class LeaveApplicationController extends Controller
{

    function __construct(){
        $this->middleware('permission:leave_application_submit',['only'=>['index','insert','processEmployeeLeaveApplicationFormByRequestedParameter']]);  //
        $this->middleware('permission:leave_application_update',['only'=>['getLeaveApplicationPendingList','getALeaveApplicationRecord','updateALeaveApplicationRecord']]);  //
        $this->middleware('permission:leave_application_rejection',['only'=>['rejectALeaveApplication']]);  // delete application by status udpate

    }


  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

  public function index()
  {

    $leave_reasons = (new LeaveApplicationDataService())->getLeaveReasonRecordsForDropdown();
    $application_status = (new LeaveApplicationDataService())->getLeaveApplicationStatusForDropdown();
    return view('hrmanagement::pages.leave_application.leave_application',[
         'data' =>[
            'leave_reasons'=>$leave_reasons,
            'application_status' =>$application_status,

            ]
        ]);


  }




  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */
  public function insert(Request $request)
  {

        try{

            $this->validate($request, [
              'app_employee_id'=>'required',
              'leave_reason_id' => 'required',
              'start_date' => 'required',
              'end_date' => 'required',
              'leave_paper' => 'required',
              'leave_contract' => 'required',
            ], []);


           // return response()->json(['success'=>true,'status'=>203,'message'=>'','a'=>$request->all()]);
            $first = new DateTime($request->start_date);
            $last = new DateTime($request->end_date);

            $leave_days = $last->diff($first);
            $leave_days = $leave_days->format('%a');
            $login_id = Auth::user()->id;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($request->app_employee_id);

             if($anEmployee){

                $uplodedPath  = "";
                if ($request->hasFile('leave_paper')) {
                  $file = $request->file('leave_paper');
                  $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeLeaveApplicationPaper($file, null);
                }

                $application_id = (new LeaveApplicationDataService())->insertLeaveApplicationInformation($anEmployee->emp_auto_id,1,$request->leave_reason_id,$leave_days,$request->app_date
                ,$request->start_date,$request->end_date,$login_id,$request->application_status,$request->remarks,$request->reference_by,$uplodedPath);

                (new EmployeeDataService())->updateAnEmployeeLeaveContractDuration($anEmployee->emp_auto_id,$request->leave_contract);

                if ($application_id) {
                     return response()->json(['success'=>true,'status'=>200,'message'=>'Successfully Submitted', 'data'=>$request->all()]);
                } else {
                    return response()->json(['success'=>false,'status'=>403,'message'=>'Successfully Submitted', 'data'=>$request->all()]);
                }

            }
        }catch(Exception $ex){
                 return response()->json(['success'=>false,'status'=>403,'message'=>'Operation Failed, Please try again' ,'error'=>$ex]);;
        }




  }



  public function getLeaveApplications(Request $request)
    {

      if($request->employee_id && (Auth::user()->id == 40  || Auth::user()->id ==56 )){
          // id 40 = IBRAHIM ADEL RAJEH SAAD, 56 =  IBRAHIM MOHAMED IBRAHIM

            $login_id = Auth::user()->id;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->employee_id);

             if($anEmployee){


                $arecords = (new LeaveApplicationDataService())->findAnEmployeeLeaveApplicationInformationByEmpAutoID($anEmployee->emp_auto_id, Auth::user()->branch_office_id);
                if($arecords== null){
                    $today = Carbon::now();
                    $arecords = (new LeaveApplicationDataService())->insertLeaveApplicationInformation($anEmployee->emp_auto_id,1,1,40,$today,$today,$today,$login_id,1,null,null,null);
                }

             }
        }

        $records = (new LeaveApplicationDataService())->getLeaveApplicationPendingRecordsForLisViewWithPagination($request, Auth::user()->branch_office_id);
        return response()->json([
            'data' =>  $records,
            'pagination' => [
                'current_page' =>1, // $records->currentPage(),
                'last_page' =>1,// $records->lastPage(),
                'per_page' =>1000,// $records->perPage(),
                'total' =>count($records),// $records->total(),
                'from' =>1,// $records->firstItem(),
                'to' =>1,// $records->lastItem()
            ],
            'status'=>200,'success'=>true,
        ]);
    }
  public function getLeaveApprovedButSalaryPendingApplications(Request $request)
    {

        $records = (new LeaveApplicationDataService())->getLeaveApplicationApprovedButSalaryPendingRecordsForListView($request, Auth::user()->branch_office_id);
        return response()->json([
            'data' =>  $records,
            'pagination' => [
                'current_page' =>1, // $records->currentPage(),
                'last_page' =>1,// $records->lastPage(),
                'per_page' =>1000,// $records->perPage(),
                'total' =>count($records),// $records->total(),
                'from' =>1,// $records->firstItem(),
                'to' =>1,// $records->lastItem()
            ],
            'status'=>200,'success'=>true,
        ]);
    }





  public function getALeaveApplicationRecord(Request $request){
    try{

      $record = (new LeaveApplicationDataService())->getLeaveApplicationDetailsByLeaveAutoId((int)$request->leav_auto_id);
      if(count($record)){
        $record = $record[0];
      }
      return response()->json(['success'=>true,'status'=>200,'message'=>'',"data"=>$record,'a'=>$request->leav_auto_id]);

    }catch(Exception $ex){
      return response()->json(['success'=>false,'status'=>404,'message'=>'Operation Failed, Please Try Again','error'=>"error"]);

    }
  }

  public function updateALeaveApplicationRecord(Request $request)
  {

        try{


                $first = new DateTime($request->start_date);
                $last = new DateTime($request->end_date);
                $leave_days = $last->diff($first);
                $leave_days = $leave_days->format('%a');
                $login_id = Auth::user()->id;

                $uplodedPath  = "";
                if ($request->hasFile('exit_paper')) {
                    $file = $request->file('exit_paper');
                    $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeLeaveApplicationPaper($file, null);
                    (new LeaveApplicationDataService())->updateLeaveApplicationExitPaperPath($request->leav_auto_id,$uplodedPath);
                    $request->application_status = 4; // if upload exit paper that means application approved
                    // we have to hanle UI dropdown salary closed option later
                }

                $application_id = (new LeaveApplicationDataService())->updateLeaveApplicationInformationByAdmin($request->leav_auto_id,$request->leave_reason_id,$leave_days
                ,$request->start_date,$request->end_date,$login_id,$request->application_status,$request->admin_comments,$uplodedPath);

                if($application_id){
                    return response()->json(['success'=>true,'status'=>200,'message'=>'Successfully Updated',"error"=>'error','a'=>$request->all()]);
                }else{
                    return response()->json(['success'=>false,'status'=>404,'message'=>'Update Operation Failed, Please Try Again',"error"=>'error','dd'=>$request->all()]);
                }

        }catch(Exception $ex){
                    return response()->json(['success'=>false,'status'=>404,'message'=>'Update Operation Failed, Please Try Again',"error"=>'error','dd'=>$request->all()]);

        }

  }

  public function rejectALeaveApplication($leav_auto_id){

    try{

      $isSuccess = (new LeaveApplicationDataService())->rejectALeaveApplication((int)$leav_auto_id,Auth::user()->id);
      if($isSuccess){
        return response()->json(['success'=>true,'status'=>200,'message'=>'Successfully Deleted',"error"=>'error','a'=>$leav_auto_id]);
      }else{
        return response()->json(['success'=>false,'status'=>404,'message'=>'Delete Operation Failed, Please Try Again',"error"=>'error','a'=>$leav_auto_id]);
      }

    }catch(Exception $ex){
      return response()->json(['success'=>false,'status'=>404,'message'=>'Operation Failed, Please Try Again','error'=>"error"]);

    }
  }



  // Print Preview Leave Application Form

  public function processEmployeeLeaveApplicationFormByRequestedParameter(Request $request)
{
    try{

        $company = (new CompanyDataService())->findCompanryProfile();
        $prepared_by = Auth::user()->name;

        if($request->form_type == 1){
            $company = (new CompanyDataService())->findCompanryProfile();
            return view('admin.leave.leave_application_blank_form',compact('company','prepared_by'));

        }else{

            $searchByDb_Column = $request->searchType;
            $employee_searching_value = $request->searchValue;

            $employeeInfo = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_searching_value, $searchByDb_Column,Auth::user()->branch_office_id);
            if (count($employeeInfo) <= 0) {
                return "Employee Not Found, Please Input Correct Information";
            }
            $employee = $employeeInfo[0];
            $leave_last_record = (new LeaveApplicationDataService())->getAnEmployeeLastLeaveApplicationInformationByEmpAutoID($employee->emp_auto_id);
            return view('admin.leave.leave_application_form', compact('employee','company', 'leave_last_record'));
        }

    }catch(Exception $ex){
        return "System Operation Failed, Please try again ";
    }



}









  /* ======================================================================== */
}
