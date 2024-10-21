<?php

namespace App\Modules\System;

use App\Http\Requests\CalenderFormRequest;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class CalendarController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Calendar')
        ];
        $this->viewData['pageTitle'] = __('Calendar');

        return $this->view('calendar.index',$this->viewData);
    }


    public function ajax(Request $request){
        $start  = $request->start;
        $end    = $request->end;

        $data = Reminder::whereRaw("DATE(date_time) BETWEEN '$start' AND '$end'")
            ->where('staff_id',Auth::id())
            ->get();

        if(!$data){
            return [];
        }

        $result = [];
        foreach ($data as $key => $value){

            switch ($value->sign_type){
                case 'App\Models\Property':
                    $className = 'fc-event-danger fc-event-solid-success';
                    break;

                case 'App\Models\Request':
                case 'App\Models\LeadData':
                    $className = 'fc-event-danger fc-event-solid-danger';
                    break;

                case 'App\Models\Client':
                    $className = 'fc-event-danger fc-event-solid-warning';
                    break;

                default:
                    $className = 'fc-event-danger fc-event-solid-info';
                    break;
            }

            $result[] = [
                'title'=> $value->comment,
                'start'=> $value->date_time->format('Y-m-d H:i:s'),
                'className'=> $className,
                'id'=> $value->id
            ];
        }

        return $result;
    }

    public function show(Request $request){
        $id = $request->id;

        $data = Reminder::where('id',$id)->where('staff_id',Auth::id())->first();

        if(!$data) return ['status'=> false];

        $table = '<table class="table">
                    <thead>
                    <tr>
                    <th>'.__('Key').'</th>
                    <th>'.__('Value').'</th>
                    </tr>
                    </thead>
                    
                    <tbody>
                    <tr>
                        <td>'.__('ID').'</td>
                        <td>'.$data->id.'<input type="hidden" value="'.$data->id.'" id="modal_reminder_id"></td>
                    </tr>
                    <tr>
                        <td>'.__('By').'</td>
                        <td><a href="'.route('system.staff.show',$data->staff_id).'" target="_blank">'.$data->staff->fullname.'</a></td>
                    </tr>';

        if($data->sign_type){
            switch ($data->sign_type){
                case 'App\Models\Property':
                    $url = route('system.property.show',$data->sign_id);
                    break;

                case 'App\Models\Request':
                    $url = route('system.request.show',$data->sign_id);
                    break;

                case 'App\Models\LeadData':
                    $url = route('system.lead-data.show',$data->sign_id);
                    break;

                case 'App\Models\Client':
                    $url = route('system.client.show',$data->sign_id);
                    break;

                default:
                    $url = 'javascript:void(0);';
                    break;
            }

            $table.= '<tr>
                        <td>'.__('Sign').'</td>
                        <td><a target="_blank" href="'.$url.'">'.__(last(explode('\\',$data->sign_type))).'</a></td>
                    </tr>';
        }

        $table .= '<tr>
                        <td>'.__('Date & Time').'</td>
                        <td>'.$data->date_time->format('Y-m-d h:i A').'</td>
                    </tr>
                    <tr>
                        <td>'.__('Comment').'</td>
                        <td>'.$data->comment.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Created At').'</td>
                        <td>'.$data->created_at->format('Y-m-d h:i A').'</td>
                    </tr>
                    </tbody>
                    
                    </table>';


        return [
            'status'=> true,
            'id'=> $data->id,
            'sign_type'=> $data->sign_type,
            'sign_id'=> $data->sign_id,
            'table'=> $table
        ];

    }

    public function store(CalenderFormRequest $request){
        $requestData = $request->all();
        $requestData['staff_id'] = Auth::id();

        if(!empty($requestData['sign_type'])){
            switch ($requestData['sign_type']){
                case 'property':
                    $check = App\Models\Property::find($requestData['sign_id']);
                    $modal = 'App\Models\Property';
                    break;
                case 'request':
                    $check = App\Models\Request::find($requestData['sign_id']);
                    $modal = 'App\Models\Request';
                    break;

                case 'leads':
                    $check = App\Models\LeadData::find($requestData['sign_id']);
                    $modal = 'App\Models\LeadData';
                    break;
                case 'client':
                    $check = App\Models\Client::find($requestData['sign_id']);
                    $modal = 'App\Models\Client';
                    break;
            }

            if(!$check){
                return $this->response(
                    false,
                    11001,
                    __('Unable to check :name',['name'=>$requestData['sign_type']])
                );
            }

            $requestData['sign_type'] = $modal;

        }else{
            $requestData['sign_type']   = null;
            $requestData['sign_id']     = null;
        }

        $insertData = Reminder::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully')
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not add the data')
            );
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        Reminder::where('id',$id)->delete();

        return [
            'status'=> true
        ];

    }


}
