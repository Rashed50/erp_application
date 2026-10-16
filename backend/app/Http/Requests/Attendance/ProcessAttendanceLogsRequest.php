<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ProcessAttendanceLogsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'device_serial_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}
