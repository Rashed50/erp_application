<?php

namespace App\Http\Requests\Hr;

use App\Http\Requests\Concerns\ValidatesEmployeeAdvance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeAdvanceRequest extends FormRequest
{
    use ValidatesEmployeeAdvance;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employee-advances.update`
     * middleware on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The employee of an advance is fixed; only its terms change.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->advanceRules();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(fn (Validator $validator) => $this->validateAdvance($validator));
    }
}
