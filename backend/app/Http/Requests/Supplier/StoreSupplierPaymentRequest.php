<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Models\ChartOfAccount;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Rules\Approved;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierPaymentRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:supplier-payments.create`
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
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->whereNull('deleted_at'), new Approved(Supplier::class)],
            'purchase_id' => ['nullable', Rule::exists('purchases', 'id')->whereNull('deleted_at'), new Approved(Purchase::class)],
            'payment_account_id' => ['required', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at'), new Approved(ChartOfAccount::class)],
            'debit_account_id' => ['nullable', 'different:payment_account_id', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at'), new Approved(ChartOfAccount::class)],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'bill_amount' => ['required', 'numeric', 'min:0.01'],
            'bank_charge' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:20480'],
        ];
    }

    /**
     * The money must leave an open asset (cash/bank) transaction account, the
     * debit account (Accounts Payable when left out) must be an open
     * transaction account, a linked purchase must belong to the supplier,
     * still owe at least the bill amount and be settled through Accounts
     * Payable, and the predefined accounts the entry posts to must exist.
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
                (float) $this->input('bank_charge', 0) > 0
                    ? [LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER, LedgerPostingService::BANK_CHARGES_NUMBER]
                    : [LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER],
            );

            if ($this->filled('debit_account_id')) {
                $this->validateEntryAccount($validator, 'debit_account_id', (int) $this->input('debit_account_id'));
            }

            if ($this->filled('purchase_id')) {
                $purchase = Purchase::find($this->input('purchase_id'));
                $payable = resolve(LedgerPostingService::class)->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER);

                if ($this->filled('debit_account_id') && (int) $this->input('debit_account_id') !== $payable?->id) {
                    $validator->errors()->add('debit_account_id', __('A purchase invoice is settled through Accounts Payable; choose it as the debit account.'));
                } elseif ($purchase->supplier_id !== (int) $this->input('supplier_id')) {
                    $validator->errors()->add('purchase_id', __('This purchase does not belong to the selected supplier.'));
                } elseif ((float) $this->input('bill_amount') > $purchase->due_amount) {
                    $validator->errors()->add('bill_amount', __('Bill amount cannot exceed the purchase due amount (:amount).', ['amount' => $purchase->due_amount]));
                }
            }
        });
    }
}
