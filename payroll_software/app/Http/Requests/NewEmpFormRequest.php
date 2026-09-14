<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class NewEmpFormRequest extends FormRequest
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
        $today = Carbon::now();
        $min_age = $today->subYears(18)->format('Y-m-d');

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
                    'emp_id' => 'required | string',
                    'emp_name' => 'required | string',
                    'agency_id' => 'integer | required',
                    'sponsor_id' => 'integer | required',
                    'passport_no' => 'required | string',
                    'passport_expire_date' => 'required|date|after_or_equal:today',
                    'akama_no' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:10|min:10',
                    'akama_expire' => 'required|date|after_or_equal:today',
                    'mobile_no' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/',
                    'emp_type_id' => 'integer | required',
                    'designation_id' => 'integer | required',
                    'country_id' => 'integer | required',
                    'division_id' => 'integer | required',
                    'district_id' => 'integer | required',
                    'date_of_birth' => [
                        'required',
                        'date',
                        'before_or_equal:' . $min_age
                    ],
                    'gender' => 'required',
                    'maritus_status' => 'required',
                    'accomd_ofb_id' => 'integer | required',
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
            'emp_id.string' => 'Please Enter Employee ID',
            'emp_name.string' => 'Please Enter Employee Name',
            'agency_id.required' => 'Please select any agency name',
            'sponsor_id.required' => 'Please select any sponsor name',
            'passport_no.required' => 'Please enter employee passport number',
            'passport_expire_date.required' => 'Please select a date',
            'passport_expire_date.date' => 'Please enter a valid date',
            'passport_expire_date.after_or_equal' => 'The date must be today or later',
            'akama_no.required' => 'Please enter a valid Employee Iqama number',
            'akama_expire.required' => 'Please select a date',
            'akama_expire.date' => 'Please enter a valid date',
            'akama_expire.after_or_equal' => 'The date must be today or later',
            'mobile_no.required' => 'Please enter a valid Employee mobile number',
            'emp_type_id.required' => 'Please select any employee type',
            'designation_id.required' => 'Please select any designation name',
            'country_id.required' => 'Please select any country name',
            'division_id.required' => 'Please select any division name',
            'district_id.required' => 'Please select any division name',
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to sign up',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The selected gender is invalid.',
            'maritus_status.required' => 'The marital status field is required',
            'maritus_status.in' => 'The selected marital status is invalid',
            'accomd_ofb_id.required' => 'Please select any villa name',
        ];
    }
}
