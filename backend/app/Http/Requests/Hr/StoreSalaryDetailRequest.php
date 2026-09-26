<?php

namespace App\Http\Requests\Hr;

use App\Models\SalaryDetail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalaryDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:salary-configs.*`
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
        $amount = ['required', 'numeric', 'min:0', 'max:9999999999'];

        return [
            'effective_date' => [
                'required',
                'date',
                Rule::unique('salary_details', 'effective_date')
                    ->where('employee_id', $this->employeeId())
                    ->ignore($this->route('salary_detail')),
            ],
            'basic_salary' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'house_rent' => $amount,
            'medical_allowance' => $amount,
            'transport_allowance' => $amount,
            'food_allowance' => $amount,
            'other_allowance' => $amount,
            'overtime_rate' => $amount,
            'other_deduction' => $amount,
            'deduction_basis' => ['required', Rule::in(SalaryDetail::DEDUCTION_BASES)],
            'status' => ['required', 'boolean'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'effective_date.unique' => 'This employee already has a salary configuration with this effective date.',
        ];
    }

    protected function employeeId(): ?int
    {
        return $this->route('employee')?->id;
    }
}
