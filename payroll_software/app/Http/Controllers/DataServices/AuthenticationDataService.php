<?php

namespace App\Http\Controllers\DataServices;


use App\Models\User;
use App\Models\AccessPermission;
use App\Models\UserLoginActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;



class AuthenticationDataService
{

    public function countTotalUsersInTheSystem()
    {
        return User::count();
    }

    public function countTotalUsersInABranchOffice($branch_office_id)
    {
        return User::where('branch_office_id', $branch_office_id)->count();
    }

    public function getAllUsers()
    {
        return  User::get();
    }

    public function findUserById($id)
    {
        return  User::where('id', $id)->first();
    }



    public function getLoginUserName()
    {
        return Auth::user()->name;
    }



    public function getAllActiveUsersForDropdownList()
    {
        return  User::select('users.id', 'users.name', 'employee_infos.employee_id')->where('status', 1)
            // ->where('id', '!=', $user_id)->where('id', '!=', 1)
            ->leftjoin('employee_infos', 'users.emp_auto_id', '=', 'employee_infos.emp_auto_id')->orderBy('employee_infos.employee_id', 'ASC')->get();
    }


    public function getUsersThoseAreInsertedEmployeeAdvanceInformation($branch_office_id)
    {
        return    DB::select('call getUsersThoseAreInsertedEmployeeAdvance1(?)', array($branch_office_id));
    }


    public function getUsersWithOutLoginUser($user_id)
    {
        return  $all = User::where('status', 1)->where('id', '!=', $user_id)->where('id', '!=', 1)->get();
    }

    public function findAnUserDetailsByEmployeeId($employee_id)
    {
        return  User::where('employee_infos.employee_id', $employee_id)
            ->leftjoin('employee_infos', 'users.emp_auto_id', '=', 'employee_infos.emp_auto_id')->first();
    }

    public function findUserByEmpIdAndPhoneNumber($emp_id, $phone_number)
    {
        return  $fineEmp = User::where('emp_auto_id', $emp_id)->where('phone_number', $phone_number)->first();
    }
    public function checkThisEmployeeAlreadyHasUserAccountByEmpAutoId($emp_auto_id)
    {
        return   User::where('emp_auto_id', $emp_auto_id)->count() > 0 ? true : false;
    }

    public function createNewUser($employee_name, $emp_id, $email, $phone_number, $password, $role_id)
    {
        $pass = Hash::make($password);
        return  $insert = User::insert([
            'name' => $employee_name,
            'emp_id' => $emp_id,
            'email' => $email,
            'phone_number' => $phone_number,
            'password' => $pass,
            'role_id' => $role_id,
            'created_at' => Carbon::now(),
        ]);
    }

