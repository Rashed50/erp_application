<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:suppliers.update` middleware
     * on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Note: `opening_balance` is intentionally not editable here, for the same
     * reason as Customer — it is fixed at creation time and only
     * `current_balance` moves afterwards, via ledger transactions.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($this->route('supplier'))],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'address' => ['sometimes', 'nullable', 'string'],
            'vat_no' => ['sometimes', 'nullable', 'string', 'max:100'],
            'payment_term' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'contact_person' => ['sometimes', 'nullable', 'string', 'max:255'],
            'contact_person_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'contact_person_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'active_status' => ['sometimes', 'boolean'],
            'branch_office_id' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}
