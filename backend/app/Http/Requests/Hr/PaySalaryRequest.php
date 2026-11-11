<?php

namespace App\Http\Requests\Hr;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaySalaryRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Predefined accounts a salary payment posts to (the first payment of a
     * salary approved before approval booked it also books its expense).
     *
     * @var array<int, string>
     */
    public const REQUIRED_ACCOUNTS = [
        LedgerPostingService::SALARY_EXPENSE_NUMBER,
        LedgerPostingService::EMPLOYEE_ADVANCE_NUMBER,
        LedgerPostingService::SALARY_PAYABLE_NUMBER,
    ];

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct'],
            // The cash/bank account the net salaries are paid from.
            'payment_account_id' => ['required', 'integer', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'payment_date' => ['required', 'date'],
        ];
    }

    /**
     * The money must leave an open asset (cash/bank) account, and the salary
     * expense, employee advance and salary payable accounts the entries post
     * to must exist.
     */
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
                self::REQUIRED_ACCOUNTS,
            );
        });
    }
}
