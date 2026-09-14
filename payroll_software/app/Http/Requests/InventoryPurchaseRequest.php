<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryPurchaseRequest extends FormRequest
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
                    'store_id' => 'integer|required',
                    'invoice_no' => 'string|required',
                    'invoice_date' => 'date|required',
                    'received_date' => 'date|required',
                    'purchase_by' => 'string|required',
                    'purchase_from' => 'string|nullable',
                    'chalan_no' => 'string|nullable',
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

    public function messages()
    {
        return [
            'invoice_no.required' => 'Enter Items Invoice Number',
            'invoice_date.required' => 'Enter Items Invoice Date',
            'received_date.required' => 'Enter Items Received Date',
            'purchase_by.required' => 'Please Enter Purchase By Name',
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

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
