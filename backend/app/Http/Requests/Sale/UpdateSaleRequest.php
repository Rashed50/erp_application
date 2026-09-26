<?php

namespace App\Http\Requests\Sale;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:sales.update` middleware
     * on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * `customer_id` is intentionally not accepted here, for the same reason
     * as a purchase's `supplier_id` — moving a sale to a different customer
     * would require transferring its ledger entry between two customers'
     * balances, which this API does not support.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'work_order_id' => ['sometimes', 'nullable', Rule::exists('work_orders', 'id')->where('customer_id', $this->route('sale')?->customer_id)->withoutTrashed()],
            'invoice_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('sales', 'invoice_number')->ignore($this->route('sale'))],
            'description' => ['sometimes', 'nullable', 'string'],
            'issue_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:issue_date'],
            'notes' => ['sometimes', 'nullable', 'string'],

            'items' => ['sometimes', 'required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer', Rule::exists('products', 'id')->withoutTrashed()],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.vat' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Payments already received on the sale are tagged with its work order,
     * so the work order can only change while nothing has been paid.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $sale = $this->route('sale');

            if (! $this->exists('work_order_id') || ! $sale || (float) $sale->paid_amount <= 0) {
                return;
            }

            if ((int) $this->input('work_order_id') !== (int) $sale->work_order_id) {
                $validator->errors()->add('work_order_id', 'The work order cannot be changed after a payment has been received.');
            }
        });
    }
}
