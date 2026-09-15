<?php

namespace App\Http\Requests\IncomeExpense;

use App\Models\IncomeExpenseAccount;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomeExpenseTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:income-expenses.create`
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
            'type' => ['required', Rule::in(['income', 'expense'])],
            'income_expense_account_id' => ['required', 'different:payment_account_id', Rule::exists('income_expense_accounts', 'id')],
            'payment_account_id' => ['required', Rule::exists('income_expense_accounts', 'id')],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Every entry is a genuine double entry: the category account must
     * actually be an account of the entry's own type, and the payment
     * account must be an asset account (cash/bank), never a category.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->hasAny(['type', 'income_expense_account_id', 'payment_account_id'])) {
                return;
            }

            $account = IncomeExpenseAccount::find($this->input('income_expense_account_id'));
            if ($account && $account->type !== $this->input('type')) {
                $validator->errors()->add('income_expense_account_id', "This account is not a(n) {$this->input('type')} account.");
            }

            $paymentAccount = IncomeExpenseAccount::find($this->input('payment_account_id'));
            if ($paymentAccount && $paymentAccount->type !== 'asset') {
                $validator->errors()->add('payment_account_id', 'The payment account must be an asset account.');
            }
        });
    }
}
