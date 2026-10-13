<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ReturnAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'received_by' => ['nullable', 'integer', 'exists:employee_infos,emp_auto_id'],
            'destination_warehouse_id' => ['required', 'integer', 'exists:inventory_warehouses,id'],
            'condition' => ['required', 'in:new,good,fair,damaged,beyond_repair'],
            'reason' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
