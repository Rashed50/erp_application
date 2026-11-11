<?php

namespace App\Http\Requests\Asset;

use App\Models\InventoryWarehouse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:warehouses.create`
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
            'warehouse_code' => ['required', 'string', 'max:50', Rule::unique('inventory_warehouses', 'warehouse_code')],
            'name' => ['required', 'string', 'max:200'],
            'warehouse_type' => ['required', Rule::in(array_keys(InventoryWarehouse::TYPES))],
            'parent_id' => ['nullable', 'integer', Rule::exists('inventory_warehouses', 'id')->where('status', true)],
            'work_order_id' => ['nullable', 'integer', Rule::exists('work_orders', 'id')->whereNull('deleted_at')],
            'manager_employee_id' => ['nullable', 'integer', Rule::exists('employee_info', 'id')->whereNull('deleted_at')],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
        ];
    }
}
