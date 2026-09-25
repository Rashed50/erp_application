<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Models\Purchase;
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
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'purchase_id' => ['nullable', Rule::exists('purchases', 'id')->whereNull('deleted_at')],
            'payment_account_id' => ['required', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'bill_amount' => ['required', 'numeric', 'min:0.01'],
            'bank_charge' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:20480'],
        ];
    }

    /**
     * The money must leave an open asset (cash/bank) transaction account, a
     * linked purchase must belong to the supplier and still owe at least the
     * bill amount, and the predefined accounts the entry posts to must exist.
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

            if ($this->filled('purchase_id')) {
                $purchase = Purchase::find($this->input('purchase_id'));

                if ($purchase->supplier_id !== (int) $this->input('supplier_id')) {
                    $validator->errors()->add('purchase_id', 'This purchase does not belong to the selected supplier.');
                } elseif ((float) $this->input('bill_amount') > $purchase->due_amount) {
                    $validator->errors()->add('bill_amount', "Bill amount cannot exceed the purchase due amount ({$purchase->due_amount}).");
                }
            }
        });
    }
}
