<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class PayFormRequest extends FormRequest
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
                return [
                        'reason_id'=>  'required|int',
                        'client_id'=>  'nullable|int|'.Rule::exists('clients','id'),
                        'staff_id'=>  'nullable|int|'.Rule::exists('staff','id'),
                        'payment_method_id'=>  'nullable|int|'.Rule::exists('payment_methods','id'),
                        'locker_id'=>  'required|int|'.Rule::exists('lockers','id'),
                        'price'    => 'required|numeric',
                        'note'     => 'nullable|string',
                        'date'     =>  'required|date_format:"Y-m-d"',
                ];
            }
            case 'PUT':
            case 'PATCH':
                {
                return [
                        'reason_id'=>  'required|int',
                        'client_id'=>  'nullable|int|'.Rule::exists('clients','id'),
                        'staff_id'=>  'nullable|int|'.Rule::exists('staff','id'),
                        'payment_method_id'=>  'nullable|int|'.Rule::exists('payment_methods','id'),
                        'locker_id'=>  'required|int|'.Rule::exists('lockers','id'),
                        'price'    => 'required|numeric',
                        'note'     => 'nullable|string',
                        'date'     =>  'required|date_format:"Y-m-d"',
                    ];
                }
            default:break;
        }

    }
}
