<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacilityCompaniesFormRequest extends FormRequest
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
                // Facility Companies Validation
                $validation = [
                    'name' => 'required|string',
                    'area_ids.*' => 'required|int|exists:areas,id',
                    'area_ids' => 'required|array',
                    'due_id' => [
                        'required',
                        'int',
                        Rule::exists('dues','id')
                    ],
                    'company_pay_id' => 'nullable|int',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                // Facility Companies Validation
                $validation = [
                    'name' => 'required|string',
                    'area_ids.*' => 'required|int|exists:areas,id',
                    'area_ids' => 'required|array',
                    'due_id' => [
                        'required',
                        'int',
                        Rule::exists('dues','id')
                    ],
                    'company_pay_id' => 'nullable|int',
                ];

                return $validation;

            }
            default:break;
        }

    }
}
