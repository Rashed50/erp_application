<?php

namespace App\Http\Controllers\Admin\Vehicle;

use App\Http\Controllers\Admin\AdvancePayController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\TransportationDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Http\Request;
use App\Models\VehicleFineRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function GuzzleHttp\Promise\all;

class VehicleController extends Controller
{


  function __construct()
  {       
       $this->middleware('permission:vichle-add', ['only' => ['index','insert','edit','updateVehicleRelatedInfo','updateVehicleRelatedImages','delete']]);
   }
   
       // Store or Update VehOwner
    public function saveVehOwner(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'veh_own_title' => 'required|string|max:255'
        ]);
    
        (new TransportationDataService())->createOrUpdateVehOwner($request->veh_typ_id,$request->veh_own_title);
  return response()->json(['message' => 'Owner saved']);   
     }
    
    // Store or Update VehType
    public function saveVehType(Request $request)
    {
            $request->validate([
                'veh_type_title' => 'required|string|max:255'
            ]);
    
         (new TransportationDataService())->createOrUpdateVehType($request->veh_typ_id,$request->veh_type_title);
    
          return response()->json(['message' => 'Vehicle Type saved']);
    }


  public function index(){
       $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    $all = (new TransportationDataService())->getAllActiveVehicaleInfos();
    
     $veh_owners =(new TransportationDataService())->getVehOwners();
    $veh_types =(new TransportationDataService())->getVehTypes();
    
    return view('admin.vechicle.all', compact('all', 'projects','veh_owners','veh_types'));
  }

  public function insert(Request $request){

     if($request->veh_model_number == null){
      $request->veh_model_number = "";
     }
     if($request->veh_brand_name == null){
      $request->veh_brand_name = "";
     }
      if((new TransportationDataService())->checkThisVehicleAlreadyExist($request->veh_plate_number)){
        Session::flash('error', 'This Plate Number Vehicle Already Exist');
        return redirect()->back();
      }

      $veh_id = (new TransportationDataService())->insertVehicleInfoWithVehicleId($request);
 
     // Vehicle Insurance Certificate
     if ($request->hasFile('veh_ins_certificate')) {
        $file = $request->file('veh_ins_certificate');
        $uplodedPath = (new  UploadDownloadController())->uploadVehicleInsuranceCertificate($file, null);
        (new TransportationDataService())->updateVehicleInsuranceCertificateDbPath($veh_id, $uplodedPath , 'veh_ins_certificate');
      }
      // Vehicle Registration Certificate
      if ($request->hasFile('veh_reg_certificate')) {
        $file = $request->file('veh_reg_certificate');
        $uplodedPath = (new  UploadDownloadController())->uploadVehicleRegistrationCertificate($file, null);
        (new TransportationDataService())->updateVehicleRegistrationCertificateDbPath($veh_id, $uplodedPath, 'veh_reg_certificate');
      }
      // Vehicle Photo
      if ($request->hasFile('veh_photo')) {
        $file = $request->file('veh_photo');
        $uplodedPath = (new  UploadDownloadController())->uploadVehiclePhoto($file, null);
        (new TransportationDataService())->updateVehiclePhotoDbPath($veh_id, $uplodedPath, 'veh_photo');
      }

      if ($veh_id) {
        Session::flash('success', 'Successfully Added Vahicle informations');
        return redirect()->back();
      } else {
        Session::flash('error', 'Something Went Wrong, Please Try Again!');
        return redirect()->back();
      }
    }

  public function edit($id){
    $edit = (new TransportationDataService())->getSingleVehicleDetails($id);
  //  dd($edit);
    return view('admin.vechicle.edit', compact('edit'));
  }

  public function updateVehicleRelatedInfo(Request $request)
  {
    $update = (new TransportationDataService())->updateSingleVehileRelatedInfo($request);

      /* redirect back */
      if ($update) {
        Session::flash('success', 'Successfully Updated Vehicle Inforamtions');
        return redirect()->route('add-new.vehicle');
      } else {
        Session::flash('error', 'value');
        return redirect()->back();
      }
  }

  public function updateVehicleRelatedImages(Request $request){
    $id = $request->veh_id;

    $find_veh_info = (new TransportationDataService())->getSingleVehicleInfoWithVehIdForImageUpdate($id);
    $veh_id = $find_veh_info->veh_id;

    $UploadDownloadConObj = new  UploadDownloadController();
    $update = false;
    // Vehicle Insurance Certificate
    if ($request->hasFile('veh_ins_certificate')) {
        $file = $request->file('veh_ins_certificate');
        $uplodedPath = $UploadDownloadConObj->uploadVehicleInsuranceCertificate($file, $find_veh_info->veh_ins_certificate);
        $update = (new TransportationDataService())->updateVehicleInsuranceCertificateDbPath($veh_id, $uplodedPath , 'veh_ins_certificate');
      }
      // Vehicle Registration Certificate
      if ($request->hasFile('veh_reg_certificate')) {
        $file = $request->file('veh_reg_certificate');
        $uplodedPath = $UploadDownloadConObj->uploadVehicleRegistrationCertificate($file, $find_veh_info->veh_reg_certificate);
        $update = (new TransportationDataService())->updateVehicleRegistrationCertificateDbPath($veh_id, $uplodedPath, 'veh_reg_certificate');
      }
      // Vehicle Photo
      if ($request->hasFile('veh_photo')) {
        $file = $request->file('veh_photo');
        $uplodedPath = $UploadDownloadConObj->uploadVehiclePhoto($file, $find_veh_info->veh_photo);
        $update = (new TransportationDataService())->updateVehiclePhotoDbPath($veh_id, $uplodedPath, 'veh_photo');
      }

    /* redirect back */
    if ($update) {
        Session::flash('success', 'Successfully Updated Vehicle Related Documents');
        return redirect()->route('add-new.vehicle');
        } else {
        Session::flash('error', 'value');
        return redirect()->back();
    }
  }

  public function delete($id){
    $delete = (new TransportationDataService())->singleVehicleStatusInActive($id);

    /* redirect back */
    if ($delete) {
      Session::flash('success', 'Successfully Deleted Vehicle Informations');
      return redirect()->back();
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }


  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */

  public function findVehicleFineRecord($id)
  {
    return VehicleFineRecord::where('vfr_auto_id', $id)->first();
  }


  public function vehicleFineFormWithRecords()
  {
    $vehicleFineRecord = VehicleFineRecord::where('vehicle_fine_records.status', 1)
      ->join('vehicles', 'vehicle_fine_records.veh_id', '=', 'vehicles.veh_id')
      ->join('employee_infos', 'vehicle_fine_records.employee_id', '=', 'employee_infos.emp_auto_id')
      ->orderBy('vfr_auto_id', 'DESC')->get();
    return view('admin.vechicle.vechicle_fine', compact('vehicleFineRecord'));
  }


  public function searchVehicleInformation(Request $request){
    $vehicle = (new TransportationDataService())->searchForVehicleInformationWithPlateNumber($request);

    if ($vehicle) {
      return  response()->json(['data' => $vehicle, 'success' => 'true', 'status_code' => 200]);
    } else {
      return response()->json(['success' => 'false', 'status_code' => '401', 'error' => 'error', 'message' => $validator->errors()]);
    }
  }

  /*
  |--------------------------------------------------------------------------
  |  Vehicle Fine OPERATION
  |--------------------------------------------------------------------------
  */

  public function editVehicleFineFormSubmit($id)
  {
    $fineRecord = VehicleFineRecord::where('status', 1)->where('vfr_auto_id', $id)->firstOrFail();
    return view('admin.vechicle.vechicle_fine_edit', compact('fineRecord'));
  }
  public function insertVehicleFineFormSubmit(Request $request)
  {
    $creator = Auth::user()->id;
    $insert = VehicleFineRecord::insertGetId([
      'employee_id' => $request['emp_auto_id'],
      'veh_id' => $request['veh_id'],
      'date' => $request['date'],
      'amount' => $request['amount'],
      'remarks' => $request['remarks'],
      'entered_id' => $creator,
      'created_at' => Carbon::now()->toDateTimeString(),
    ]);
    $amount = $request['amount'];
    // insert fine amount as advance pay to that employee
    $advPurposeType = 5;  // Vehicle Penalty Fee
    $installMonth = 1;
    $insert =  (new AdvancePayController())->insertEmployeeAdvance($request['emp_auto_id'], $advPurposeType, $amount, $installMonth, $request['remarks'], $request['date'], $creator);

     (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($request['emp_auto_id'], $amount, 2);


    if ($insert) {
      Session::flash('success', 'value');
      return redirect()->back();
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }
  public function updateVehicleFineFormSubmit(Request $request)
  {
    $id = $request['id'];
    $creator = Auth::user()->id;
    $update = VehicleFineRecord::where('status', 1)->where('vfr_auto_id', $id)->update([
      'employee_id' => $request['emp_auto_id'],
      'veh_id' => $request['veh_id'],
      'date' => $request['date'],
      'amount' => $request['amount'],
      'remarks' => $request['remarks'],
      'entered_id' => $creator,
      'updated_at' => Carbon::now()->toDateTimeString(),
    ]);

    if ($update) {
      Session::flash('success', 'value');
      return redirect()->route('add.vehicle.fine');
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }
  public function deleteVehicleFineForm($id)
  {

    $record = $this->findVehicleFineRecord($id);
    $advPurposeTypeId = 5;
    $advanceRecord = (new EmployeeAdvanceDataService())->findLastAdvanceRecordByEmpAutoIdAndAdvancePurposeId($record->employee_id, $advPurposeTypeId);

    if ($advanceRecord->adv_amount == $record->amount) {

      $delete = VehicleFineRecord::where('status', 1)->where('vfr_auto_id', $id)->update([
        'status' => 0
      ]);
      $insert =  (new EmployeeAdvanceDataService())->deleteAdvanceRecordById($advanceRecord->id);
       (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($record->employee_id, (-1 * $record->amount), 2);


      if ($delete) {
        Session::flash('delete_success', 'value');
        return redirect()->back();
      } else {
        Session::flash('delete_error', 'value');
        return redirect()->back();
      }
    } else {
      Session::flash('delete_error', 'value');
      return redirect()->back();
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Company  Driver Info Related Operations
  |--------------------------------------------------------------------------
  */
  public function driverInfosAddUI(){
    
    $all = (new TransportationDataService())->getAllActiveDriverInfos();
    return view('admin.vechicle.driver.index', compact('all'));
  }

  public function driverInfoInsertRequest(Request $request){
    
      if( (new TransportationDataService())->checkThisDriverAlreadyExist($request->dri_empId) ){
        Session::flash('error', 'This Driver Already Exist');
        return redirect()->back();
      }
      
      $dri_id = (new TransportationDataService())->insertDriverInfoWithDriverId($request);

      if ($dri_id <= 0) {
        Session::flash('error', 'Something Went Wrong, Please Try Again!');
        return redirect()->back();
      }  
      // Driver Iqama Certificate
      if ($request->hasFile('dri_iqama_certificate')) {
          $file = $request->file('dri_iqama_certificate');
          $uplodedPath = (new  UploadDownloadController())->uploadDriverIqamaCertificate($file, null);
          (new TransportationDataService())->updateDriverIqamaCertificateDbPath($dri_id, $uplodedPath , 'dri_iqama_certificate');
      }
      // Driving License Certificate
      if ($request->hasFile('dri_license_certificate')) {
          $file = $request->file('dri_license_certificate');
          $uplodedPath = (new  UploadDownloadController())->uploadDrivingLicenseCertificate($file, null);
          (new TransportationDataService())->updateDrivingLicenseCertificateDbPath($dri_id, $uplodedPath, 'dri_license_certificate');
      }
      // Driver Insurance Certificate
    //   if ($request->hasFile('dri_ins_certificate')) {
    //       $file = $request->file('dri_ins_certificate');
    //       $uplodedPath = (new  UploadDownloadController())->uploadDriverInsuranceCertificate($file, null);
    //       (new TransportationDataService())->updateDriverInsuranceCertificateDbPath($dri_id, $uplodedPath, 'dri_ins_certificate');
    //   }
      
      Session::flash('success', 'Successfully Added Driver information');
      return redirect()->back();
 
    
  }

  public function driverInfoEditUI($id){
    $edit = (new TransportationDataService())->getSingleDriverInfoWithDriverAutoID($id);
    return view('admin.vechicle.driver.edit', compact('edit'));
  }

  public function driverInfoUpdateRequest(Request $request){
    $update = (new TransportationDataService())->updateSingleDriverInfos($request);

      /* redirect back */
      if ($update) {
        Session::flash('success', 'Successfully Updated Driver Inforamtions');
        return redirect()->route('driver-info-add-ui');
      } else {
        Session::flash('error', 'value');
        return redirect()->back();
      }
  }

  public function driverImageUpdateRequest(Request $request){
    $id = $request->dri_auto_id;
    $find_dri_info = (new TransportationDataService())->getSingleDriverInfoWithDriverIdForImageUpdate($id);
    $dri_id = $find_dri_info->dri_auto_id;

    $UploadDownloadConObj = new  UploadDownloadController();
    $update = false;
    // Driver Iqama Certificate
    if ($request->hasFile('dri_iqama_certificate')) {
        $file = $request->file('dri_iqama_certificate');
        $uplodedPath = $UploadDownloadConObj->uploadDriverIqamaCertificate($file, $find_dri_info->dri_iqama_certificate);
        $update = (new TransportationDataService())->updateDriverIqamaCertificateDbPath($dri_id, $uplodedPath , 'dri_iqama_certificate');
    }
    // Driving License Certificate
    if ($request->hasFile('dri_license_certificate')) {
        $file = $request->file('dri_license_certificate');
        $uplodedPath = $UploadDownloadConObj->uploadDrivingLicenseCertificate($file,  $find_dri_info->dri_license_certificate);
        $update = (new TransportationDataService())->updateDrivingLicenseCertificateDbPath($dri_id, $uplodedPath, 'dri_license_certificate');
    }
    // Driver Insurance Certificate
    // if ($request->hasFile('dri_ins_certificate')) {
    //     $file = $request->file('dri_ins_certificate');
    //     $uplodedPath = $UploadDownloadConObj->uploadDriverInsuranceCertificate($file,  $find_dri_info->dri_ins_certificate);
    //     $update = (new TransportationDataService())->updateDriverInsuranceCertificateDbPath($dri_id, $uplodedPath, 'dri_ins_certificate');
    // }

    /* redirect back */
    if ($update) {
        Session::flash('success', 'Successfully Updated Driver Related Documents');
        return redirect()->route('driver-info-add-ui');
        } else {
        Session::flash('error', 'value');
        return redirect()->back();
    }
  }

  public function updateDriverActiveInactiveStatus($driv_id){
      
      try{
         
              $driver = (new TransportationDataService())->getSingleDriverInfoWithDriverAutoID($driv_id);
              if(is_null($driver)){
                Session::flash('error', 'Driver Record Not Found'.$driv_id);
                return redirect()->back();
              }

              $deactivate = (new TransportationDataService())->updateDriverActiveInactiveStatus($driv_id, !$driver->status,Auth::user()->id );
              if ($deactivate) {
                  Session::flash('success', $driver->status == 1 ? "Driver Successfully Deactivated" : "Driver Successfully Activated");
                  return redirect()->route('driver-info-add-ui');
                  } else {
                  Session::flash('error', 'Driver Update Operation Failed. Please Try Again '.$driv_id);
                  return redirect()->back();
              }
          }catch(Exception $ex){
            Session::flash('error', 'Driver Update Operation Failed. Please Try Again '.$driv_id);
            return redirect()->back();
          }


  }

  /*
  |--------------------------------------------------------------------------
  | Company  Driver Vehicle Info Related Operations
  |--------------------------------------------------------------------------
  */
  public function driverVehicleInfosAddUI(){

    $drivers = (new TransportationDataService())->getAllActiveDriverInfoForDropdown();
    $vehicles = (new TransportationDataService())->getAllActiveVehicleInfoForDropdown();
    $all = (new TransportationDataService())->getAllActiveDriverVehicleInfoWithDetails();
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    return view('admin.vechicle.vehicle-driver.index', compact('drivers', 'vehicles', 'projects', 'all'));
  }

  public function driverVehicleInfoInsertRequest(Request $request){
      
      
      
     if($request->driv_id == null || $request->veh_id == null){
        Session::flash('error', 'Please Select Driver and Vehicle ');
        return redirect()->back();
    }
    
    $insert = (new TransportationDataService())->insertVehicleDriverRecordsInfos($request);

    if ($insert) {
        $drivID = $request->driv_id;
         // I do not knwo why use this $inActiveOnDriverTable = (new TransportationDataService())->driverDataInActiveInDriverTable($drivID);

        Session::flash('success', 'Successfully Added Vehicle Driver Inforamtions');
        return redirect()->back();
    } else {
        Session::flash('error', 'Please Try Again With New Driver Information!');
        return redirect()->back();
    }
  }

  public function driverVehicleInfoEditUI($id){
    $drivers = (new TransportationDataService())->getAllActiveDriverInfoForDropdown();
    $vehicles = (new TransportationDataService())->getAllActiveVehicleInfoForDropdown();
    $edit = (new TransportationDataService())->getSingleDriverVehicleInfosWithDriverVehicleAutoIDForEdit($id);

    return view('admin.vechicle.vehicle-driver.edit', compact('drivers', 'vehicles', 'edit'));
  }

  public function driverVehicleInfoUpdateRequest(Request $request){
      
      if($request->driv_id == null || $request->veh_id == null || $request->driv_veh_auto_id == null){
        Session::flash('error', 'Please Select Driver and Vehicle ');
        return redirect()->back();
    } 
    
    $update = (new TransportationDataService())->singleDriverVehicleInfosUpdate($request);

    /* redirect back */
    if ($update) {
        Session::flash('success', 'Successfully Updated Vehicle Driver Inforamtions');
        return redirect()->route('driver-vehicle-info-add-ui');
        } else {
        Session::flash('error', 'Somthing Went Wrong! Please Try With New Driver Infos');
        return redirect()->back();
    }
  }

  public function driverVehicleInfoDeActiveWithReleasedDate($id){
    $deactivate = (new TransportationDataService())->singleDriverVehicleInfoDeActiveWithReleasedDate($id);

    if ($deactivate) {
         // need to update this system
      //  $ActiveOnDriverTable = (new TransportationDataService())->driverDataActiveInDriverTable($id);


        Session::flash('success', 'Successfully Released Vehicle Driver Inforamtions');
        return redirect()->route('driver-vehicle-info-add-ui');
    } else {
        Session::flash('error', 'Somthing Went Wrong!');
        return redirect()->back();
    }
  }

  // Upload photos and video 

  public function uploadDriverVehiclePhotosVideo(Request $request){
 
        $photo_path =null;
        if($request->hasFile('photo_upload')){ 
            $file = $request->file('photo_upload');
            $photo_path = (new  UploadDownloadController())->uploadDriverVehiclePhotos($file, null);   
        }  
        $isSuccess = (new TransportationDataService())->saveVehicleDriverPhotoVideo($request->modal_driv_id ,$request->modal_veh_id,$request->file_type,$photo_path, $request->video_url, $request->remarks);
        if($isSuccess > 0){
            Session::flash('success', 'Successfully Uploaded');
         } else {
            Session::flash('error', 'Somthing Went Wrong!');
         }
        return redirect()->back();
  }
  
  
   public function getDriverDetailsInformationByEmployeeInfo(Request $request){

        try{
            $driver = (new TransportationDataService())->getDriverDetailsInformationByEmployeeInformation($request->employee_id);
    
          if($driver == null){
            return json_encode(['status' => 403, 'success'  => false, 'message' => 'Driver Information Not Found!!!']);
          }
    
          $driver_veh_record = (new TransportationDataService())->getADriverDrivingVehicleInformation($driver->dri_auto_id);
            return json_encode([
                'status' => 200,
                'success'  => true,
                'driver_info' => $driver,
                'driver_vehicle_info' => $driver_veh_record,
                "message"=>""
            ]);
        }catch(Exception $message){
            return json_encode(['status' => 403, 'success'  => false, 'error'=>"error", 'message' => 'System Operation ']);
        }
        
    
   }





    /*
    |--------------------------------------------------------------------------
    | Company  Vehicle Related Reports
    |--------------------------------------------------------------------------
    */
    public function VehicleReportUI(){

      $vehicles = (new TransportationDataService())->getAssignedOrNotAllActiveVehicleInfoForDropdown();
     $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
      return view('admin.report.vehicles.vehicle_report_processing_form', compact('vehicles', 'projects'));
    }
    
    public function getVechileDetailsInformationReport(Request $request){

      try{
          
        $company = (new CompanyDataService())->findCompanryProfile();
        $vehicle = (new TransportationDataService())->searchAVehicleDetailsInformation($request->search_value);

        if($vehicle){

          $driver_veh_records = (new TransportationDataService())->searchAVehicleDriverAssignAllRecords($vehicle->veh_id);
          $report_generated_by = Auth::user()->name;
          return view('admin.report.vehicles.single_vehicle_details',compact('vehicle','driver_veh_records','company','report_generated_by'));
        }else {
          return redirect()->back();
        }

      }catch(Exception $ex){
        return "Sysem Error Occured, Please try Again";
      }
    }
    
    public function allActiveCompanyVehiclesReport(Request $request){
      
    
      $company = (new CompanyDataService())->findCompanryProfile();
      if($request->report_type == 1){
          $vehicles = (new TransportationDataService())->getAllActiveVehicaleInfos();
          return view('admin.report.vehicles.all_vehicles_report', compact('vehicles', 'company'));
      }else if($request->report_type == 2){
        // Driver Not Assing Vehicles list
        $vehicles = (new TransportationDataService())->getVehiclesWhichAreNotAssignedDriver();
        return view('admin.report.vehicles.all_vehicles_report', compact('vehicles', 'company'));

      }else if($request->report_type == 3){
        // Driver Not Assing Vehicles list
        $vehicles = (new TransportationDataService())->getVehiclesWhichAlreadyAssignedDriver();
        return view('admin.report.vehicles.all_vehicles_report', compact('vehicles', 'company'));

      }else if($request->report_type == 4){
        // all driver name   
      
        $driverInfo = (new TransportationDataService())->getAllDriverInformationForReports();
   
        return view('admin.report.vehicles.driver_list_report', compact('driverInfo', 'company'));
      }else if($request->report_type == 5){
        // all drvier that not assigned   
        
         $driverInfo = (new TransportationDataService())->getAllDriversThoseHaveNoVehicleNowReports();
         return view('admin.report.vehicles.driver_list_report', compact('driverInfo', 'company'));
      
        // $driverInfo = (new TransportationDataService())->getAllDriverInformationForReports();
   
        // return view('admin.report.vehicles.driver_list_report', compact('driverInfo', 'company'));
      }
      
      
      
    }
    public function vehicleNameWiseDriverAllInformationsCollect(Request $request){
      $company = (new CompanyDataService())->findCompanryProfile();
      $driverInfo = (new TransportationDataService())->getAllDriverInfoWithSpecificVehicle($request->veh_id);
       return view('admin.report.vehicles.vehicle_wise_driver_report', compact('driverInfo', 'company'));
    }
    public function vehicleTypeAndCompanyNameWiseAllActiveVehiclesReportProcess(Request $request){
      $company = (new CompanyDataService())->findCompanryProfile();
      $vehicleRecords = (new TransportationDataService())->getCompanyNameAndVehicleTypeWiseAllVehicleRecords($request->company_id, $request->veh_type_id);
       return view('admin.report.vehicles.company_name_veh_type_wise_report', compact('company', 'vehicleRecords'));
    }


        // project base vehicles report
    public function processProjectWiseVehicleDetailsReport(Request $request)
    {
        
        $project_id_list = $request->input('proj_id');
        if(is_null($project_id_list)){
            $project_id_list =  (new ProjectDataService())->getAllActiveProjectIDs();
        }
        $records = (new TransportationDataService())->getListOfVehicleDetailsWithDriverInformationByProjectID($project_id_list);
        $login_user = Auth::user()->name;
        $company = (new CompanyDataService())->findCompanryProfile();
        return view('admin.report.vehicles.project_wise_vehicle_report', compact('records', 'company','login_user'));

    }
    
        // show vehicles report from Dashboard
    public function processAllVehicleRecordsWithDriverDetailsReport()
    {
        $project_id_list =  (new ProjectDataService())->getAllActiveProjectIDs();
        $records = (new TransportationDataService())->getListOfVehicleDetailsWithDriverInformationByProjectID($project_id_list);
        $login_user = Auth::user()->name;
        $company = (new CompanyDataService())->findCompanryProfile();
        return view('admin.report.vehicles.project_wise_vehicle_report', compact('records', 'company','login_user'));

    }



 








}
