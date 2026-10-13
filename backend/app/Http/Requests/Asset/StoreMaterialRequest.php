<?php

namespace App\Http\Requests\Asset;

use App\Models\MaterialRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:material-requests.create`
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
            'work_order_id' => ['nullable', 'integer', Rule::exists('work_orders', 'id')->whereNull('deleted_at')],
            'request_date' => ['nullable', 'date'],
            'required_date' => ['nullable', 'date', 'after_or_equal:request_date'],
            'priority' => ['required', Rule::in(MaterialRequest::PRIORITIES)],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', Rule::exists('item_names', 'item_id')->where('item_status', true)],
            'items.*.requested_qty' => ['required', 'numeric', 'gt:0'],
            'items.*.remarks' => ['nullable', 'string'],
        ];
    }
}
