<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodFormRequest extends FormRequest
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
                    'name' => 'required|unique:payment_methods,name',
                    'field_name' => 'required|array',
                    'field_name.*' => 'required',
                    'field_value' => 'required|array',
                    'field_value.*' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'name' => 'required|unique:payment_methods,name,'.$id,
                        'field_name' => 'required|array',
                        'field_name.*' => 'required',
                        'field_value' => 'required|array',
                        'field_value.*' => 'required',
                    ];
                }
            default:break;
        }

    }
}
