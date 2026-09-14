<?php
 namespace App\Http\Controllers\DataServices;

use App\Models\EmpTUVInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

 class EmpTUVInfoDataService
{
 
    public function checkThisEmpInfoIsExist($emp_auto_id){
        $empInfo = EmpTUVInfo::where('emp_auto_id', $emp_auto_id)->count();
        return $empInfo > 0 ? false : true;
    }

    public function insertAnEmployeeTUVCardInformation($emp_auto_id, $tvu_card_no, $catg_id,$company_id,$tvu_issue_date,$tvu_expire_date,$create_by_id,$branch_office_id){
        // if ($this->checkThisEmpInfoIsExist($request->emp_auto_id) == false) {
        //     return 0;
        // } else {
            return EmpTUVInfo::insertGetId([
                'emp_auto_id' => $emp_auto_id,
                'card_no' => $tvu_card_no,
                'trade_id' => $catg_id,
                'company_id' => $company_id,
                'issue_date' => $tvu_issue_date,
                'expire_date' => $tvu_expire_date,
                'create_by_id' => $create_by_id,
                'branch_office_id' => $branch_office_id,
                'created_at' => Carbon::now(),
            ]);
      //  }
    }

    public function getAllEmployeeTUVDetailsInfoForReport($branch_office_id){
        return EmpTUVInfo::with('employee')->with('designation')->where('branch_office_id',$branch_office_id)->orderBy('tuv_auto_id', 'DESC')->get();
    }

    public function updateEmployeeTUVPhotoDbPath($tuvID, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  EmpTUVInfo::where('tuv_auto_id', $tuvID)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }

    
    
}
