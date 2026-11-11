<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ReturnEmployeeInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'numeric', 'gt:0'],
            'destination_warehouse_id' => ['required', 'integer', 'exists:inventory_warehouses,id'],
            'received_by' => ['nullable', 'integer', 'exists:employee_infos,emp_auto_id'],
            'condition' => ['required', 'in:new,good,fair,damaged,beyond_repair'],
            'reason' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
