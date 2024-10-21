<?php

namespace App\Modules\System;

use App\Http\Requests\SmsFormRequest;
use App\Models\Sms;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class SmsController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Sms::select([
                'id',
                'client_id',
                'content',
                'status',
                'created_at'
            ])->orderBy('id', 'DESC');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id', function($data){
                    return $data->client ? $data->client->fullname.'<br>('.__(ucfirst($data->client->type)).' )': '--';
                })
                ->addColumn('content', function($data){
                    return $data->content ? $data->content : '--';
                })
                ->addColumn('status', function($data){
                    if($data->status == 'error'){
                        return  '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__(ucfirst($data->status)).'</span>';
                    }
                    if ($data->status == 'pending'){
                        return  '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst($data->status)).'</span>';
                    }
                    return  '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst($data->status)).'</span>';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.sms.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                            
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Client'),
                __('Content'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('SMS')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted SMS');
            }else{
                $this->viewData['pageTitle'] = __('SMS');
            }

            return $this->view('sms.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('SMS'),
            'url'=> route('system.sms.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Send SMS'),
        ];

        $this->viewData['pageTitle'] = __('Send SMS');

        return $this->view('sms.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SmsFormRequest $request){

        $body = $request->sms_content;
        $sms_sent_counter = 0;
        $sms_failed_counter = 0;
        if($request->send_to == 'all'){
            $all_clients = App\Models\Client::where('status','active')->get();
            foreach ($all_clients as $client){
                $to = $client->mobile;
                if(!empty($to) && !empty($body)){
                    $send =  send_sms($to, $body);
                    if($send){
                        $sms_sent_counter ++;
                    }else{
                        $sms_failed_counter ++;
                    }
                    sleep(5);
                }
            }
        }else{
            foreach ($request->client_id as $k_id => $v_id){
                $client  = App\Models\Client::find($v_id);
                $to = $client->mobile;
                if(!empty($to) && !empty($body)){
                   $send =  send_sms($to, $body);
                   if($send){
                       $sms_sent_counter ++;
                   }else{
                       $sms_failed_counter ++;
                   }
                    sleep(5);
                }

            }
        }

        if($sms_sent_counter > 0){
            return $this->response(
                true,
                200,
                __('Sms (:sent) Sent Successfully And (:failed) Failed',['sent'=>$sms_sent_counter,'failed'=>$sms_failed_counter])
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not send any sms')
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider,Request $request){

        abort(404);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider,Request $request){

        // Main View Vars

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(SliderFormRequest $request, Slider $slider)
    {

        abort(404);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = __('Sms deleted successfully');
        $sms = Sms::find($id);
        if(!$sms){
            abort(404);
        }
        $sms->delete();

        return $this->response(true,200,$message);
    }



}