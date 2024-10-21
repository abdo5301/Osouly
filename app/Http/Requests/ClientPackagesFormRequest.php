<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientPackagesFormRequest extends FormRequest
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
                $validation = [
                    'service_id' => 'int|required|exists:services,id',
                    'client_id' => 'int|required|exists:clients,id',
                    'transaction_id' => 'int|nullable|exists:transactions,id',
                    'date_from'      =>  'date_format:"Y-m-d"|required',
                    'date_to'      =>  'date_format:"Y-m-d"|required',
                   // 'service_count' => 'numeric|nullable',
                    'status' => 'string|required|in:pendding,active,in-active,cancel,expired',
                    //'count_per_day' => 'numeric|nullable',
                ];

                if(isset($this->package_id) && empty($this->package_id)){
                    $validation['package_id'] = 'int|required|exists:services,id';
                }

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {
                    $validation = [
                        'service_id' => 'int|required|exists:services,id',
                        'client_id' => 'int|required|exists:clients,id',
                        'transaction_id' => 'int|nullable|exists:transactions,id',
                        'date_from'      =>  'date_format:"Y-m-d"|required',
                        'date_to'      =>  'date_format:"Y-m-d"|required',
                        //'service_count' => 'numeric|nullable',
                        'status' => 'string|required|in:pendding,active,in-active,cancel,expired',
                        //'count_per_day' => 'numeric|nullable',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
