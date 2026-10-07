<?php

namespace App\Http\Requests\Hr;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdvanceRecoveryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employee-advances.recover`
     * middleware on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Cash paid back by the employee. The outstanding balance check is done
     * by the service under a row lock.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'recovery_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
