<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceFormRequest extends FormRequest
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
                    'property_id' => 'int|nullable|exists:properties,id',
                    'client_id' => 'int|required|exists:clients,id',
                    'installment_id' => 'int|nullable|exists:installments,id',
                    'property_due_id' => 'int|nullable|exists:property_dues,id',
                    'amount' => 'numeric|required',
                    'date'      =>  'date_format:"Y-m-d"|required',
                    'status' => 'string|required|in:unpaid,paid',
                    'notes' => 'string|nullable',
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
                {
                    $validation = [
                        'property_id' => 'int|nullable|exists:properties,id',
                        'client_id' => 'int|required|exists:clients,id',
                        'installment_id' => 'int|nullable|exists:installments,id',
                        'property_due_id' => 'int|nullable|exists:property_dues,id',
                        'amount' => 'numeric|required',
                        'date'      =>  'date_format:"Y-m-d"|required',
                        'status' => 'string|required|in:unpaid,paid',
                        'notes' => 'string|nullable',
                    ];

                    return $validation;

                }
            default:break;
        }

    }
}
