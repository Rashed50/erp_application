<?php

namespace App\Http\Requests\IncomeExpense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncomeExpenseTransactionRequest extends FormRequest
{
    use ValidatesEntryAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:income-expenses.update`
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
            'type' => ['sometimes', 'required', Rule::in(['income', 'expense'])],
            'income_expense_account_id' => ['sometimes', 'required', 'different:payment_account_id', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'payment_account_id' => ['sometimes', 'required', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'transaction_date' => ['sometimes', 'required', 'date'],
            'reference_no' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }

    /**
     * Same cross-account checks as creation, resolved against the existing
     * transaction for any field this partial update omits.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->hasAny(['type', 'income_expense_account_id', 'payment_account_id'])) {
                return;
            }

            $transaction = $this->route('income_expense');

            $this->validateEntryAccounts(
                $validator,
                $this->input('type', $transaction->type),
                (int) $this->input('income_expense_account_id', $transaction->income_expense_account_id),
                (int) $this->input('payment_account_id', $transaction->payment_account_id),
            );
        });
    }
}
