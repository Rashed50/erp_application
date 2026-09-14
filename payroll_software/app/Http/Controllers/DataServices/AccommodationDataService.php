<?php

namespace App\Http\Controllers\DataServices;

use App\Models\OfficeBuilding;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AccommodationDataService
{

    /*
     ==========================================================================
         ======== Company Office Building Rent Database Operation ========
     ==========================================================================
    */
    public function getAllActiveOfficeBuildingNameIdAndCityForDropdownList(){
        return OfficeBuilding::select('ofb_id', 'ofb_name' , 'ofb_city_name')->where('status', 1)->get();
    }
    public function getAllOfficeBuilidingRentInformations(){
        return OfficeBuilding::orderBy('ofb_id', 'DESC')->where('status',1)->get();
    }
    public function singleOfficeBuildingRentInformations($ofb_id){
        return  OfficeBuilding::where('ofb_id',$ofb_id)->first();
    }
    public function singleOfficeBuildingRentInformationsWithActiveStatus($ofb_id){
        return  OfficeBuilding::where('status',1)->where('ofb_id',$ofb_id)->first();
    }
    public function getAllEngagedOfficeBuildingRentInfos(){
      return OfficeBuilding::where('status',1)->orderBy('ofb_id','DESC')->get();
    }

    public function insertNewOfficeBuildingInformationsWithOfficeBuildingAutoID(
        $ofb_name,
        $ofb_rent_date,
        $ofb_rent_form,
        $ofb_owner_mobile,
        $ofb_accomodation_capacity,
        $ofb_rent_amount,
        $ofb_advance_amount,
        $ofb_agrement_date,
        $ofb_expiration_date,
        $ofb_city_name,
        $ofb_location_details,
        $emp_auto_id
    ){
        return OfficeBuilding::insertGetId([
            'ofb_name' => $ofb_name,
            'ofb_rent_date' => $ofb_rent_date,
            'ofb_rent_form' => $ofb_rent_form,
            'ofb_owner_mobile' => $ofb_owner_mobile,
            'ofb_accomod_capacity' => $ofb_accomodation_capacity,
            'ofb_rent_amount' => $ofb_rent_amount,
            'ofb_advance_amount' => $ofb_advance_amount,
            'ofb_agrement_date' => $ofb_agrement_date,
            'ofb_expiration_date' => $ofb_expiration_date,
            'ofb_city_name' => $ofb_city_name,
            'ofb_loct_details' => $ofb_location_details,
            'ofb_manage_by_emp_auto_id' => $emp_auto_id,
            'create_by_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }
    public function updateOfficeBuildingDeedPeperDbPath($ofb_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  OfficeBuilding::where('ofb_id', $ofb_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }
    public function updateOfficeBuildingRentInformations(
        $ofb_id,
        $ofb_name,
        $ofb_rent_date,
        $ofb_rent_form,
        $ofb_owner_mobile,
        $ofb_accomodation_capacity,
        $ofb_rent_amount,
        $ofb_advance_amount,
        $ofb_agrement_date,
        $ofb_expiration_date,
        $ofb_city_name,
        $ofb_location_details,
        $emp_auto_id
    ){
        return OfficeBuilding::where('ofb_id',$ofb_id)->update([
            'ofb_name' => $ofb_name,
            'ofb_rent_date' => $ofb_rent_date,
            'ofb_rent_form' => $ofb_rent_form,
            'ofb_owner_mobile' => $ofb_owner_mobile,
            'ofb_accomod_capacity' => $ofb_accomodation_capacity,
            'ofb_rent_amount' => $ofb_rent_amount,
            'ofb_advance_amount' => $ofb_advance_amount,
            'ofb_agrement_date' => $ofb_agrement_date,
            'ofb_expiration_date' => $ofb_expiration_date,
            'ofb_city_name' => $ofb_city_name,
            'ofb_loct_details' => $ofb_location_details,
            'ofb_manage_by_emp_auto_id' => $emp_auto_id,
            'create_by_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function officeBuildingRentalStatusAsDeActive($ofb_id){
       return OfficeBuilding::where('status',1)->where('ofb_id',$ofb_id)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }






}

