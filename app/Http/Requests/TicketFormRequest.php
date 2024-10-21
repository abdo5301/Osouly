<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketFormRequest extends FormRequest
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
                    //'status'=>'required|string|in:new,pending_support,pending_client,solve,close',
                    'client_id'=>  'required|int|'.Rule::exists('clients','id'),
                    'ticket_title'=> 'required|string',
                    'ticket_content'=> 'required|string',
                    'ticket_image'=> 'nullable|mimes:jpeg,jpg,png|max:20000',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
                {
                    $validation = [
                        'comment'=> 'required|string',
                        'ticket_image'=> 'nullable|mimes:jpeg,jpg,png|max:20000',
                    ];

                    return $validation;
                }
            default:break;
        }

    }
}
