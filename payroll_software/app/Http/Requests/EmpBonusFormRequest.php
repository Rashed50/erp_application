<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpBonusFormRequest extends FormRequest
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
                        'emp_auto_id' => 'required',
                        'bonus_type' => 'integer | required',
                        'month' => 'integer | required',
                        'year' => 'integer | required',
                        'bonus_amount' => 'integer | required',
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
            'emp_auto_id.required' => 'Please Select Employee',
            'bonus_type.required' => 'Please Select Bonus Type',
            'month.required' => 'Select Bonus Month',
            'year.required' => 'Select Bonus Year',
            'bonus_amount.required' => 'Please Input Bonus Amount',
        ];
    }
}
