<?php

namespace App\Http\Requests\Asset;

use App\Models\ItemName;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemNameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the `permission:item-names.update`
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
        /** @var ItemName $item */
        $item = $this->route('item_name');

        return [
            // An item may keep its current category / sub category even after they were deactivated.
            'icatg_id' => [
                'required', 'integer',
                Rule::exists('item_categories', 'icatg_id')->where(function (Builder $query) use ($item) {
                    $query->where('status', true)->orWhere('icatg_id', $item->icatg_id);
                }),
            ],
            'iscatg_id' => [
                'required', 'integer',
                Rule::exists('item_sub_categories', 'iscatg_id')
                    ->where('icatg_id', $this->integer('icatg_id'))
                    ->where(function (Builder $query) use ($item) {
                        $query->where('status', true)->orWhere('iscatg_id', $item->iscatg_id);
                    }),
            ],
            'itype_id' => ['required', 'integer', Rule::in(array_keys(ItemName::TYPES))],
            'item_name' => [
                'required', 'string', 'max:50',
                Rule::unique('item_names', 'item_name')
                    ->where('iscatg_id', $this->integer('iscatg_id'))
                    ->ignore($item),
            ],
            'item_title' => ['required', 'string', 'max:50'],
            'item_code' => ['required', 'string', 'max:10', Rule::unique('item_names', 'item_code')->ignore($item)],
            'item_status' => ['sometimes', 'boolean'],
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
