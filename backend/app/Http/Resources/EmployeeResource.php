<?php

namespace App\Http\Resources;

use App\Models\EmployeeBankDetail;
use App\Models\EmployeeDetail;
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
            'division_id' => $this->division_id,
            'district_id' => $this->district_id,
            'upazila_id' => $this->upazila_id,
            'division' => $this->whenLoaded('division', fn () => $this->division?->name),
            'district' => $this->whenLoaded('district', fn () => $this->district?->name),
            'upazila' => $this->whenLoaded('upazila', fn () => $this->upazila?->name),
            'joining_date' => $this->joining_date?->toDateString(),
            'last_working_date' => $this->last_working_date?->toDateString(),
            'department_id' => $this->department_id,
            'designation_id' => $this->designation_id,
            'department' => $this->whenLoaded('department', fn () => $this->department?->name),
            'designation' => $this->whenLoaded('designation', fn () => $this->designation?->name),
            'employment_type' => $this->employment_type,
            'status' => $this->status,
            'detail' => $this->whenLoaded('detail', fn () => $this->detail ? [
                ...$this->detail->only(EmployeeDetail::FIELDS),
                'permanent_division' => $this->detail->relationLoaded('permanentDivision') ? $this->detail->permanentDivision?->name : null,
                'permanent_district' => $this->detail->relationLoaded('permanentDistrict') ? $this->detail->permanentDistrict?->name : null,
                'permanent_upazila' => $this->detail->relationLoaded('permanentUpazila') ? $this->detail->permanentUpazila?->name : null,
            ] : null),
            'bank' => $this->whenLoaded('bankDetail', fn () => $this->bankDetail?->only(EmployeeBankDetail::FIELDS)),
            'files' => EmployeeFileResource::collection($this->whenLoaded('files')),
            'salary_details' => SalaryDetailResource::collection($this->whenLoaded('salaryDetails')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
