<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpAdvancePaymentRequest extends FormRequest
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
                        'emp_id' => 'integer | required',
                        'adv_purpose_id' => 'integer | required',
                        'adv_amount' => 'numeric | required',
                        'installes_month' => 'integer | required',
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
}
