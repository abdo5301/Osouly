<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ImporterFormRequest extends FormRequest
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
                    'connector'         => 'required|string|in:OLX,Aqarmap',
                    'area_id'           => 'required|int|exists:areas,id',
                    'property_type_id'  => 'required|int|exists:property_types,id',
                    'purpose_id'        => 'required|int|exists:purposes,id',
                    'space_from'        => 'nullable|int',
                    'space_to'          => 'nullable|int',
                    'price_from'        => 'nullable|int',
                    'price_to'          => 'nullable|int',
                    'page_start'        => 'required|int',
                    'page_end'          => 'required|int'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'connector'         => 'required|string|in:OLX,Aqarmap',
                    'area_id'           => 'required|int|exists:areas,id',
                    'property_type_id'  => 'required|int|exists:property_types,id',
                    'purpose_id'        => 'required|int|exists:purposes,id',
                    'space_from'        => 'nullable|int',
                    'space_to'          => 'nullable|int',
                    'price_from'        => 'nullable|int',
                    'price_to'          => 'nullable|int',
                    'page_start'        => 'required|int',
                    'page_end'          => 'required|int'
                ];
            }
            default:break;
        }

    }
}
