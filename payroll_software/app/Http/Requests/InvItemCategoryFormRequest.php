<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvItemCategoryFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
       // return $this->user()->can('view settings');
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
                        'icatg_name' => 'string | required',
                        'itype_id' => 'integer | required'
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
            'icatg_name.string' => 'Category Name Must be String',
            'icatg_name.required' => 'Please Enter Category Name',
        ];
    }
}
