<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ChartOfAccountStoreRequest extends FormRequest
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

        if ($this->has('_method')) {
            $method = $this->get('_method');
        }

        switch ($method) {
            case 'POST':
                return [
                    'acct_type_id' => 'required|integer',
                    'chart_of_acct_name' => 'required|string',
                    'chart_of_acct_number' => 'required|string',
                 //   'account_id' => 'nullable|string',
                    'acct_balance' => 'nullable|numeric',
                    'opening_date' => 'required|date',
                    'active_status' => 'nullable|integer',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    // Add your rules for PUT/PATCH methods if needed
                ];

            default:
                return [];
        }
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'acct_type_id.required' => 'Select Account Type Name.',
            'chart_of_acct_name.required' => 'Enter Account Name.',
            'chart_of_acct_number.required' => 'Enter Account Number.',
            'opening_date.required' => 'Please Select Account Opening Date.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()
            ->withErrors($validator)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
