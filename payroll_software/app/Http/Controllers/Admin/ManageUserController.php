<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Session;

class ManageUserController extends Controller
{
  /*
      =============================
      ======DATABASE OPERATION=====
      =============================
    */
  public function getAllUser()
  {
    return $all = User::get();
  }

  public function getFindId($id)
  {
    return $find = User::where('id', $id)->first();
  }

  public function getEmployee()
  {
    return $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, 1);
  }

  public function getRole()
  {
    return $all = Role::get();
  }


  public function activeInactive(Request $request)
  {
    $id = $request->user_id;
    $user = (new AuthenticationDataService())->findUserById($request->Id);


    if ($user->status == 0) {
      $userStatus = (new AuthenticationDataService())->activateUserAuthentication($id);
      return response()->json(['success' => 'Sucessfully Completed', 'value' => 1]);
    } else {
      $userStatus = (new AuthenticationDataService())->deactivateUser($id);
      return response()->json(['success' => 'Sucessfully Completed', 'value' => 1]);
    }
  }

  // Edit User Role
  public function editRole(Request $request)
  {
    $all = (new AuthenticationDataService())->getARole($request->Id);
    return json_encode($all);
  }


  /*
      =============================
      ========BLADE OPERATION======
      =============================
    */
  public function index()
  {
      dd('ooo');
    $user_id =  Auth::user()->id;
    $all = (new AuthenticationDataService())->getUsersWithOutLoginUser($user_id);
    $role = $this->getRole();
    return view('admin.manage-user.index', compact('role', 'all'));
  }





  public function edit($id)
  {
    $edit = $this->getFindId($id);
    $role = $this->getRole();
    return view('admin.manage-user.edit', compact('edit', 'role'));
  }

  public function insert(Request $request)
  {
    // form validation
    $this->validate($request, [
      'password' => 'required|min:8|confirmed'
    ], []);
    /* making */
    $fineEmp = (new AuthenticationDataService())->findUserByEmpIdAndPhoneNumber($request->emp_id, $request->phone_number);

    if ($fineEmp) {
      Session::flash('duplicate', 'value');
      return Redirect()->back();
    } else {
      $insert = (new AuthenticationDataService())->createNewUser(
        $request->employee_name,
        $request->emp_id,
        $request->email,
        $request->phone_number,
        $request->password,
        $request->role_id
      );

      if ($insert) {
        Session::flash('success', 'value');
        return Redirect()->back();
      } else {
        Session::flash('error', 'value');
        return Redirect()->back();
      }
    }
  }


  public function updatePassword(Request $request)
  {
    // form validation
    $request->validate([
      'oldPassword' => 'required',
      'password' => 'required|min:8',
      'password_confirmation' => 'required|min:8',
    ]);

    $oldDbPass =  (new AuthenticationDataService())->findUserById($request->id);
    $db_pass = $oldDbPass->password;
    $current_password = $request->oldPassword;
    $newpass = $request->password;
    $confirmpass = $request->password_confirmation;

    if (Hash::check($current_password, $db_pass)) {
      if ($newpass === $confirmpass) {

        (new AuthenticationDataService())->updateUserPassword($request->id, $newpass);
        Session::flash('success', 'value');
        return Redirect()->back();
      } else {
        Session::flash('passwordNotMatch', 'value');
        return Redirect()->back();
      }
    } else {
      Session::flash('oldPasswordNotMatch', 'value');
      return Redirect()->back();
    }
  }




  public function update(Request $request)
  {
    /* insert data in database */

    $update = (new AuthenticationDataService())->updateUserRole($request->id, $request->role_id);
    if ($update) {
      Session::flash('success_update', 'value');
      return Redirect()->back();
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }


  public function delete($id)
  {
    $delete = (new AuthenticationDataService())->deactivateUser($id);
    if ($delete) {
      Session::flash('delete', 'value');
      return Redirect()->back();
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }
}
