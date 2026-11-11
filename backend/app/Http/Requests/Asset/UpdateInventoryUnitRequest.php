<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:inventory-units.update`
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
        $unit = $this->route('inventory_unit');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('inventory_units', 'name')->ignore($unit)],
            'short_name' => ['required', 'string', 'max:20', Rule::unique('inventory_units', 'short_name')->ignore($unit)],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
