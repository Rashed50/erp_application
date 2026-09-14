<?php

namespace App\Http\Controllers\Admin\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;


class UserController extends Controller
{

      function __construct(){
        $this->middleware('permission:system_user_list',['only'=>['index']]);
        $this->middleware('permission:system_user_create',['only'=>['index','create','store']]);
        $this->middleware('permission:system_user_edit',['only'=>['edit','update']]);
     }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $br_ids = (Auth::user()->branch_office_id) == 1 ? [1,2] : [Auth::user()->branch_office_id];
        $data = User::select('users.id','users.name','users.email','users.status','users.emp_auto_id','users.profile_image','employee_infos.employee_id')
            ->join('employee_infos','users.emp_auto_id','=','employee_infos.emp_auto_id') // inner join
            ->whereIn('users.branch_office_id', $br_ids)
            ->where('users.id', '!=', Auth::user()->id)
            ->orderBy('users.id', 'DESC')
            ->paginate(70);

        $activity_form_names = DB::table('user_interface_forms')->select('uif_auto_id','uif_title')->get();


        return view('admin.users.index', compact('data', 'activity_form_names'))->with('i', ($request->input('page', 1) - 1) * 70);
     }
    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(Auth::user()->id == 1){
            // all rules for user superadmin
            $roles = Role::pluck('name', 'name')->all();

        }else{
            // without superadmin role
            $roles = Role::whereNotIn('id',[4])->pluck('name', 'name')->all();
        }

        if(Auth::user()->id == 1)
            $company_branches = (new CompanyDataService())->getACompanyListOfBranchForDropdownlist([1,2]);
        else
            $company_branches = (new CompanyDataService())->getACompanyListOfBranchForDropdownlist([Auth::user()->branch_office_id]);

        return view('admin.users.create', compact('roles','company_branches'));
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */


    public function store(Request $request)
    {

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'roles' => 'required'
            ]);
            $input = $request->all();
            $input['status'] = 1;
            $input['branch_office_id'] =(int) $request->branch_office;


            if($request['empAutoIDForThisUser']){
                $input['is_emp'] = 1;
                $input['emp_auto_id'] = intval($request['empAutoIDForThisUser']);
            }else {
                $input['is_emp'] = 0;
                $input['emp_auto_id'] = 144; // default emp id 100 , auto id 144
            }

            $input['us_updated_by'] = Auth::user()->id;
            $anEmp = (new AuthenticationDataService())->checkThisEmployeeAlreadyHasUserAccountByEmpAutoId( $request['empAutoIDForThisUser']);
            if ($anEmp) {
                Session::flash('error', 'This Employee has an User Account');
                return Redirect()->back();
            } else {

            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $user->assignRole($request->input('roles'));

              // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(30,1, Auth::user()->id,$input['emp_auto_id'],null);


            return redirect()->route('users.index')->with('success', 'User created successfully');
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
         if(Auth::user()->id == 1){
            // all rules for user superadmin
            $roles = Role::pluck('name', 'name')->all();
        }else{
            // without superadmin role
            $roles = Role::whereNotIn('id',[4])->pluck('name', 'name')->all();
        }
        $userRole = $user->roles->pluck('name', 'name')->all();
        foreach ($userRole as $arole){
            $existing_role = $arole;
        }

         // user permission list
        $user_role_ids = $user->roles->pluck('id')->all();
        $user_permissions = Permission::select('permissions.id','permissions.name','permissions.guard_name')->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->whereIn("role_has_permissions.role_id",$user_role_ids)
            ->get();


        return view('admin.users.edit', compact('user', 'roles', 'userRole','existing_role','user_permissions'));
    }

       // user Information Update from admin user
    public function update(Request $request, $id)
    {
       if(is_null($id)){
            Session::flash('error', 'User Not Found ');
            return redirect()->back();
        }

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
             'roles' => 'required'
        ]);

        $user = User::find($id);
        if (!is_null($request->get('name')) &&  !is_null($request->get('email')) &&  !is_null($request->get('phone_number')))
        {
            $activeInactive = $request->has('lock_checkbox') == true ? 1:0;
            (new AuthenticationDataService())->updateUserProfileInformationByAdmin($id,$request->get('name'),$request->get('email'),$request->get('phone_number'), $activeInactive,Auth::user()->id);
              // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(18,2, Auth::user()->id, $user->emp_auto_id,null);

        }

        // user role update

         $user->syncRoles($request->input('roles'));

        if (is_null($request->get('password')) ||  is_null($request->get('password_confirmation')) )
        {
               // login user activities record
              (new AuthenticationDataService())->InsertLoginUserActivity(21,2, Auth::user()->id, $user->emp_auto_id,null);
              return redirect()->route('users.index')->with('success', 'User updated successfully');
        }

      return  $this->changePasswordSave($request);
    }

    private function changePasswordSave(Request $request)
    {

        $user = User::find($request->id);
        $new_password = Hash::make($request->get('password'));
        $conf_password = Hash::make($request->get('password_confirmation'));

        if (strcmp($new_password, $conf_password) == 0)
        {
            Session::flash('error', 'Password Did Not Match');
            return redirect()->back();
        }

        $user->forceFill([
            'password' => Hash::make(request()->input('password')),
        ])->save();

        // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(17,2, Auth::user()->id, $user->emp_auto_id,null);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }




 // Login User Profile information update form
    public function profile()
    {
        $user = User::find(Auth::user()->id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
      //  return view('admin.users.user-profile', compact('user', 'roles', 'userRole'));

        $user_role_ids = $user->roles->pluck('id')->all();
        $user_permissions = Permission::select('permissions.id','permissions.name','permissions.guard_name')->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->whereIn("role_has_permissions.role_id",$user_role_ids)
            ->get();

        return view('admin.users.user-profile', compact('user', 'roles', 'userRole','user_permissions'));

    }

 // Login User Profile information Udpate Request . Own profile update
   public function profileInformationUpdate(Request $request)
    {



         try{

            $auth = Auth::user();
            $user =  User::find($auth->id);
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $uplodedPath = (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, $user->profile_image);
                $user->profile_image = $uplodedPath;
                $user->name = $request->name;
                $update = $user->save();
            }
            $this->validate($request, [
                'name' => 'required',
                'phone_number' => 'required',
                'current_password' => 'required|string',
                'password' => 'required|confirmed|min:8|string'
            ]);
            // The passwords matches
            if (!Hash::check($request->get('current_password'), $auth->password))
            {
                return back()->with('error', "Current Password is Invalid");
            }

            // Current password and new password same
            if (strcmp($request->get('current_password'), $request->password) == 0)
            {
                return redirect()->back()->with("error", "New Password cannot be same as your current password.");
            }

            $user->password =  Hash::make($request->password);
             $user->phone_number  = $request->phone_number ;
            $update = $user->save();
             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(17,2, Auth::user()->id, $user->emp_auto_id,null);
            if ($update) {
                Auth::logout();
                return redirect('/login');
            } else {
                Session::flash('error', 'Update Operation Failed, Please try Again');
                return redirect()->back();
            }

        }catch(Exception $ex){
            Session::flash('error', 'Update Operation Failed, Please try Again');
            return redirect()->back();
        }

    }




    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    // public function update(Request $request, $id)
    // {
    //     $this->validate($request, [
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,' . $id,
    //         // 'password' => 'confirmed',
    //         'roles' => 'required'
    //     ]);

    //     $input = $request->all();
    //     if (!empty($input['password'])) {
    //         $input['password'] = Hash::make($input['password']);
    //     } else {
    //         $input = Arr::except($input, array('password'));
    //     }

    //     $input['status'] = $request->lock_checkbox == "on" ? 1: 0;
    //     $user = User::find($id);
    //     $user->update($input);
    //     DB::table('model_has_roles')->where('model_id', $id)->delete();
    //     $user->assignRole($request->input('roles'));
    //     return redirect()->route('users.index')->with('success', 'User updated successfully');
    // }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

     // activity history report
    public function processAndShowLoginUserOrEmployeeActivityHistory(Request $request){


        $employee = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($request->employee_id,Auth::user()->branch_office_id);
        if($request->report_type == 0 || $request->report_type == 1){
            // Activity Title base report
            // employee_id: employeeId,
            //     from_date: $('#from_date').val(),
            //     to_date: $('#to_date').val(),
            //     report_type: reportType,
            //     : $('#activity_type').val()
              $emp_auto_id = null;
             if($employee){
                $emp_auto_id = $employee->emp_auto_id;
             }
              $activity_title_ids = $request->activity_title_ids ? explode(',', $request->activity_title_ids) : [];

            // dd($request->all());

            $data_records = (new AuthenticationDataService())->searchActivityTitleBaseReport($activity_title_ids,$emp_auto_id,$request->from_date,$request->to_date,$request->report_type);
         }
        // else if(!$employee){

        //     $data_records = (new AuthenticationDataService())->searchLoginUserActivityHistoryDateToDateForReport($request->from_date,$request->to_date);
        // }
        // else if($request->report_type == 0) {

        //     // Login User History
        //     $user = User::where('emp_auto_id',$employee->emp_auto_id)->first();
        //     if(! $user){
        //         return 'System User Information Not Found ';
        //     }
        //     $login_user_id =  $user->id;
        //     $data_records = (new AuthenticationDataService())->searchLoginUserActivityHistoryForReport($login_user_id,$request->from_date,$request->to_date);

        // }else if($request->report_type == 1){
        //     // employee history
        //     $data_records = (new AuthenticationDataService())->searchEmployeeOrLoginUserActivityHistoryForReport($employee->emp_auto_id);
        //  }



         $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::user()->name;
        return view('admin.users.user_or_emp_activity_history_report', compact('login_name','data_records', 'company'));
    }




}
