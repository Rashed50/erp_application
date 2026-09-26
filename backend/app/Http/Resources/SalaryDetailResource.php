<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryDetailResource extends JsonResource
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
            'effective_date' => $this->effective_date?->toDateString(),
            'basic_salary' => (float) $this->basic_salary,
            'house_rent' => (float) $this->house_rent,
            'medical_allowance' => (float) $this->medical_allowance,
            'transport_allowance' => (float) $this->transport_allowance,
            'food_allowance' => (float) $this->food_allowance,
            'other_allowance' => (float) $this->other_allowance,
            'total_allowance' => $this->total_allowance,
            'monthly_gross' => $this->monthly_gross,
            'overtime_rate' => (float) $this->overtime_rate,
            'other_deduction' => (float) $this->other_deduction,
            'deduction_basis' => $this->deduction_basis,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'is_used_by_payroll' => $this->when(isset($this->salary_histories_count), fn () => $this->salary_histories_count > 0),
            'created_at' => $this->created_at,
        ];
    }
}
