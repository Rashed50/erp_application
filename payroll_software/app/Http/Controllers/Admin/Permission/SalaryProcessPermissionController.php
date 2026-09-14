<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Models\AccessPermission;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;


class SalaryProcessPermissionController extends Controller
{

    public function salaryProcessPermissionListUi()
    {
        $all = (new AuthenticationDataService())->getAllAccessPermissionList();
        $month = (new CompanyDataService())->getAllMonth();
        return view('admin.permission.salary-process.all', compact('all', 'month'));
    }

    public function salaryProcessingPermissionInsert(Request $request)
    {
        if($request->permission_id){

            $is_lock =  $request->lock_checkbox == "on" ? 1: 0;
            $update = (new AuthenticationDataService())->updateSalaryPermissionInfos($request->permission_id, $request->month, $request->year, $request->lock_date,  $is_lock );
            if($update){
                Session::flash('success', 'Successfully Updated');
                return redirect()->route('salary-process-permission-ui');
            }else{
                Session::flash('error','Update Operation Failed, May Be Data Already Exist');
                return redirect()->back();
            }
        }else { 
            $insert = (new AuthenticationDataService())->insertNewSalaryProcessPermision($request->month, $request->year, $request->lock_date);
            if ($insert > 0) {
                Session::flash('success', 'Successfully Added Salary Process Permission');
                return redirect()->back();
            } else {
                Session::flash('error', 'Operation Failed, May Be Data Already Exist');
                return redirect()->back();
            }
        }
    }
 

    // checkSalaryProcessPermission
    public function checkSalaryProcessPermission($month, $year)
    {
        return  (new AuthenticationDataService())->getSalaryProcessAccessPermission($month,$year);
    }
}
