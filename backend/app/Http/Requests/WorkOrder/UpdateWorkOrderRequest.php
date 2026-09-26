<?php

namespace App\Http\Requests\WorkOrder;

use App\Models\WorkOrder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:work-orders.update`
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
            'customer_id' => ['sometimes', 'required', Rule::exists('customers', 'id')->withoutTrashed()],
            'work_title' => ['sometimes', 'required', 'string', 'max:255'],
            'work_order_no' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('work_orders', 'work_order_no')->ignore($this->route('work_order'))],
            'issue_date' => ['sometimes', 'required', 'date'],
            'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'retention_percent' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'deliver_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['sometimes', 'required', Rule::in(WorkOrder::STATUSES)],
        ];
    }
}
