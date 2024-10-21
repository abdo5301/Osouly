<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageFormRequest extends FormRequest
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
                // Page Validation
                $validation = [
                    'key'=> 'required|string',
                    'title_ar'=> 'required|string',
                    'title_en'=> 'required|string',
                    'content_ar'=> 'required|string',
                    'content_en'=> 'required|string',
                    'meta_key_ar'=> 'nullable|string',
                    'meta_key_en'=> 'nullable|string',
                    'meta_description_ar'=> 'nullable|string',
                    'meta_description_en'=> 'nullable|string',
                    'video_url'=> 'nullable|string|url',
                    'sort'=> 'nullable|numeric',

                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {
                    // Page Validation
                    $validation = [
                        'key'=> 'required|string',
                        'title_ar'=> 'required|string',
                        'title_en'=> 'required|string',
                        'content_ar'=> 'required|string',
                        'content_en'=> 'required|string',
                        'meta_key_ar'=> 'nullable|string',
                        'meta_key_en'=> 'nullable|string',
                        'meta_description_ar'=> 'nullable|string',
                        'meta_description_en'=> 'nullable|string',
                        'video_url'=> 'nullable|string|url',
                        'sort'=> 'nullable|numeric',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
