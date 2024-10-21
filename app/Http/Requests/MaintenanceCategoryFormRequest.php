<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class MaintenanceCategoryFormRequest extends FormRequest
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
                    'parent_id'     =>  'nullable|int|exists:maintenance_categories,id',
                    'name_ar'     =>  'required|string',
                    'name_en'      =>  'required|string'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'parent_id'     =>  'nullable|int|exists:maintenance_categories,id',
                    'name_ar'     =>  'required|string',
                    'name_en'      =>  'required|string'
                ];
            }
            default:break;
        }

    }
}
