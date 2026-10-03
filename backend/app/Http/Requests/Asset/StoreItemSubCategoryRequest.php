<?php

namespace App\Http\Requests\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemSubCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:item-sub-categories.create`
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
            'icatg_id' => ['required', 'integer', Rule::exists('item_categories', 'icatg_id')->where('status', true)],
            'iscatg_name' => [
                'required', 'string', 'max:50',
                Rule::unique('item_sub_categories', 'iscatg_name')->where('icatg_id', $this->integer('icatg_id')),
            ],
            'iscatg_code' => ['required', 'string', 'max:10', Rule::unique('item_sub_categories', 'iscatg_code')],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'icatg_id.exists' => __('The selected category is invalid or inactive.'),
        ];
    }
}
