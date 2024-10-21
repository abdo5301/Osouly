<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractFormRequest extends FormRequest
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
        $id = $this->segment(3);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST': {
                // Contract Validation
                $validation = [
                    'property_id' => 'int|required|exists:properties,id',
                    'renter_id' => 'int|required|exists:clients,id',
                    'contract_template_id' => 'int|required|exists:contract_templates,id',
                    'date_from' => 'date_format:"Y-m-d"|required',
                    'date_to' => 'date_format:"Y-m-d"|required',
                    'price' => 'numeric|required',
                    'contract_type' => 'string|required|in:year,month,day',
                    'insurance_price' => 'numeric|required',
                    'deposit_rent' => 'numeric|required',
                    'status' => 'string|required|in:pendding,active,cancel',
                    'pay_from'      =>  'required|date_format:"Y-m-d"',
                    'pay_to'      =>  'required|date_format:"Y-m-d"',
                    'increase_value'      =>  'required|numeric',
                    'increase_percentage'      =>  'required|numeric',
                    'increase_from'      =>  'required|date_format:"Y-m-d"',
                    'pay_every'      =>  'required|int',
                    'pay_at' => 'string|required|in:start,end',
                    'calendar' => 'string|required|in:m,h',
                    'limit_to_pay'      =>  'required|int',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {
                    // Contract Validation
                    $validation = [
                        'property_id' => 'int|required|exists:properties,id',
                        'renter_id' => 'int|required|exists:clients,id',
                        //'contract_template_id' => 'int|required|exists:contract_templates,id',
                        'date_from' => 'date_format:"Y-m-d"|required',
                        'date_to' => 'date_format:"Y-m-d"|required',
                        'price' => 'numeric|required',
                        'contract_type' => 'string|required|in:year,month,day',
                        'insurance_price' => 'numeric|required',
                        'deposit_rent' => 'numeric|required',
                        'status' => 'string|required|in:pendding,active,cancel',
                        'pay_from'      =>  'required|date_format:"Y-m-d"',
                        'pay_to'      =>  'required|date_format:"Y-m-d"',
                        'increase_value'      =>  'required|numeric',
                        'increase_percentage'      =>  'required|numeric',
                        'increase_from'      =>  'required|date_format:"Y-m-d"',
                        'pay_every'      =>  'required|int',
                        'pay_at' => 'string|required|in:start,end',
                        'calendar' => 'string|required|in:m,h',
                        'limit_to_pay'      =>  'required|int',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
