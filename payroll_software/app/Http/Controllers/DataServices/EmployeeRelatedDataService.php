<?php
namespace App\Http\Controllers\DataServices;

use App\Models\Country;
use App\Models\Division;
use App\Models\Department;
use App\Models\District;
use App\Models\EmployeeActivity;
use App\Models\EmployeeCategory;
use App\Models\EmployeeStatusRecord;
use App\Models\EmployeeType;
use App\Models\EmpProjectHistory;
use App\Models\JobStatus;
use App\Models\ProjectImgUpload;
use App\Models\ProjectInfo;
use App\Models\Sponsor;
use App\Models\BankName;
use App\Models\EmployeeBankDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeRelatedDataService
{
    
    /*
     ============================================================================
     ========= Iqama date update by excel file upload temporary table methods ===
     ============================================================================
    */

    // public function InsertIqamaExpireUploadDataInTemporaryTable($emp_Auto_id,$employee_id,$iqama_no,$expire_date){
    //     DB::insert("insert into temp_table_emp_iqama_update values(?,?,?,?)",array($emp_Auto_id,$employee_id,$iqama_no,$expire_date));
    // }
    
    public function InsertIqamaExpireUploadDataInTemporaryTable($emp_Auto_id,$employee_id,$iqama_no,$expire_date){
        DB::insert("insert into temp_table_emp_iqama_update(emp_auto_id,employee_id,iqama_no,expire_date) values(?,?,?,?)",array($emp_Auto_id,$employee_id,$iqama_no,$expire_date));
    }
    
    // 
    public function InsertIqamaRenewalExpenseUploadDataInTemporaryTable($emp_Auto_id,$employee_id,$iqama_no,$expire_date,$jawazat_fee,$maktab_alamal_fee,$bd_amount,$medical_insurance,$others_fee,
    $jawazat_penalty,$total_amount,$duration,$renewal_date,$remarks,$reference_emp_id){
        DB::insert("insert into temp_table_emp_iqama_update(emp_auto_id,employee_id,iqama_no,expire_date,jawazat_fee,maktab_alamal_fee,bd_amount,medical_insurance,others_fee,jawazat_penalty,total_amount,duration,renewal_date,remarks,reference_emp_id) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($emp_Auto_id,$employee_id,$iqama_no,$expire_date,$jawazat_fee,$maktab_alamal_fee,$bd_amount,$medical_insurance,$others_fee,
        $jawazat_penalty,$total_amount,$duration,$renewal_date,$remarks,$reference_emp_id));
    } 
    
    public function removeIqamaExpireUploadedAllDataFromTemporaryTable(){
         DB::table('temp_table_emp_iqama_update')->delete();
    }
    public function getIqamaExpireUploadedAllDataFromTemporaryTable(){ 
      return   DB::table('temp_table_emp_iqama_update')->get(); 
    }


    /*
     ========================================================================== 
     ============================= Country ==================================== 
     ========================================================================== 
    */
    public function insertNewCountry($country_name)
    {
        return Country::insert([
            'country_name' => $country_name,
        ]);
    }
    public function getAllCountry()
    {
        return   Country::get();
    }
    public function getAllCountryForDropdownList()
    {
        return DB::table('countries')->select('id', 'country_name')->get();
    }

    /*
     ========================================================================== 
     ============================= Department  ================================
     ========================================================================== 
    */
    public function getAllDepartment()
    {
        return  Department::get();
    }


    /*
     ========================================================================== 
     ============================= Division  ================================== 
     ========================================================================== 
    */

    public $division_table = 'divisions';
    public function insertNewDivision($country_id, $division_name)
    {
        if ($this->checkDivisionIsExist($division_name, $country_id) == false) {
            return  $insert = Division::insert([
                'country_id' => $country_id,
                'division_name' => $division_name,
            ]);
        }
        return false;
    }
    public function getDivisions($division_id)
    {
        if ($division_id == null) {
            return Division::orderBy('division_name', 'ASC')->get();//  DB::table($this->division_table)->get();
        } else {
            return DB::table($this->division_table)->where('division_id', $division_id)->first();
        }
    }
    public function getDivisionByCountryId($country_id)
    {
        return DB::table($this->division_table)->select('division_id', 'division_name', 'country_id')->where('country_id', $country_id)->orderBy('division_name', 'ASC')->get();

        // return Division::where('country_id', $country_id)->orderBy('division_name', 'ASC')->get();
    }

    public function checkDivisionIsExist($division_name, $country_id)
    {

        $count = DB::table($this->division_table)->where('division_name', $division_name)->where('country_id', $country_id)->count();
        return $count > 0 ? true : false;
    }


    public function updateDivision($divId, $country_id, $division_name)
    {
        return Division::where('division_id', $divId)->update([
            'country_id' => $country_id,
            'division_name' => $division_name,
            'updated_at' => Carbon::now(),
        ]);
    }




    /*
     ========================================================================== 
     ============================= District =================================== 
     ========================================================================== 
    */
    public function getDistricts($id)
    {
        if ($id == null) {
            return District::orderBy('district_id', 'DESC')->get();
        } else {
            return District::where('district_id', $id)->orderBy('district_id', 'DESC')->get();
        }
    }

    public function getDistrictByDivisionId($division_id)
    {
        return District::where('division_id', $division_id)->orderBy('district_name', 'ASC')->get();
    }
    public function checkDistrictIsExist($district_name, $division_id, $country_id)
    {

        $count = District::where('country_id', $country_id)->where('division_id', $division_id)->where('district_name', $district_name)->count();
        return $count > 0 ? true : false;
    }

    public function insertNewDistrict($district_name, $division_id, $country_id)
    {
        return District::insert([
            'country_id' => $country_id,
            'division_id' => $division_id,
            'district_name' => $district_name,
        ]);
    }
    public function updateDistrict($district_id, $district_name, $division_id, $country_id)
    {
        return District::where('district_id', $district_id)->update([
            'country_id' => $country_id,
            'division_id' => $division_id,
            'district_name' => $district_name,
        ]);
    }


    /*
     ========================================================================== 
     ============================= Employee Job Status ============================== 
     ========================================================================== 
    */

    public function getEmployeeJobStatusTitle($empStatusId)
    {
        return JobStatus::where('id', $empStatusId)->first()->title;
    }

    public function getEmployeeStatus()
    {

        return JobStatus::all();
    }

    /*
     ========================================================================== 
     ============================= Employee Type ============================== 
     ========================================================================== 
    */

    public $employ_type_table = 'employee_types';
    public function getAnEmployeeType($id)
    {
        return  EmployeeType::where('id', $id)->get();
    }
    public function getAnEmployeeTypeName($id)
    {
        return  EmployeeType::where('id', $id)->pluck('name');
    }

    public function getAllEmployeeType()
    {
        return  EmployeeType::get();
    }

    public function getEmployeeTypeForDropdown()
    {
        return DB::table($this->employ_type_table)->select('id', 'name')->get();
    }
    
     /*
     ==========================================================================
     ============================= Employee Designation Head ==================
     ==========================================================================
    */
 
    
    public function getDesignationHeadRecordsForDropdown()
    {
        return DB::table('designation__heads')->select('dh_auto_id', 'des_head_name')
            ->where('status', 1)->orderBy('des_head_name', 'ASC')->get();
    }
    
    public function getAllActiveDesignationHeadIDs()
    {
       return DB::table('designation__heads')->where('status',1)->orderBy('des_head_name', 'ASC')->pluck('dh_auto_id')->toArray();
    }
    
     public function getListOfDesignationHeadRecordsByMultipleAutoIds($dh_auto_id_array=null)
    {
        if($dh_auto_id_array== null){
            return DB::table('designation__heads')->select('dh_auto_id', 'des_head_name')
            ->where('status', 1)->orderBy('des_head_name', 'ASC')
            ->get();
        }
        return DB::table('designation__heads')->select('dh_auto_id', 'des_head_name')
            ->where('status', 1)->orderBy('des_head_name', 'ASC')
            ->whereIn('dh_auto_id',$dh_auto_id_array)
            ->get();
    }


    /*
     ========================================================================== 
     ============================= Employee Category=========================== 
     ========================================================================== 
    */

    public $empCategoryInfoTable = 'employee_categories';
    public function getEmpAllCategoryInfoForDropdown()
    {
        return DB::table($this->empCategoryInfoTable)->select('catg_id', 'catg_name')
            ->where('catg_status', 1)->orderBy('catg_name', 'ASC')->get();
    }
    
      public function getAllActiveCategoriesForDropdownByEmployeeTypeId($employee_type_id=null)
    {
        if($employee_type_id == null){
            return EmployeeCategory::select('catg_id', 'catg_name')->where('catg_status', 1)->orderBy('catg_name', 'ASC')->get();
        }else{
            return EmployeeCategory::select('catg_id', 'catg_name')->where('catg_status', 1)->where('emp_type_id', $employee_type_id)->orderBy('catg_name', 'ASC')->get();
        }
    }
    
      public function getEmpAllCategoryWithRankingSequence()
    {
        return EmployeeCategory::select('catg_id', 'catg_name')
            ->where('catg_status', 1)->orderBy('rank_code', 'ASC')->get();
    }
    
    public function getEmployeeAllCategory()
    {
        return  DB::select('CALL getAllTradeCategoryWithDesignationHead()');
      
    }
    
    
    
    public function getAllActiveCategoryIDAsArrayWithRankingSequence()
    {
        return EmployeeCategory::where('catg_status', 1)->orderBy('rank_code', 'ASC')->pluck('catg_id')->toArray();

    }
    public function getAllActiveCategoryIDAsArrayWithRankingSequenceByEmpTypeIds($emp_type_ids, $branch_office_id=null)
    {
        if($branch_office_id == null){
            return EmployeeCategory::where('catg_status', 1)
                ->whereIn('emp_type_id',$emp_type_ids)
                ->orderBy('rank_code', 'ASC')->pluck('catg_id')->toArray();
        }else{
            return EmployeeCategory::where('catg_status', 1)
                ->whereIn('emp_type_id',$emp_type_ids)
                ->orderBy('rank_code', 'ASC')->pluck('catg_id')->toArray();
        }
    }
    
    public function getListOfDesignationRecordsByMultipleAutoIds($catg_id_list=null,$emp_type_ids, $branch_office_id=null)
    {
        
        if($catg_id_list== null){

          return  EmployeeCategory::where('catg_status', 1)
            ->orderBy('rank_code', 'ASC')
            ->whereIn('emp_type_id',$emp_type_ids)
            ->select('catg_id','catg_name')->get();

        }else{
            return   EmployeeCategory::where('catg_status', 1)
                ->orderBy('rank_code', 'ASC')
                ->whereIn('catg_id',$catg_id_list)
                ->whereIn('emp_type_id',$emp_type_ids)
                ->select('catg_id','catg_name')->get();
        }


    }

    public function getEmployeeCategory($id)
    {
        return  EmployeeCategory::where('catg_status', 1)->where('catg_id', $id)->first();
    }
     public function getEmployeeCategoryName($id)
    {
        return  EmployeeCategory::where('catg_status', 1)->where('catg_id', $id)->first()->catg_name;
    }
    
    public function insertNewCategoryName($catg_name, $emp_type_id,$dh_auto_id)
    {
        if ($this->checkThisEmployeeTradeIsExist($catg_name, $emp_type_id)) {
            return 0;
        } else {
            return EmployeeCategory::insert([
                'catg_name' => $catg_name,
                'emp_type_id' => $emp_type_id,
                'dh_auto_id' => $dh_auto_id,
                'created_at' => Carbon::now(),
            ]);
        }
    }
    
    
    public function checkThisEmployeeTradeIsExist($catg_name, $emp_type_id){

        $reqCatNLow = strtolower($catg_name);
        $reqCatNUp = strtoupper($catg_name);
        $empCategory = EmployeeCategory::where('catg_name', $reqCatNLow)
        ->orWhere('catg_name', $reqCatNUp)
        ->orWhere('catg_name', $catg_name)
        ->first();
        if ($empCategory != null) {
            return true;
        } else {false;}
    }

    
  public function updateCategoryName($id, $catg_name, $emp_type_id,$dh_auto_id)
    {
        // if ($this->checkThisEmployeeTradeIsExist($catg_name, $emp_type_id)) {
        //     return 0;
        // } else {
        
         $reqCatNLow = strtolower($catg_name);
            $reqCatNUp = strtoupper($catg_name);
            $cat_records = EmployeeCategory::where('catg_name', $reqCatNLow)
                            ->orWhere('catg_name', $reqCatNUp)
                            ->orWhere('catg_name', $catg_name)
                            ->count();
            if($cat_records > 1 )
                return 0;
            
            return EmployeeCategory::where('catg_id', $id)->update([
                'catg_name' => $catg_name,
                'emp_type_id' => $emp_type_id,
                'dh_auto_id' => $dh_auto_id,
                'updated_at' => Carbon::now(),
            ]);
       // }
    }

    public function deleteCategory($id)
    {
        return EmployeeCategory::where('catg_id', $id)->update([
            'catg_status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }

    /*
     ========================================================================== 
     ============================= Employee Sponser ============================ 
     ========================================================================== 
    */

    public $sponserInfoTable = 'sponsors';

    // will be remove 2.7.24 
    // public function getAllSponserInfoForDropdown1()
    // {
    //     return DB::table($this->sponserInfoTable)->select('spons_id', 'spons_name')
    //         ->where('status', 1)->orderBy('spons_name', 'ASC')->get();
    // }

    public function getAllSponserInfoForDropdown($branch_office_id=null)
    {
        return DB::table($this->sponserInfoTable)->select('spons_id', 'spons_name')
            ->where('status', 1)->where('branch_office_id', $branch_office_id == null ? 1 : Auth::user()->branch_office_id)
            ->orderBy('spons_name', 'ASC')->get();
    }
    
    // join with subcontract_infos table 
    public function getAllSubcontractorsSponsorsForDropdown($branch_office_id=null)
    {
         return  DB::table($this->sponserInfoTable)->select('spons_id', 'spons_name')
            ->where('sponsors.status', 1)
            ->where('sponsors.branch_office_id', $branch_office_id == null ? 1 : Auth::user()->branch_office_id)
            ->where('sponsors.owner_name','Subcon')
            ->orderBy('sponsors.spons_name', 'ASC')->get();
            
    //   return  DB::table($this->sponserInfoTable)->select('spons_id', 'spons_name')
    //         ->leftjoin('subcontractor_infos','sponsors.spons_id' ,'=', 'subcontractor_infos.sponsor_id')
    //         ->where('sponsors.status', 1)
    //         ->where('subcontractor_infos.act_status',1)
    //         ->where('sponsors.branch_office_id', $branch_office_id == null ? 1 : Auth::user()->branch_office_id)
    //         ->where('sponsors.owner_name','Subcon')
    //         ->orderBy('subcontractor_infos.subcon_name', 'ASC')->get();

    }
    public function getAllActiveSponserInfoByMultipleId($spons_id_list_array)
    {
        return DB::table($this->sponserInfoTable)->select('spons_id', 'spons_name')->whereIn('spons_id', $spons_id_list_array)
            ->where('status', 1)->orderBy('spons_name', 'ASC')->get();
    }
    public function getListOfActiveSponserInfoOrderBySponsorIdAndByMultipleId($spons_id_list_array)
    {
        return DB::table($this->sponserInfoTable)->select('spons_id', 'spons_name')->whereIn('spons_id', $spons_id_list_array)
            ->where('status', 1)->orderBy('spons_id', 'ASC')->get();
    }
    
    public function getAllSponser()
    {
        return  Sponsor::where('status', 1)->orderBy('spons_id', 'DESC')->get();
    }
    
    public function getAllSponsorsInformationForListView($branch_office_id)
    {
        return  Sponsor::orderBy('status', "DESC")->orderBy('spons_id', 'DESC')
        ->where('branch_office_id',$branch_office_id)->get();
    }
    
    public function getAllActiveSponsorIdAsArray()
    {
         return   Sponsor::where('status', 1)->orderBy('spons_name', 'ASC')->pluck('spons_id')->toArray();
    }
    
    public function getAllActiveSponsorIdAsArrayOfABranchOffice($branch_office_id)
    {
         return   Sponsor::where('branch_office_id',$branch_office_id)->where('status', 1)->orderBy('spons_name', 'ASC')->pluck('spons_id')->toArray();
    }
    
    public function getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes($branch_office_id,$sponsor_types)
    {
         return   Sponsor::where('branch_office_id',$branch_office_id)
         ->whereIn('owner_name',$sponsor_types)
         ->where('status', 1)->orderBy('spons_name', 'ASC')->pluck('spons_id')->toArray();
    }
    
    public function getAllActiveSubcontractorSponsorIdAsArrayOfABranchOffice($branch_office_id)
    {
         return   Sponsor::where('branch_office_id',$branch_office_id)
         ->where('owner_name','Subcon')
         ->where('status', 1)->orderBy('spons_name', 'ASC')->pluck('spons_id')->toArray();
    }
    
    public function searchASponserBySponsorId($id)
    {
        return Sponsor::where('spons_id', $id)->first();
    }

    public function findASponser($id)
    {
        return DB::table($this->sponserInfoTable)->where('status', 1)->where('spons_id', $id)->first();
    }
    
     public function findASponserBySponsorNameAsSubContractor($name)
    {
        return DB::table($this->sponserInfoTable)->where('status', 1)->where('spons_name', $name)->first();
    }
    
     public function getASponserNameBySponerId($id)
    {
        return (Sponsor::where('spons_id', $id)->first())->spons_name;
        
    }
 
   
    public function insertNewSponerName($spons_name)
    {
        if ($this->CheckThisSponserNameIsExist($spons_name)) {
            return 0;
        } else {
             return Sponsor::insert([
                'spons_name' => $spons_name,
                'create_by_id' => Auth::user()->id, // $creator,
                 'branch_office_id' => Auth::user()->branch_office_id,
                'created_at' => Carbon::now(),
            ]);
        }
    }
    public function CheckThisSponserNameIsExist($spons_name)
    {
       return Sponsor::whereRaw('LOWER(`spons_name`) LIKE ? ',['%'.strtolower($spons_name).'%'])->get()->count() > 0 ? true: false;

    }
    
    public function updateSponerName($spons_id, $spons_name,$owner_name,$status)
    {
        // if ($this->CheckThisSponserNameIsExist($spons_name)) {
        //     return 0;
        // } else {
            return Sponsor::where('spons_id', $spons_id)->update([
                'spons_name' => $spons_name,
                'owner_name' => $owner_name,
                'status' => $status,
                'create_by_id' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
       // }
    }
    
    public function updateSponorAsInactive($spons_id)
    {
        return Sponsor::where('spons_id', $spons_id)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }

    /*
     ========================================================================== 
     ============================= Company Project ============================ 
     ========================================================================== 
    */

    public $projectInfoTable = 'project_infos';

    public function getAllProjectInfoForDropdown()
    {
        return DB::table($this->projectInfoTable)->select('proj_id', 'proj_name')
            ->where('status', 1)->orderBy('proj_name', 'ASC')->get();
    }
    public function getAllProjectInformation()
    {
        return  ProjectInfo::where('status', 1)->orderBy('proj_id', 'DESC')->get();
    }
    /* GET FIND Project */
    public function findAProjectInformation($proj_id)
    {
        // return DB::table($this->projectInfoTable)->where('status', 1)->where('proj_id', $proj_id)->first();
        return ProjectInfo::where('status', 1)->where('proj_id', $proj_id)->first();
    }
    
     public function getProjectNameByProjectId($proj_id)
    {
        return (ProjectInfo::where('proj_id', $proj_id)->first())->proj_name ?? '';
    }
    public function findLoginUserProject()
    {
        $loginUserId = Auth::user()->id;
        $user = (new AuthenticationDataService())->findUserById($loginUserId);
        $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmail($user->email);
        if ($employee) {
            $project  = $this->findAProjectInformation($employee->project_id);
            if ($project == null) {
                return $this->getAllProjectInfoForDropdown();
            }
            return [$project];
        } else {
            return $this->getAllProjectInfoForDropdown();
        }
    }
    /* GET Project MULTIPLE IMAGE */
    public function getProjectMultipleImage($project_id)
    {
        return ProjectImgUpload::where('project_id', $project_id)->get();
    }

    public function deleteProject($proj_id)
    {
        return ProjectInfo::where('proj_id', $proj_id)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }
    // public function insertNewProjectInformation($proj_name, $starting_date, $address, $proj_code, $proj_budget, $proj_deadling, $proj_description, $proj_main_thumb_Path)
    // {
    //     return ProjectInfo::insert([
    //         'proj_name' => $proj_name,
    //         'starting_date' => $starting_date,
    //         'address' => $address,
    //         'proj_code' => $proj_code,
    //         'proj_budget' => $proj_budget,
    //         'proj_deadling' => $proj_deadling,
    //         'proj_description' => $proj_description,
    //         'proj_main_thumb' => $proj_main_thumb_Path,
    //         'created_at' => Carbon::now(),
    //     ]);
    // }
    // public function updateProjectInformation($proj_id, $proj_name, $starting_date, $address, $proj_code, $proj_budget, $proj_deadling, $proj_description)
    // {
    //     return ProjectInfo::where('proj_id', $proj_id)->update([
    //         'proj_name' => $proj_name,
    //         'starting_date' => $starting_date,
    //         'address' => $address,
    //         'proj_code' => $proj_code,
    //         'proj_budget' => $proj_budget,
    //         'proj_deadling' => $proj_deadling,
    //         'proj_description' => $proj_description,
    //         'updated_at' => Carbon::now(),
    //     ]);
    // }

    public function assignProjectIncharge($proj_id, $emp_id)
    {
        return ProjectInfo::where('proj_id', $proj_id)->update([
            'proj_Incharge_id' => $emp_id,
            'updated_at' => Carbon::now()
        ]);
    }


    /*
     ========================================================================== 
     ================ Employee Assign to New Project ==========================
     ========================================================================== 
    */

    public function updateAnEmployeeProjectHistoryByProjectRelease($emp_proj_history_id, $release_date, $updated_by,$approved_by)
    {
        return  EmpProjectHistory::where('eph_id',$emp_proj_history_id)->update([
            'relesed_date' => $release_date,
            'create_by_id' => $updated_by,
            'approved_by' => $approved_by,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function findAnEmployeeAssignedProjectHistory($emp_Auto_id){
      return  EmpProjectHistory::select('eph_id','emp_id','project_id','asigned_date','relesed_date','status')
        ->where('emp_id',$emp_Auto_id)->orderBy('eph_id', 'DESC')->get()->first();
    }

     
    public function assignEmployeeToNewProject($emp_auto_id, $project_id, $asigned_date, $relesed_date, $created_by,$remarks)
    {
      
        $empProjWorkRecord = $this->findAnEmployeeAssignedProjectHistory($emp_auto_id);
        if($empProjWorkRecord){
            $this->updateAnEmployeeProjectHistoryByProjectRelease($empProjWorkRecord->eph_id,$relesed_date,$created_by,$created_by);        
            $asigned_date = Carbon::createFromFormat('Y-m-d', $asigned_date);
            $asigned_date = $asigned_date->addDays(1);
            $asigned_date = date("Y-m-d",strtotime($asigned_date));
        }
        return  EmpProjectHistory::insertGetId([
            'emp_id' => $emp_auto_id,
            'project_id' => $project_id,
            'asigned_date' => $asigned_date, 
            'create_by_id' => $created_by,
             'remarks' => $remarks,
            'created_at' => Carbon::now(),
        ]);
    }

    // Find Employe Project History For Report
     public function getAnEmployeeWorkingProjectHisotry($emp_auto_id){
        return EmpProjectHistory::where('emp_id', $emp_auto_id)
                ->leftjoin('employee_infos', 'emp_project_histories.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('project_infos', 'emp_project_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->get();
    }
 
    /*
     ==========================================================================
     ================ Employee Job Status =====================================
     ==========================================================================
    */

    public function insertEmployeeJobStatusUpdateRecord($emp_Auto_id, $date, $job_status)
    {
        return  EmployeeStatusRecord::insertGetId([
            'emp_id' => $emp_Auto_id,
            'date' => $date,
            'job_status' => $job_status,
            'create_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }
    
    


    /*
     ========================================================================== 
     ================ Employee Agency Information =============================
     ========================================================================== 
    */

    public function getAgencyInformationForDropdownList()
    {
        return DB::table('agency_infos')->select('agc_info_auto_id', 'agc_title')->orderBy('agc_title', 'ASC')->get();
       

    }
    
    
    
     /*
     ==========================================================================
     ================ Employee Activity Records  =============================
     ==========================================================================
    */
    public function employeeWiseActivityRecordInsert(
      $emp_auto_id,
      $activity_remarks,
      $activity_description,
      $activityType,
      $activity_date,
      $job_status,
      $createBy
    ){
        return EmployeeActivity::insertGetId([
            'emp_auto_id' => $emp_auto_id,
            'remarks' => $activity_remarks,
            'description' => $activity_description,
            'emp_act_type_id'=> $activityType,
            'job_status' =>  $job_status,
            'create_by_id' => $createBy,
            'create_at' =>   $activity_date,
        ]);
    }
    
    
    public function getEmployeeActivityEmployeeDetailsListReport($project_id_list,$job_status,$from_date,$to_date,$branch_office_id){
       return $records = DB::SELECT('CALL getEmployeeActivityEmployeeListDateToDateReport(?,?,?,?)',array($job_status,$from_date,$to_date,$branch_office_id));
    }
    
    // public function searchRunawayEmployeesByDateToDateForHRReport($job_status,$from_date,$to_date,$branch_office_id){
    //     return  DB::SELECT('CALL searchRunawayEmployeesByDateToDateReport(?,?,?,?)',array($job_status,$from_date,$to_date,$branch_office_id));
    // }
    
     public function getVacationApprovedMonthlyEmployeeSummaryReportByMonthAndYear($month,$job_status,$year,$branch_office_id){

      return  EmployeeActivity::selectRaw('year(create_at) year, monthname(create_at) month, count( DISTINCT (emp_auto_id)) total_emp')
                  ->groupBy('year', 'month')
                  ->whereMonth('create_at',$month)
                  ->whereYear('create_at',$year)
                  ->where('job_status',$job_status)
                  ->where('branch_office_id',$branch_office_id)
                  ->orderBy('year', 'desc')
                  ->first();
     }
     
     
     /*
     ==========================================================================
     ================ Employee Bank Details Records  =============================
     ==========================================================================
    */
    
      public function insertNewBankName($bank_name,$branch_office_id){
        return BankName::insertGetId([
            'bn_name' => $bank_name,
            'branch_office_id'=>$branch_office_id
        ]);
    }
    
    public function updateBankNameByAutoId($bn_auto_id ,$bank_name){
        return BankName::where('bn_auto_id',$bn_auto_id)->update([
            'bn_name' => $bank_name,
        ]);
    }
    
    public function getListOfBankName(){
        return BankName::get();
    }
    
    public function getListOfBankNameOfABranchForDropdown($branch_office_id){
        return BankName::where('branch_office_id',$branch_office_id)->get();
    }


    public function insertEmployeeBankDetailsInformation($emp_auto_id,$account_holder_name,$account_number, $acc_iban, $bank_id, $remarks, $created_by,$date){
          return EmployeeBankDetails::insertGetId([
              'emp_auto_id' => $emp_auto_id,
              'acc_holder_name' => $account_holder_name,
              'acc_number' => $account_number,
              'acc_iban'=> $acc_iban,
              'bank_id' =>  $bank_id,
              'created_by' => $created_by, 
              'remarks' => $remarks, 
              'created_at'=>$date
          ]);
    }

    public function updateEmployeeBankDetailsInformation($ebd_auto_id,$account_holder_name,$account_number, $acc_iban, $bank_id, $remarks, $updated_by ,$date){
        return EmployeeBankDetails::where('ebd_auto_id',$ebd_auto_id)->update([            
            'acc_holder_name' => $account_holder_name,
            'acc_number' => $account_number,
            'acc_iban'=> $acc_iban,
            'bank_id' =>  $bank_id,
            'updated_by' => $updated_by , 
            'remarks' => $remarks,
            'updated_at' =>$date 
        ]);
    }
    public function inactiveAnEmployeePreviousBankAccount($ebd_auto_id,$remarks, $updated_by ,$date){
        return EmployeeBankDetails::where('ebd_auto_id',$ebd_auto_id)->update([           
            'is_active' =>  0,
            'updated_by' => $updated_by , 
            'remarks' => $remarks,
            'updated_at' =>$date 
        ]);
    }

    public function getAnEmployeeBankInformationRecordByEmployeeAutoId($emp_auto_id){
        return EmployeeBankDetails::where('emp_auto_id',$emp_auto_id)->where('is_active',1)->first();
    }

 
    
    public function getAnEmployeeDetailsAndBankInformationRecordByEmployeeId($employee_id,$branch_office_id){
        return EmployeeBankDetails::select('ebd_auto_id','employee_infos.employee_id','employee_infos.emp_auto_id','acc_holder_name','acc_number','acc_iban','bank_id','is_active','remarks','employee_infos.employee_name','employee_infos.akama_no','employee_infos.mobile_no','employee_infos.hourly_employee','salary_details.payment_method')
                ->leftjoin('employee_infos', 'employee_bank_details.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->where('employee_infos.employee_id',$employee_id)
                ->where('employee_infos.branch_office_id',$branch_office_id)
                ->where('employee_bank_details.is_active',1)->first();
    }
    
    public function getAnEmployeeBankInformationRecordByAutoId($ebd_auto_id){
        return EmployeeBankDetails::where('ebd_auto_id',$ebd_auto_id)->first();
    }
    
    
        public function getWPSEmployeeInfoWithBankDetailsForAttenanceSummaryReportByEmployeeId($employee_id,$branch_office_id)
    {
       return EmployeeBankDetails::select('ebd_auto_id','employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.email','acc_holder_name','acc_number','acc_iban','employee_bank_details.is_active','remarks','employee_infos.employee_name',
        'employee_infos.akama_no','employee_infos.mobile_no','employee_infos.hourly_employee','employee_categories.catg_name','bank_names.bn_name','bank_names.bank_code','salary_details.payment_method')
                ->leftjoin('employee_infos', 'employee_bank_details.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('employee_infos.employee_id',$employee_id)
                ->where('employee_infos.branch_office_id',$branch_office_id)
                ->where('employee_bank_details.is_active',1)->first();
    }
    
    
    
    
    
    
    
}
