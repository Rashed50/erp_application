<?php

namespace App\Http\Requests\ChartOfAccount;

use App\Models\ChartOfAccount;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChartOfAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:ledger-accounts.update`
     * middleware on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * `account_type_id` is intentionally not editable here — changing the type
     * of an account after entries have posted against it would misclassify
     * their accounting history.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var ChartOfAccount $account */
        $account = $this->route('ledger_account');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_number' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('chart_of_accounts', 'account_number')->ignore($account->id)],
            'parent_id' => ['sometimes', 'nullable', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'opening_date' => ['sometimes', 'date'],
            'is_transaction' => ['sometimes', 'boolean'],
            'active_status' => ['sometimes', 'boolean'],
            'is_closed' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Keep the hierarchy and posting rules intact when the account moves or
     * changes kind.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var ChartOfAccount $account */
            $account = $this->route('ledger_account');

            if ($account->is_predefined && $this->hasAny(['parent_id', 'is_transaction', 'is_closed'])) {
                $validator->errors()->add('name', 'A predefined account can only be renamed or activated/deactivated.');

                return;
            }

            if ($this->filled('parent_id')) {
                $parent = ChartOfAccount::find($this->input('parent_id'));

                if ($parent->id === $account->id || in_array($parent->id, $account->descendantIds(), true)) {
                    $validator->errors()->add('parent_id', 'An account cannot be moved under itself or one of its own children.');
                } elseif ($parent->is_transaction || $parent->is_closed) {
                    $validator->errors()->add('parent_id', 'The parent must be an open group account.');
                } elseif ($parent->account_type_id !== $account->account_type_id) {
                    $validator->errors()->add('parent_id', 'A child account must have the same type as its parent.');
                }
            }

            if ($this->boolean('is_transaction') && $account->children()->exists()) {
                $validator->errors()->add('is_transaction', 'An account with child accounts cannot be a transaction account.');
            }

            if ($this->has('is_transaction') && ! $this->boolean('is_transaction')
                && ($account->categoryEntries()->exists() || $account->paymentEntries()->exists())) {
                $validator->errors()->add('is_transaction', 'An account with posted entries must stay a transaction account.');
            }
        });
    }
}
