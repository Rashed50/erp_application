<?php

namespace App\Http\Controllers\DataServices;

use App\Models\BannerInfo;
use App\Models\MainContractorInfo;
use App\Models\AgencyInfo;
use App\Models\CompanyProfile;
use App\Models\BranchOffice;
use App\Models\BankDetails;
use App\Models\SubCompanyInfo;
use App\Models\Currency;
use App\Models\Month;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyDataService
{
    /*
     ========================================================================== 
     =============================Available Currency ========================== 
     ========================================================================== 
    */

    public function getAvailableCurrency()
    {
        return $all = Currency::get();
    }

    /*
     ========================================================================== 
     ============================= Company Profile ============================ 
     ========================================================================== 
    */
    public function getCompanyProfileInformation()
    {
        return $all = CompanyProfile::all();
    }
    
    
    public function findACompanryProfileAllInformation()
    {
        $profile = CompanyProfile::where('comp_id', 1)->firstOrFail();
        if ($profile == null) {
            $profile = new CompanyProfile();
        }
        return $profile;
    }

    public function findCompanryProfile()
    {
        $profile = CompanyProfile::select('comp_id','comp_name_en','comp_name_arb','comp_address','comp_email1','comp_phone1','comp_support_number','comp_hotline_number','com_logo')->where('comp_id', 1)->firstOrFail();
        if ($profile == null) {
            $profile = new CompanyProfile();
        }
        return $profile;
    }

    // public function findCompanryProfile()
    // {
    //     $profile = CompanyProfile::where('comp_id', 1)->firstOrFail();
    //     if ($profile == null) {
    //         $profile = new CompanyProfile();
    //     }
    //     return $profile;
    // }


    public function insertCompanyInformation($req)
    {
        return $update = CompanyProfile::where('comp_id', 1)->update([
            'comp_name_en' => $req->comp_name_en,
            'comp_name_arb' => $req->comp_name_arb,
            'curc_id' => $req->curc_id,
            'comp_address' => $req->comp_address,
            'comp_email1' => $req->comp_email1,
            'comp_email2' => $req->comp_email2,
            'comp_phone1' => $req->comp_phone1,
            'comp_phone2' => $req->comp_phone2,
            'comp_mobile1' => $req->comp_mobile1,
            'comp_mobile2' => $req->comp_mobile2,
            'comp_support_number' => $req->comp_support_number,
            'comp_hotline_number' => $req->comp_hotline_number,
            'comp_description' => $req->comp_description,
            'comp_mission' => $req->comp_mission,
            'comp_vission' => $req->comp_vission,
            'comp_contact_address' => $req->comp_contact_address,
            'updated_at' => Carbon::now(),
        ]);
    }
    
    
    
       /*
     ==========================================================================
     ============================= Company Branch Information ========================
     ==========================================================================
    */

    // public function getACompanyListOfBranchForDropdownlist($company_id){
    //   return BranchOffice::select("braoff_auto_id","branch_name_en")->get();
    // }
     public function getACompanyListOfBranchForDropdownlist($branch_auto_ids){
       return BranchOffice::select("braoff_auto_id","branch_name_en")->whereIn('braoff_auto_id',$branch_auto_ids)->get();
    }

    public function getABranchDetailsInformationByBranchAutoId($branch_auto_id){
        $branch_info = BranchOffice::select("branch_name_en","branch_name_native","branch_address")->where("braoff_auto_id",$branch_auto_id)->firstOrFail();
        $branch_info->comp_name_en = $branch_info->branch_name_en;
        $branch_info->comp_name_arb = $branch_info->branch_name_native;
        $branch_info->comp_address = $branch_info->branch_address;
        return $branch_info;

     }


  /*
     ==========================================================================
     ============================= Sub Company Profile ========================
     ==========================================================================
    */
    
      public function subCompanyNameInsertRequest($sb_comp_name, $sb_comp_name_arb, $company_id, $sb_comp_mobile1, $sb_vat_no, $sb_comp_email1,
     $sb_comp_email2, $sb_comp_phone1, $sb_comp_phone2, $sb_comp_address, $sb_details){

        if (count($this->CheckSubCompanyIsExist($sb_comp_name)) >0) {
            return 0;
        } else {
            return SubCompanyInfo::insert([
            'sb_comp_name' => $sb_comp_name,
            'sb_comp_name_arb' => $sb_comp_name_arb,
            'company_id' => $company_id,
            'sb_comp_address' => $sb_comp_address,
            'sb_comp_mobile1' => $sb_comp_mobile1,
            'sb_vat_no' => $sb_vat_no,
            'sb_comp_email1' => $sb_comp_email1,
            'sb_comp_email2' => $sb_comp_email2,
            'sb_comp_phone1' => $sb_comp_phone1,
            'sb_comp_phone2' => $sb_comp_phone2,
            'sb_comp_contact_parson_details' => $sb_details,
            'entered_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
          ]);
        }
    }

    public function CheckSubCompanyIsExist($sb_comp_name){
      $sbCompNUp = strtoupper($sb_comp_name);
      $sbCompNLow = strtolower($sb_comp_name);
      return SubCompanyInfo::where('sb_comp_name' , $sb_comp_name)
                      ->orWhere('sb_comp_name', $sbCompNUp)
                      ->orWhere('sb_comp_name', $sbCompNLow)
                      ->get();

    }
    
      public function getAnSubContractorDetailsInfoByID($subContractorID){
        return SubCompanyInfo::where('sb_comp_id', $subContractorID)->first();
    }

    public function SubCompanyNameUpdateRequest($sb_comp_id ,$sb_comp_name, $sb_comp_name_arb, $company_id, $sb_comp_mobile1, $sb_vat_no, $sb_comp_email1,
    $sb_comp_email2, $sb_comp_phone1, $sb_comp_phone2, $sb_comp_address, $sb_details){

        if (count($this->CheckSubCompanyIsExist($sb_comp_name)) > 1) {
            return 0;
        } else {
            return SubCompanyInfo::where('sb_comp_id',$sb_comp_id)->update([
            'sb_comp_name' => $sb_comp_name,
            'sb_comp_name_arb' => $sb_comp_name_arb,
            'company_id' => $company_id,
            'sb_comp_address' => $sb_comp_address,
            'sb_comp_mobile1' => $sb_comp_mobile1,
            'sb_vat_no' => $sb_vat_no,
            'sb_comp_email1' => $sb_comp_email1,
            'sb_comp_email2' => $sb_comp_email2,
            'sb_comp_phone1' => $sb_comp_phone1,
            'sb_comp_phone2' => $sb_comp_phone2,
            'sb_comp_contact_parson_details' => $sb_details,
            'entered_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
          ]);
        }
    }
   
   
 
    
    public function getSubCompanyList(){
        return SubCompanyInfo::where('status', 1)->get();
      }
      
      public function getSubCompanyById($id){
        return  SubCompanyInfo::where('status',1)->where('sb_comp_id',$id)->firstOrFail();
      }

      public function getSubCompanyListForDropdown()
      {
          return DB::table("sub_company_infos")->select('sb_comp_id', 'sb_comp_name','sb_vat_no')
              ->where('status', 1)->orderBy('sb_comp_id', 'DESC')->get();
      }

 public function deleteASubCompanyById($id){
        return SubCompanyInfo::where('sb_comp_id',$id)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
          ]);
    }

    /*
     ========================================================================== 
     ============================= Website Banner ============================= 
     ========================================================================== 
    */

    public function getBannersInformation()
    {
         return   BannerInfo::select('ban_title','ban_subtitle','ban_caption','ban_image')->where('status', 1)->get();
    }

    public function findBannerInformation($id)
    {
        return $find = BannerInfo::where('status', 1)->where('ban_id', $id)->first();
    }


    public function insertNewBanner($req)
    {
        $insert = BannerInfo::insert([
            'ban_title' => $req->ban_title,
            'ban_subtitle' => $req->ban_subtitle,
            'ban_caption' => $req->ban_caption,
            'ban_description' => $req->ban_description,
            'company_id' => $req->company_id,
            'entered_id' => Auth::user()->id,
            'ban_image' => $banner_image_name,
            'created_at' => Carbon::now(),
        ]);
    }


    public function deleteBanner($ban_id)
    {
        return $delete = BannerInfo::where('ban_id', $ban_id)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateBannerInformation($ban_id, $ban_title, $ban_subtitle, $ban_caption, $ban_description, $company_id)
    {
        return $update = BannerInfo::where('status', 1)->where('ban_id', $ban_id)->update([
            'ban_title' => $ban_title,
            'ban_subtitle' => $ban_subtitle,
            'ban_caption' => $ban_caption,
            'ban_description' => $ban_description,
            'company_id' => $company_id,
            'entered_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }
    public function updateBannerInformationWithBannerImage($ban_id, $ban_title, $ban_subtitle, $ban_caption, $ban_description, $company_id, $banner_image_name)
    {
        return $update = BannerInfo::where('status', 1)->where('ban_id', $ban_id)->update([
            'ban_title' => $ban_title,
            'ban_subtitle' => $ban_subtitle,
            'ban_caption' => $ban_caption,
            'ban_description' => $ban_description,
            'company_id' => $company_id,
            'ban_image' => $banner_image_name,
            'entered_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }

    /*
     ========================================================================== 
     ============================= Month ====================================== 
     ========================================================================== 
    */

    public function getAllMonth()
    {
        return $all = Month::get();
    }
    public function getMonthById($id)
    {
        if ($id > 12)
            return null;
        return $all = Month::where('month_id', $id)->first();
    }


    /*
     ========================================================================== 
     ============================= Country ==================================== 
     ========================================================================== 
    */
    
    
    
    
    
    
    
    /*
     ========================================================================== 
     ============================= Agency Information ========================= 
     ========================================================================== 
    */

    public function saveNewAgencyInformation($name, $address, $contact_no)
    {
        if ($this->CheckAgencyNameIsExist($name, $address, $contact_no)) {
            return 0;
        } else {
            return AgencyInfo::insert([
                'agc_title' => $name,
                'office_address' => $address,
                'contact_no' => $contact_no,
                'created_at' => Carbon::now(),
            ]);
        }
    }
    public function CheckAgencyNameIsExist($name)
    {
        $agencyNLow = strtolower($name);
        $findAgency = AgencyInfo::whereRaw( 'LOWER(`agc_title`) LIKE ?', [ $agencyNLow ] )->first();
        if ($findAgency != null) {
             return true;
        } else {
             return false;
        }
    }

    public function getAllAgencies()
    {
        return AgencyInfo::orderBy('agc_info_auto_id','desc')->get();
    }
    
    
     /*
     ==========================================================================
     ============================= Bank Informations =========================
     ==========================================================================
    */
   
    public function getBankInfoBySubCompanyId($subCompID){
        return BankDetails::select('id', 'bank_name', 'account_no')->where('sub_company_id', $subCompID)->orderBy('id', 'ASC')->get();
    }
    public function getAllActiveBankAccountNameForDropdownlist(){
        return BankDetails::select('id', 'bank_name', 'account_no')->where('account_status', 1)->orderBy('id', 'ASC')->get();
    }
    public function getAllBankInformations(){
        return BankDetails::with('subcompany')->orderBy('id', 'DESC')->get();
    }
    public function getSingleBankInformations($bankID){
        return BankDetails::where('id', $bankID)->first();
    }
    public function insertBankInformations($request){
        if ($this->checkThisBankAccountIsExist($request->bank_accnt_no)) {
            return 0;
        } else {
            return BankDetails::insertGetID([
                'bank_name' => $request->bank_name,
                'sub_company_id' => $request->sub_company_id,
                'account_type' => $request->bank_accnt_type,
                'account_no' => $request->bank_accnt_no,
                'ibank_no' => $request->ibank_no,
                'beneficiary_name' => $request->beneficiary_name,
                'create_by_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }
    }

    public function checkThisBankAccountIsExist($accountNo){
        return BankDetails::where('account_no', $accountNo)->count() > 0 ? true:false;
        
    }
    
    public function checkThisBankAccountNoIsExistBeforUpdateBankAccountNo($bank_auto_id,$accountNo){
        return (BankDetails::where('account_no', $accountNo)->whereNotIn('id', [$bank_auto_id])->count()) > 0 ? true:false;
   }

    public function updateBankInformations($request, $accnt_status){
        if ($this->checkThisBankAccountNoIsExistBeforUpdateBankAccountNo($request->bank_accont_id, $request->bank_accnt_no)) {
            return false;
        } else {

            return BankDetails::where('id', $request->bank_accont_id)->update([
                'bank_name' => $request->bank_name,
                'sub_company_id' => $request->sub_company_id,
                'account_type' => $request->bank_accnt_type,
                'beneficiary_name' => $request->beneficiary_name,
                'account_status' => $accnt_status,
                'ibank_no' => $request->ibank_no,
                'account_no' => $request->bank_accnt_no,
                'update_by_id' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
        }
    }


    /*
     ==========================================================================
        ================== Main Contractor Informations ===================
     ==========================================================================
    */
    public function getAllActiveMainContractorForDropdownList(){
        return MainContractorInfo::select('mc_auto_id', 'en_name', 'phone_no', 'vat_no')->where('mc_status', 1)->orderBy('mc_auto_id', 'ASC')->get();
    }
    public function getAllMainContractorInfos(){
        return MainContractorInfo::orderBy('mc_auto_id', 'DESC')->get();
    }

    public function insertMainContractor($request){
        if ($this->checkThisContractorIsExist($request->mc_name_en) == false) {
            return 0;
        } else {
            return MainContractorInfo::insertGetID([
                'en_name' => $request->mc_name_en,
                'ar_name' => $request->mc_name_arb,
                'phone_no' => $request->mc_phone_no,
                'vat_no' => $request->mc_vat_no,
                'mc_email' => $request->mc_email,
                'mc_address' => $request->mc_address,
                'create_by_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }
    }
    private function checkThisContractorIsExist($contractorNameEN){
        $nameCheck = MainContractorInfo::where('en_name', $contractorNameEN)->count();
        return $nameCheck > 0 ? false : true;
    }
    public function updateMainContractor($request,$status){
        if ($this->checkThisContractorIsExist($request->mc_name_en) == false) {
            return 0;
        } else {
            return MainContractorInfo::where('mc_auto_id', $request->mainContractorID)->update([
                'en_name' => $request->mc_name_enM,
                'ar_name' => $request->mc_name_arbM,
                'phone_no' => $request->mc_phone_noM,
                'vat_no' => $request->mc_vat_noM,
                'mc_email' => $request->mc_emailM,
                'mc_address' => $request->mc_addressM,
                'mc_status' => $status,
                'update_by_id' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
        }
    }
    
    
    
    
}
