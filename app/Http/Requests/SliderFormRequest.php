<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SliderFormRequest extends FormRequest
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
                // Slider Validation
                $validation = [
                    'title_ar'=> 'nullable|string',
                    'title_en'=> 'nullable|string',
                    'description_ar'=> 'nullable|string',
                    'description_en'=> 'nullable|string',
                    'image' =>  'mimes:jpeg,jpg,png|required|max:10000',
                    'video_url'=> 'nullable|string|url',
                    'url'=> 'nullable|string|url',
                    'type'=> 'required|string|in:main_web,main_mob,main_mob,board,renter,owner',
                    'sort'=> 'nullable|int',
                    'status'=> 'required|string|in:active,in-active',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {
                    // Services Validation
                    $validation = [
                        'title_ar'=> 'nullable|string',
                        'title_en'=> 'nullable|string',
                        'description_ar'=> 'nullable|string',
                        'description_en'=> 'nullable|string',
                        'image' =>  'mimes:jpeg,jpg,png|max:10000',
                        'video_url'=> 'nullable|string|url',
                        'url'=> 'nullable|string|url',
                        'type'=> 'required|string|in:main_web,main_mob,main_mob,board,renter,owner',
                        'sort'=> 'nullable|int',
                        'status'=> 'required|string|in:active,in-active',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
