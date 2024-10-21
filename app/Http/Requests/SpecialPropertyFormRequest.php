<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecialPropertyFormRequest extends FormRequest
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
                $validation = [
                    'property_id' => 'int|required|exists:properties,id',
                    'client_package_id' => 'int|required|exists:client_packages,id',
                    'created_by' => 'int|required|exists:clients,id',
                    'start_date'      =>  'date_format:"Y-m-d"|required',
                    'end_date'      =>  'date_format:"Y-m-d"|required',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {
                    $validation = [
                        'property_id' => 'int|required|exists:properties,id',
                        'client_package_id' => 'int|required|exists:client_packages,id',
                        'created_by' => 'int|required|exists:clients,id',
                        'start_date'      =>  'date_format:"Y-m-d"|required',
                        'end_date'      =>  'date_format:"Y-m-d"|required',
                    ];


                    return $validation;

                }
            default:break;
        }

    }
}
