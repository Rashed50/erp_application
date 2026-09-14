<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvItemNamesFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->method();
        if (null !== $this->get('_method', null)) {
            $method = $this->get('_method');
        }
        $this->offsetUnset('_method');
        switch ($method) {

            case 'GET':
                break;
            case 'POST':
                    $this->rules = [
                        'itype_id' => 'integer | required',
                        'icatg_id' => 'integer | required',
                        'iscatg_id' => 'integer | required',
                        'item_deta_name' => 'string | required',
                    ];
                    break;

            case 'PUT':
            case 'PATCH':
                break;
            default:
                break;
        }

        return $this->rules;
    }

    public function messages()
    {
        return [
            'itype_id.required' => 'Please Select Any Item Type',
            'icatg_id.required' => 'Please Select Any Category Name',
            'iscatg_id.required' => 'Please Enter Any Subcategory Name',
            'item_deta_name.string' => 'Item Details Name Must be String',
        ];
    }
}
