<?php

namespace App\Http\Requests\Hr;

use App\Http\Requests\Concerns\ValidatesWorkRecord;
use App\Models\Employee;
use App\Models\EmployeeWork;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeWorkRequest extends FormRequest
{
    use ValidatesWorkRecord;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employee-works.create`
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
            'salary_month' => ['required', 'date_format:Y-m'],
            ...$this->workRules(),
        ];
    }

    /**
     * One work record per employee and month.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $month = CarbonImmutable::createFromFormat('Y-m-d', $this->input('salary_month').'-01')->startOfDay();
            $employee = Employee::find($this->input('employee_id'));

            $duplicate = EmployeeWork::query()
                ->where('employee_id', $this->input('employee_id'))
                ->whereDate('salary_month', $month->toDateString())
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('salary_month', 'A work record for this employee and month already exists.');

                return;
            }

            $this->validateWorkRecord($validator, $employee, $month);
        });
    }
}
