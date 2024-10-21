<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ParameterFormRequest extends FormRequest
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
                $validation =  [
                    'property_type_id'  =>  'required|int|exists:property_types,id',
                    'column_name'       =>  'required|string|unique:parameters,column_name',
                    'name_ar'           =>  'required|string',
                    'name_en'           =>  'required|string',
                    'type'              =>  'required|string|in:text,textarea,number,select,multi_select,radio,checkbox',
                    'default_value'     =>  'nullable|string',
                    'required'          =>  'required|in:yes,no',
                    'show_in_request'   =>  'required|in:yes,no',
                    'show_in_property'   =>  'required|in:yes,no',
                    'position'          =>  'required|int'
                ];

                switch ($this->type){
                    case 'select':
                    case 'multi_select':
                    case 'radio':
                    case 'checkbox':
                        $validation['multi_request']        = 'required|in:yes,no';
                        $validation['options']              = 'required|array';
                        $validation['options.value.*']      = 'required|string';
                        $validation['options.name_ar.*']    = 'required|string';
                        $validation['options.name_en.*']    = 'required|string';
                        break;

                    case 'number':
                        $validation['between_request']    = 'required|in:yes,no';
                        break;
                }

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation =  [
                    'name_ar'         =>  'required|string',
                    'name_en'         =>  'required|string',
                    'type'            =>  'required|string|in:text,textarea,number,select,multi_select,radio,checkbox',
                    'default_value'   =>  'nullable|string',
                    'required'        =>  'required|in:yes,no',
                    'show_in_request' =>  'required|in:yes,no',
                    'show_in_property' =>  'required|in:yes,no',
                    'position'        =>  'required|int'
                ];

                switch ($this->type){
                    case 'select':
                    case 'multi_select':
                    case 'radio':
                    case 'checkbox':
                        $validation['multi_request']        = 'required|in:yes,no';
                        $validation['options']              = 'required|array';
                        $validation['options.value.*']      = 'required|string';
                        $validation['options.name_ar.*']    = 'required|string';
                        $validation['options.name_en.*']    = 'required|string';
                        break;

                    case 'number':
                        $validation['between_request']    = 'required|in:yes,no';
                        break;
                }

                return $validation;
            }
            default:break;
        }

    }
}
