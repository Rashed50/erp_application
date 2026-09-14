<?php

namespace Modules\HrManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\DataServices\{EmployeeDataService, AuthenticationDataService, EmployeeRelatedDataService, AccommodationDataService, CompanyDataService, ProjectDataService};
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\{Validator, Auth, Log, DB};
use Illuminate\Validation\Rule;
use Exception;




class EmloyeeInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    function __construct()
    {


        $this->middleware('permission:employee_related_file_upload', ['only' => ['updateEmployeeFile',]]);
        $this->middleware('permission:salarydetails-edit', ['only' => ['updateEmployeeSalaryInformation']]);
        $this->middleware('permission:employee_iqama_no_update', ['only' => ['updateIqamaAndPassportUpdate']]);
        $this->middleware('permission:single_employee_transfer', ['only' => ['updateWorkingProject']]);
        $this->middleware('permission:employee_sponsor_update', ['only' => ['updateSponsor']]);
        $this->middleware('permission:employee_agency_update', ['only' => ['updateEmployeeReference']]);
        $this->middleware('permission:emp_blood_group_update', ['only' => ['updateEmployeeBloodGroup']]);
        $this->middleware('permission:employee_accommodation_update', ['only' => ['updateEmployeeAccommadation']]);
        $this->middleware('permission:add_remarks_on_employee_action', ['only' => ['updateEmployeeAjeerDocument']]);
        $this->middleware('permission:employee_designation_update', ['only' => ['updateTradeAndDesignation']]);


        /// employee transfer
        $this->middleware('permission:multiple-employee-transfer', ['only' => ['multipleEmployeeTransferFormSubmit']]);
    }


    public function index()
    {

        $empWorkRating = []; // EmpWorkActivityRatingEnum::cases();
        $designations =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(); //getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
        // dd($projects);
        // compact('empWorkRating','designations'),
        $allEmployeeStatus = []; // $employeeStatusOBJ->getEmployeeStatus();
        $employeeTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $countries = (new EmployeeRelatedDataService())->getAllCountryForDropdownList();
        $departments = (new EmployeeRelatedDataService())->getAllDepartment();
        $agencies = (new CompanyDataService())->getAllAgencies();
        $accommodations = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
        return view('hrmanagement::pages.employee_info.employee_update', [
            'data' => [
                'activity_types'   => [],
                'empTypes' => $employeeTypes,
                'designations' => $designations,
                // 'relig' => $relig,
                'country' => $countries,
                'departments' => $departments,
                'projects' => $projects,
                'agencies' => $agencies,
                'sponsors' => $sponsors,
                'accommodations' => $accommodations,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function searchNextNewEmployeeUniqueID(Request $request, $new_emp_type)
    {
        // try {

        //return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
        $find_next_emp_id =   (new EmployeeDataService())->searchNewEmployeeUniqueEmployeeID($new_emp_type);
        if ($find_next_emp_id) {
            return response()->json(['status' => 200, 'success' => true, 'data' => $find_next_emp_id]);
        } else {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
        }
        // } catch (Exception $ex) {
        //     return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
        // }
    }

    public function create()
    {


        $designation =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $countryList =  (new EmployeeRelatedDataService())->getAllCountry();
        $empTypes = (new EmployeeRelatedDataService())->getAllEmployeeType();
        $allDepart = (new EmployeeRelatedDataService())->getAllDepartment();
        $empIdGeneret = (new EmployeeDataService())->generateEmployeeId();
        // $relig = $this->getReligion();
        $proj = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $agencies = (new EmployeeRelatedDataService())->getAgencyInformationForDropdownList();
        $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();


        return view('hrmanagement::pages.employee_info.new_employee', [
            'data' => [
                'activity_types'   => [],
                'empTypes' => $empTypes,
                'empIdGeneret' => $empIdGeneret,
                'designations' => $designation,
                // 'relig' => $relig,
                'country' => $countryList,
                'allDepartment' => $allDepart,
                'projects' => $proj,
                'agencies' => $agencies,
                'sponsors' => $sponsor,
                'accomdOfficeBuilding' => $accomdOfficeBuilding,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */

    public function store(Request $request)
    {
        try {

            $request->validate([
                // Identity
                'employee_id'           => 'required|string|max:40|unique:employee_infos,employee_id',
                'emp_name'               => 'required|string|max:50',
                'company_id'             => 'required',

                // Agency / Sponsor
                'agency_id'              => 'required|integer',
                'sponsor_id'             => 'required|integer',

                // Passport / Iqama
                'passfort_no'            => 'required|string|max:40|unique:employee_infos,passfort_no',
                'passport_expire_date'   => 'required|date',
                'akama_no'               => 'required|string|max:10|unique:employee_infos,akama_no',
                'akama_expire'           => 'required|date',

                // Employment
                'emp_type_id'            => 'required|integer',
                'designation_id'         => 'nullable|integer',
                'department_id'          => 'nullable|integer',
                'project_id'             => 'nullable|integer',
                'accomd_ofb_id'          => 'nullable|integer',

                // Contact
                'mobile_no'              => 'required|max:20',
                'phone_no'               => 'nullable|max:20',
                'country_phone_no'       => 'nullable|max:20',
                //  'email'                  => 'nullable|email|unique:employee_infos,email',

                // Address
                'present_address'        => 'nullable|string',
                'country_id'             => 'nullable|integer',
                'division_id'            => 'nullable|integer',
                'district_id'            => 'nullable|integer',
                'post_code'              => 'nullable|string|max:20',
                'details'                => 'nullable|string',

                // Personal
                'date_of_birth'          => 'nullable|date',
                'blood_group'            => 'nullable|string|max:5',
                'maritus_status'         => 'nullable|in:0,1',
                'gender'                 => 'nullable|in:M,F',
                'religion'               => 'nullable|integer',
                'ref_employee_id'        => 'nullable|string|max:100',
                'remarks'                => 'nullable|string',

                // Dates
                'appointment_date'       => 'nullable|date',
                'joining_date'           => 'nullable|date',
                'confirmation_date'      => 'nullable|date',
            ], [
                'employee_id.required'   => 'Employee ID is missing, please reload the page and try again.',
                'emp_name.required'      => 'Please enter employee name!',
                'agency_id.required'     => 'You must select this field!',
                'sponsor_id.required'    => 'You must select this field!',
                'emp_type_id.required'   => 'You must select this field!',
                'mobile_no.required'     => 'Abshar Mobile No is required!',
                'akama_no.unique'        => 'This Iqama number already exists!',
                'passfort_no.unique'     => 'This passport number already exists!',
                // 'email.unique'           => 'This email already exists!',
            ]);

            // --- Insert -----------------------------------------------------------
            $creator = Auth::user()->id;
            $emp_auto_id = (new EmployeeDataService())->insertNewEmployee($request);

            if ($emp_auto_id > 0) {

                (new EmployeeDataService())->addEmployeeSalaryDetails($emp_auto_id, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

                (new EmployeeDataService())->insertEmployeeDetailsInformation(
                    $emp_auto_id,
                    $request->department_id,
                    0,
                    $request->religion,
                    0,
                    $request->country_phone_no,
                    $request->agency_id,
                    $request->gender,
                    $request->maritus_status,
                    $request->blood_group,
                    $request->present_address,
                    $request->ref_employee_id,
                    $request->remarks
                );

                if ($request->project_id == '' || $request->project_id == null) {
                    $request->merge(['project_id' => 0]);
                }

                (new EmployeeRelatedDataService())->assignEmployeeToNewProject(
                    $emp_auto_id,
                    $request->project_id,
                    Carbon::now(),
                    null,
                    $creator,
                    null
                );

                return response()->json([
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Employee Add Successfully!'
                ]);
            } else {

                return response()->json([
                    'status'  => 403,
                    'success' => false,
                    'error'   => 'Employee ID Already Assigned, ',
                    'message' => 'Please Reload and Try Again'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "success" => false,
                "message" => "Server Error: " . $e->getMessage()
            ], 500);
        }
    }


    public function checkEmployeeUniqueInformationBeforeAddNewEmployee(Request $request)
    {
        $find_emp = (new EmployeeDataService())->checkThisValueIsExistInServerDatabase($request->value, $request->dbcolum_name);
        if ($find_emp) {
            return response()->json(['status' => 200, 'success' => true, 'data' => 1, 'error' => 'Already Exist this information']);
        } else {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => '']);
        }
    }





    // Employee Transfer Form Return
    public function multipleEmployeeTransferForm()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        return view("hrmanagement::pages.employee_transfer.emp_transfer", compact("projects"));
    }






    /*
    ===================================================================
    ===================== API Methods =================================
    ===================================================================
 */


    // === Upload Employee File/Image ===
    public function updateEmployeeFile(Request $request)
    {
        try {

            $emp_auto_id = $request->emp_auto_id;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id);
            if (is_null($anEmployee)) {
                return response()->json(['status' => 404, 'success' => false, 'error' => 'Employee Not Found',   'message' => 'Update Operation Failed']);
            }

            $update = false;
            // profile photo
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, $anEmployee->profile_photo);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'profile_photo');
                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, 'profile update');
            }
            // passport_photo upload
            if ($request->hasFile('passport_photo')) {
                $file = $request->file('passport_photo');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeePassportFile($file, $anEmployee->pasfort_photo);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'pasfort_photo');
                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, 'passport file update');
            }
            // Iqama File
            if ($request->hasFile('akama_photo')) {
                $file = $request->file('akama_photo');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, $anEmployee->akama_photo);

                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, null);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, 'iqama file update');
            }

            // covid_certificate
            if ($request->hasFile('covid_certificate')) {
                $file = $request->file('covid_certificate');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeCOVIDCertificateFile($file, $anEmployee->covid_certificate);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'covid_certificate');
                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, 'covid_certificate file update');
            }

            // Medical Report
            if ($request->hasFile('medical_report')) {
                $file = $request->file('medical_report');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeMedicalReportFile($file, $anEmployee->medical_report);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'medical_report');
                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, 'medical_report file update');
            }


            // appoint letter update
            if ($request->hasFile('appoint_letter')) {
                $file = $request->file('appoint_letter');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeAppointmentLetterFile($file, $anEmployee->employee_appoint_latter);
                $update =  (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'employee_appoint_latter');
                (new AuthenticationDataService())->InsertLoginUserActivity(37, 2, Auth::user()->id, $emp_auto_id, 'Offer Letter update');
            }

            if ($request->hasFile('ajeer_file')) {

                $file = $request->file('ajeer_file');
                $uplodedPath = (new  UploadDownloadController())->uploadEmployeeAjeerFile($file, $anEmployee->ajeer_file);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'ajeer_file');
                (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $emp_auto_id, 'ajeer_file update');
            }

            if ($update) {
                return response()->json(['status' => 200, 'success' => true,   'message' => 'Successfully Updated']);
            } else {
                return response()->json(['status' => 404, 'success' => false, 'error' => 'Employee Not Found',   'message' => 'Update Operation Failed', ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'An error occurred while uploading the file',
                'error'   => $e->getMessage(),
            ], 500);
        }



    }



    public function updateEmployeeSalaryInformation(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'emp_auto_id' => 'required|exists:employee_infos,emp_auto_id',
            'emp_type_id' => 'required|integer',
            'staff_employee' => 'required|boolean',
            'payment_method' => 'required|string|in:Cash,Bank',
            'basic_hours' => 'required|numeric',
            'basic_amount' => 'required|numeric',
            'hourly_rent' => 'required|numeric',
            'house_rent' => 'nullable|numeric',
            'mobile_allowance' => 'nullable|numeric',
            'food_allowance' => 'nullable|numeric',
            'contribution_amoun' => 'nullable|numeric',
            'saudi_tax' => 'nullable|numeric',
            'medical_allowance' => 'nullable|numeric',
            'local_travel_allowance' => 'nullable|numeric',
            'conveyance_allowance' => 'nullable|numeric',
            'increment_amount' => 'nullable|numeric',
            'others' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'dd' => $request->all()], 422);
        }
        // Begin transaction to ensure atomicity

        try {

            $update_by = Auth::user()->id;
            $emp_auto_id = $request->emp_auto_id;

            $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
            if (! $employee) {
                return response()->json(["status" => 403, "success" => false, 'error' => $request->all(), 'message' =>  "Employee Information not Found"]);
            }

            if (strtolower($request->payment_method) == 'bank') {
                // update employee salary payment methond
                $employee = (new EmployeeRelatedDataService())->getAnEmployeeDetailsAndBankInformationRecordByEmployeeId($employee->employee_id, 1);
                if ($employee == null) {
                    $request->payment_method = 'Cash';
                } else {
                    $request->payment_method = 'Bank';
                }
            }
            DB::beginTransaction();
            $update = (new EmployeeDataService())->updateEmployeeSalaryAllInformation($request, $update_by);
            $hourly_employee = $request->hourly_employee == true ? 1 : NULL;
            $update = (new EmployeeDataService())->updateEmployeeTypeIdAndIsEmployeeStaffAndHourlyEmployee((int)$emp_auto_id, (int)$request->staff_employee, (int)$request->emp_type_id, $hourly_employee, $update_by);
            $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rent;

            (new EmployeeDataService())->insertAnEmployeeSalaryUpdateHistory(
                (int)$emp_auto_id,
                $request->basic_amount,
                $request->basic_hours,
                $request->house_rent,
                $request->hourly_rent,
                $request->mobile_allowance,
                $request->medical_allowance,
                $request->local_travel_allowance,
                $request->conveyance_allowance,
                $request->food_allowance,
                $request->contribution_amoun,
                $request->increment_amount,
                $request->saudi_tax,
                $request->others,
                $request->hourly_employee == true ? 1 : 0,
                $request->payment_method,
                $update_by
            );


            // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(2, 2, $update_by, $emp_auto_id, $salary_amount);
            // Commit the transaction
            DB::commit();
            return response()->json(["status" => 200, "success" => true, 'message' =>  "Successfully Updated", 'dd' => $request->all()]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'request_data' => $request->all()], 500);
        }
    }

    public function searchEmployeeForEditEmployeeInforDetails(Request $request)
    {

        if ($request->search_by == 1) {
            // searching by name or passport or iqama or employee id
            $employee = (new EmployeeDataService())->searchingEmployeeInfoByAnyColumnValueMatching($request->employee_searching_value, Auth::user()->branch_office_id);
        } else {
            $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->employee_searching_value, $request->search_by, Auth::user()->branch_office_id);
        }

        if (count($employee) > 0) {
            return json_encode([
                'success'  => true,
                'status' => 200,
                'error' => null,
                'findEmployee' =>  $employee,
            ]);
        } else {
            return json_encode([
                'success'  => false,
                'status' => 404,
                'error' => 'error',
                'message' => 'Employee Not Found',
            ]);
        }


        // return response()->json(["status" =>200, "success" => true, 'message'=>  "Successfully Updated",'dd' => $request->all() ]);
        $searchByDb_Column = $request->search_by;
        $employee_searching_value = $request->employee_searching_value;
        $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_searching_value, $searchByDb_Column, Auth::user()->branch_office_id);



        $living_camps = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        $projects = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);

        $allEmployeeStatus = []; // $employeeStatusOBJ->getEmployeeStatus();
        $designations =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $employeeTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $countries = (new EmployeeRelatedDataService())->getAllCountryForDropdownList();
        $departments = (new EmployeeRelatedDataService())->getAllDepartment();


        $agencies = (new CompanyDataService())->getAllAgencies();
        $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

        if (count($employee) > 0) {
            return json_encode([
                'success'  => true,
                'status' => 200,
                'error' => null,
                'findEmployee' =>  $employee,
                'living_camps' => $living_camps,
                'projects' => $projects,
                'allEmployeeStatus' => $allEmployeeStatus,
                'designations' => $designations,
                'agencies' => $agencies,
                'sponsors' => $sponsor,
                'employeeTypes' => $employeeTypes,
                'countries' => $countries,
                'departments' => $departments,

            ]);
        } else {
            return json_encode([
                'success'  => false,
                'status' => 404,
                'error' => 'error',
                'message' => 'Employee Not Found',
            ]);
        }
    }

    public function updateAnEmployeeAllInforDetails(Request $request)
    {

        // return response()->json(["status" =>200, "success" => true, 'message'=>  "Successfully Updated",'dd' => $request->all() ]);

        $validator = Validator::make($request->all(), [
            'emp_auto_id' => 'required|integer|exists:employee_infos,emp_auto_id',
            'employee_id' => [
                'required',
                'integer',
                Rule::unique('employee_infos', 'employee_id')->ignore($request->emp_auto_id, 'emp_auto_id')
            ],
            'emp_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-]+$/',
            'agency_id' => 'required|integer|exists:agency_infos,agc_info_auto_id',
            'sponsor_id' => 'required|integer|exists:sponsors,spons_id',
            'passfort_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employee_infos', 'passfort_no')->ignore($request->emp_auto_id, 'emp_auto_id')
            ],
            'passport_expire_date' => 'required|date',
            'akama_no' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employee_infos', 'akama_no')->ignore($request->emp_auto_id, 'emp_auto_id')
            ],
            'akama_expire' => 'required|date',
            'mobile_no' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9+\-\s]+$/',
                Rule::unique('employee_infos', 'mobile_no')->ignore($request->emp_auto_id, 'emp_auto_id')
            ],
            'phone_no' => 'nullable|string|max:15|regex:/^[0-9+\-\s]+$/',
            'country_phone_no' => 'nullable|max:20|regex:/^[0-9+\-\s]+$/',
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('employee_infos', 'email')->ignore($request->emp_auto_id, 'emp_auto_id')
            ],
            //  'present_address' => 'required|string|max:500',
            'country_id' => 'nullable|integer|exists:countries,id',
            'division_id' => 'nullable|integer|exists:divisions,division_id',
            'district_id' => 'nullable|integer|exists:districts,district_id',
            'post_code' => 'nullable|string|max:20',
            'details' => 'nullable|string|max:500',
            'emp_type_id' => 'required|integer|exists:employee_types,id',
            'designation_id' => 'required|integer|exists:employee_categories,catg_id',
            'project_id' => 'nullable|integer|exists:project_infos,proj_id',
            'department_id' => 'nullable|integer|exists:departments,dep_id',
            'date_of_birth' => 'nullable|date|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'maritus_status' => 'required|in:1,2',
            'gender' => 'required|in:1,2',
            // 'religion' => 'nullable|integer|exists:religions,relig_id',
            'ref_employee_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
            'appointment_date' => 'required|date|before_or_equal:today',
            'joining_date' => 'nullable|date|before_or_equal:today',
            'confirmation_date' => 'nullable|date|after_or_equal:joining_date',
        ], [
            'emp_name.regex' => 'Employee name should only contain letters, spaces, and hyphens',
            'passport_expire_date.after' => 'Passport expiry date must be a future date',
            'akama_expire.after' => 'Iqama expiry date must be a future date',
            'mobile_no.regex' => 'Please enter a valid mobile number',
            'date_of_birth.before' => 'Employee must be at least 18 years old',
            'appointment_date.before_or_equal' => 'Appointment date cannot be in the future',
            'confirmation_date.after_or_equal' => 'Confirmation date must be after or equal to joining date',
        ]);




        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'dd' => $request->all()], 422);
        }
        // Begin transaction to ensure atomicity

        try {

            $update_by = Auth::user()->id;
            $emp_auto_id = $request->emp_auto_id;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id);
            if (is_null($anEmployee)) {
                return response()->json(['status' => 404, 'success' => false, 'error' => 'Employee Not Found',   'message' => 'Update Operation Failed']);
            }


            DB::beginTransaction();
            $update = (new EmployeeDataService())->updateAnEmployeeAllBasicInformationFromEmpEdit($request, $update_by);
            // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(15, 2, $update_by, $emp_auto_id, null);
            // Commit the transaction
            DB::commit();
            return response()->json(["status" => 200, "success" => true, 'message' =>  "Successfully Updated", 'dd' => $request->all()]);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'request_data' => $request->all()], 500);
        }
    }


    public function updateIqamaAndPassportUpdate(Request $request)
    {
        try {
            // return response()->json(["status" => 200, "success" => false, 'message' =>  "Update Successfully ", 'form_data' => $request->all()]);

            $emp_auto_id = $request->emp_auto_id;
            $iqamaNo = $request->akama_no_up;
            $passport = $request->passport_no_up;
            $isThisIqamaExist = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
            $isThisPassportExist = (new EmployeeDataService())->getAnEmployeeInfoByEmpPassportNo($passport);


            if ($isThisIqamaExist) {
                $isThisIqamaExist = true;
            } else {
                $isThisIqamaExist = false;
                $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
                if ($request->hasFile('akama_photo')) {
                    $file = $request->file('akama_photo');
                    $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, $anEmployee->akama_photo);
                    $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
                }
                if ($iqamaNo != null) {

                    (new EmployeeDataService())->updateAnEmployeeIqamaNumberAndExpiredateFromStatusOption($emp_auto_id, $iqamaNo, $request->akama_expire);
                    // login user activities record
                    (new AuthenticationDataService())->InsertLoginUserActivity(5, 2, Auth::user()->id, $emp_auto_id, null);
                }
            }
            if ($isThisPassportExist) {
                $isThisPassportExist = true;
            } else {
                $isThisPassportExist = false;
                if ($request->hasFile('passport_file')) {
                    $file1 = $request->file('passport_file');
                    $uplodedPath1 = (new  UploadDownloadController())->uploadEmployeePassportFile($file1, $anEmployee->pasfort_photo);
                    $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath1, 'pasfort_photo');
                }
                if ($passport != null) {
                    (new EmployeeDataService())->updateEmployeePassportInformation($emp_auto_id,   $passport);
                    // login user activities record
                    (new AuthenticationDataService())->InsertLoginUserActivity(6, 2, Auth::user()->id, $emp_auto_id, null);
                }
            }
            if ($isThisIqamaExist && $isThisPassportExist) {

                return response()->json(["status" => 403, "success" => false, 'message' =>  "Passport and Iqama Number both are Exist ", 'dd' => $request->all()]);
            } else if ($isThisIqamaExist) {
                return response()->json(["status" => 403, "success" => false, 'message' =>  "Iqama Number Already Exist ", 'dd' => $request->all()]);
            } else if ($isThisPassportExist) {

                return response()->json(["status" => 403, "success" => false, 'message' =>  "Passport Number Already Exist", 'dd' => $request->all()]);
            }
            return response()->json(["status" => 200, "success" => true, 'message' =>  "Sucessfully Updated", 'dd' => $request->all()]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "success" => false,
                "message" => "Server Error: " . $e->getMessage()
            ], 500);
        }
    }





    public function updateWorkingProject(Request $request)
    {



        $emp_auto_id = $request->emp_auto_id;
        $project_id = $request->projectStatus;
        $assign_date = $request->date;

        if ($project_id == null) {


            return response()->json(["status" => 403, "success" => false, 'message' =>  "Operation Failed, Please try again ", 'dd' => $request->all()]);
        }
        $update = (new EmployeeDataService())->updateEmployeeAssignedProject($emp_auto_id, $project_id);
        if ($update) {
            (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $project_id, $assign_date, $assign_date, Auth::user()->id, null);
            (new AuthenticationDataService())->InsertLoginUserActivity(10, 2, Auth::user()->id, $emp_auto_id, null);

            return response()->json(["status" => 200, "success" => true, 'message' =>  "Successfully Updated Project Info", 'dd' => $request->all()]);
        } else {
            return  response()->json(["status" => 403, "success" => false, 'message' =>  "Operation Failed, Please try again ", 'dd' => $request->all()]);
        }

        return response()->json(["status" => 200, "success" => true, 'message' =>  "Sucessfully Updated", 'dd' => $request->all()]);
    }


    public function updateSponsor(Request $request)
    {
        // return response()->json(["status" => 200, "success" => false, 'message' =>  "Update Successfully ", 'form_data' => $request->all()]);
        try {
            $emp_auto_id = $request->emp_auto_id;
            $prev_spons_id = $request->emp_prev_sponsor_id;
            $curnt_spons_id = $request->emp_current_sponsor;

            if ($curnt_spons_id == null) {
                return response()->json(["status" => 403, "success" => false, 'message' =>  "Operation Failed, Please try again l "]);
            }

            $update = (new EmployeeDataService())->updateEmployeeSponsorInfo($emp_auto_id, $curnt_spons_id);
            if ($update) {
                // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(7, 2, Auth::user()->id, $emp_auto_id, null);
                return response()->json(["status" => 200, "success" => true, 'message' =>  "Successfully Updated Sponsor info"]);
            } else {
                return response()->json(["status" => 403, "success" => false, 'message' =>  "Operation Failed, Please try again later"]);
            }
        } catch (Exception $e) {

            return response()->json(["status" => 200, "success" => false, 'message' =>  "Sucessfully Sponsor Updated ", 'error' => $e->getMessage(),]);
        }
    }


    public function updateEmployeeReference(Request $request)
    {

        // return response()->json(["status" => 200, "success" => false, 'message' =>  "Update Successfully dd ", 'form_data' => $request->all()]);

        try {
            $emp_auto_id = $request->emp_auto_id;
            $agency_id = $request->emplyoeeAgency;
            $ref_person_info = $request->ref_employee_id;
            $ref_contact_no = $request->ref_contact_no;
            $remarks = $request->remarks;


            if ($agency_id != null &&  $emp_auto_id  != null) {
                (new EmployeeDataService())->updateEmployeeAgencyInfo($emp_auto_id, $agency_id);

                // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(8, 2, Auth::user()->id, $emp_auto_id, null);
            }
            if ($ref_person_info != null &&  $emp_auto_id  != null) {
                (new EmployeeDataService())->updateAnEmployeeReferencePersonInformation($emp_auto_id, $ref_person_info, $ref_contact_no, $remarks);
            }
            return response()->json(["status" => 200, "success" => true, 'message' =>  "Update Successfully "]);
        } catch (Exception $ex) {

            return response()->json(["status" => 500, "success" => false, 'message' =>  "Operation failed, " . $ex->getMessage(), 'error' => $ex->getMessage()]);
        }
    }
    public function updateEmployeeBloodGroup(Request $request)
    {

        // return response()->json(["status" => 200, "success" => false, 'message' =>  "Update Successfully ", 'form_data' => $request->all()]);

        $emp_auto_id = $request->emp_auto_id;
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);


        $uplodedPath = "";
        if ($request->hasFile('blood_group_paper')) {

            $file = $request->file('blood_group_paper');
            $uplodedPath = (new  UploadDownloadController())->uploadEmployeeBloodGroupPaper($file, $anEmployee->blood_group_file);
        }
        $update = (new EmployeeDataService())->updateAnEmployeeBloodGroupInfo($emp_auto_id, $request->blood_group, $uplodedPath);

        //  (new AuthenticationDataService())->InsertLoginUserActivity(10, 2, Auth::user()->id, $emp_auto_id, null);

        return response()->json(["status" => 200, "success" => false, 'message' =>  "Update Successfully ", 'form_data' => $request->all()]);
    }


    public function updateEmployeeAccommadation(Request $request)
    {


        try {
            if (is_null($request->email) && is_null($request->mobile_no_up) && is_null($request->phone_no_up) && is_null($request->country_phone_no) && is_null($request->emplyoeeAccommodationBuiling)) {
                return response()->json([
                    'status' => 403,
                    'success' => false,
                    'error' => 'Please Input Employee Contact Mobile Information',
                    'message' => 'Update Operation Failed'
                ]);
            }

            if (is_null($request->email) == false) {
                (new EmployeeDataService())->updateAnEmployeeEmailAddress($request->emp_auto_id, $request->email);
            }

            if (is_null($request->mobile_no_up) == false || is_null($request->phone_no_up) == false) {
                (new EmployeeDataService())->updateAnEmployeeMobileNumber($request->emp_auto_id, $request->mobile_no_up, $request->phone_no_up);
            }

            if (is_null($request->country_phone_no) == false) {
                (new EmployeeDataService())->updateAnEmployeeHomeCountryContactNumber($request->emp_auto_id, $request->country_phone_no);
            }

            if (is_null($request->living_building_id) == false) {
                (new EmployeeDataService())->updateAnEmployeeAccommodationVilla($request->emp_auto_id, $request->living_building_id);
            }


            (new AuthenticationDataService())->InsertLoginUserActivity(36, 2, Auth::user()->id, $request->emp_auto_id, null);

            return response()->json([
                "status" => 200,
                "success" => true,
                'message' => "Successfully Updated",

            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => 500,
                "success" => false,
                "message" => "Server Error: " . $e->getMessage()
            ], 500);
        }
    }


    public function updateEmployeeAjeerDocument(Request $request)
    {

        // return response()->json(["status" => 200, "success" => false, 'message' =>  "Update Successfully ", 'form_data' => $request->all()]);

        $emp_auto_id = $request->input_emp_auto_id;
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);

        if ($anEmployee && $request->hasFile('ajeer_file')) {

            $file = $request->file('ajeer_file');
            $uplodedPath = (new  UploadDownloadController())->uploadEmployeeAjeerFile($file, $anEmployee->ajeer_file);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'ajeer_file');

        } else {

            return response()->json(["status" => 401, "success" => false, 'message' =>  "Employee Not Found or Ajeer File Not Uploaded "]);
        }

        //   (new AuthenticationDataService())->InsertLoginUserActivity(10, 2, Auth::user()->id, $emp_auto_id, null);

        return response()->json(["status" => 200, "success" => true, 'message' =>  "Ajeer Document Successfully  "]);
    }



    // Search EMployee by Emp ID and Update Employee Trade/Designation Or Employee Multi Expert Trade/Designation Update
    public function updateTradeAndDesignation(Request $request)
    {

        try {

            $emp_auto_id = $request->emp_auto_id;
            $emplDesig_id = $request->emplyoeeDesignation;
            $empMultiDesignations = $request->empMultiExptDesignation;
            $empWorkActivityRating = $request->empWorkActivityRating;

            if (is_null($emp_auto_id) || ($emplDesig_id == null && $empMultiDesignations == null && $empWorkActivityRating == null)) {
                return response()->json(["status" => 403, "success" => false, 'message' =>  "Please Select Employee Trade Name ", 'dd' => $request->all()]);
            } else {
                if ($emplDesig_id != null) {
                    (new EmployeeDataService())->updateEmployeeDesignationStatus($emp_auto_id, $emplDesig_id);
                }
                if ($empWorkActivityRating != null) {
                    (new EmployeeDataService())->updateAnEmployeeWorkRatingInfo($request->emp_auto_id, $request->empWorkActivityRating);
                }
                // temporarry close this feature
                if ($empMultiDesignations != null) {
                    $empMultiDesignations = explode(",", $empMultiDesignations);
                    foreach ($empMultiDesignations as $trade_id) {
                        (new EmployeeDataService())->insertAnEmpMultipleTradeExpertnessInformation($emp_auto_id, $trade_id, Auth::user()->id);
                    }
                }

                // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(11, 2, Auth::user()->id, $emp_auto_id, null);


                return response()->json(["status" => 200, "success" => true, 'message' =>  "Successfully Updated",]);
            }

        } catch (Exception $ex) {
            return response()->json(["status" => 403, "success" => false, 'message' =>  'Update Failed, Please try Again.' . $ex]);
        }
    }


    public function unapprovedList()
    {

        try {
            $employees = (new EmployeeDataService())->getListOfNewEmployeesThoseAreWaitingForApprovalForVueComponent(-1, 0, Auth::user()->branch_office_id);
            //$empTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
            return  response()->json(["status" => 200, "success" => true, 'message' =>  "", 'data' => $employees]);
        } catch (Exception $ex) {
            return response()->json(["status" => 403, "success" => false, 'message' =>  'Failed to delete. Please try Again.' . $ex]);
        }
    }

    // Unapproved emp delete operation
    public function destroy($id)
    {
        try {
            $anemp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($id);
            if ($anemp && $anemp->job_status == 0) {
                $isDeleted = (new EmployeeDataService())->deleteAnEmployeeWithServiceDetailsByAutoIdBeforeApproval($id);
                (new UploadDownloadController())->deleteAnEmplyoeeAllfilesBeforeApproval($anemp);
                return  response()->json(["status" => 200, "success" => true, 'message' =>  "Successfully deleted"]);
            }
            return response()->json(["status" => 404, "success" => false, 'message' =>  'Failed to delete. Please try Again.']);
        } catch (Exception $ex) {
            return response()->json(["status" => 403, "success" => false, 'message' =>  'Failed to delete. Please try Again.' . $ex]);
        }
    }


    // === Updated Employee Job Status ===
    public function approvalOfNewInsertedEmployees(Request $request)
    {
        try {
            $request->validate([
                'emp_auto_ids' => 'required|array',
                'emp_auto_ids.*' => 'integer|exists:employee_infos,emp_auto_id'
            ]);

            $emp_auto_ids = $request->emp_auto_ids;
            foreach ($emp_auto_ids as $id) {

                (new EmployeeDataService())->approvalOfInsertedNewEmployee($id, 1, Auth::user()->id); //  1 = Active Employee
                $emp = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($id);
                if ($emp) {
                    (new EmployeeDataService())->insertAnEmployeeSalaryUpdateHistory(
                        $emp->emp_auto_id,
                        $emp->basic_amount,
                        $emp->basic_hours,
                        $emp->house_rent,
                        $emp->hourly_rent,
                        $emp->mobile_allowance,
                        $emp->medical_allowance,
                        $emp->local_travel_allowance,
                        $emp->conveyance_allowance,
                        $emp->food_allowance,
                        $emp->cpf_contribution,
                        $emp->increment_amount,
                        $emp->saudi_tax,
                        $emp->others1,
                        $emp->hourly_employee == 1 ? 1 : 0,
                        $emp->payment_method != null ? $emp->payment_method : 'Cash',
                        Auth::user()->id,
                    );
                }
            }
            return response()->json(['status' => 200, 'success' => true, 'message' => 'Successfully Approved']);
        } catch (\Exception $e) {
            return response()->json(['status' => 404, 'success' => false, 'message' => 'Update Operation Failed. Please Try Again', 'error' => $e->getMessage()]);
        }
    }

    // Update Employee Salary before Approval
    public function updateSalaryAtApproval(Request $request)
    {
        $request->validate([
            'emp_auto_id' => 'required|exists:employee_infos,emp_auto_id',
            'emp_type_id' => 'required|integer',
            'basic_amount' => 'required|numeric|min:0',
            'basic_hours' => 'required|numeric|min:0',
            'food_allowance' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0'
        ]);

        try {

            // $employee = EmployeeInfo::findOrFail($request->emp_auto_id);

            $findEmp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
            if ($findEmp ==  null) {
                return response()->json(['status' => 403, 'success' => false, 'message' => "Employee not found", 'dd' => $request->all()], 500);
            }

            $hourly_emp = NULL;
            if ($request->has('hourly_employee')) {
                if ($request->hourly_employee == 1) {
                    $hourly_emp = 1;
                    $request->basic_amount = 0;
                    $request->basic_hours = 0;
                }
            }


            // $employee->update([
            //     'emp_type_id' => $request->emp_type_id,
            //     'hourly_employee' => $request->hourly_employee ?? 0,
            //     'basic_amount' => $request->basic_amount,
            //     'basic_hours' => $request->basic_hours,
            //     'mobile_allowance' => $request->mobile_allowance ?? 0,
            //     'medical_allowance' => $request->medical_allowance ?? 0,
            //     'saudi_tax' => $request->saudi_tax ?? 0,
            //     'food_allowance' => $request->food_allowance,
            //     'hourly_rent' => $request->hourly_rate
            // ]);
            (new EmployeeDataService())->updateEmployeeInfoEmployeeTypeAndIsHourlyEmployee($findEmp->emp_auto_id, $request->emp_type_id, $hourly_emp, Auth::user()->id);
            $updateSalary = (new EmployeeDataService())->updateEmployeeSalaryDetailsBeforeJobApproval(
                $findEmp->emp_auto_id,
                $request->basic_amount,
                $request->basic_hours,
                $request->hourly_rate,
                $request->food_allowance,
                $request->mobile_allowance,
                $request->medical_allowance,
                $request->saudi_tax,
                Auth::user()->id
            );
            if ($updateSalary) {
                // login user activities record
                $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rate;
                (new AuthenticationDataService())->InsertLoginUserActivity(24, 2, Auth::user()->id, $findEmp->emp_auto_id, $salary_amount);
            }

            return response()->json(['status' => 200, 'success' => true, 'message' => 'Employee salary updated', 'dd' => $request->all()]);
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    // ==================================================================
    // ================== EMPLOYEE TRANSFER =============================
    // ==================================================================

    public function multipleEmployeeTransferFormSubmit(Request $request)
    {

        try {
            $emp_list = $request->emp_auto_id;
            $emp_list = array_map('trim', explode(",", $emp_list));
            $creator = Auth::user()->id;
            $assign_date = $request->assign_date;

            if ($request['assigned_project'] == null) {
                return response()->json(['success' => false, 'status' => 403, 'message' => 'Select Project And Try Again', 'error' => 'error']);
            } else {

                foreach ($emp_list as  $emp_auto_id) {
                    (new EmployeeDataService())->updateEmployeeAssignedProject((int) $emp_auto_id, $request['assigned_project']);
                    (new EmployeeRelatedDataService())->assignEmployeeToNewProject((int) $emp_auto_id, $request['assigned_project'], $assign_date, $assign_date, $creator, $request->remarks);
                }
            }

            (new AuthenticationDataService())->InsertLoginUserActivity(13, 2, Auth::user()->id, $request->emp_auto_id, null);

            return response()->json(['success' => true, 'status' => 200, 'message' => 'Successfully Completed']);
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'status' => 500, 'message' => 'Operation Failed, Please Try Again', 'error' => $ex->getMessage()]);
        }
    }


    public function getProjectWiseEmployeeListForEmployeeTransfer(Request $request)
    {
        try {
            $multi_emp_id = $request->multi_emp_id;
            if (!is_null($multi_emp_id)) {

                $allEmplId = explode(",", $multi_emp_id);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $employee_list = (new EmployeeDataService())->getEmployeesInfoByMultipleEmployeeIDForEmployeeTransfer($allEmplId, Auth::user()->branch_office_id);
                return response()->json(['success' => true, 'status' => 200, "data" => $employee_list]);
            } elseif ($request->project_id > 0) {
                $employee_list = (new EmployeeDataService())->getEmployeesInfoByProjectIdForEmployeeTransfer($request->project_id, 1, Auth::user()->branch_office_id);
                return response()->json(['success' => true, 'status' => 200, "data" => $employee_list]);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Employee Not Found. Please Try Again', 'error' => 'error']);
            }
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'status' => 500, 'message' => 'Operation Failed, Please Try Again', 'error' => $ex->getMessage()]);
        }
    }
}
