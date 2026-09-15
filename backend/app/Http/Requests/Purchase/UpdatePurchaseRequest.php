<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:purchases.update` middleware
     * on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * `supplier_id` is intentionally not accepted here. Moving a purchase to a
     * different supplier would require transferring its ledger entry between
     * two suppliers' balances, which this API does not support — a purchase's
     * supplier is fixed at creation time, the same way a customer's
     * `opening_balance` is fixed after creation.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purchase_type' => ['sometimes', 'required', 'string', Rule::in(['product', 'service'])],
            'invoice_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('purchases', 'invoice_number')->ignore($this->route('purchase'))],
            'description' => ['sometimes', 'nullable', 'string'],
            'issue_date' => ['sometimes', 'required', 'date'],
            'purchase_date' => ['sometimes', 'required', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],

            'items' => ['sometimes', 'required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.vat' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
