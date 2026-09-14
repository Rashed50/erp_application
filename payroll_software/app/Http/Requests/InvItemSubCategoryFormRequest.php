<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvItemSubCategoryFormRequest extends FormRequest
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
                        'iscatg_name' => 'string | required',
                        'itype_id' => 'integer | required',
                        'icatg_id' => 'integer | required'
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
            'iscatg_name.string' => 'Subcategory Name Must be String',
            'iscatg_name.required' => 'Please Enter Subcategory Name',
            'itype_id.required' => 'Please Select Item Type',
            'icatg_id.required' => 'Please Select Category Name',
        ];
    }
}
