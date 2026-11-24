<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Models\CustomerTransaction;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerTransactionRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

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
     * A payment received must name the cash/bank account the money came
     * into, so it is posted to the general ledger (debit that account,
     * credit Accounts Receivable) and the books stay in step with the
     * customer's balance. Invoices are not accepted here: they come from a
     * sale, which also posts them to the general ledger.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_type' => ['required', Rule::in([CustomerTransaction::TYPE_PAYMENT_RECEIVED, CustomerTransaction::TYPE_ADJUSTMENT])],
            'work_order_id' => ['nullable', Rule::exists('work_orders', 'id')->where('customer_id', $this->route('customer')?->id)->withoutTrashed()],
            'invoice_no' => ['nullable', 'string', 'max:100'],
            'debit' => ['nullable', 'numeric', 'min:0'],
            'credit' => ['nullable', 'numeric', 'min:0'],
            'payment_account_id' => [
                'nullable',
                Rule::requiredIf($this->isPaymentReceived()),
                Rule::prohibitedIf(! $this->isPaymentReceived()),
                Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at'),
            ],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * A ledger entry moves the balance in exactly one direction: either a debit
     * or a credit, never both, and never neither. A payment received is always
     * a credit, into an open cash/bank account.
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

            if ($this->isPaymentReceived() && $debit > 0) {
                $validator->errors()->add('debit', __('A payment received must be a credit.'));
            }

            if ($this->isPaymentReceived() && $this->filled('payment_account_id') && ! $validator->errors()->has('payment_account_id')) {
                $this->validatePaymentAccounts($validator, 'payment_account_id', (int) $this->input('payment_account_id'), [LedgerPostingService::ACCOUNTS_RECEIVABLE_NUMBER]);
            }
        });
    }

    private function isPaymentReceived(): bool
    {
        return $this->input('transaction_type') === CustomerTransaction::TYPE_PAYMENT_RECEIVED;
    }
}
