<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class BanksBranchesFormRequest extends FormRequest
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
                    'file'                      => 'required|file|mimes:xls,xlsx',
                    'columns_data_bank_code'         => 'required|string|in:'.implode(',',range('A','Z')),
                    'columns_data_branch_code'         => 'required|string|in:'.implode(',',range('A','Z')),
                    'columns_data_name_ar'         => 'required|string|in:'.implode(',',range('A','Z')),
                    'columns_data_name_en'         => 'required|string|in:'.implode(',',range('A','Z')),
                    'ignore_first_row'          => 'required|string|in:yes,no',
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
