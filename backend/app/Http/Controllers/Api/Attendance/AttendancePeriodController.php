<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendancePeriod;
use App\Services\Attendance\AttendancePeriodService;
use Illuminate\Http\Request;

class AttendancePeriodController extends Controller
{
    public function index()
    {
        return AttendancePeriod::query()->orderByDesc('year')->orderByDesc('month')->paginate(24);
    }

    public function submit(Request $request)
    {
        $data = $request->validate(['year' => ['required', 'integer', 'min:2000', 'max:2200'], 'month' => ['required', 'integer', 'min:1', 'max:12']]);
        $period = AttendancePeriod::query()->firstOrCreate(['year' => $data['year'], 'month' => $data['month']], ['status' => 'open']);

        if ($period->status !== 'open') abort(422, 'Only an open period can be submitted.');
        $period->forceFill(['status' => 'submitted', 'submitted_by_employee_auto_id' => $request->user()?->emp_auto_id, 'submitted_at' => now()])->save();

        return response()->json(['message' => 'Attendance period submitted.', 'data' => $period->refresh()]);
    }

    public function approve(Request $request, AttendancePeriodService $service)
    {
        $data = $request->validate(['year' => ['required', 'integer', 'min:2000', 'max:2200'], 'month' => ['required', 'integer', 'min:1', 'max:12']]);
        return response()->json(['data' => $service->approve($data['year'], $data['month'], $request->user()?->emp_auto_id)]);
    }

    public function lock(Request $request, AttendancePeriodService $service)
    {
        $data = $request->validate(['year' => ['required', 'integer', 'min:2000', 'max:2200'], 'month' => ['required', 'integer', 'min:1', 'max:12']]);
        return response()->json(['message' => 'Attendance period locked.', 'data' => $service->lock($data['year'], $data['month'], $request->user()?->emp_auto_id)]);
    }
}
