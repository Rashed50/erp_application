<?php

namespace Modules\HrManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDailyActivityRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'da_subject'         => 'required|string|max:255',
            'da_details'         => 'required|string',
            'da_type_id'         => 'required|exists:daily_activities_type,id',
            'da_for_emp_id'      => 'required',
            'da_responsible_emp' => 'required',
            // 'da_status'          => 'nullable|in:pending,in_progress,completed,cancelled',
            // 'da_progress'        => 'nullable|integer|min:0|max:100',
            'da_status_remarks'  => 'nullable|string',
            'da_attached_file'   => 'nullable|file|max:5120', // 5MB max
        ];
    }

    public function messages()
    {
        return [
            'da_type_id.exists'         => 'The selected activity type is invalid.',
            'da_for_emp_id.exists'      => 'The selected employee is invalid.',
            'da_responsible_emp.exists' => 'The responsible employee is invalid.',
        ];
    }
}
