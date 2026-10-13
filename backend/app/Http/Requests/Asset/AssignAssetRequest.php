<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:assets.assign`
     * middleware on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', Rule::exists('employee_info', 'id')->whereNull('deleted_at')],
            'work_order_id' => ['nullable', 'integer', Rule::exists('work_orders', 'id')->whereNull('deleted_at')],
            'expected_return_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
