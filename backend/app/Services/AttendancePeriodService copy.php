<?php

namespace App\Services\Attendance;

use App\Models\AttendancePeriod;
use App\Models\DailyAttendance;
use Illuminate\Support\Facades\DB;

class AttendancePeriodService
{
    public function lock(int $year, int $month, ?int $actorEmployeeAutoId): AttendancePeriod
    {
        return DB::transaction(function () use ($year, $month, $actorEmployeeAutoId) {
            $period = AttendancePeriod::query()->firstOrCreate(
                ['year' => $year, 'month' => $month],
                ['status' => 'open']
            );

            if ($period->status !== 'approved' && $period->status !== 'locked') {
                abort(422, 'Attendance period must be approved before it can be locked.');
            }

            $start = sprintf('%04d-%02d-01', $year, $month);
            $end = date('Y-m-t', strtotime($start));

            DailyAttendance::query()
                ->whereBetween('attendance_date', [$start, $end])
                ->whereNull('locked_at')
                ->update(['locked_at' => now(), 'updated_at' => now()]);

            $period->forceFill(['status' => 'locked', 'locked_at' => now()])->save();
            return $period->refresh();
        });
    }

    public function approve(int $year, int $month, ?int $actorEmployeeAutoId): AttendancePeriod
    {
        $period = AttendancePeriod::query()->firstOrCreate(
            ['year' => $year, 'month' => $month],
            ['status' => 'open']
        );

        if (!in_array($period->status, ['submitted', 'approved'], true)) {
            abort(422, 'Only a submitted attendance period can be approved.');
        }

        $period->forceFill([
            'status' => 'approved',
            'approved_by_employee_auto_id' => $actorEmployeeAutoId,
            'approved_at' => now(),
        ])->save();

        return $period->refresh();
    }
}
