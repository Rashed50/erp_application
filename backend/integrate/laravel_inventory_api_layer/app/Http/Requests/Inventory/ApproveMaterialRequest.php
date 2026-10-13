<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ApproveMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:material_request_items,id'],
            'items.*.approved_qty' => ['required', 'numeric', 'gte:0'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
