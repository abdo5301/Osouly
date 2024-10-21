<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CampaignFormRequest extends FormRequest
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
            case 'POST':
            case 'PUT':
            case 'PATCH':{
                return [
                    'campaign_title'     =>  'required|string',
                    'campaign_content'     =>  'required|string',
                ];
            }

            default:break;
        }

    }
}
