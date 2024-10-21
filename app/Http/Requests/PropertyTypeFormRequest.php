<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PropertyTypeFormRequest extends FormRequest
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
                    'name_ar'     =>  'required|string',
                    'image'        =>  'mimes:jpeg,jpg,png,svg,ico|max:10000',
                    'name_en'      =>  'required|string'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_ar'     =>  'required|string',
                    'image'        =>  'mimes:jpeg,jpg,png,svg,ico|max:10000',
                    'name_en'      =>  'required|string'
                ];
            }
            default:break;
        }

    }
}
