<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:item-categories.update`
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
        $category = $this->route('item_category');

        return [
            'icatg_name' => ['required', 'string', 'max:50', Rule::unique('item_categories', 'icatg_name')->ignore($category)],
            'icatg_code' => ['required', 'string', 'max:10', Rule::unique('item_categories', 'icatg_code')->ignore($category)],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
