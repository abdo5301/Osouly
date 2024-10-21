<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StaffFormRequest extends FormRequest
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
        if($this->segment(3) == 'change-password'){
            return [
                'password'      =>  'required|string|min:6|confirmed'
            ];
        }

        $id = $this->segment(3);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'firstname'             => 'required|string',
                    'lastname'              => 'required|string',
                    'email'                 => 'required|string|email|unique:staff,email',
                    'mobile'                => 'required|string|unique:staff,mobile',
                    'gender'                => 'required|string|in:male,female',
                    'birthdate'             => 'required|string|date_format:"Y-m-d"|before_or_equal:"'.date('Y-m-d').'"',
                    'national_id'           => 'required|numeric|digits:14|unique:staff,national_id',
                    'address'               => 'required|string',
                    'password'              => 'required|string|min:6|confirmed',
                    'job_title'             => 'required|string',
                    'status'                => 'required|string|in:active,in-active',
                    'permission_group_id'   => 'required|int|exists:permission_groups,id',
                    'avatar'                => 'nullable|image'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {

                return [
                    'firstname'             => 'required|string',
                    'lastname'              => 'required|string',
                    'email'                 => 'required|string|email|unique:staff,email,'.$id,
                    'mobile'                => 'required|string|unique:staff,mobile,'.$id,
                    'gender'                => 'required|string|in:male,female',
                    'birthdate'             => 'required|string|date_format:"Y-m-d"|before_or_equal:"'.date('Y-m-d').'"',
                    'national_id'           => 'required|numeric|digits:14|unique:staff,national_id,'.$id,
                    'address'               => 'required|string',
                    'password'              => 'nullable|string|min:6|confirmed',
                    'job_title'             => 'required|string',
                    'status'                => 'required|string|in:active,in-active',
                    'permission_group_id'   => 'required|int|exists:permission_groups,id',
                    'avatar'                => 'nullable|image'
                ];
            }
            default:break;
        }

    }
}
