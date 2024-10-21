<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CalenderFormRequest extends FormRequest
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
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST': {
                $validation = [
                    'date_time' => 'required|date_format:"Y-m-d H:i:s"|after:"'.date('Y-m-d H:i:s').'"',
                    'comment'   => 'required|string',
                ];

                if($this->sign_type){
                    $validation['sign_type']    = 'required|string|in:property,client,request,leads';
                    $validation['sign_id']      = 'required|numeric';
                }

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
                {
                    return [];
                }
            default:break;
        }

    }
}
