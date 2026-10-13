<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReceivePurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:purchase-orders.receive`
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
            'received_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'gte:0'],
            'items.*.batch_no' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.serials' => ['nullable', 'array'],
            'items.*.serials.*.serial_number' => ['required', 'string', 'max:150', 'distinct', Rule::unique('inventory_item_serials', 'serial_number')],
            'items.*.serials.*.asset_tag' => ['nullable', 'string', 'max:100', 'distinct', Rule::unique('inventory_assets', 'asset_tag')],
            'items.*.serials.*.barcode' => ['nullable', 'string', 'max:150', 'distinct', Rule::unique('inventory_item_serials', 'barcode')],
            'items.*.serials.*.warranty_expiry' => ['nullable', 'date'],
        ];
    }
}
