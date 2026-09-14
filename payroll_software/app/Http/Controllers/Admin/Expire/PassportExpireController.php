<?php

namespace App\Http\Controllers\Admin\Expire;

use App\Http\Controllers\Controller; 
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Enum\JobStatusEnum;

class PassportExpireController extends Controller
{
  public function index()
  {

    $emp_type_id = (new EmployeeRelatedDataService())->getAllEmployeeType();



    $emp = (new EmployeeDataService())->getAllEmployeesInformation(-1, 1);
    return view('admin.expired.passport-expired.index', compact('emp_type_id', 'emp'));
  }
}
