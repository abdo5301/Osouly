<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CallFormRequest extends FormRequest
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

                if($this->call_id){
                    $validation = [
                        'call_id'           => 'required|int|exists:calls,id',
                        'call_purpose_id'   => 'required|int|exists:call_purpose,id',
                        'call_status_id'    => 'required|int|exists:call_status,id',
                        'type'              => 'required|in:in,out',
                        'description'       => 'required|string'
                    ];
                }else{
                    $validation = [
                        'client_id'         => 'required|int|exists:clients,id',
                        'call_purpose_id'   => 'required|int|exists:call_purpose,id',
                        'call_status_id'    => 'required|int|exists:call_status,id',
                        'type'              => 'required|in:in,out',
                        'description'       => 'required|string'
                    ];
                }

                if($this->remind_me == 'yes'){
                    $validation['remind_me_on'] = 'required|date_format:"Y-m-d H:i:s"|after:"'.date('Y-m-d H:i:s').'"';

                }

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_ar'  =>  'required|string',
                    'name_en'  =>  'required|string',
                    'color'    =>  'required|string'
                ];
            }
            default:break;
        }

    }
}
