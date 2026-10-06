<?php

namespace App\Http\Requests\Asset;

use App\Models\ItemName;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemNameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:item-names.create`
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
            'iscatg_id' => [
                'required', 'integer',
                Rule::exists('item_sub_categories', 'iscatg_id')
                    ->where('icatg_id', $this->integer('icatg_id'))
                    ->where('status', true),
            ],
            'itype_id' => ['required', 'integer', Rule::in(array_keys(ItemName::TYPES))],
            'item_name' => [
                'required', 'string', 'max:50',
                Rule::unique('item_names', 'item_name')->where('iscatg_id', $this->integer('iscatg_id')),
            ],
            'item_title' => ['required', 'string', 'max:50'],
            'item_code' => ['required', 'string', 'max:10', Rule::unique('item_names', 'item_code')],
            'item_status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'icatg_id.exists' => __('The selected category is invalid or inactive.'),
            'iscatg_id.exists' => __('The selected sub category is invalid, inactive or not under the selected category.'),
        ];
    }
}
