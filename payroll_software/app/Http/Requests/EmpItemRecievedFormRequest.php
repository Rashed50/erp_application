<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpItemRecievedFormRequest extends FormRequest
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
      

     * @return array
     */
    public function rules()
    { 
       // dd($this->all());
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
                        'emp_id' => 'required',
                        'item_code_auto_id' => 'required',
                        'quantity' => 'required',
                        'store_id' => 'required',
                        'item_brand_id' => ' required',
                        'item_det_unit' => 'required',
                        'recieved_date' => 'required'                        
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
            'emp_auto_id' => 'Please Input Item Received Employee ',
            'item_code_auto_id' => 'Please Search Received Item ',
            'quantity' => 'Please Input Item Quantity',
            'store_id' => 'Please Select Store Name',
            'item_brand_id' => 'Please Select Item Brand Name',
            'item_det_unit' => 'Please Select Item Unit',
            'recieved_date' => 'Please Select Date'
        ];
    }
}


 // $table->id('item_received_auto_id');
        // $table->integer('emp_auto_id');
        // $table->integer('item_auto_id');
        // $table->float('approved_qty',5,2);
        // $table->float('received_qty',5,2);
        // $table->string('model_no')->nullable();
        // $table->string('serial_no')->nullable();
        // $table->integer('store_id');
        // $table->integer('brand_id');
        // $table->integer('item_unit');             
        // $table->date('received_date')->nullable();
        // $table->date('approved_date')->nullable();
        // $table->integer('approved_by')->nullable();
        // $table->integer('update_by')->nullable();
        // $table->integer('insert_by');