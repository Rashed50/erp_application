<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $money = collect([
            'working_days', 'present_days', 'absent_days', 'paid_leave_days', 'unpaid_leave_days',
            'overtime_hours', 'overtime_rate', 'basic_salary', 'house_rent', 'medical_allowance',
            'transport_allowance', 'food_allowance', 'other_allowance', 'total_allowance', 'overtime_amount',
            'bonus', 'other_addition', 'gross_salary', 'per_day_rate', 'absence_deduction',
            'unpaid_leave_deduction', 'other_deduction', 'advance_deduction', 'total_deduction', 'net_salary', 'paid_amount',
        ])->mapWithKeys(fn (string $column) => [$column => (float) $this->{$column}])->all();

        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'salary_detail_id' => $this->salary_detail_id,
            'emp_work_id' => $this->emp_work_id,
            'salary_month' => $this->salary_month?->format('Y-m'),
            'employee_code' => $this->employee?->employee_code,
            'employee_name' => $this->employee?->name,
            'department' => $this->employee?->department?->name,
            'designation' => $this->employee?->designation?->name,
            'days_in_month' => $this->days_in_month,
            'employed_days' => $this->employed_days,
            ...$money,
            'deduction_basis' => $this->deduction_basis,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'generated_by' => $this->generated_by,
            'generated_by_name' => $this->whenLoaded('generator', fn () => $this->generator?->name),
            'generated_at' => $this->generated_at,
            'approved_by' => $this->approved_by,
            'approved_by_name' => $this->whenLoaded('approver', fn () => $this->approver?->name),
            'approved_at' => $this->approved_at,
            'paid_by' => $this->paid_by,
            'paid_at' => $this->paid_at,
            'due_amount' => $this->dueAmount(),
            'payments' => SalaryPaymentResource::collection($this->whenLoaded('payments')),
            'cancelled_by' => $this->cancelled_by,
            'cancelled_at' => $this->cancelled_at,
            'cancel_reason' => $this->cancel_reason,
        ];
    }
}
