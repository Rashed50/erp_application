<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueEmployeeInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employee-inventory.issue`
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
            'warehouse_id' => ['required', 'integer', Rule::exists('inventory_warehouses', 'id')->where('status', true)],
            'employee_id' => ['required', 'integer', Rule::exists('employee_info', 'id')->whereNull('deleted_at')],
            'item_id' => ['required', 'integer', Rule::exists('item_names', 'item_id')],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'serial_id' => ['nullable', 'integer'],
            'work_order_id' => ['nullable', 'integer', Rule::exists('work_orders', 'id')->whereNull('deleted_at')],
            'expected_return_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
