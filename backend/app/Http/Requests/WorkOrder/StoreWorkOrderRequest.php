<?php

namespace App\Http\Requests\WorkOrder;

use App\Models\WorkOrder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:work-orders.create`
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
            'customer_id' => ['required', Rule::exists('customers', 'id')->withoutTrashed()],
            'work_title' => ['required', 'string', 'max:255'],
            'work_order_no' => ['required', 'string', 'max:255', Rule::unique('work_orders', 'work_order_no')],
            'issue_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'retention_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'deliver_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['nullable', Rule::in(WorkOrder::STATUSES)],
        ];
    }
}
