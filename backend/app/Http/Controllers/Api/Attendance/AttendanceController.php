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
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;

use Illuminate\Validation\Rule;


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

            $employee = Employee::query()->where('employee_code', $request->employee_id)->first();
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
            'status' => [
                'required',
                'string',
                'in:present,absent,late,half_day,leave,holiday,weekly_off,incomplete,offsite'
            ],
        ]);



        foreach ($data['ids'] as $employeeId) {
            $service->manualCheckIn(
                $employeeId,
                $data['attendance_date'],
                $data['first_punch_at'],
                $request->user()?->id,
                $data['status']
            );
        }

        return response()->json([
            'message' => count($data['ids']) . ' employee(s) IN recorded. OUT is pending.',
        ], 201);
    }



    public function checkOut(Request $request, AttendanceCalculationService $service): JsonResponse
    {

        // return response()->json(['status' => 200, 'success' => true, 'dd' => $request->all()]);

        $data = $request->validate([
            'ids'             => ['required', 'array', 'min:1'],
            'ids.*'           => ['required', 'integer'],
            'attendance_date' => ['required', 'date_format:Y-m-d'],
            'last_punch_at'   => ['required', 'date_format:Y-m-d H:i:s'],
            'shift_id'        => ['nullable'],
            'status' => [
                'required',
                'string',
                'in:present,absent,late,half_day,leave,holiday,weekly_off,incomplete,offsite'
            ],
            'break_minutes'   => ['nullable', 'integer', 'min:0', 'max:720'],
        ]);



        foreach ($data['ids'] as $employeeId) {
            $service->manualCheckOut(
                $employeeId,
                $data['attendance_date'],
                $data['last_punch_at'],
                $request->user()?->id,
                isset($data['break_minutes']) ? (int) $data['break_minutes'] : null
            );
        }

        return response()->json([
            'message' => count($data['ids']) . ' employee(s) OUT recorded; hours and overtime calculated.',
        ]);
    }



    public function update(Request $request, int $id): JsonResponse
    {
        $attendance = DailyAttendance::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in([
                'present',
                'absent',
                'late',
                'half_day',
                'leave',
                'holiday',
                'weekly_off',
                'incomplete',
                'offsite',
            ])],
            'first_punch_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'last_punch_at'  => ['nullable', 'date_format:Y-m-d H:i:s'],
        ]);

        if (
            $data['first_punch_at'] && $data['last_punch_at']
            && $data['last_punch_at'] < $data['first_punch_at']
        ) {
            return response()->json([
                'message' => 'Last punch cannot be before first punch.',
            ], 422);
        }

        $attendance->update($data);

        return response()->json([
            'message' => 'Attendance updated successfully.',
            'data'    => $attendance->load('employee'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        DailyAttendance::findOrFail($id)->delete();

        return response()->json(['message' => 'Attendance deleted successfully.']);
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
