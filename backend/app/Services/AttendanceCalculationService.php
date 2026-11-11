<?php

namespace App\Services\Attendance;

use App\Models\DailyAttendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\EmployeeInfo;
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
        $groups = $logs->groupBy(fn ($log) => trim((string) $log->pin));

        $processed = 0;
        $unmapped = 0;
        $skipped = 0;

        foreach ($groups as $pin => $employeeLogs) {
            $employee = EmployeeInfo::query()->where('employee_id', $pin)->first();

            if (!$employee) {
                AttendanceLog::query()->whereIn('id', $employeeLogs->pluck('id'))
                    ->update(['processing_status' => 'unmapped', 'processing_error' => 'No employee matches device PIN / employee_id.']);
                $unmapped++;
                continue;
            }

            $tz = $device?->timezone ?: $employeeLogs->first()?->device?->timezone ?: config('app.timezone');
            $dayLogs = $employeeLogs->filter(fn ($log) => Carbon::parse($log->event_time, $tz)->toDateString() === $date);
            if ($dayLogs->isEmpty()) {
                continue;
            }

            $first = $dayLogs->min('event_time');
            $last = $dayLogs->max('event_time');
            $attendance = DailyAttendance::query()
                ->where('employee_auto_id', $employee->emp_auto_id)
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
                $employee, $date, $firstAt, $lastAt, $breakMinutes, $grossHours,
                $workedHours, $standardHours, $overtimeThreshold, $overtimeHours,
                $status, $dayLogs, &$processed
            ) {
                $attendance = DailyAttendance::query()->updateOrCreate(
                    ['employee_auto_id' => $employee->emp_auto_id, 'attendance_date' => $date],
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
            $data, $employeeAutoId, $actorEmployeeAutoId, $date, $first, $last,
            $breakMinutes, $standardHours, $threshold, $gross, $worked, $status
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
                        'status', 'first_punch_at', 'last_punch_at', 'break_minutes',
                        'worked_hours', 'overtime_hours', 'approved_overtime_hours', 'notes',
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
}
