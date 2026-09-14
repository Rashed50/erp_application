<?php

namespace Modules\Payroll\Http\Controllers\Salary;

use Modules\Payroll\Entities\PartialSalary;
use App\Models\EmployeeInfo;
use App\Models\SalarySheetUpload;
// use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DataServices\{SalaryProcessDataService,EmployeeRelatedDataService,ProjectDataService,CompanyDataService};
use App\Http\Controllers\Admin\Helper\UploadDownloadController;


class PayslipUploadController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:monthly_salary_sheet_upload', ['only' => ['index']]);
        $this->middleware('permission:payslip_upload_add', ['only' => ['index','store']]);
        $this->middleware('permission:payslip_upload_edit', ['only' => ['index','update']]);
        $this->middleware('permission:payslip_upload_search', ['only' => ['index','search']]);
        $this->middleware('permission:payslip_upload_delete', ['only' => ['index','destroy']]);

    }

        // Employee Salary sheet upload UI
    public function index()
    {



        return view('payroll::pages.Payroll.payslip_upload');
    }



  /**
   * Upload Salary Sheet
   */

    public function store(Request $request)
    {
      try{

            $insert = 0;
            if ($request->hasFile('file_name')) {

                $file_path =  (new UploadDownloadController())->uploadEmployeeSalarySheet($request->file('file_name'),null);
                $file_name =  "nothing"; // this column will be delete soon
                $insert = (new SalaryProcessDataService())->insertUploadedSalaryShetInformation($request->no_of_emp ,$request->salary_date ,$request->month ,$request->year, Auth::user()->id,$request->remarks,$file_name ,$file_path);
            }

            if ($insert) {
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Salary sheet uploaded successfully'], 200);
            } else {
                return response()->json(['success' => false, 'status' => 400, 'message' => "Failed to upload salary sheet. Please try again."], 400);

            }

        }catch(Exception $ex){
            return response()->json(['success' => false, 'status' => 500, 'message' => $ex->getMessage()], 500);

        }


    }




  /**
   * Search Salary Sheets with Filters
   */
  public function search(Request $request)
  {
        try {
        // Build query based on filters
        // $query = SalarySheetUpload::query();
        $query = SalarySheetUpload::query()
            ->join('users', 'salary_sheet_uploads.uploaded_by', '=', 'users.id')
            ->select('salary_sheet_uploads.*', 'users.name as uploader_name');

        // Filter by employee ID if provided
        if ($request->has('employee_id') && !empty($request->employee_id)) {
            $query->where('no_of_emp', $request->employee_id);
        }

        // Filter by month if provided
        if ($request->has('month') && !empty($request->month)) {
            $query->where('month', $request->month);
        }

        // Filter by year if provided
        if ($request->has('year') && !empty($request->year)) {
            $query->where('year', $request->year);
        }

        // Filter by salary date if provided
        if ($request->has('date') && !empty($request->date)) {
            $query->where('salary_date', $request->date);
        }

        // Get results ordered by latest first
        $results = $query->orderBy('ss_auto_id', 'DESC')->get();

        if ($results->isEmpty()) {
            return response()->json([
            'status' => 404,
            'message' => 'No salary sheets found matching your criteria',
            'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Salary sheets found',
            'data' => $results
        ]);

        } catch (Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Error searching salary sheets: ' . $e->getMessage()
        ], 500);
        }
  }

  public function update(Request $request)
  {
        try {
        // Validate input
        $this->validate($request, [
            'record_id' => 'required|integer',
            'no_of_emp' => 'nullable|integer',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'salary_date' => 'required|date',
            'remarks' => 'nullable|string|max:200'
        ]);

        // Get the record
        $recordId = $request->record_id;
        $salarySheet = SalarySheetUpload::find($recordId);

        if (!$salarySheet) {
            return response()->json([
            'status' => 404,
            'message' => 'Salary sheet record not found'
            ], 404);
        }

        // Update the record
        $salarySheet->update([
            'no_of_emp' => $request->no_of_emp,
            'month' => $request->month,
            'year' => $request->year,
            'salary_date' => $request->salary_date,
            'remarks' => $request->remarks
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Salary sheet updated successfully',
            'data' => $salarySheet
        ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 422,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);

        } catch (Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Error updating salary sheet: ' . $e->getMessage()
        ], 500);
        }
  }

     // Delete Uploaded Salary Sheet
  public function destroy($salary_uploaded_info_auto_id)
  {
       try{
            $record =  (new SalaryProcessDataService())->getAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id);
            if(!$record){
                return response()->json([
                    'status' => 404,
                    'message' => 'Salary sheet uploaded record not found'.$salary_uploaded_info_auto_id
                ], 404);
            }

            $isSuccess =  (new SalaryProcessDataService())->deleteAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id);
            if($isSuccess){
                 (new UploadDownloadController())->deleteUploadedSalarySheet($record->file_path);
                return response()->json([
                    'status' => 200,
                    'message' => 'Salary sheet uploaded file deleted successfully'
                ], 200);

            }else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Failed to delete uploaded salary sheet. Please try again.'
                ], 400);

            }
        }catch(Exception $ex){
                return response()->json([
                    'status' => 500,
                    'message' => 'Error deleting salary sheet: ' . $ex->getMessage()
                ], 500);
        }


  }


  /**
   * Get All Salary Sheets
   */
  public function getAllSalarySheets()
  {
    try {
      $allSheets = SalarySheetUpload::orderBy('ss_auto_id', 'DESC')->get();

      return response()->json([
        'status' => 200,
        'message' => 'All salary sheets retrieved',
        'data' => $allSheets
      ]);

    } catch (Exception $e) {
      return response()->json([
        'status' => 500,
        'message' => 'Error retrieving salary sheets: ' . $e->getMessage()
      ], 500);
    }
  }



      /*
    =======================================================================
    Employee Mobile Bill Information
    =======================================================================
  */
    public function manageEmployeeMobileBillRelatedInformation(Request $request)
    {
        try{
                    if( (int) $request->operation_type == 1){
                        if ($request->bill_month != null && $request->bill_year != null && $request->bill_project_id != null && $request->hasFile('bill_paper')) {
                            $uploadedPath = (new  UploadDownloadController())->uploadEmployeeMobileBillPaper($request->file('bill_paper'), null);
                            $bill_id = (new SalaryProcessDataService())->storeEmployeeMobileBillInformation($request->bill_month, $request->bill_year, $request->bill_project_id,$uploadedPath,Auth::user()->id);
                            return response()->json([
                                'status' => 200,
                                'success' => true,
                                'message' => 'Successfully Uploaded',
                            ]);

                        }
                    }
                    else if ( (int) $request->operation_type == 2 )
                    {
                        $months =   is_null($request->month) ? [1,2,3,4,5,6,7,8,9,11,12]:[$request->month];
                        $project_ids = is_null($request->project_id) ? (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id) : [$request->project_id];
                        $year = $request->year;
                        $search_records = (new SalaryProcessDataService())->searchEmployeeMobileBillRecordsForListViewByYearMonthProject($months,$year ,$project_ids,Auth::user()->branch_office_id);
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'data' => $search_records,
                            'message' => '',
                        ]);
                    }
                    else {
                        return response()->json([
                            'success' => false,
                            'status' => 200,
                            'error' => 'error',
                            'message' => 'Please Try Again With All Information',
                            'd'=> $request->all()
                        ]);
                    }

            }catch(Exception $ex){
                return response()->json([
                    'success' => false,
                    'status' => 200,
                    'error' => 'error',
                    'message' => 'Operation Failed. Please Try Again With All Information ',
                    'ee' =>$ex,
                ]);
            }
    }




}
