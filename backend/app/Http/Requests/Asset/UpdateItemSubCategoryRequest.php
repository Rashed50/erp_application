<?php

namespace App\Http\Requests\Asset;

use App\Models\ItemSubCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemSubCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:item-sub-categories.update`
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
        /** @var ItemSubCategory $subCategory */
        $subCategory = $this->route('item_sub_category');

        return [
            // A sub category may keep its current category even after that category was deactivated.
            'icatg_id' => [
                'required', 'integer',
                Rule::exists('item_categories', 'icatg_id')->where(function (Builder $query) use ($subCategory) {
                    $query->where('status', true)->orWhere('icatg_id', $subCategory->icatg_id);
                }),
            ],
            'iscatg_name' => [
                'required', 'string', 'max:50',
                Rule::unique('item_sub_categories', 'iscatg_name')
                    ->where('icatg_id', $this->integer('icatg_id'))
                    ->ignore($subCategory),
            ],
            'iscatg_code' => ['required', 'string', 'max:10', Rule::unique('item_sub_categories', 'iscatg_code')->ignore($subCategory)],
            'status' => ['sometimes', 'boolean'],
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
