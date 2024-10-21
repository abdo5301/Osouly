<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaFormRequest extends FormRequest
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
                    'area_type_id'=> 'required|int|exists:area_types,id',
                    'name_ar'=> 'required|string',
                    'name_en'=> 'required|string',
                    'latitude'=> 'nullable|numeric',
                    'longitude'=> 'nullable|numeric',
                    'has_property_model'=> 'nullable',
                    'olx_id'=> 'nullable|string',
                    'aqarmap_id'=> 'nullable|string',
                    'propertyfinder_id'=> 'nullable|string'
                ];

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'=> 'required|string',
                    'name_en'=> 'required|string',
                    'latitude'=> 'nullable|numeric',
                    'longitude'=> 'nullable|numeric',
                    'has_property_model'=> 'nullable',
                    'olx_id'=> 'nullable|string',
                    'aqarmap_id'=> 'nullable|string',
                    'propertyfinder_id'=> 'nullable|string'
                ];

                return $validation;
            }
            default:break;
        }

    }
}
