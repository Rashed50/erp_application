<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Models\ChartOfAccount;
use App\Rules\Approved;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierTransactionRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:supplier-transactions.create`
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
            'payment_account_id' => [
                'nullable',
                Rule::requiredIf($this->isBillPayment()),
                Rule::prohibitedIf(! $this->isBillPayment()),
                Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at'),
                new Approved(ChartOfAccount::class),
            ],
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
                $validator->errors()->add('debit', __('A transaction cannot have both a debit and a credit amount.'));
            }

            if ($debit <= 0 && $credit <= 0) {
                $validator->errors()->add('debit', __('A transaction must have either a debit or a credit amount.'));
            }

            if ($this->isBillPayment() && $debit <= 0) {
                $validator->errors()->add('debit', __('A bill payment must be a debit.'));
            }

            if ($this->isPurchase() && $credit <= 0) {
                $validator->errors()->add('credit', __('A purchase must be a credit.'));
            }

            if ($this->isBillPayment() && $this->filled('payment_account_id') && ! $validator->errors()->has('payment_account_id')) {
                $this->validatePaymentAccounts($validator, 'payment_account_id', (int) $this->input('payment_account_id'), [LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER]);
            }
        });
    }

    private function isBillPayment(): bool
    {
        return $this->input('transaction_type') === 'Bill Payment';
    }

    private function isPurchase(): bool
    {
        return $this->input('transaction_type') === 'Purchase';
    }
}
