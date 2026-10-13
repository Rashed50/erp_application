<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:inventory-transfers.create`
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
            'from_warehouse_id' => ['required', 'integer', Rule::exists('inventory_warehouses', 'id')->where('status', true)],
            'to_warehouse_id' => ['required', 'integer', 'different:from_warehouse_id', Rule::exists('inventory_warehouses', 'id')->where('status', true)],
            'transfer_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', Rule::exists('item_names', 'item_id')],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
