<?php

namespace App\Http\Requests\Hr;

use App\Http\Requests\Concerns\ValidatesEmployeeAdvance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeAdvanceRequest extends FormRequest
{
    use ValidatesEmployeeAdvance;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employee-advances.create`
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
            'employee_id' => ['required', 'integer', Rule::exists('employee_info', 'id')->withoutTrashed()],
            ...$this->advanceRules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(fn (Validator $validator) => $this->validateAdvance($validator));
    }
}
