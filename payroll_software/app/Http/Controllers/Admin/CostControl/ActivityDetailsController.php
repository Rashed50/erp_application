<?php

namespace App\Http\Controllers\Admin\CostControl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\CostControlDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ActivityDetailsController extends Controller
{ 
   
     // Middleware
   function __construct()
   {
      //$this->middleware('permission:project-add', ['only' => ['index']]);
      $this->middleware('permission:cost_controll_activity_element_insert', ['only' => ['index','storeActivityElementRequest']]);
      $this->middleware('permission:cost_controll_activity_name_insert', ['only' => ['index', 'storeActivityNameRequest']]);
      $this->middleware('permission:cost_controll_activity_details_record_insert', ['only' => ['loadActivityDetailsInsertUserInterface', 'storeNewActivityDetailsInformation','deleteAnActivityDetailsRecord']]);
      $this->middleware('permission:cost_controll_activity_report', ['only' => ['loadCostControllReportProcessingUI', 'getProjectPlotActivityElementsWiseActivityDetailsReport','multipleEmployeeWiseActivityDetailsReport']]);
      
   
   }


  
  /*
    |--------------------------------------------------------------------------
    |                     Activity Element
    |--------------------------------------------------------------------------
    */

  public function storeActivityElementRequest(Request $req)
  {
   
    $this->validate($req, [
      'activity_element_name' => 'required',
     // 'activity_element_code' => 'required'      
    ], [
      'activity_element_name' => 'Please Input Activity ELement',
     // 'activity_element_code' => 'Please Input Plot Name'
    ]);

      $insert = (new CostControlDataService())->insertNewActivityElementInformation(
        $req->activity_element_name,
       // $req->activity_element_code, 
        Auth::user()->id
      );

    if($insert) {
      Session::flash('success', 'Successfully Inserted');
      return redirect()->back();
    } else {
      Session::flash('error', 'Operation Failed. Activity Element may be Already Exist, Please Check');
      return redirect()->back();
    }


  }

  


    /*
    |--------------------------------------------------------------------------
    |                     Activity Name
    |--------------------------------------------------------------------------
    */

    public function storeActivityNameRequest(Request $req)
    {
    
      $this->validate($req, [
        'activity_name' => 'required',
       // 'activity_element_code' => 'required'      
      ], [
        'activity_name' => 'Please Input Activity Name',
       // 'activity_element_code' => 'Please Input Plot Name'
      ]);
    
        $insert = (new CostControlDataService())->insertNewActivityName(
          $req->activity_name,
         // $req->activity_element_code, 
          Auth::user()->id
        );
   
      if($insert) {
        Session::flash('success', 'Successfully Inserted');
        return redirect()->back();
      } else {
        Session::flash('error', 'Operation Failed. Activity Name may be Already Exist, Please Check');
        return redirect()->back();
      }
  
  
    }
  

    /*
    |--------------------------------------------------------------------------
    |                     Activity Details Information
    |--------------------------------------------------------------------------
    */

    public function storeNewActivityDetailsInformation(Request $req)
    {
 
        $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($req->emp_id);
        
        if($emp == null){
          Session::flash('error', 'Operation Failed. Employee Not Found');
          return redirect()->back();
        }
       
        $insert = (new CostControlDataService())->insertNewActivityDetailsInformation(
          $req->proj_name,
          $req->plot_name, 
          $req->activity_element,
          $req->activity_name, 
          $emp->emp_auto_id,
          $req->total_emp, 
          $req->working_hours,
          $req->total_hours, 
          $req->working_shift,
          $req->working_date, 
          $req->remarks, 
          Auth::user()->id
        );
     
      if($insert) {
        Session::flash('success', 'Successfully Saved');
        return redirect()->back();
      } else {
        Session::flash('error', 'Operation Failed. Please Input All Required Information');
        return redirect()->back();
      } 
  
    }
    public function deleteAnActivityDetailsRecord($act_det_rec_auto_id)
    {  
      
      $delete = (new CostControlDataService())->deleteAnActivityDetailsRecords($act_det_rec_auto_id);     
      if($delete) {
        Session::flash('success', 'Successfully Saved');
        return redirect()->back();
      } else {
        Session::flash('error', 'Operation Failed. Please Input All Required Information');
        return redirect()->back();
      } 
  
    }


    /*
    |--------------------------------------------------------------------------
    |  COST CONTROLL REPORT OPERATION
    |--------------------------------------------------------------------------
    */

    public function multipleEmployeeWiseActivityDetailsReport(Request $request){

        $allEmplId = explode(",", $request->multiple_employee_Id);
        $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
        $records = (new CostControlDataService())->getMultipleEmployeeWiseActivityDetailsReport($allEmplId);
        $company = (new CompanyDataService())->findCompanryProfile();
        $report_title = "Multiple Employees Activities Details";
         return view('admin.report.cost_control.emp_id_activity_details_report', compact('records','company','report_title'));
    }

    public function getProjectPlotActivityElementsWiseActivityDetailsReport(Request $request){

     
      $records = (new CostControlDataService())->getProjectPlotActivityElementWiseActivityDetailsReport([$request->project_id_list],[$request->plot_name],
      $request->activity_element_list,$request->activity_name_list);
      $company = (new CompanyDataService())->findCompanryProfile();
      $report_title = "Multiple Employees Activities Details";
       return view('admin.report.cost_control.emp_id_activity_details_report', compact('records','company','report_title'));
  }



  /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

  public function index()
  {    
    $activity_elements =  (new CostControlDataService())->getAllActiveActivityElemenListForDropdown();  
    $activity_names =  (new CostControlDataService())->getAllActiveActivityNameListForDropdown();
    return view('admin.cost_control.create', compact('activity_elements','activity_names'));
  }

  public function loadActivityDetailsInsertUserInterface()
  {    
  
    $projects = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
    //  $plots = (new ProjectDataService())->getAllActiveProjectPlotListForDropdown(null);
    $activity_elements =  (new CostControlDataService())->getAllActiveActivityElemenListForDropdown();  
    $activity_names =  (new CostControlDataService())->getAllActiveActivityNameListForDropdown();
    $activity_details_records =  (new CostControlDataService())->getActivityDetailsRecords(20);
    return view('admin.cost_control.activity_detail_index', compact('projects','activity_elements','activity_names','activity_details_records'));
  }

  public function loadCostControllReportProcessingUI(){

    $projects = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
    $plots = (new ProjectDataService())->getAllActiveProjectPlotListForDropdown(null);
    $activity_elements =  (new CostControlDataService())->getAllActiveActivityElemenListForDropdown();  
    $activity_names =  (new CostControlDataService())->getAllActiveActivityNameListForDropdown(); 
    return view('admin.report.cost_control.cost_control_report_processing_ui', compact('projects','activity_elements','activity_names','plots'));

   }




  

    
}
