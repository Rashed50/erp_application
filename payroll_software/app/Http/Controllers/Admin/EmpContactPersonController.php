<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Http\Request;
use App\Models\EmpContactPerson;
use Carbon\Carbon;
use Session;
use Auth;

class EmpContactPersonController extends Controller{
    /*
    |--------------------------------------------------------------------------
    |  DATABASE OPERATION
    |--------------------------------------------------------------------------
    */

    public function getAll(){
      return $all = EmpContactPerson::where('status',1)->get();
    }

    public function findId($id){
      return $find = EmpContactPerson::where('status',1)->where('ecp_id',$id)->first();
    }

    public function insert(Request $request){
      // form validation
      $this->validate($request,[
        'emp_id' => 'required',
        'ecp_name' => 'required',
        'ecp_mobile1' => 'required',
        'ecp_relationship' => 'required',
        'details' => 'required',
      ],[

      ]);
      /* Check Valid Employee */
      $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->emp_id);
       if($findEmployee){
        $emp_id = $findEmployee->emp_auto_id;
        $user = Auth::user()->id;
        /* insert data in database */
        $insert = EmpContactPerson::insert([
          'emp_id' => $emp_id,
          'ecp_name' => $request->ecp_name,
          'ecp_mobile1' => $request->ecp_mobile1,
          'ecp_mobile2' => $request->ecp_mobile2,
          'ecp_email' => $request->ecp_email,
          'ecp_relationship' => $request->ecp_relationship,
          'details' => $request->details,
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
        'ecp_name' => 'required',
        'ecp_mobile1' => 'required',
        'ecp_relationship' => 'required',
        'details' => 'required',
      ],[

      ]);

      $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->emp_id);
       $emp_id = $findEmployee->emp_auto_id;
      $user = Auth::user()->id;
      $id = $request->id;
      /* insert data in database */
      $update = EmpContactPerson::where('status',1)->where('ecp_id',$id)->update([
        'emp_id' => $emp_id,
        'ecp_name' => $request->ecp_name,
        'ecp_mobile1' => $request->ecp_mobile1,
        'ecp_mobile2' => $request->ecp_mobile2,
        'ecp_email' => $request->ecp_email,
        'ecp_relationship' => $request->ecp_relationship,
        'details' => $request->details,
        'create_by_id' => $user,
        'updated_at' => Carbon::now(),
      ]);
      /* redirect page */
      if($update){
        Session::flash('success_update','value');
        return redirect()->route('emp-contact-person');
      } else{
        Session::flash('error','value');
        return redirect()->back();
      }


    }





    /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

    public function index(){
      $all = $this->getAll();
      return view('admin.emp-contact.all',compact('all'));
    }

    public function edit($id){
      $edit = $this->findId($id);
      return view('admin.emp-contact.edit',compact('edit'));
    }



    /*
    |--------------------------------------------------------------------------
    |  API OPERATION
    |--------------------------------------------------------------------------
    */
}
