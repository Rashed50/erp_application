<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ManualCheckOutRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer'],
            'attendance_date' => ['required', 'date_format:Y-m-d'],
            'out_time' => ['required', 'date'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:720'],
        ];
    }
}
