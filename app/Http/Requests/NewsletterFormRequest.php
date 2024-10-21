<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterFormRequest extends FormRequest
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
            case 'POST':{
                return [
                    'email'     =>  'required|string|email|unique:newsletters,email',
                ];
            }
            case 'PUT':
            case 'PATCH':{
                return [
                    'email'     =>  'required|string|email|unique:newsletters,email,'.$id,
                ];
            }

            default:break;
        }

    }
}
