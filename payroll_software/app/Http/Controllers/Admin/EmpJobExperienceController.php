<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Http\Request;
use App\Models\EmpJobExperience;
use App\Models\EmployeeInfo;
use Carbon\Carbon;
use Session;
use Auth;

class EmpJobExperienceController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */

  public function getAll(){
    return $all = EmpJobExperience::where('status',1)->get();
  }

  public function findId($id){
    return $find = EmpJobExperience::where('status',1)->where('ejex_id',$id)->first();
  }

  public function insert(Request $request){
    // form validation
    $this->validate($request,[
      'emp_id' => 'required',
      'ejex_title' => 'required',
      'company_name' => 'required',
      'designation' => 'required',
      'responsibity' => 'required',
      'starting_date' => 'required',
      'end_date' => 'required',
    ],[

    ]);

    $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->emp_id);
     if($findEmployee){
      $emp_id = $findEmployee->emp_auto_id;
      $user = Auth::user()->id;
      /* insert data in database */
      $insert = EmpJobExperience::insert([
        'emp_id' => $emp_id,
        'ejex_title' => $request->ejex_title,
        'starting_date' => $request->starting_date,
        'end_date' => $request->end_date,
        'company_name' => $request->company_name,
        'designation' => $request->designation,
        'responsibity' => $request->responsibity,
        'create_by_id' => $user,
        'created_at' => Carbon::now(),
      ]);
      /* redirect page */
      if($insert){
        Session::flash('success','value');
        return redirect()->back();
      } else{
        Session::flash('error','value');
        return redirect()->back();
      }
    /* end if start else */
    }else{
      Session::flash('invalid_employee','value');
      return redirect()->back();
    /* end else */
    }





  }

  public function update(Request $request){
    // form validation
    $this->validate($request,[
      'emp_id' => 'required',
      'ejex_title' => 'required',
      'company_name' => 'required',
      'designation' => 'required',
      'responsibity' => 'required',
      'starting_date' => 'required',
      'end_date' => 'required',
    ],[

    ]);

    $findEmployee = EmployeeInfo::where('employee_id',$request->emp_id)->first();
    if($findEmployee){
      $emp_id = $findEmployee->emp_auto_id;
      $user = Auth::user()->id;
      $id = $request->id;
      /* insert data in database */
      $update = EmpJobExperience::where('status',1)->where('ejex_id',$id)->update([
        'emp_id' => $emp_id,
        'ejex_title' => $request->ejex_title,
        'starting_date' => $request->starting_date,
        'end_date' => $request->end_date,
        'company_name' => $request->company_name,
        'designation' => $request->designation,
        'responsibity' => $request->responsibity,
        'create_by_id' => $user,
        'updated_at' => Carbon::now(),
      ]);
      /* redirect page */
      if($update){
        Session::flash('success_update','value');
        return redirect()->route('emp-job-experience');
      } else{
        Session::flash('error','value');
        return redirect()->back();
      }
    /* end if and start else */
    }else{
      Session::flash('invalid_employee','value');
      return redirect()->back();
    /* end else */
    }

  }

  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

  public function index(){
    $all = $this->getAll();
    return view('admin.emp-job.all',compact('all'));
  }

  public function edit($id){
    $edit = $this->findId($id);
    return view('admin.emp-job.edit',compact('edit'));
  }



  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */

}
