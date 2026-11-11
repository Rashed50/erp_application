<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'pin' => ['nullable', 'string', 'max:100'],
            'processing_status' => ['nullable', 'in:pending,processed,unmapped,ignored,error'],
            'device_serial_number' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $query = AttendanceLog::query()->with('device');

        if ($request->filled('date_from')) $query->whereDate('event_time', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('event_time', '<=', $request->date_to);
        if ($request->filled('pin')) $query->where('pin', $request->pin);
        if ($request->filled('processing_status')) $query->where('processing_status', $request->processing_status);
        if ($request->filled('device_serial_number')) {
            $query->whereHas('device', fn ($q) => $q->where('serial_number', $request->device_serial_number));
        }

        return $query->orderByDesc('event_time')
            ->paginate(min(max((int) $request->input('per_page', 50), 1), 200));
    }
}
