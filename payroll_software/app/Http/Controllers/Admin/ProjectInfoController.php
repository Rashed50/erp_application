<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use App\Models\ProjectInfo;
use App\Models\EmployeeInfo;
use App\Models\ProjectImgUpload;
use Carbon\Carbon;
use Exception;
use DateTime;
use Session;
use Image;
use Auth;



class ProjectInfoController extends Controller
{

     // Middleware
   function __construct()
   {
    // $this->middleware('permission:project-add', ['only' => ['index', 'insert','storeProjectNewPlotInformation']]);
     $this->middleware('permission:project-add', ['only' => ['index', 'insert']]);
     $this->middleware('permission:project_new_plot_add', ['only' => ['storeProjectNewPlotInformation']]);
     $this->middleware('permission:project-edit', ['only' => ['edit', 'update']]);
     $this->middleware('permission:project-delete', ['only' => ['delete']]);
     $this->middleware('permission:projectincharge-list', ['only' => ['InsertProjectInchage','addProjectInchage']]);
     $this->middleware('permission:projectimage-list', ['only' => ['loadProjectImageUPloadUI','loadProjectImageUPloadRequest','deleteUploadedProjectImageInformation']]);


   }


  /*
    |--------------------------------------------------------------------------
    |  DATABASE OPERATION
    |--------------------------------------------------------------------------
    */


  public function getMultipleImage($proj_id)
  {
    return $find = (new EmployeeRelatedDataService())->getProjectMultipleImage($proj_id);
  }

  public function findEmployee(Request $request)
  {
    $emp_id = $request->emp_id;
    $findEmployee = EmployeeInfo::with('department', 'category')->where('employee_id', $emp_id)->first();
    if ($findEmployee) {
      return response()->json(['findEmployee' => $findEmployee]);
    } else {
      return response()->json(['status' => 'error']);
    }
  }
  /* FIND VALID Employee */
  public function validEmployee(Request $request)
  {
    $emp_id = $request->emp_id;
    $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($emp_id);
    if ($findEmployee) {
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }
  /* DELETE Project */
  public function delete($id)
  {
    $delete = (new EmployeeRelatedDataService())->deleteProject($id);// project status inactive

    if ($delete) {
      Session::flash('success', 'Successfully Inactivated The Project');
      return redirect()->back();
    } else {
      Session::flash('error', 'Operation Failed to Inactivated the Project');
      return redirect()->back();
    }
  }


 /* INSERT Project */
  public function insert(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'proj_name' => 'required',
      'starting_date' => 'required',
      'address' => 'required',
      'proj_code' => 'required',
      'proj_budget' => 'required',
      'proj_deadling' => 'required',
    ], [
      'proj_budget' => 'Please Input All Required Data'
    ]);




        $uplodPath = "";
       // Iqama File
       if ($req->hasFile('agreement_file')) {
            $file = $req->file('agreement_file');
            $uplodPath =  (new  UploadDownloadController())->uploadProjectContractPaper($file, null);
      }

      $insert = (new ProjectDataService())->insertNewProjectInformation(
        $req->proj_name,
        $req->starting_date,
        $req->address,
        $req->proj_code,
        $req->proj_budget,
        $req->proj_deadling,
        $req->proj_description,
        $req->working_status == "on" ? 1: 0,
        $req->boq_clearance_duration,
        $uplodPath,
        $req->color_code,
        Auth::user()->branch_office_id,
        Auth::user()->id
      );


    if ($insert) {
      Session::flash('success', 'Successfully Added');
      return redirect()->back();
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }


