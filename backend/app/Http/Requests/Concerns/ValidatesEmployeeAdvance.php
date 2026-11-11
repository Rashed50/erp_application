<?php

namespace App\Http\Requests\Concerns;

use App\Services\LedgerPostingService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * Shared rules for an advance salary, used by both the store and the update
 * request.
 */
trait ValidatesEmployeeAdvance
{
    use ValidatesPaymentAccounts;

    /**
     * @return array<string, array<mixed>>
     */
    protected function advanceRules(): array
    {
        return [
            'advance_date' => ['required', 'date'],
            // The cash/bank account the advance is paid from.
            'payment_account_id' => ['required', 'integer', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'amount' => ['required', 'numeric', 'min:1', 'max:9999999999'],
            'installment_count' => ['required', 'integer', 'min:1', 'max:120'],
            // Defaults to amount / installment count when left out.
            'installment_amount' => ['nullable', 'numeric', 'gt:0', 'lte:amount'],
            'deduction_start_month' => ['required', 'date_format:Y-m'],
            'purpose' => ['nullable', 'string', 'max:150'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Installments cannot start before the month the advance was given, and
     * the advance must be paid from an open cash/bank account.
     */
    protected function validateAdvance(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $advanceMonth = CarbonImmutable::parse($this->input('advance_date'))->format('Y-m');

        if ($this->input('deduction_start_month') < $advanceMonth) {
            $validator->errors()->add('deduction_start_month', __('Deduction cannot start before the month the advance was given.'));
        }

        $this->validatePaymentAccounts(
            $validator,
            'payment_account_id',
            (int) $this->input('payment_account_id'),
            [LedgerPostingService::EMPLOYEE_ADVANCE_NUMBER],
        );
    }
}