    public function updateUserProfileInformation($userId, $name, $phone_number, $password)
    {
        $pass = Hash::make($password);
        return   User::where('id', $userId)->update([
            'name' => $name,
            // 'email' => $email,
            'phone_number' => $phone_number,
            'password' => $pass,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateUserProfileInformationByAdmin($userId, $name, $email, $phone_number, $activeInactive_status, $updated_by)
    {
        return   User::where('id', $userId)->update([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone_number,
            'status' => $activeInactive_status,
            'updated_at' => Carbon::now(),
            'us_updated_by' => $updated_by
        ]);
    }

    public function updateUserPassword($id, $newpass)
    {
        return  User::where('id', $id)->update([
            'password' => Hash::make($newpass)
        ]);
    }

    public function deactivateUser($id)
    {
        return  User::where('status', 1)->where('id', $id)->delete();
    }
    public function activateUserAuthentication($id)
    {
        return   User::where('id', $id)->update([
            'status' => 1,
            'updated_at' => Carbon::now(),
        ]);
    }


    public function getUserRole()
    {
        return $all = Role::get();
    }

    public function getARole($role_auto_id)
    {
        return  Role::where('role_auto_id', $role_auto_id)->orderBy('role_auto_id', 'ASC')->get();
    }
    public function updateUserRole($id, $role_id)
    {
        return $update = User::where('id', $id)->update([
            'role_id' => $role_id,
            'updated_at' => Carbon::now(),
        ]);
    }

    /*
     ===========================================================================
       ==================== Login History Database Operation ===================
     ===========================================================================
    */

    public function insertLoginUserHistory(User $userinfo, $current_timestamp)
    {

        DB::table('login_histories')->insert(
            ['user_id' => $userinfo->id, 'email' => $userinfo->email, 'login_ip' => $userinfo->ip, 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]
        );
    }



    /*
     ===========================================================================
       ==================== Salary Processing Permission ====================
     ===========================================================================
    */
    public function getAllAccessPermissionList()
    {
        return AccessPermission::orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
    }

    private function checkThisRecordAlreadyExist($month, $year)
    {
        return AccessPermission::where('month', $month)->where('year', $year)->count() > 0 ? true : false;
    }

    public function insertNewSalaryProcessPermision($month, $year, $lock_date)
    {
        if ($this->checkThisRecordAlreadyExist($month, $year)) {
            return 0;
        }
        return AccessPermission::insertGetId([
            'month' => $month,
            'year' => $year,
            'lock_date' => $lock_date,
            'create_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }

    public function updateSalaryPermissionInfos($permID, $month, $year, $lock_date, $is_lock)
    {

        return AccessPermission::where('id', $permID)->update([
            // 'month' => $month,
            //  'year' => $year,
            'lock_date' => $lock_date,
            'is_Lock' => $is_lock,
            'update_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function deniedSalaryProcessPermission($permID)
    {
        return AccessPermission::where('id', $permID)->update([
            'is_Lock' => 1,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function granteSalaryProcessPermission($permID)
    {
        return AccessPermission::where('id', $permID)->update([
            'is_Lock' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function getSalaryProcessAccessPermission($month, $year)
    {

        return   AccessPermission::where('month', $month)->where('year', $year)->where('is_Lock', false)->count() > 0 ? true : false;
    }


    /*
     ===========================================================================
     ====================== Login User Activities History ======================
     ===========================================================================
    */

    public function InsertLoginUserActivity($user_interface_form_id, $operation_type_id, $login_user_id, $emp_auto_id, $salary_amount)
    {
        try {

            UserLoginActivity::insert([
                'ui_form_id' => $user_interface_form_id,
                'uo_type_id' => $operation_type_id,
                'user_id' => $login_user_id,
                "emp_auto_id" => $emp_auto_id,
                'salary_amount' => $salary_amount
            ]);
        } catch (Exception $ex) {
            return null;
        }
    }

    // date to date all login user activities
    public function searchLoginUserActivityHistoryDateToDateForReport($from_date, $to_date)
    {

        return DB::select('CALL searchLoginUserActivitiesHistoryDateToDateInTheSystem(?,?)', array($from_date, $to_date));
    }
    // activity history report
    public function searchLoginUserActivityHistoryForReport($login_user_id, $from_date, $to_date)
    {

        return DB::select('CALL searchAUserActivitiesHistoryInTheSystem(?,?,?)', array($login_user_id, $from_date, $to_date));
    }

    public function searchEmployeeOrLoginUserActivityHistoryForReport($emp_auto_id)
    {
        return  DB::select('CALL searchAEmployeeActivitiesHistoryInTheSystem(?)', array($emp_auto_id));
    }

    // login user activity history report
    public function searchActivityTitleBaseReport($user_interface_form_ids, $emp_auto_id, $from_date, $to_date,$report_type)
    {

           $query = UserLoginActivity::leftjoin('user_interface_forms', 'user_login_activities.ui_form_id', '=', 'user_interface_forms.uif_auto_id')
                ->leftjoin('user_operation_types', 'user_login_activities.ui_form_id', '=', 'user_operation_types.uot_auto_id')
                ->leftjoin('users', 'user_login_activities.user_id', '=', 'users.id')
                ->leftjoin('employee_infos', 'user_login_activities.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->select(
                    'user_login_activities.*',
                     'user_operation_types.uot_name',
                     'users.name as operation_by',
                    'user_interface_forms.uif_title',
                    'employee_infos.employee_name',
                    'employee_infos.employee_id',
                    'employee_infos.akama_no',
                    'employee_categories.catg_name'
                );


             if (count($user_interface_form_ids) != 0) {
                $query->whereIn('user_login_activities.ui_form_id', $user_interface_form_ids);
             }else {
                    // ->select('uif_auto_id')
                   $form_ids = DB::table('user_interface_forms')->get()->pluck('uif_auto_id')->toArray();
                  // dd($form_ids);
                   $query->whereIn('user_login_activities.ui_form_id', $form_ids);
             }


            if ($emp_auto_id != null) {
                if((int)$report_type == 1){
                    // normal employee id
                    $query->where('user_login_activities.emp_auto_id', $emp_auto_id);
                }else{
                    // employee id is a system user
                    $user = User::where('emp_auto_id', $emp_auto_id)->first();
                    if($user){
                        $query->where('user_login_activities.user_id', $user->id);
                    }else{
                        // invalid system user employee_id
                        return [];
                    }
                }

            }

            if ($from_date != null && $to_date != null) {
               $query->whereDate('user_login_activities.created_at', '>=', $from_date)
                      ->whereDate('user_login_activities.created_at', '<=', $to_date);
            }
            return $query->orderBy('user_login_activities.created_at', 'desc')->get();

    }




}
