<?php

namespace App\Http\Requests\IncomeExpense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeExpenseAccountRequest extends FormRequest
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
     * `type` is intentionally not editable here — changing the type of an
     * account after entries have posted against it would misclassify their
     * accounting history.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'active_status' => ['sometimes', 'boolean'],
        ];
    }
}
