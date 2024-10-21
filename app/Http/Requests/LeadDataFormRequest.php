<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class LeadDataFormRequest extends FormRequest
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
            'mobile.unique'=> __('The mobile is already exist :name',['name'=>getClientByMobile($this->mobile)])
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
            case 'POST':
          {
                $validation = [
                    'name'         => 'required|string',
                    //'mobile'       => 'required|numeric',
                    'mobile'        =>  'required|numeric|unique:lead_data,mobile',
                    'email'        =>  'nullable|string|email',
                    'description'  => 'nullable|string',
                    'project_name'  => 'nullable|string',
                    'campaign_name'  => 'nullable|string',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    //'lead_status_id' =>  'required|int|exists:lead_status,id',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
        {
            $validation = [
                'name'         => 'required|string',
                //'mobile'       => 'required|numeric',
                'mobile'        =>  'required|numeric|unique:lead_data,mobile,'.$id,
                'email'        =>  'nullable|string|email',
                'description'  => 'nullable|string',
                'project_name'  => 'nullable|string',
                'campaign_name'  => 'nullable|string',
                'data_source_id' => 'required|int|exists:data_sources,id',
                //'lead_status_id' =>  'required|int|exists:lead_status,id',
            ];

            return $validation;

        }

            default:break;
        }

    }


}
