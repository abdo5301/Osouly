<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageFormRequest extends FormRequest
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
                // Packages Validation
                $validation = [
                    'key'=> 'required|string',
                    'service_id'=> 'int|required|exists:services,id',
                    'title_ar'=> 'required|string',
                    'title_en'=> 'required|string',
                    'content_ar'=> 'required|string',
                    'content_en'=> 'required|string',
                    'meta_key_ar'=> 'nullable|string',
                    'meta_key_en'=> 'nullable|string',
                    'meta_description_ar'=> 'nullable|string',
                    'meta_description_en'=> 'nullable|string',
                    'price'=> 'nullable|numeric',
                    'offer'=> 'nullable|numeric',
                    'duration'=> 'nullable|numeric',
                    'count'=> 'nullable|numeric',
                    'status'=> 'required|string|in:active,in-active',
                    'discount_type'=> 'nullable|string|in:fixed,percentage',
                    'discount_value'=> 'nullable|numeric',
                    'discount_from'  =>  'date_format:"Y-m-d"|nullable',
                    'discount_to'  =>  'date_format:"Y-m-d"|nullable',
                    //'type'=> 'nullable|string|in:manage,star,ads',
                    'type_count'=> 'nullable|numeric',
                    'properties_count'=> 'nullable|numeric',
                    'discount_code'=> 'nullable|string',
                    'discount_code_value'=>  'nullable|numeric',
                    'discount_code_from'=>   'date_format:"Y-m-d"|nullable',
                    'discount_code_to'=>  'date_format:"Y-m-d"|nullable',
                    'percentage'=> 'nullable|numeric',
                    //'subscribers_count'=> 'nullable|numeric',
                    //'unsubscribers_count'=> 'nullable|numeric',
                    //'subscribe_monthly'=> 'nullable|numeric',
                    //'subscribe_from'=>  'date_format:"Y-m-d"|nullable',
                   // 'subscribe_to'=>  'date_format:"Y-m-d"|nullable',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {
                    // Packages Validation
                    $validation = [
                        'key'=> 'required|string',
                        'service_id'=> 'int|required|exists:services,id',
                        'title_ar'=> 'required|string',
                        'title_en'=> 'required|string',
                        'content_ar'=> 'required|string',
                        'content_en'=> 'required|string',
                        'meta_key_ar'=> 'nullable|string',
                        'meta_key_en'=> 'nullable|string',
                        'meta_description_ar'=> 'nullable|string',
                        'meta_description_en'=> 'nullable|string',
                        'price'=> 'nullable|numeric',
                        'offer'=> 'nullable|numeric',
                        'duration'=> 'nullable|numeric',
                        'count'=> 'nullable|numeric',
                        'status'=> 'required|string|in:active,in-active',
                        'discount_type'=> 'nullable|string|in:fixed,percentage',
                        'discount_value'=> 'nullable|numeric',
                        'discount_from'  =>  'date_format:"Y-m-d"|nullable',
                        'discount_to'  =>  'date_format:"Y-m-d"|nullable',
                        //'type'=> 'nullable|string|in:manage,star,ads',
                        'type_count'=> 'nullable|numeric',
                        'properties_count'=> 'nullable|numeric',
                        'discount_code'=> 'nullable|string',
                        'discount_code_value'=>  'nullable|numeric',
                        'discount_code_from'=>   'date_format:"Y-m-d"|nullable',
                        'discount_code_to'=>  'date_format:"Y-m-d"|nullable',
                        'percentage'=> 'nullable|numeric',
                        //'subscribers_count'=> 'nullable|numeric',
                        //'unsubscribers_count'=> 'nullable|numeric',
                        //'subscribe_monthly'=> 'nullable|numeric',
                        //'subscribe_from'=>  'date_format:"Y-m-d"|nullable',
                        //'subscribe_to'=>  'date_format:"Y-m-d"|nullable',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
