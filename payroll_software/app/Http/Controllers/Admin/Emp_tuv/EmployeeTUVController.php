<?php

namespace App\Http\Controllers\Admin\Emp_tuv;

use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmpTUVInfoDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
 use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;


class EmployeeTUVController extends Controller
{
    function __construct(){
        
        $this->middleware('permission:emp_tuv_information_insert',['only'=>['loadEmployeeTUVInsertForm','insertEmployeeTUVInformationsRequest']]);
    } 

    /* ==================== Employee TUV Certifications Related Works ==================== */
    public function loadEmployeeTUVInsertForm(){
        $designations =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        return view('admin.emp_tuv.index',compact('designations'));
    }
  
    public function insertEmployeeTUVInformationsRequest(Request $request){

        try{

            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
            if($request->company_id == "" || $request->catg_id == "" || $request->tvu_card_no == "" || $request->tuv_photo == ""){
                Session::flash('error', 'Please Select Company Name, Designation, TUV Photo & TUV Card No'); //
                return redirect()->back();
            }else if($anEmployee) {

                $tuvID = (new EmpTUVInfoDataService())->insertAnEmployeeTUVCardInformation($request->emp_auto_id, $request->tvu_card_no, $request->catg_id,$request->company_id,$request->tvu_issue_date,$request->tvu_expire_date,
                 Auth::user()->id, $anEmployee->branch_office_id);
            }else {
                Session::flash('error', 'Employee Not Found');
                return redirect()->back();
            }

            if ($tuvID) {
                $UploadDownloadConObj = new  UploadDownloadController();
                if ($request->hasFile('tuv_photo')) {
                    $file = $request->file('tuv_photo');
                    $uplodedPath = $UploadDownloadConObj->uploadEmployeeTUVPhoto($file, null);
                    (new EmpTUVInfoDataService())->updateEmployeeTUVPhotoDbPath($tuvID, $uplodedPath , 'emp_tuv_photo');
                }

                Session::flash('success', 'Successfuly Saved');
                  return $this->loadEmployeeTUVInsertForm();
            } else {
                Session::flash('error', 'Operation Failed, Maybe This Is Employee Info Already Exist');
                return redirect()->back();
            }
        }catch(Exception $ex){
            Session::flash('error', 'Operation Failed');
            return redirect()->back();
        }

    }
    
    
}
