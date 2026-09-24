<?php

namespace App\Http\Requests\Sale;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalePaymentRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:sale-payments.create`
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
            'payment_account_id' => ['required', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * A payment can never exceed what is still outstanding on the sale, and
     * must be received into an open cash/bank account.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $validator->errors()->has('payment_account_id')) {
                $this->validatePaymentAccounts($validator, 'payment_account_id', (int) $this->input('payment_account_id'), [LedgerPostingService::ACCOUNTS_RECEIVABLE_NUMBER]);
            }

            if ($validator->errors()->has('amount')) {
                return;
            }

            $sale = $this->route('sale');
            $amount = (float) $this->input('amount');

            if ($sale && $amount > $sale->due_amount) {
                $validator->errors()->add('amount', "Payment amount cannot exceed the due amount ({$sale->due_amount}).");
            }
        });
    }
}
