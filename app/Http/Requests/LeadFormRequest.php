<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class LeadFormRequest extends FormRequest
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
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST': {
                $validation = [
                    'name'                      => 'required|string',
                    'file'                      => 'required|file|mimes:xls,xlsx',
                    'columns_data_name'         => 'required|string|in:'.implode(',',range('A','Z')),
                    'columns_data_mobile'       => 'required|string|in:'.implode(',',range('A','Z')),
                    'columns_data_email'        => 'nullable|string|in:'.implode(',',range('A','Z')),
                    'columns_data_description'  => 'nullable|string|in:'.implode(',',range('A','Z')),
                    'columns_data_project_name'   => 'nullable|string|in:'.implode(',',range('A','Z')),
                    'columns_data_campaign_name'  => 'nullable|string|in:'.implode(',',range('A','Z')),
                    'ignore_first_row'          => 'required|string|in:yes,no',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    //'lead_status_id' => 'required|int|exists:lead_status,id'

                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {

                }
            default:break;
        }

    }


}
