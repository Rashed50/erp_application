<?php

namespace App\Http\Controllers\Admin\EmpLeave;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\EmpLeave\LeaveReasonController;
use App\Http\Controllers\Admin\EmpLeave\LeaveTypeController;
use Illuminate\Http\Request;
use App\Models\EmpLeave;
use App\Models\User;
use Carbon\Carbon;
use Session;
use DateTime;
use Auth;

class EmpLeaveController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function getAll()
  {
    return $all = EmpLeave::with('employee')->where('status', 'pending')->orderBy('emleave_id', 'DESC')->get();
  }


  public function showForm($leave_id)
  {
    $findAppl = EmpLeave::where('emleave_id', $leave_id)->first();
    return json_encode($findAppl);
  }




  public function insert(Request $request)
  {
    /*
    | ------------------ Backend Form Validation
    */
    $this->validate($request, [
      'leave_type_id' => 'required',
      'leave_reason_id' => 'required',
      'start_date' => 'required',
      'end_date' => 'required',
      'description' => 'required',
    ], []);

    $start = $request->start_date;
    $end = $request->end_date;

    $first = new DateTime($start);
    $last = new DateTime($end);

    $interval = $last->diff($first);
    $days = $interval->format('%a');

    $emp_id = Auth::user()->id;

    $emp = EmpLeave::where('emp_id', $emp_id)->first();
    // dd($emp);

    /*
    | ------------------ Insert Data in Database
    */
    if ($emp) {
      Session::flash('error_exist', 'value');
      return redirect()->back();
    } else {
      $insert = EmpLeave::insert([
        'emp_id' => $emp_id,
        'leave_type_id' => $request->leave_type_id,
        'leave_reason_id' => $request->leave_reason_id,
        'required_day' => $days,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'description' => $request->description,
        'created_at' => Carbon::now(),
      ]);

      if ($insert) {
        Session::flash('success', 'value');
        return redirect()->back();
      } else {
        Session::flash('error', 'value');
        return redirect()->back();
      }
    }
  }

  public function approve(Request $request)
  {
    /*
      | ------------------ Backend Form Validation
      */
    $this->validate($request, [
      'start_date' => 'required',
      'end_date' => 'required',
    ], []);

    $start = $request->start_date;
    $end = $request->end_date;

    $first = new DateTime($start);
    $last = new DateTime($end);

    $interval = $last->diff($first);
    $days = $interval->format('%a');

    $emp_id = Auth::user()->id;
    /*
      | ------------------ Insert Data in Database
      */
    $update = EmpLeave::where('emleave_id', $request->id)->update([
      'approve_day' => $days,
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
      'status' => 'approve',
      'approve_by_id' => $emp_id,
      'updated_at' => Carbon::now(),
    ]);

    if ($update) {
      Session::flash('success', 'value');
      return redirect()->back();
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }







  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

  public function index()
  {
    /* ================ LeaveType */
    $levTypeObj = new LeaveTypeController();
    $allType = $levTypeObj->getAll();
    /* ================ LeaveReason */
    $levReasonObj = new LeaveReasonController();
    $allReason = $levReasonObj->getAll();

    return view('admin.employee-leave.apply', compact('allType', 'allReason'));
  }

  public function applyList()
  {
    $all = $this->getAll();
    //dd($all);
    return view('admin.employee-leave.apply-list', compact('all'));
  }









  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */










  /* ======================================================================== */
}
