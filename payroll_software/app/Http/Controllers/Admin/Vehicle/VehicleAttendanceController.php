<?php

namespace App\Http\Controllers\Admin\Vehicle;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\VehicleAttendance;
use App\Http\Controllers\Controller;
use function GuzzleHttp\Promise\all;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
// use App\Http\Controllers\Admin\AdvancePayController;
// use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
// use App\Http\Controllers\DataServices\EmployeeDataService;
// use App\Http\Controllers\Admin\Helper\UploadDownloadController;

use App\Http\Controllers\DataServices\TransportationDataService;
// use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;



class VehicleAttendanceController extends Controller
{
    public function index()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $vehicles = (new TransportationDataService())->getAllActiveVehicaleInfos();
        $records = VehicleAttendance::select('vehicle_attendances.*', 'vehicles.veh_name', 'vehicles.veh_plate_number', 'project_infos.proj_name')
                ->leftjoin('vehicles', 'vehicle_attendances.veh_auto_id', '=', 'vehicles.veh_id')
                ->leftjoin('project_infos', 'vehicle_attendances.working_project_id', '=', 'project_infos.proj_id')
                ->latest('veh_atten_auto_id')->take(30)->get();
        return view('admin.vechicle.attendance.index', compact('records','projects','vehicles'));

     }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'atten_date' => 'required|date',
            'working_project_id' => 'required',
            'veh_auto_id' => 'required',
          //  'driver_name' => 'required',
            'driver_iqama' => 'required',
          //  'driver_phone_no' => 'required',
        ]);

        $date = Carbon::parse($request->atten_date);

        VehicleAttendance::create([
            'atten_date' => $request->atten_date,
            'atten_day' => $date->format('l'),
            'atten_month' => $date->format('F'),
            'atten_year' => $date->format('Y'),
            'working_project_id' => $request->working_project_id,
            'veh_auto_id' => $request->veh_auto_id,
            'driver_name' => $request->driver_name,
            'driver_iqama' => $request->driver_iqama,
            'driver_phone_no' => $request->driver_phone_no,
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Attendance added successfully.');
    }

    public function deleteARecord($id)
    {
        try {
            $record = VehicleAttendance::findOrFail($id);
            $record->delete();
            return response()->json(['status' => 200,'success' =>true, 'message' => 'Record deleted successfully.']);

        } catch (Exception $e) {
                  return response()->json(['success' => false, 'status' => 401, 'error' => 'error', 'message' => $e->getMessage()]);
         }
    }

    public function update(Request $request)
    {
       // dd($request->all());
       try {
            $validated = $request->validate([
                'veh_atten_auto_id' => 'required|exists:vehicle_attendances,veh_atten_auto_id',
                'edit_date' => 'required|date',
                'edit_project' => 'required',
                'edit_vehicle' => 'required',
              //  'edit_driver' => 'required',
                'edit_iqama' => 'required',
              //  'edit_phone' => 'required',
            ]);

            $attendance = VehicleAttendance::findOrFail($request->veh_atten_auto_id);
            $date = Carbon::parse($request->edit_date);

            $attendance->update([
                'atten_date' => $request->edit_date,
                'atten_day' => $date->format('l'),
                'atten_month' => $date->format('F'),
                'atten_year' => $date->format('Y'),
                'working_project_id' => $request->edit_project,
                'veh_auto_id' => $request->edit_vehicle,
                'driver_name' => $request->edit_driver,
                'driver_iqama' => $request->edit_iqama,
                'driver_phone_no' => $request->edit_phone,
                'remarks' => $request->edit_remarks,

            ]);

            return redirect()->back()->with('success', 'Attendance updated successfully.');

        } catch (Exception $e) {
          //  dd($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function report(Request $request)
    {
        $query = VehicleAttendance::query();

        if ($request->veh_auto_id) {
            $query->where('veh_auto_id', $request->veh_auto_id);
        }

        if ($request->owner_type) {
            $query->where('owner_type', $request->owner_type); // if column exists
        }

        if ($request->from && $request->to) {
            $query->whereBetween('atten_date', [$request->from, $request->to]);
        }

        $records = $query->get();

        return view('attendance.report', compact('records'));
    }
}
