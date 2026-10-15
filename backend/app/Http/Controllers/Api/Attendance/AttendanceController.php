<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Attendance\StoreManualAttendanceRequest;
// use App\Models\DailyAttendance;
// use App\Models\Employee;
// use App\Services\Attendance\AttendanceCalculationService;
// use Illuminate\Http\Request;

use App\Models\DailyAttendance;
use App\Models\Employee;
use App\Services\Attendance\AttendanceCalculationService;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;   // ✅ Correct import
use App\Http\Responses\ApiResponse;



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
            $employee = Employee::query()->where('employee_id', $request->employee_id)->first();
            if (!$employee) return response()->json(['data' => [], 'message' => 'Employee not found.']);
            $query->where('employee_auto_id', $employee->id);
        }

        return $query->orderByDesc('attendance_date')
            ->paginate(min(max((int) $request->input('per_page', 25), 1), 200));
    }

    public function getEmployeesForAttendanceIN(Request $request): JsonResponse
    {
        $employees = $this->employeeService->getActiveEmployees();

        return ApiResponse::success([
            'employees' => EmployeeResource::collection($employees),
        ]);
    }
    public function getEmployeesForAttendanceOUT(Request $request): JsonResponse
    {
        $employees = DailyAttendance::query()
            ->whereDate('attendance_date', $request->date)
            ->where('status', 'present')
            ->with('employee')
            ->get()
            ->pluck('employee');

        return ApiResponse::success([
            'employees' => EmployeeResource::collection($employees),
        ]);
    }

    public function show(DailyAttendance $dailyAttendance)
    {
        return $dailyAttendance->load('employee', 'shift', 'adjustments');
    }


    public function checkIn(Request $request, AttendanceCalculationService $service): JsonResponse
    {
        $data = $request->validate([
            'ids'             => ['required', 'array', 'min:1'],
            'ids.*'           => ['required', 'integer'],
            'attendance_date' => ['required', 'date_format:Y-m-d'],
            'first_punch_at'  => ['required', 'date_format:Y-m-d H:i:s'],
            'shift_id'        => ['nullable'],
            'status'          => ['nullable', 'string'],
        ]);

        // IN time must belong to the selected attendance date
        if (substr($data['first_punch_at'], 0, 10) !== $data['attendance_date']) {
            return response()->json([
                'message' => 'IN time date must match attendance date.',
            ], 422);
        }

        foreach ($data['ids'] as $employeeId) {
            $service->manualCheckIn(
                $employeeId,
                $data['attendance_date'],
                $data['first_punch_at'],
                $request->user()?->id
            );
        }

        return response()->json([
            'message' => count($data['ids']) . ' employee(s) IN recorded. OUT is pending.',
        ], 201);
    }

    public function checkOut(\Illuminate\Http\Request $request, AttendanceCalculationService $service)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer'],
            'attendance_date' => ['required', 'date_format:Y-m-d'],
            'out_time' => ['required', 'date'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:720'],
        ]);

        $record = $service->manualCheckOut(
            $data['employee_id'],
            $data['attendance_date'],
            $data['out_time'],
            $request->user()?->id,
            isset($data['break_minutes']) ? (int) $data['break_minutes'] : null
        );
        return response()->json(['message' => 'OUT recorded; hours and overtime calculated.', 'data' => $record->load('employee')]);
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
