<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyDuesFormRequest extends FormRequest
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
                // Property Dues Validation
                $validation = [
                    'property_id' => 'int|required|exists:properties,id',
                    'due_id' => 'int|required|exists:dues,id',
                    'value' => 'numeric|required',
                    'name'      =>  'required|string',
                    'type' => 'string|required|in:owner,renter',
                    'duration' => 'string|required|in:year,month,day,one_time',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {
                    // Property Dues Validation
                    $validation = [
                        'property_id' => 'int|required|exists:properties,id',
                        'due_id' => 'int|required|exists:dues,id',
                        'value' => 'numeric|required',
                        'name'      =>  'required|string',
                        'type' => 'string|required|in:owner,renter',
                        'duration' => 'string|required|in:year,month,day,one_time',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
