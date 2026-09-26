<?php

namespace App\Http\Requests\Hr;

use App\Http\Requests\Concerns\ValidatesWorkRecord;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeWorkRequest extends FormRequest
{
    use ValidatesWorkRecord;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employee-works.update`
     * middleware on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The employee and month of a work record are fixed; only its figures change.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->workRules();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $work = $this->route('employee_work');

            $this->validateWorkRecord($validator, $work?->employee, $work ? CarbonImmutable::parse($work->salary_month) : null);
        });
    }
}
