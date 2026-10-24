<?php

namespace App\Http\Requests\Hr;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalaryPaymentRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:payroll.pay` middleware
     * on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * All or part of one salary's due amount. The due amount check is done
     * by the service under a row lock.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'payment_account_id' => ['required', 'integer', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validatePaymentAccounts(
                $validator,
                'payment_account_id',
                (int) $this->input('payment_account_id'),
                PaySalaryRequest::REQUIRED_ACCOUNTS,
            );
        });
    }
}
