<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:inventory_warehouses,id'],
            'employee_auto_id' => ['required', 'integer', 'exists:employee_infos,emp_auto_id'],
            'item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'issued_by' => ['nullable', 'integer', 'exists:employee_infos,emp_auto_id'],
            'project_id' => ['nullable', 'integer', 'exists:project_infos,proj_id'],
            'serial_id' => ['nullable', 'integer', 'exists:inventory_item_serials,id'],
            'expected_return_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
