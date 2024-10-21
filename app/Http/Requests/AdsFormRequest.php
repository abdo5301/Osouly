<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdsFormRequest extends FormRequest
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
                    'title_ar'=> 'nullable|string',
                    'title_en'=> 'nullable|string',
                    'date_from' => 'required|date_format:"Y-m-d"',
                    'date_to' => 'required|date_format:"Y-m-d"',
                    'image' =>  'mimes:jpeg,jpg,png,gif|required|max:10000',
                    'url'=> 'required|string|url',
                    'page'=> 'required|string|in:home,property_list,property',
                    'type'=> 'required|string|in:google,osouly',
                ];

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
                {
                    // Ads Validation
                    $validation = [
                        'title_ar'=> 'nullable|string',
                        'title_en'=> 'nullable|string',
                        'date_from' => 'required|date_format:"Y-m-d"',
                        'date_to' => 'required|date_format:"Y-m-d"',
                        'image' =>  'mimes:jpeg,jpg,png,gif|max:10000',
                        'url'=> 'required|string|url',
                        'page'=> 'required|string|in:home,property_list,property',
                        'type'=> 'required|string|in:google,osouly',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
