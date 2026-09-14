<?php

namespace App\Http\Controllers\Admin\Expire;

use App\Http\Controllers\Controller; 
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Enum\JobStatusEnum;


class IqamaExpireController extends Controller
{
  public function index()
  {
    $emp_type_id = (new EmployeeRelatedDataService())->getAllEmployeeType();

    /* employee info */
    // $empob = new EmployeeInfoController();
    // $emp = $empob->getAllEmployees();
    $emp = (new EmployeeDataService())->getAllEmployeesInformation(-1, 1);
    return view('admin.expired.iqama-expired.index', compact('emp_type_id', 'emp'));
  }

  // public function expiredDate(Request $request){
  //     $emp_type_id = $request->emp_id;
  //
  //     $currentDate = Carbon::now()->format();
  //
  //     if($emp_type_id == 1){
  //         $employee = EmployeeInfo::with("employeeType","category")->where('emp_type_id',1)->orderBy('emp_auto_id','DESC')->get();
  //
  //
  //
  //
  //         return response()->json(['employee' => $employee, 'date' => $currentDate ]);
  //     }else{
  //       $employee = EmployeeInfo::with("employeeType","category")->where('emp_type_id',2)->orderBy('emp_auto_id','DESC')->get();
  //
  //
  //
  //
  //
  //       return response()->json(['employee' => $employee, 'date' => $currentDate ]);
  //     }
  // }







}
