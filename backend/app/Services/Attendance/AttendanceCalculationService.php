<?php

namespace App\Services\Attendance;

use App\Models\DailyAttendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceCalculationService
{
    public function processDate(string $date, ?string $deviceSerialNumber = null): array
    {
        $settings = AttendanceSetting::query()->where('name', 'default')->first();
        $device = $deviceSerialNumber
            ? AttendanceDevice::query()->where('serial_number', $deviceSerialNumber)->firstOrFail()
            : null;

        $query = AttendanceLog::query()
            ->whereDate('event_time', $date)
            ->whereIn('processing_status', ['pending', 'processed']);

        if ($device) {
            $query->where('attendance_device_id', $device->id);
        }

        $logs = $query->orderBy('event_time')->get();
        $groups = $logs->groupBy(fn($log) => trim((string) $log->pin));

        $processed = 0;
        $unmapped = 0;
        $skipped = 0;

        foreach ($groups as $pin => $employeeLogs) {
            $employee = Employee::query()->where('id', $pin)->first();

            if (!$employee) {
                AttendanceLog::query()->whereIn('id', $employeeLogs->pluck('id'))
                    ->update(['processing_status' => 'unmapped', 'processing_error' => 'No employee matches device PIN / employee_id.']);
                $unmapped++;
                continue;
            }

            $tz = $device?->timezone ?: $employeeLogs->first()?->device?->timezone ?: config('app.timezone');
            $dayLogs = $employeeLogs->filter(fn($log) => Carbon::parse($log->event_time, $tz)->toDateString() === $date);
            if ($dayLogs->isEmpty()) {
                continue;
            }

            $first = $dayLogs->min('event_time');
            $last = $dayLogs->max('event_time');
            $attendance = DailyAttendance::query()
                ->where('employee_auto_id', $employee->id)
                ->whereDate('attendance_date', $date)
                ->first();

            if ($attendance && ($attendance->locked_at || $attendance->source === 'manual')) {
                $skipped++;
                continue;
            }

            $breakMinutes = $attendance?->break_minutes ?? $settings?->default_break_minutes ?? 60;
            $standardHours = (float) ($attendance?->standard_work_hours ?? $settings?->standard_work_hours ?? 8);
            $overtimeThreshold = (float) ($attendance?->overtime_threshold_hours ?? $settings?->overtime_threshold_hours ?? 8);

            $firstAt = Carbon::parse($first, $tz);
            $lastAt = Carbon::parse($last, $tz);
            $grossHours = max(0, $firstAt->diffInSeconds($lastAt, false) / 3600);
            $workedHours = max(0, $grossHours - ($breakMinutes / 60));
            $isSinglePunch = $firstAt->equalTo($lastAt);
            $status = $isSinglePunch ? 'incomplete' : 'present';
            $overtimeHours = max(0, $workedHours - $overtimeThreshold);

            DB::transaction(function () use (
                $employee,
                $date,
                $firstAt,
                $lastAt,
                $breakMinutes,
                $grossHours,
                $workedHours,
                $standardHours,
                $overtimeThreshold,
                $overtimeHours,
                $status,
                $dayLogs,
                &$processed
            ) {
                $attendance = DailyAttendance::query()->updateOrCreate(
                    ['employee_auto_id' => $employee->id, 'attendance_date' => $date],
                    [
                        'source' => 'biometric',
                        'status' => $status,
                        'first_punch_at' => $firstAt,
                        'last_punch_at' => $lastAt,
                        'break_minutes' => $breakMinutes,
                        'gross_hours' => round($grossHours, 2),
                        'worked_hours' => round($workedHours, 2),
                        'standard_work_hours' => $standardHours,
                        'overtime_threshold_hours' => $overtimeThreshold,
                        'overtime_hours' => round($overtimeHours, 2),
                        // Never auto-approve overtime.
                        'approved_overtime_hours' => 0,
                        'payable_day_fraction' => $status === 'present' ? 1 : 0,
                    ]
                );

                AttendanceLog::query()->whereIn('id', $dayLogs->pluck('id'))
                    ->update([
                        'processing_status' => 'processed',
                        'processing_error' => null,
                        'processed_at' => now(),
                    ]);
                $processed++;
            });
        }

        return [
            'date' => $date,
            'groups_found' => $groups->count(),
            'processed_employees' => $processed,
            'unmapped_pin_groups' => $unmapped,
            'skipped_existing_or_locked' => $skipped,
        ];
    }


    public function manualCheckIn(string $employeeId, string $date, string $inTime, ?int $actorId = null, string $status = 'present', int $workOrderId = 0): DailyAttendance
    {
        $employee = Employee::query()->where('id', $employeeId)->firstOrFail();
        $settings = AttendanceSetting::query()->where('name', 'default')->first();

        return DB::transaction(function () use ($employee, $date, $inTime, $actorId, $settings, $status, $workOrderId) {
            $row = DailyAttendance::query()->where('employee_auto_id', $employee->id)
                ->whereDate('attendance_date', $date)->lockForUpdate()->first();

            if ($row?->locked_at) abort(422, 'Attendance record is locked.');
            if ($row?->first_punch_at) abort(422, 'IN has already been recorded for this employee and date.');
            if ($row?->last_punch_at) abort(422, 'OUT has already been recorded for this employee and date.');

            return DailyAttendance::query()->updateOrCreate(
                ['employee_auto_id' => $employee->id, 'attendance_date' => $date],
                [
                    'source' => 'manual',
                    'status' => $status,
                    'first_punch_at' => Carbon::parse($inTime),
                    'last_punch_at' => null,
                    'break_minutes' => $settings?->default_break_minutes ?? 60,
                    'gross_hours' => 0,
                    'worked_hours' => 0,
                    'standard_work_hours' => $settings?->standard_work_hours ?? 8,
                    'overtime_threshold_hours' => $settings?->overtime_threshold_hours ?? 8,
                    'overtime_hours' => 0,
                    'approved_overtime_hours' => 0,
                    'payable_day_fraction' => 0,
                    'entered_by_employee_auto_id' => $actorId,
                    'work_order_id' => $workOrderId,
                ]
            );
        });
    }

    public function manualCheckOut(string $employeeId, string $date, string $outTime, ?int $actorId = null, ?int $breakMinutes = null): DailyAttendance
    {
        $employee = Employee::query()->where('id', $employeeId)->firstOrFail();
        $settings = AttendanceSetting::query()->where('name', 'default')->first();

        return DB::transaction(function () use ($employee, $date, $outTime, $actorId, $breakMinutes, $settings) {
            $row = DailyAttendance::query()->where('employee_auto_id', $employee->id)
                ->whereDate('attendance_date', $date)->lockForUpdate()->first();

            if (!$row || !$row->first_punch_at) abort(422, 'Record IN first before recording OUT.');
            if ($row->locked_at) abort(422, 'Attendance record is locked.');
            if ($row->last_punch_at) abort(422, 'OUT has already been recorded for this employee and date.');

            $in = Carbon::parse($row->first_punch_at);
            $out = Carbon::parse($outTime);
            if ($out->lessThanOrEqualTo($in)) abort(422, 'OUT time must be later than IN time. Overnight shifts are not handled by this endpoint.');

            $break = $breakMinutes ?? (int) ($row->break_minutes ?? $settings?->default_break_minutes ?? 60);
            $gross = max(0, $in->diffInSeconds($out, false) / 3600);
            $worked = max(0, $gross - ($break / 60));
            $threshold = (float) ($row->overtime_threshold_hours ?? $settings?->overtime_threshold_hours ?? 8);

            $row->fill([
                'source' => 'manual',
                'status' => 'present',
                'last_punch_at' => $out,
                'break_minutes' => $break,
                'gross_hours' => round($gross, 2),
                'worked_hours' => round($worked, 2),
                'standard_work_hours' => $row->standard_work_hours ?? $settings?->standard_work_hours ?? 8,
                'overtime_threshold_hours' => $threshold,
                'overtime_hours' => round(max(0, $worked - $threshold), 2),
                'approved_overtime_hours' => 0,
                'payable_day_fraction' => 1,
                'entered_by_employee_auto_id' => $actorId ?? $row->entered_by_employee_auto_id,
            ])->save();

            return $row->refresh();
        });
    }

    public function recalculateManual(array $data, int $employeeAutoId, ?int $actorEmployeeAutoId = null): DailyAttendance
    {
        $settings = AttendanceSetting::query()->where('name', 'default')->first();
        $date = $data['attendance_date'];

        $first = !empty($data['first_punch_at']) ? Carbon::parse($data['first_punch_at']) : null;
        $last = !empty($data['last_punch_at']) ? Carbon::parse($data['last_punch_at']) : null;
        $breakMinutes = (int) ($data['break_minutes'] ?? $settings?->default_break_minutes ?? 60);
        $standardHours = (float) ($settings?->standard_work_hours ?? 8);
        $threshold = (float) ($settings?->overtime_threshold_hours ?? 8);

        $gross = ($first && $last && $last->greaterThanOrEqualTo($first))
            ? $first->diffInSeconds($last, false) / 3600
            : 0;
        $worked = max(0, $gross - $breakMinutes / 60);
        $status = $data['status'] ?? (($first && $last && !$first->equalTo($last)) ? 'present' : 'incomplete');

        return DB::transaction(function () use (
            $data,
            $employeeAutoId,
            $actorEmployeeAutoId,
            $date,
            $first,
            $last,
            $breakMinutes,
            $standardHours,
            $threshold,
            $gross,
            $worked,
            $status
        ) {
            $existing = DailyAttendance::query()
                ->where('employee_auto_id', $employeeAutoId)
                ->whereDate('attendance_date', $date)
                ->lockForUpdate()->first();

            if ($existing?->locked_at) {
                abort(422, 'This attendance record is locked and cannot be edited.');
            }

            $overtime = max(0, $worked - $threshold);
            $values = [
                'source' => 'manual',
                'status' => $status,
                'first_punch_at' => $first,
                'last_punch_at' => $last,
                'break_minutes' => $breakMinutes,
                'gross_hours' => round($gross, 2),
                'worked_hours' => round($worked, 2),
                'standard_work_hours' => $standardHours,
                'overtime_threshold_hours' => $threshold,
                'overtime_hours' => round($overtime, 2),
                'payable_day_fraction' => (float) ($data['payable_day_fraction'] ?? ($status === 'present' ? 1 : ($status === 'half_day' ? 0.5 : 0))),
                'notes' => $data['notes'] ?? null,
                'entered_by_employee_auto_id' => $actorEmployeeAutoId,
                'approved_overtime_hours' => 0,
            ];

            if ($existing) {
                \App\Models\AttendanceAdjustment::query()->create([
                    'daily_attendance_id' => $existing->id,
                    'requested_by_employee_auto_id' => $actorEmployeeAutoId,
                    'old_values' => $existing->only([
                        'status',
                        'first_punch_at',
                        'last_punch_at',
                        'break_minutes',
                        'worked_hours',
                        'overtime_hours',
                        'approved_overtime_hours',
                        'notes',
                    ]),
                    'requested_values' => $values,
                    'reason' => $data['adjustment_reason'] ?? 'Manual attendance update',
                    'status' => 'approved',
                    'reviewed_by_employee_auto_id' => $actorEmployeeAutoId,
                    'reviewed_at' => now(),
                ]);
            }

            return DailyAttendance::query()->updateOrCreate(
                ['employee_auto_id' => $employeeAutoId, 'attendance_date' => $date],
                $values
            );
        });
    }

    public function generateMonthlyWorkRecords(int $year, int $month): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $salaryMonth = $startDate->toDateString();

        return DB::transaction(function () use (
            $startDate,
            $endDate,
            $salaryMonth
        ) {
            // Find employees who have attendance records for this month.
            $employeeIds = DB::table('daily_attendances')
                ->whereBetween('attendance_date', [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ])
                ->distinct()
                ->pluck('employee_auto_id');

            $processed = 0;

            foreach ($employeeIds as $employeeId) {
                $records = DB::table('daily_attendances')
                    ->where('employee_auto_id', $employeeId)
                    ->whereBetween('attendance_date', [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ])
                    ->get();

                $workingDays = 0;
                $presentDays = 0;
                $absentDays = 0;
                $paidLeaveDays = 0;
                $unpaidLeaveDays = 0;
                $totalWorkingHours = 0;
                $overtimeHours = 0;

                foreach ($records as $record) {
                    $status = $record->status;

                    // Exclude holidays and weekly off days from scheduled
                    // working days. Incomplete records remain reviewable.
                    if (!in_array($status, ['holiday', 'weekly_off'])) {
                        $workingDays++;
                    }

                    // Use payable day fractions for present-day totals.
                    if (in_array($status, ['present', 'late', 'offsite'])) {
                        $presentDays += (float) $record->payable_day_fraction;
                    } elseif ($status === 'half_day') {
                        $presentDays += (float) $record->payable_day_fraction;
                    } elseif ($status === 'absent') {
                        $absentDays++;
                    }

                    // Sum recorded working hours.
                    $totalWorkingHours += (float) $record->worked_hours;

                    // Only approved overtime is included in payroll-ready totals.
                    $overtimeHours += (float) $record->approved_overtime_hours;
                }

                // Count approved leave by its leave type.
                $leaves = DB::table('attendance_leaves')
                    ->where('employee_auto_id', $employeeId)
                    ->whereBetween('leave_date', [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ])
                    ->where('status', 'approved')
                    ->get(['leave_type', 'day_fraction']);

                foreach ($leaves as $leave) {
                    if ($leave->leave_type === 'unpaid') {
                        $unpaidLeaveDays += (float) $leave->day_fraction;
                    } else {
                        $paidLeaveDays += (float) $leave->day_fraction;
                    }
                }

                // Create or update one monthly summary per employee.
                DB::table('emp_works')->updateOrInsert(
                    [
                        'employee_id' => $employeeId,
                        'salary_month' => $salaryMonth,
                    ],
                    [
                        'working_days' => $workingDays,
                        'present_days' => round($presentDays, 2),
                        'absent_days' => $absentDays,
                        'paid_leave_days' => round($paidLeaveDays, 2),
                        'unpaid_leave_days' => round($unpaidLeaveDays, 2),
                        'total_working_hours' => round($totalWorkingHours, 2),
                        'overtime_hours' => round($overtimeHours, 2),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $processed++;
            }

            return [
                'salary_month' => $salaryMonth,
                'employees_processed' => $processed,
            ];
        });
    }



    public function getDailyAttendancePunchOutPendingEmployees(array $employeeIds=[], string $date, string $workOrderId = null){
         $query = DailyAttendance::query()
            ->whereDate('attendance_date', $date)
            ->where('status', 'present')
            ->whereNull('last_punch_at')
            ->with('employee'); 
        
        if (!empty($employeeIds)) {
            $query->whereHas('employee', function ($q) use ($employeeIds) {
                $q->whereIn('employee_code', $employeeIds);
            });
        } 
        if($workOrderId){
            $query->where('work_order_id', $workOrderId);
        }     
        // returns a collection of Employee models by plucking the 'employee' relationship from the DailyAttendance records 
        return $query->get()
            ->pluck('employee');
    }
}
