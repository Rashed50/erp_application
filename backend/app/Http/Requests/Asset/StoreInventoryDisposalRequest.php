<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryDisposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:inventory-disposals.create`
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
            'warehouse_id' => ['required', 'integer', Rule::exists('inventory_warehouses', 'id')],
            'disposal_date' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', Rule::exists('item_names', 'item_id')],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.serial_id' => ['nullable', 'integer', 'distinct', Rule::exists('inventory_item_serials', 'id')],
            'items.*.reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