  /* UPDATE Project */
  public function update(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'proj_name' => 'required',
      'starting_date' => 'required',
      'address' => 'required',
      'proj_code' => 'required',
      'proj_budget' => 'required',
      'proj_deadling' => 'required',
    ], [
      'proj_budget' => 'Please Input All Required Data'
    ]);
    /* insert data in database */


    $id = $req->id;
    $update = (new ProjectDataService())->updateProjectInformation(
      $id,
      $req->proj_name,
      $req->starting_date,
      $req->address,
      $req->proj_code,
      $req->proj_budget,
      $req->proj_deadling,
      $req->proj_description ?? " ",
      $req->working_status == "on" ? 1:0, // 0 = completed, 1 = running
      $req->boq_clearance_duration,
      $req->project_status,
      $req->color_code,
      Auth::user()->id
    );


    if ($update) {
      Session::flash('success', 'Successfully Updated');
      return redirect()->route('project-info');
    } else {
      Session::flash('error', 'Update Operation Failed');
      return redirect()->back();
    }


  }




  /* Insert Project In-charge */
  public function InsertProjectInchage(Request $request)
  {
    $project_id = $request->proj_name;
    $emp_id = $request->emp_id;
    /* insert Project incharge */
    $update = (new EmployeeRelatedDataService())->assignProjectIncharge($project_id, $emp_id);

    /* redirect back */
    if ($update) {

      Session::flash('success_incharge', 'value');
      return redirect()->route('project-info');
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }

  // Project List Report

    public function generateProjectReport(Request $request){
        try {

            $project_records =  (new ProjectDataService())->getProjectsInformationReportByWorkingStatus($request->project_working_status,$request->project_status);
            $report_title  = '';
            $company = (new CompanyDataService())->findCompanryProfile();
            return view('admin.project-info.projects_list_report', compact('project_records','report_title','company'));

        } catch (Exception $e) {
            return "Operation Failed. Reload the page and please try again " ;
        }
    }



  /*
    |--------------------------------------------------------------------------
    |  Plot Information
    |--------------------------------------------------------------------------
    */

  public function storeProjectNewPlotInformation(Request $req)
  {

    $this->validate($req, [
      'project_auto_id' => 'required',
      'plot_name' => 'required'
    ], [
      'project_auto_id' => 'Please Select Project',
      'plot_name' => 'Please Input Plot Name'
    ]);

      $insert = (new ProjectDataService())->insertProjectNewPlotInformations(
        $req->project_auto_id,
        $req->plot_name,
        Auth::user()->id
      );

    if($insert) {
      Session::flash('success', 'Successfully Inserted');
      return redirect()->back();
    } else {
      Session::flash('error', 'Operation Failed. Plot Name may be Already Exist, Please Input Information Correctly');
      return redirect()->back();
    }


  }

  public function getPlotInformationByProjectId($project_id){

      $arecord = (new ProjectDataService())->getAPotRecordByProjectIdForDropdownList($project_id);
      if ($arecord) {
        return response()->json(['status' =>200,'success'=>true, 'data' => $arecord]);
      } else {
        return response()->json(['status' =>404,'success'=>false, 'error' => 'Record Not Found']);
      }
  }



  /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

  public function index()
  {

    $all =  (new ProjectDataService())->getAllProjectsInformationForListView(Auth::user()->branch_office_id);
    $project_code = (new ProjectDataService())->generateProjectCode();
    return view('admin.project-info.index', compact('all','project_code'));


  }


  public function add()
  {
    return view('admin.project-info.add');
  }

  public function edit($id)
  {
    $edit = (new ProjectDataService())->findAProjectInformation($id);
    return view('admin.project-info.edit', compact('edit'));
  }

  public function view($id)
  {
    $view = (new ProjectDataService())->findAProjectInformation($id);
    return view('admin.project-info.view', compact('view'));
  }

  public function addProjectInchage()
  {
    $allProject = (new ProjectDataService())->getAllProjectInformation();
    return view('admin.project-info.project-incharge', compact('allProject'));
  }



    /*
        |--------------------------------------------------------------------------
        |  Project Image Upload Section
        |--------------------------------------------------------------------------
    */


  public function loadProjectImageUPloadUI(){
     $getProject =  (new ProjectDataService())->getAllActiveProjectListForDropdown();
     return view('admin.project-info.images-upload.all',compact('getProject'));
   }

   public function searchProjectImage(Request $request){
       $project_id = $request->project_id;
       $findImage = ProjectImgUpload::where('project_id',$project_id)->get();
       return response()->json([ 'findImage' => $findImage]);
   }

   public function deleteUploadedProjectImageInformation($id){
     $delete = ProjectImgUpload::where('proj_img_id',$id)->delete();
     $old_image = ProjectImgUpload::where('proj_img_id',$id)->first();
     unlink($old_image->photo_path);
   }

   public function loadProjectImageUPloadRequest(Request $request){

     $images = $request->file('project_image');

     foreach ($images as $img) {
         $make_name = 'project_image'.time().uniqid().'.'.$img->getClientOriginalExtension();
         Image::make($img)->resize(450,500)->save('uploads/project/mult-image/'.$make_name);
         $uplodPath = 'uploads/project/mult-image/'.$make_name;

         $uploader = Auth::user()->id;
         $insert =  ProjectImgUpload::insert([
             'project_id' => $request->project_id,
             'photo_path' => $uplodPath,
             'uploaded_by_id' => $uploader,
             'created_at' => Carbon::now(),
         ]);
     }

     if($insert){
       Session::flash('success','Successfully Uploaded');
       return redirect()->back();
     } else{
       Session::flash('error','Operation Failed');
       return redirect()->back();
     }


   }





}
