<?php

namespace App\Http\Requests\ChartOfAccount;

use App\Models\ChartOfAccount;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChartOfAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:ledger-accounts.create`
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
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50', Rule::unique('chart_of_accounts', 'account_number')],
            // Omitted for a child account, which inherits its parent's type.
            'account_type_id' => ['required_without:parent_id', 'nullable', Rule::exists('account_types', 'id')],
            'parent_id' => ['nullable', Rule::exists('chart_of_accounts', 'id')->whereNull('deleted_at')],
            'opening_date' => ['nullable', 'date'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'is_transaction' => ['nullable', 'boolean'],
            'active_status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * A child account must hang under an open group account of its own type.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->hasAny(['parent_id', 'account_type_id']) || ! $this->filled('parent_id')) {
                return;
            }

            $parent = ChartOfAccount::find($this->input('parent_id'));

            if ($parent->is_transaction) {
                $validator->errors()->add('parent_id', 'A transaction account cannot have child accounts.');
            }

            if ($parent->is_closed) {
                $validator->errors()->add('parent_id', 'A closed account cannot have child accounts.');
            }

            if ($this->filled('account_type_id') && (int) $this->input('account_type_id') !== $parent->account_type_id) {
                $validator->errors()->add('account_type_id', 'A child account must have the same type as its parent.');
            }
        });
    }
}
