<?php

namespace App\Http\Requests\Hr;

use App\Models\Employee;
use App\Models\EmployeeDetail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:employees.create`
     * middleware on the route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * `employee_code` is optional: a sequential EMP-xxxx code is assigned
     * when it is left blank.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->employeeRules();
    }

    /**
     * @return array<string, array<mixed>>
     */
    protected function employeeRules(?Employee $ignore = null): array
    {
        return [
            'employee_code' => ['nullable', 'string', 'max:50', Rule::unique('employee_info', 'employee_code')->ignore($ignore)],
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(Employee::GENDERS)],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'joining_date' => ['required', 'date'],
            'last_working_date' => ['nullable', 'date', 'after_or_equal:joining_date', 'required_if:status,Resigned,Terminated'],
            'department' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['required', Rule::in(Employee::EMPLOYMENT_TYPES)],
            'status' => ['required', Rule::in(Employee::STATUSES)],

            'detail' => ['nullable', 'array'],
            'detail.national_id' => ['nullable', 'string', 'max:50'],
            'detail.passport_no' => ['nullable', 'string', 'max:50'],
            'detail.marital_status' => ['nullable', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'detail.blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'detail.permanent_address' => ['nullable', 'string', 'max:1000'],
            'detail.payment_method' => ['nullable', Rule::in(EmployeeDetail::PAYMENT_METHODS)],
            'detail.bank_name' => ['nullable', 'required_if:detail.payment_method,Bank', 'string', 'max:255'],
            'detail.bank_branch' => ['nullable', 'string', 'max:255'],
            'detail.bank_account_name' => ['nullable', 'string', 'max:255'],
            'detail.bank_account_no' => ['nullable', 'required_if:detail.payment_method,Bank', 'string', 'max:50'],
            'detail.emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'detail.emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'detail.emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'detail.notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'detail.bank_name' => 'bank name',
            'detail.bank_account_no' => 'bank account number',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'last_working_date.required_if' => 'The last working date is required for a resigned or terminated employee.',
        ];
    }
}
