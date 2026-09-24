<?php

namespace App\Http\Requests\FundTransfer;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFundTransferRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:fund-transfers.create`
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
            'credit_account_id' => ['required', 'different:debit_account_id', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'debit_account_id' => ['required', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'receipt_no' => ['nullable', 'string', 'max:255'],
            'transfer_date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'bank_charge' => ['nullable', 'numeric', 'min:0'],
            'vat' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:20480'],
        ];
    }

    /**
     * Money can only move between open asset (cash/bank) transaction
     * accounts, and any bank charge or VAT needs the Bank Charges account.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->hasAny(['credit_account_id', 'debit_account_id'])) {
                return;
            }

            $charges = (float) $this->input('bank_charge', 0) + (float) $this->input('vat', 0);

            $this->validatePaymentAccounts(
                $validator,
                'credit_account_id',
                (int) $this->input('credit_account_id'),
                $charges > 0 ? [LedgerPostingService::BANK_CHARGES_NUMBER] : [],
            );
            $this->validatePaymentAccounts($validator, 'debit_account_id', (int) $this->input('debit_account_id'), []);
        });
    }
}
