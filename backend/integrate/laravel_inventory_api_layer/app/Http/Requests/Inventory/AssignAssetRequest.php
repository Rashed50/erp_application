<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class AssignAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_auto_id' => ['required', 'integer', 'exists:employee_infos,emp_auto_id'],
            'assigned_by' => ['nullable', 'integer', 'exists:employee_infos,emp_auto_id'],
            'project_id' => ['nullable', 'integer', 'exists:project_infos,proj_id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:inventory_warehouses,id'],
            'expected_return_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
