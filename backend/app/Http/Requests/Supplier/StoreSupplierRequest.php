<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Concerns\ValidatesPaymentAccounts;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Rules\Approved;
use App\Services\LedgerPostingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    use ValidatesPaymentAccounts;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:suppliers.create` middleware
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
            'name' => ['required', 'string', 'max:255'],
            'supplier_type' => ['nullable', Rule::in(Supplier::TYPES)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->withoutTrashed()],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'vat_no' => ['nullable', 'string', 'max:100'],
            'payment_term' => ['nullable', 'integer', 'min:0'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:30'],
            'contact_person_email' => ['nullable', 'email', 'max:255'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'opening_balance_account_id' => [
                Rule::requiredIf(fn () => $this->input('supplier_type') === Supplier::TYPE_SERVICE && (float) $this->input('opening_balance', 0) > 0),
                'nullable',
                Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at'),
                new Approved(ChartOfAccount::class),
            ],
            'active_status' => ['nullable', 'boolean'],
            'branch_office_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * The opening balance is credited to Accounts Payable, so the account
     * debited for it must be another open transaction account. A service
     * supplier has to name one (typically an expense account).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('opening_balance_account_id') || $validator->errors()->has('opening_balance_account_id')) {
                return;
            }

            $this->validateEntryAccount($validator, 'opening_balance_account_id', (int) $this->input('opening_balance_account_id'), [LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER]);

            $payable = resolve(LedgerPostingService::class)->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER);

            if ($payable && $payable->id === (int) $this->input('opening_balance_account_id')) {
                $validator->errors()->add('opening_balance_account_id', __('The opening balance account cannot be Accounts Payable.'));
            }
        });
    }
}
