<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAdvanceResource extends JsonResource
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
            'department' => $this->whenLoaded('employee', fn () => $this->employee?->department?->name),
            'designation' => $this->whenLoaded('employee', fn () => $this->employee?->designation?->name),
            'advance_date' => $this->advance_date?->toDateString(),
            'amount' => (float) $this->amount,
            'installment_count' => $this->installment_count,
            'installment_amount' => (float) $this->installment_amount,
            'deduction_start_month' => $this->deduction_start_month?->format('Y-m'),
            'recovered_amount' => (float) $this->recovered_amount,
            'outstanding_amount' => $this->outstandingAmount(),
            'purpose' => $this->purpose,
            'remarks' => $this->remarks,
            'status' => $this->status,
            'recoveries' => EmployeeAdvanceRecoveryResource::collection($this->whenLoaded('recoveries')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
