<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestFormRequest extends FormRequest
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
                // Property Validation
                $validation = [
                    'property_id' => 'required|int|exists:properties,id',
                    'status'=> 'required|string|in:new,pendding,accept,reject,cancel',
                    'renter_id' => [
                        'required',
                        'int',
                        Rule::exists('clients','id')/*->where(function ($query) {
                            $query->where('type','client');
                        })*/
                    ],

                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                // Property Validation
                $validation = [
                    'property_id' => 'required|int|exists:properties,id',
                    'status'=> 'required|string|in:new,pendding,accept,reject,cancel',
                    'renter_id' => [
                        'required',
                        'int',
                        Rule::exists('clients','id')/*->where(function ($query) {
                            $query->where('type','client');
                        })*/
                    ],
                ];

                return $validation;

            }
            default:break;
        }

    }
}
