<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsFormRequest extends FormRequest
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
                // Slider Validation
                $validation = [
                    'send_to'=>'required|string|in:all,some',
                    'sms_content'=> 'required|string',
                ];

                if($this->send_to == 'some'){
                    $validation['client_id'] = [
                        'required',
                        'array'
                    ];
                    $validation['client_id.*'] = [
                        'nullable',
                        'int',
                        Rule::exists('clients','id')
                    ];
                }

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {

                }
            default:break;
        }

    }
}
