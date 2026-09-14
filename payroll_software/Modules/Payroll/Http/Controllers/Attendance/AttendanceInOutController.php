<?php


namespace Modules\Payroll\Http\Controllers\Attendance;

use Illuminate\Http\Request;
use Modules\Payroll\Entities\OvertimeSheet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Auth;
use App\Http\Controllers\DataServices\{ProjectDataService, EmployeeAttendanceDataService, WPSEmployeeDataService};
use App\Http\Controllers\Admin\Helper\{HelperController};

class AttendanceInOutController
{

    public function index()
    {

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        // $data = [
        //     'projects'=>$projects
        // ];
        return view('payroll::pages.attendance.in_out', ['data' => [
            'projects' => $projects,
        ]]);
    }


    public function employeeAttendanceBioSearch(Request $request)
    {

    

        //return response()->json(['status' => 200, 'success' => true, "data" => $request->all()]);
        try {
            $daymonthyear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
            $isNightShift = 0;
            //   $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($request->proj_name,
            //   $daymonthyear[0],$daymonthyear[1],$daymonthyear[2],$request->isNightShift);

            if ($request->emp_ids != null) {
                $allEmplId = explode(",", $request->emp_ids);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByMultipleEmpIDAndDayMonthYear($allEmplId, $request->proj_name, $daymonthyear[0], $daymonthyear[1], $daymonthyear[2], $request->isNightShift);
            } else {
                $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear(
                    $request->project_ids[0],
                    $daymonthyear[0],
                    $daymonthyear[1],
                    $daymonthyear[2],
                    $request->isNightShift
                );
            }

            if (count($attendentedEmpLst) > 0) {
                return response()->json(['status' => 200, 'success' => true, "data" => $attendentedEmpLst]);
            } else {
                return response()->json(['status' => 404, 'success' => false, 'error' => "error", 'message' => 'Employee Not Found']);
            }
        } catch (Exception $ex) {
            return response()->json(['status' => 404, 'success' => false, 'error' => "error", 'message' => 'System Error Found, Reload Page & Try Again']);
        }
    }






    // Insert Operation
    public function storeOvertimeSheet(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'ot_date'    => 'required|date',
            //  'ot_file'    => 'nullable|mimes:pdf,jpg,png|max:2048'
        ]);

        $data = $request->all();
        $data['insert_by'] = Auth::user()->id;
        $data['approved_by'] = Auth::user()->id;

        if ($request->hasFile('ot_file')) {
            // Store in S3 (as per your previous setup) or local 'public/overtime'
            $path = $request->file('ot_file')->store('ot', 's3');
            $data['ot_file'] =  $path;
        }

        OvertimeSheet::create($data);

        return response()->json(['success' => true, 'message' => 'Overtime sheet uploaded successfully!', 'status' => 200]);
    }

    // Update Operation
    public function updateOvertimeSheet(Request $request, $id)
    {
        $sheet = OvertimeSheet::findOrFail($id);

        $request->validate([
            'project_id' => 'required',
            'ot_date'    => 'required|date'
        ]);

        $data = $request->only(['project_id', 'ot_date', 'approved_by']);
        if ($request->hasFile('ot_file')) {
            // Delete old file from S3 before uploading new one
            if ($sheet->ot_file) {
                Storage::disk('s3')->delete($sheet->ot_file);
            }
            $data['ot_file'] = $request->file('ot_file')->store('ot', 's3');
        }

        $sheet->update($data);
        return response()->json(['success' => true, 'message' => 'Overtime sheet updated successfully!', 'status' => 200]);
    }
    public function deleteOvertimeSheet($id)
    {
        $sheet = OvertimeSheet::findOrFail($id);
        if ($sheet->ot_file) {
            Storage::disk('s3')->delete($sheet->ot_file);
        }
        $sheet->delete();
        return response()->json(['success' => true, 'message' => 'Overtime sheet deleted successfully!', 'status' => 200]);
    }

    public function searchOvertimeSheets(Request $request)
    {
        $query = OvertimeSheet::leftjoin('project_infos', 'project_infos.proj_id', '=', 'overtime_sheets.project_id')
            ->leftjoin('users', 'users.id', '=', 'overtime_sheets.insert_by')
            ->select('overtime_sheets.*', 'project_infos.proj_name', 'users.name as inserted_by_name');

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->ot_date) {
            $query->where('ot_date', $request->ot_date);
        } // 050 843 6722
        $records = $query->get();
        return response()->json(['success' => true, 'data' => $records, 'status' => 200]);
    }

}
