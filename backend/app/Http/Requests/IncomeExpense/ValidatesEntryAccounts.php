<?php

namespace App\Http\Requests\IncomeExpense;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use Illuminate\Contracts\Validation\Validator;

/**
 * Shared double-entry checks for income/expense transaction requests.
 */
trait ValidatesEntryAccounts
{
    /**
     * Every entry is a genuine double entry: the category account must be a
     * revenue account for income (an expense account for expense), the payment
     * account must be an asset account (cash/bank), and both must be open
     * transaction accounts that can receive postings.
     */
    protected function validateEntryAccounts(Validator $validator, string $type, int $accountId, int $paymentAccountId): void
    {
        $account = ChartOfAccount::find($accountId);
        $expectedType = $type === 'income' ? AccountType::REVENUE : AccountType::EXPENSE;
        $expectedLabel = $type === 'income' ? 'revenue' : 'expense';

        if ($account && $account->account_type_id !== $expectedType) {
            $validator->errors()->add('income_expense_account_id', "This account is not a {$expectedLabel} account.");
        } elseif ($account && ! $account->canReceiveEntries()) {
            $validator->errors()->add('income_expense_account_id', 'This account cannot receive transactions (it is a group, inactive or closed).');
        }

        $paymentAccount = ChartOfAccount::find($paymentAccountId);

        if ($paymentAccount && $paymentAccount->account_type_id !== AccountType::ASSET) {
            $validator->errors()->add('payment_account_id', 'The payment account must be an asset account.');
        } elseif ($paymentAccount && ! $paymentAccount->canReceiveEntries()) {
            $validator->errors()->add('payment_account_id', 'This account cannot receive transactions (it is a group, inactive or closed).');
        }
    }
}
