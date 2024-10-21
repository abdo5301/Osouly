<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DueFormRequest extends FormRequest
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
                // dues Validation
                $validation = [
                    'name'=> 'required|string',
                    'description'=> 'nullable|string',
                    'image' =>  'mimes:jpeg,jpg,png|max:10000',
                    'type'=> 'required|string|in:government,service',
                    'status'=> 'required|string|in:active,in-active',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {
                    // dues Validation
                    $validation = [
                        'name'=> 'required|string',
                        'description'=> 'nullable|string',
                        'image' =>  'mimes:jpeg,jpg,png|max:10000',
                        'type'=> 'required|string|in:government,service',
                        'status'=> 'required|string|in:active,in-active',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
