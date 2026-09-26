<?php

namespace App\Http\Requests\Hr;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateEmployeeRequest extends StoreEmployeeRequest
{
    /**
     * Same rules as creating, but every top-level field is optional.
     *
     * Authorization is enforced by the `permission:employees.update`
     * middleware on the route.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return collect($this->employeeRules($this->route('employee')))
            ->map(fn (array $rules, string $field) => str_contains($field, '.') ? $rules : ['sometimes', ...$rules])
            ->all();
    }
}
