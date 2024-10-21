<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaTypeFormRequest extends FormRequest
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
                    'name_ar'=> 'required|string',
                    'name_en'=> 'required|string',
                ];

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'=> 'required|string',
                    'name_en'=> 'required|string',
                ];

                return $validation;
            }
            default:break;
        }

    }
}
