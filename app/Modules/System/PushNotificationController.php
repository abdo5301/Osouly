<?php

namespace App\Modules\System;

use App\Http\Requests\PushNotificationFormRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class PushNotificationController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Send Push Notifications'),
        ];

        $this->viewData['pageTitle'] = __('Send Push Notifications');

        return $this->view('push-notifications.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PushNotificationFormRequest $request){

        $body = $request->notify_content;
        $notify_sent_counter = 0;
        $notify_failed_counter = 0;
        if($request->send_to == 'all'){
            $all_clients = App\Models\Client::where('status','active')->get();
            foreach ($all_clients as $client){
                $to = $client->firebase_token;
                if(!empty($to) && !empty($body)){
                    $send =  push_notification($body,$to);
                    if($send){
                        $notify_sent_counter ++;
                    }else{
                        $notify_failed_counter ++;
                    }
                    sleep(5);
                }
            }
        }else if ($request->send_to == 'some'){
            $clients  = App\Models\Client::whereIn('id',$request->client_id)->where('status','active')->get();
            if(!empty($clients)){
                foreach ($clients as $client){
                    $to = $client->firebase_token;
                    if(!empty($to) && !empty($body)){
                        $send =  push_notification($body, $to);
                        if($send){
                            $notify_sent_counter ++;
                        }else{
                            $notify_failed_counter ++;
                        }
                        //  sleep(5);
                    }

                }
            }
        }else if ($request->send_to == 'area'){
            $clients  = App\Models\Client::whereIn('area_id',$request->area_id)->where('status','active')->get();
            if(!empty($clients)){
                foreach ($clients as $client){
                    $to = $client->firebase_token;
                    if(!empty($to) && !empty($body)){
                        $send =  push_notification($body, $to);
                        if($send){
                            $notify_sent_counter ++;
                        }else{
                            $notify_failed_counter ++;
                        }
                      //  sleep(5);
                    }

                }
            }
        }else if ($request->send_to == 'type'){
            $clients  = App\Models\Client::where(['status'=>'active','type'=>$request->type])->get();
            if(!empty($clients)){
                foreach ($clients as $client){
                    $to = $client->firebase_token;
                    if(!empty($to) && !empty($body)){
                        $send =  push_notification($body, $to);
                        if($send){
                            $notify_sent_counter ++;
                        }else{
                            $notify_failed_counter ++;
                        }
                      //  sleep(5);
                    }
                }
            }
        }



        if($notify_sent_counter > 0){
            return $this->response(
                true,
                200,
                __('Notifications (:sent) Sent Successfully And (:failed) Failed',['sent'=>$notify_sent_counter,'failed'=>$notify_failed_counter])
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not send any notifications')
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
        abort(404);
    }



}