<?php

namespace App\Http\Requests\Concerns;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use Illuminate\Contracts\Validation\Validator;

/**
 * Shared chart of account checks for requests that record a payment which
 * posts to a cash/bank account and to predefined accounts.
 */
trait ValidatesPaymentAccounts
{
    /**
     * The money must move through an open asset (cash/bank) transaction
     * account, and the predefined accounts the double entry posts to must exist.
     *
     * @param  array<int, string>  $requiredAccountNumbers
     */
    protected function validatePaymentAccounts(Validator $validator, string $field, int $paymentAccountId, array $requiredAccountNumbers): void
    {
        $paymentAccount = ChartOfAccount::find($paymentAccountId);

        if ($paymentAccount && $paymentAccount->account_type_id !== AccountType::ASSET) {
            $validator->errors()->add($field, 'The payment account must be an asset (cash/bank) account.');
        } elseif ($paymentAccount && ! $paymentAccount->canReceiveEntries()) {
            $validator->errors()->add($field, 'This account cannot receive transactions (it is a group, inactive or closed).');
        }

        foreach ($requiredAccountNumbers as $accountNumber) {
            if (! ChartOfAccount::query()->where('account_number', $accountNumber)->exists()) {
                $validator->errors()->add($field, "The predefined account {$accountNumber} is missing. Run the chart of account seeder.");
            }
        }
    }
}
