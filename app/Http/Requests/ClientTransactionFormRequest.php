<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientTransactionFormRequest extends FormRequest
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
                    'transaction_id' => 'int|required|exists:transactions,id',
                    'client_id' => 'int|required|exists:clients,id',
                    'amount' => 'numeric|required',
                    'type' => 'string|required|in:in,out',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {
                    $validation = [
                        'transaction_id' => 'int|required|exists:transactions,id',
                        'client_id' => 'int|required|exists:clients,id',
                        'amount' => 'numeric|required',
                        'type' => 'string|required|in:in,out',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
