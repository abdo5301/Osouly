<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactFormRequest extends FormRequest
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
                // Ads Validation
                $validation = [
                    'replay'=> 'required|string',
                ];

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
                {
                    // Ads Validation
                    $validation = [
                        'replay'=> 'required|string',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
