<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Models\DailyAttendance;
use App\Models\EmployeeInfo;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function monthly(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2200'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'employee_id' => ['nullable', 'string', 'max:100'],
        ]);

        $start = sprintf('%04d-%02d-01', $validated['year'], $validated['month']);
        $end = date('Y-m-t', strtotime($start));

        $query = DailyAttendance::query()->with('employee')
            ->whereBetween('attendance_date', [$start, $end]);

        if (!empty($validated['employee_id'])) {
            $employee = EmployeeInfo::query()->where('employee_id', $validated['employee_id'])->firstOrFail();
            $query->where('employee_auto_id', $employee->emp_auto_id);
        }

        $records = $query->orderBy('employee_auto_id')->orderBy('attendance_date')->get();

        $summary = $records->groupBy('employee_auto_id')->map(function ($rows) {
            $employee = $rows->first()->employee;
            return [
                'employee_auto_id' => $employee?->emp_auto_id,
                'employee_id' => $employee?->employee_id,
                'employee_name' => $employee?->name ?? $employee?->employee_name ?? null,
                'present_days' => round($rows->sum('payable_day_fraction'), 2),
                'absent_days' => $rows->where('status', 'absent')->count(),
                'leave_days' => round($rows->where('status', 'leave')->sum('payable_day_fraction'), 2),
                'worked_hours' => round($rows->sum('worked_hours'), 2),
                'potential_overtime_hours' => round($rows->sum('overtime_hours'), 2),
                'approved_overtime_hours' => round($rows->sum('approved_overtime_hours'), 2),
                'daily_records' => $rows->values(),
            ];
        })->values();

        return response()->json([
            'period' => ['year' => (int) $validated['year'], 'month' => (int) $validated['month']],
            'employees' => $summary,
        ]);
    }
}
