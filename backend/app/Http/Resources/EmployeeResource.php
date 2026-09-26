<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_code' => $this->employee_code,
            'name' => $this->name,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'joining_date' => $this->joining_date?->toDateString(),
            'last_working_date' => $this->last_working_date?->toDateString(),
            'department' => $this->department,
            'designation' => $this->designation,
            'employment_type' => $this->employment_type,
            'status' => $this->status,
            'detail' => $this->whenLoaded('detail', fn () => $this->detail?->only([
                'national_id', 'passport_no', 'marital_status', 'blood_group', 'permanent_address',
                'payment_method', 'bank_name', 'bank_branch', 'bank_account_name', 'bank_account_no',
                'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone', 'notes',
            ])),
            'files' => EmployeeFileResource::collection($this->whenLoaded('files')),
            'salary_details' => SalaryDetailResource::collection($this->whenLoaded('salaryDetails')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
