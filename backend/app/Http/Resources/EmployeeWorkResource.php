<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeWorkResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'employee_code' => $this->whenLoaded('employee', fn () => $this->employee?->employee_code),
            'employee_name' => $this->whenLoaded('employee', fn () => $this->employee?->name),
            'department' => $this->whenLoaded('employee', fn () => $this->employee?->department),
            'designation' => $this->whenLoaded('employee', fn () => $this->employee?->designation),
            'salary_month' => $this->salary_month?->format('Y-m'),
            'working_days' => (float) $this->working_days,
            'present_days' => (float) $this->present_days,
            'absent_days' => (float) $this->absent_days,
            'paid_leave_days' => (float) $this->paid_leave_days,
            'unpaid_leave_days' => (float) $this->unpaid_leave_days,
            'overtime_hours' => (float) $this->overtime_hours,
            'bonus' => (float) $this->bonus,
            'other_addition' => (float) $this->other_addition,
            'other_deduction' => (float) $this->other_deduction,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
