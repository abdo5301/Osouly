<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyFormRequest extends FormRequest
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
        ///$floor_string_array =;
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
                    'key'=> 'required|string',
                    'importer_data_id'=> [
                        'nullable',
                        'int',
                        Rule::exists('importer_data','id')->where(function ($query) {
                            $query->whereNull('property_id');
                        })
                    ],
                    'property_type_id' => 'required|int|exists:property_types,id',
                    'purpose_id' => 'required|int|exists:purposes,id',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'area_id' => 'required|int|exists:areas,id',
                    'building_number'=> 'nullable|string',
                    'flat_number'=> 'nullable|string',
                    'building_type'=> 'required|string|in:villa,tower',
                    'area_type'=> 'required|string|in:hayi,markaz,qasm',
                    'title'=> 'nullable|string',
                    'floor'=>'nullable|array',
                    'floor.*'=> 'nullable|in:basement,ground,'.implode(',',range(1,100)),
                    'features'=>'nullable|array',
                    'features.*'=> 'nullable|exists:property_features,id',
                    'description'=> 'nullable|string',
                    'contract_type'=> 'required|string|in:year,month,day',
                    'contract_period'=> 'nullable|numeric',
                    'insurance_price'=> 'nullable|numeric',
                    'deposit_rent'=> 'nullable|numeric',
                    'price'=> 'required|numeric',
                    'space'=> 'required|numeric',
                    'address'=> 'nullable|string',
                    'street_name'=> 'nullable|string',
                    'country_id' => 'required|int|exists:areas,id',
                    'government_id' => 'required|int|exists:areas,id',
                    'city_id' => 'required|int|exists:areas,id',
                    'mogawra' => 'nullable|int',
                    'room_number' => 'nullable|int',
                    'bathroom_number' => 'nullable|int',
                    'meta_key'=> 'nullable|string',
                    'meta_description'=> 'nullable|string',
                    'mobile'=> 'nullable|numeric',
                    'latitude'=> 'nullable|numeric',
                    'longitude'=> 'nullable|numeric',
                    'video_url'=> 'nullable|string|url',

                ];

                if(!$this->importer_data_id){
                    $validation['owner_id'] = [
                        'required',
                        'int',
                        Rule::exists('clients','id')
                    ];//'required|int|exists:clients,id';
                }


                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {

                // Property Validation
                $validation = [
                    'key'=> 'required|string',
                    'property_type_id' => 'required|int|exists:property_types,id',
                    'purpose_id' => 'required|int|exists:purposes,id',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'owner_id' => [
                        'required',
                        'int',
                        Rule::exists('clients','id')
                    ],
                    'area_id' => 'required|int|exists:areas,id',
                    'building_number'=> 'nullable|string',
                    'flat_number'=> 'nullable|string',
                    'building_type'=> 'required|string|in:villa,tower',
                    'area_type'=> 'required|string|in:hayi,markaz,qasm',
                    'floor'=>'nullable|array',
                    'floor.*'=> 'nullable|in:basement,ground,'.implode(',',range(1,100)),
                    'title'=> 'nullable|string',
                    'features'=>'nullable|array',
                    'features.*'=> 'nullable|exists:property_features,id',
                    'description'=> 'nullable|string',
                    'contract_type'=> 'nullable|string|in:year,month,day',
                    'contract_period'=> 'nullable|numeric',
                    'insurance_price'=> 'nullable|numeric',
                    'deposit_rent'=> 'nullable|numeric',
                    'price'=> 'required|numeric',
                    'space'=> 'required|numeric',
                    'address'=> 'nullable|string',
                    'street_name'=> 'nullable|string',
                    'country_id' => 'required|int|exists:areas,id',
                    'government_id' => 'required|int|exists:areas,id',
                    'city_id' => 'required|int|exists:areas,id',
                    'mogawra' => 'nullable|int',
                    'room_number' => 'nullable|int',
                    'bathroom_number' => 'nullable|int',
                    'meta_key'=> 'nullable|string',
                    'meta_description'=> 'nullable|string',
                    'mobile'=> 'nullable|numeric',
                    'latitude'=> 'nullable|numeric',
                    'longitude'=> 'nullable|numeric',
                    'video_url'=> 'nullable|string|url',

                ];

                return $validation;


            }
            default:break;
        }

    }
}
