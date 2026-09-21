<?php

namespace App\Http\Requests\Customer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:customer-transactions.create`
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
            'transaction_type' => ['required', 'string', 'max:100'],
            'invoice_no' => ['nullable', 'string', 'max:100'],
            'debit' => ['nullable', 'numeric', 'min:0'],
            'credit' => ['nullable', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * A ledger entry moves the balance in exactly one direction: either a debit
     * or a credit, never both, and never neither.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $debit = (float) $this->input('debit', 0);
            $credit = (float) $this->input('credit', 0);

            if ($debit > 0 && $credit > 0) {
                $validator->errors()->add('debit', 'A transaction cannot have both a debit and a credit amount.');
            }

            if ($debit <= 0 && $credit <= 0) {
                $validator->errors()->add('debit', 'A transaction must have either a debit or a credit amount.');
            }
        });
    }
}
