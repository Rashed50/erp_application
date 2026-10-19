<?php

namespace App\Http\Requests\Quotation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:quotations.create` middleware
     * on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Money totals (`total_amount`, `discount_amount`, `vat_amount`, `net_total`)
     * are not accepted here — they are computed server-side from `items`, the
     * same as sales.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', Rule::exists('customers', 'id')],
            'invoice_number' => ['required', 'string', 'max:255', Rule::unique('quotations', 'invoice_number')],
            'description' => ['nullable', 'string'],
            'issue_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.unit_id' => ['nullable', 'integer', Rule::exists('units', 'id')],
            'items.*.count' => ['nullable', 'numeric', 'min:0'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.vat' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
