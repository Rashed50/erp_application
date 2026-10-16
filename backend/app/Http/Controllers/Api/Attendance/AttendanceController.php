<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreManualAttendanceRequest;
use App\Models\DailyAttendance;
use App\Models\EmployeeInfo;
use App\Services\Attendance\AttendanceCalculationService;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
 



class AttendanceController extends Controller
{

    public function __construct(private readonly EmployeeService $employeeService) {}

    public function index(Request $request)
    {
        $query = DailyAttendance::query()->with('employee');

        if ($request->filled('date')) $query->whereDate('attendance_date', $request->date);
        if ($request->filled('date_from')) $query->whereDate('attendance_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('attendance_date', '<=', $request->date_to);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('employee_id')) {
            $employee = EmployeeInfo::query()->where('employee_id', $request->employee_id)->first();
            if (!$employee) return response()->json(['data' => [], 'message' => 'Employee not found.']);
            $query->where('employee_auto_id', $employee->emp_auto_id);
        }

        return $query->orderByDesc('attendance_date')
            ->paginate(min(max((int) $request->input('per_page', 25), 1), 200));
    }

 

    public function show(DailyAttendance $dailyAttendance)
    {
        return $dailyAttendance->load('employee', 'shift', 'adjustments');
    }

    public function manual(StoreManualAttendanceRequest $request, AttendanceCalculationService $service)
    {
        $employee = EmployeeInfo::query()->where('employee_id', $request->validated('employee_id'))->firstOrFail();

        $actor = $this->actorEmployeeAutoId($request);
        $record = $service->recalculateManual($request->validated(), (int) $employee->emp_auto_id, $actor);

        return response()->json(['message' => 'Manual attendance saved.', 'data' => $record->load('employee')], 201);
    }

    public function processLogs(
        \App\Http\Requests\Attendance\ProcessAttendanceLogsRequest $request,
        AttendanceCalculationService $service
    ) {
        return response()->json([
            'message' => 'Attendance log processing completed.',
            'data' => $service->processDate($request->validated('date'), $request->validated('device_serial_number')),
        ]);
    }

    private function actorEmployeeAutoId(Request $request): ?int
    {
        $value = $request->user()?->emp_auto_id;
        return $value !== null ? (int) $value : null;
    }
}
