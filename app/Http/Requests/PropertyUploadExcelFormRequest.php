<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyUploadExcelFormRequest extends FormRequest
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

                // Property Validation
                $validation = [
                    'excel_file'=> 'required|file|mimes:xls,xlsx',
                    'ignore_first_row'=> 'required|in:yes,no',
                    // Client Type
                    'client_type'     =>  'required|string|in:client,investor',
                    'client_name'      =>  'required|string',
                    'client_mobile'      =>  'required|string',
                    'property_model_id' => 'nullable|int|exists:property_model,id',


                    //'property_type_id' => 'required|int|exists:property_types,id',//
                    'property_type' => 'required|string',//new


                    //'purpose_id' => 'required|int|exists:purposes,id',//
                    'purpose_id'=> 'required|string',//new

                    'price'=> 'required|string',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'area_id' => 'required|int|exists:areas,id',

                    'building_number'=> 'nullable|string',
                    'flat_number'=> 'nullable|string',

                    'property_status_id' =>  'required|int|exists:property_status,id',
                    'name'=> 'nullable|string',
                    'description'=> 'nullable|string',
                    'remarks'=> 'nullable|string',

                    //'payment_type'=> 'required|string|in:cash,installment,cash_installment',//
                    'payment_type'=> 'required|string',//new

                    'price'=> 'required|string',
                    'currency'=> 'required|string|in:EGP,USD',
                    'negotiable'=> 'required|string|in:yes,no',
                    'space'=> 'required|string',
                    'address'=> 'required|string',
                    'sales_id' => [
                        'required',
                        'int',
                        Rule::exists('staff','id')->where(function ($query) {
                            $query->whereIn('permission_group_id',explode(',',setting('sales_group')));
                        })
                    ],
                ];


                if($this->client_type == 'investor'){
                    $validation['client_investor_type'] = 'required|string|in:individual,company,broker';
                    if(in_array($this->client_investor_type,['company','broker'])){
                        $validation['client_company_name'] = 'required|string';
                    }
                }

                if(in_array($this->payment_type,['installment','cash_installment'])){
                    $validation['deposit'] = 'required|string';
                    $validation['years_of_installment'] = 'required|string';
                }

                // Parameters Validation

//                $parametersData = Parameter::where('property_type_id',$this->property_type_id)
//                    ->get([
//                        'column_name',
//                        'type',
//                        'options',
//                        'required'
//                    ]);
//                if($parametersData->isNotEmpty()){
//                    foreach ($parametersData as $key => $value){
//                        $ruleData      = [];
//                       // $ruleDataArray = [];
//
//                        $ruleData[]= 'nullable';
//                        $ruleData[]= 'string';
//
//                        // Required
////                        if($value->required == 'yes'){
////                            $ruleData[]= 'required';
////                        }else{
////                            $ruleData[]= 'nullable';
////                        }
////                        switch ($value->type){
////
////                            case 'text':
////                            case 'textarea':
////                            case 'number':
////                                $ruleData[]= 'string';
////                                break;
////
////                            case 'select':
////                            case 'radio':
////                                $ruleData[]= 'string';
////                                $ruleData[]= 'in:'.implode(',',array_column($value->options,'value'));
////                                break;
////
////                            case 'multi_select':
////                            case 'checkbox':
////                                $ruleData[]= 'array';
////                                $ruleDataArray[] = 'string';
////                                $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
////                                break;
////                        }
//
//                        $validation['p_'.$value->column_name] = implode('|',$ruleData);
//
////                        if(!empty($ruleDataArray)){
////                            $validation['p_'.$value->column_name.'.*'] = implode('|',$ruleDataArray);
////                        }
//                    }
//                }


                if(in_array($this->property_status_id,explode(',','archive_property_status'))){
                    $validation['hold_until'] = 'required|date_format:"Y-m-d"';
                }


                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {
                    return [];
                }
            default:break;
        }

    }
}
