<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
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

    public function messages(){
        return [
            'mobile.unique'=> __('The mobile has already been taken :name',['name'=>getClientByMobile($this->mobile)]),
        ];
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
                $validation = [
                    'type'     =>  'required|string|in:owner,renter',
                    'gender'     =>  'required|string|in:male,female',
                    'first_name'      =>  'required|string',
                    'second_name'      =>  'required|string',
                    'third_name'      =>  'required|string',
                    'last_name'      =>  'required|string',
                    'birth_date'      =>  'nullable|date_format:"Y-m-d"',
                    'id_number'      =>  'nullable|numeric|digits:14',
                    'area_id'      =>  'nullable|int',
                    'email'         =>  'nullable|string|email|unique:clients,email',
                    'phone'         =>  'nullable|numeric|unique:clients,phone',
                    'mobile'        =>  'required|numeric|unique:clients,mobile',
                    'address'        =>  'nullable|string',
                    'description'     =>  'nullable|string',
                    'status'        =>  'required|string|in:active,in-active,pending',
                    'personal_photo'                      =>  'mimes:jpeg,jpg,png|required|max:10000',
                    'card_face'                      =>  'mimes:jpeg,jpg,png|required|max:10000',
                    'card_back'                      =>  'mimes:jpeg,jpg,png|required|max:10000',
                    'passport'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'criminal_record'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'images.*' =>    'mimes:jpeg,jpg,png,gif|max:10000',
                    'bank_account_number'        =>  'nullable|numeric',
                    'bank_code'   => 'nullable|string',
                    'branch_code' => 'nullable|string',
                    'password'    => 'required|string|min:6|confirmed',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'type'     =>  'required|string|in:owner,renter',
                    'gender'     =>  'required|string|in:male,female',
                    'first_name'      =>  'required|string',
                    'second_name'      =>  'required|string',
                    'third_name'      =>  'required|string',
                    'last_name'      =>  'required|string',
                    'birth_date'      =>  'nullable|date_format:"Y-m-d"',
                    'id_number'      =>  'nullable|numeric|digits:14',
                    'area_id'      =>  'nullable|int',
                    'email'         =>  'nullable|string|email|unique:clients,email,'.$id,
                    'phone'         =>  'nullable|numeric|unique:clients,phone,'.$id,
                    'mobile'        =>  'required|numeric|unique:clients,mobile,'.$id,
                    'address'        =>  'nullable|string',
                    'description'     =>  'nullable|string',
                    'status'        =>  'required|string|in:active,in-active,pending',
                    'personal_photo'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'card_face'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'card_back'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'passport'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'criminal_record'                      =>  'mimes:jpeg,jpg,png|max:10000',
                    'images.*' =>    'mimes:jpeg,jpg,png,gif|max:10000',
                    'bank_account_number'        =>  'nullable|numeric',
                    'bank_code'   => 'nullable|string',
                    'branch_code' => 'nullable|string',
                    'password'              => 'nullable|string|min:6|confirmed',
                ];



                return $validation;
            }
            default:break;
        }

    }


}
