<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:suppliers.create` middleware
     * on the route.
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'vat_no' => ['nullable', 'string', 'max:100'],
            'payment_term' => ['nullable', 'integer', 'min:0'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:30'],
            'contact_person_email' => ['nullable', 'email', 'max:255'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'active_status' => ['nullable', 'boolean'],
            'branch_office_id' => ['nullable', 'integer'],
        ];
    }
}
