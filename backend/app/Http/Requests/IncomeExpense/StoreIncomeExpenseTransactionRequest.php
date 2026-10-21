<?php

namespace App\Http\Requests\IncomeExpense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomeExpenseTransactionRequest extends FormRequest
{
    use ValidatesEntryAccounts;

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
            'income_expense_account_id' => ['required', 'different:payment_account_id', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'payment_account_id' => ['required', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'work_order_id' => ['nullable', 'integer', Rule::exists('work_orders', 'id')->withoutTrashed()],
            'employee_id' => ['nullable', 'integer', Rule::exists('employee_info', 'id')->withoutTrashed()],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:20480'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->hasAny(['type', 'income_expense_account_id', 'payment_account_id'])) {
                return;
            }

            $this->validateEntryAccounts(
                $validator,
                $this->input('type'),
                (int) $this->input('income_expense_account_id'),
                (int) $this->input('payment_account_id'),
            );
        });
    }
}
