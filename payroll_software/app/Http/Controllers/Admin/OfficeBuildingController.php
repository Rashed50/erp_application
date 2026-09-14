<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\AccommodationDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Support\Facades\Session;

class OfficeBuildingController extends Controller{
    /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

    public function index(){
        $all = (new AccommodationDataService())->getAllOfficeBuilidingRentInformations();
      return view('admin.office-building.all',compact('all'));
    }

    public function edit($ofb_id){
        $edit = (new AccommodationDataService())->singleOfficeBuildingRentInformations($ofb_id);
      return view('admin.office-building.edit',compact('edit'));
    }

    public function insert(Request $request){
      $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->empId);
      $emp_auto_id = $findEmployee->emp_auto_id;

      $ofb_id = (new AccommodationDataService())->insertNewOfficeBuildingInformationsWithOfficeBuildingAutoID($request->ofb_name, $request->ofb_rent_date, $request->ofb_rent_form, $request->ofb_owner_mobile, $request->ofb_accomodation_capacity, $request->ofb_rent_amount, $request->ofb_advance_amount, $request->ofb_agrement_date, $request->ofb_expiration_date, $request->ofb_city_name, $request->ofb_location_details, $emp_auto_id);

      $UploadDownloadConObj = new  UploadDownloadController();
      // Building Rent Deed Paper
      if ($request->hasFile('ofb_dead_papers')) {
         $file = $request->file('ofb_dead_papers');
         $uplodedPath = $UploadDownloadConObj->uploadBuildingRentDeedPhoto($file, null);
         (new AccommodationDataService())->updateOfficeBuildingDeedPeperDbPath($ofb_id, $uplodedPath , 'ofb_dead_papers');
       }

       /* redirect back */
       if($ofb_id){
          Session::flash('success','Successfully! Added New Office Building Information.');
          return redirect()->back();
      }else{
          Session::flash('error','Opps! please try again.');
          return redirect()->back();
      }
    }

    public function update(Request $request){
        $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->empId);
        $emp_auto_id = $findEmployee->emp_auto_id;

        $findOfbInfo = (new AccommodationDataService())->singleOfficeBuildingRentInformations($request->id);
        $ofb_id = $findOfbInfo->ofb_id;

        $updateInfo = (new AccommodationDataService())->updateOfficeBuildingRentInformations(
            $request->id,
            $request->ofb_name,
            $request->ofb_rent_date,
            $request->ofb_rent_form,
            $request->ofb_owner_mobile,
            $request->ofb_accomodation_capacity,
            $request->ofb_rent_amount,
            $request->ofb_advance_amount,
            $request->ofb_agrement_date,
            $request->ofb_expiration_date,
            $request->ofb_city_name,
            $request->ofb_location_details,
            $emp_auto_id
        );

        $UploadDownloadConObj = new  UploadDownloadController();
        // Building Rent Deed Paper
        if ($request->hasFile('ofb_dead_papers')) {
            $file = $request->file('ofb_dead_papers');
            $uplodedPath = $UploadDownloadConObj->uploadBuildingRentDeedPhoto($file, $findOfbInfo->ofb_dead_papers);
             (new AccommodationDataService())->updateOfficeBuildingDeedPeperDbPath($ofb_id, $uplodedPath , 'ofb_dead_papers');
        }

        if($updateInfo){
            Session::flash('success','Successfully! Updated New Office Building Information.');
            return redirect()->route('rent.new-building');
        }else{
            Session::flash('error','Opps! please try again.');
            return redirect()->back();
        }
    }

    public function delete($ofb_id){
        $deactivated = (new AccommodationDataService())->officeBuildingRentalStatusAsDeActive($ofb_id);

        /* redirect back */
        if($deactivated){
            Session::flash('success','Successfully!  Delete Office Building Information.');
            return redirect()->back();
        }else{
            Session::flash('error','Opps! please try again.');
            return redirect()->back();
        }
    }






  /* _____________________________ === _____________________________ */
}
