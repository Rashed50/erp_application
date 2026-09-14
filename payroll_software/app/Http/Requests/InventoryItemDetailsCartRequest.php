<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryItemDetailsCartRequest extends FormRequest
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
                    'itype_id' => 'integer|required',
                    'icatg_id' => 'integer|required',
                    'iscatg_id' => 'integer|required',
                    'item_deta_code' => 'integer|required',
                    // 'item_det_unit' => 'integer|required',
                    'serial_no' => 'string|nullable',
                    'model_no' => 'string|nullable',
                    'quantity' => 'numeric|required',
                    // 'item_company_id' => 'integer|required',
                    'item_brand_id' => 'integer|required',
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
            'itype_id.required' => 'Please Select Any Item Type',
            'icatg_id.required' => 'Please Select Any Category Name',
            'iscatg_id.required' => 'Please Enter Any Subcategory Name',
            'item_deta_code.required' => 'Please Select Any Item Name With Code',
            // 'item_company_id.required' => 'Please Select Any Company Name',
            'item_brand_id.required' => 'Please Select Any Brand Name',
            // 'item_det_unit.required' => 'Please Select Any Item Unit type',
            'quantity.required' => 'Please Enter Items Quantity Amount',
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
