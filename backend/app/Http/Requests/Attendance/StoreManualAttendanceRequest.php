<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualAttendanceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'string', 'max:100'],
            'attendance_date' => ['required', 'date_format:Y-m-d'],
            'first_punch_at' => ['nullable', 'date'],
            'last_punch_at' => ['nullable', 'date', 'after_or_equal:first_punch_at'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:720'],
            'status' => ['nullable', 'in:present,absent,late,half_day,leave,holiday,weekly_off,incomplete,offsite'],
            'payable_day_fraction' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'adjustment_reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
