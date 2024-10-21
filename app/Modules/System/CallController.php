<?php

namespace App\Modules\System;

use App\Http\Requests\CallFormRequest;
use App\Models\Call;
use App\Models\CallPurpose;
use App\Models\CallStatus;
use App\Models\Client;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class CallController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Call::select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'created_by_staff_id',
                'created_at'
            ])->orderByDesc('id');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData,'DATE(calls.created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('calls.id',$request->id);
            }

            if($request->client_id){
                $eloquentData->where('calls.client_id',$request->client_id);
            }

            if($request->call_purpose_id){
                $eloquentData->where('calls.call_purpose_id',$request->call_purpose_id);
            }

            if($request->call_status_id){
                $eloquentData->where('calls.call_status_id',$request->call_status_id);
            }

            if($request->type){
                $eloquentData->where('calls.type',$request->type);
            }

            if($request->description){
                $eloquentData->where('calls.description','LIKE','%'.$request->description.'%');
            }

            if($request->sign_type){
                $eloquentData->where('calls.description','LIKE',$request->sign_type);
            }

            if($request->sign_id){
                $eloquentData->where('calls.sign_id',$request->sign_id);
            }

            if($request->parent_id){
                $eloquentData->where('calls.parent_id',$request->parent_id);
            }

            if($request->created_by_staff_id){
                $eloquentData->where('calls.created_by_staff_id',$request->created_by_staff_id);
            }

            if(!staffCan('call-manage-all')){
                $eloquentData->where('calls.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a href="'.route('system.'.$data->client->type.'.show',$data->client->id).'" target="_blank">'.$data->client->Fullname.'</a>' : '--';
                })
                ->addColumn('call_purpose_id',function($data){
                    return $data->call_purpose ? '<b style="color: '.$data->call_purpose->color.'">'.$data->call_purpose->{'name_'.\App::getLocale()}.'</b>' : '--';
                })
                ->addColumn('call_status_id',function($data){
                    return $data->call_status ? '<b style="color: '.$data->call_status->color.'">'.$data->call_status->{'name_'.\App::getLocale()}.'</b>' : '--';
                })
                ->addColumn('type',function($data){
                    return __(strtoupper($data->type));
                })
                ->addColumn('created_by_staff_id', function($data){
                    return $data->staff ? '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>' : '--';
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
                                <a class="dropdown-item" href="javascript:showCall('.$data->id.');"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }else{

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Calls')
            ];

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Client'),
                __('Action'),
                __('Status'),
                __('Type'),
                __('Created By'),
                __('Created At'),
                __('Action')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Calls');
            }else{
                $this->viewData['pageTitle'] = __('Calls');
            }

            $this->viewData['purposes'] = CallPurpose::get();
            $this->viewData['status']   = CallStatus::get();

            $this->viewData['client_info'] = null;
            if($request->client_id){
                $this->viewData['client_info'] = Client::findOrFail($request->client_id);
            }

            $this->viewData['sign_id'] = null;
            if($request->sign_id){
                $this->viewData['sign_id'] = $request->sign_id;
            }

            $this->viewData['sign_type'] = null;
            if($request->sign_type){
                $this->viewData['sign_type'] = $request->sign_type;
            }


            return $this->view('call.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Call Action'),
            'url'=> route('system.call-purpose.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Call Action'),
        ];

        $this->viewData['pageTitle'] = __('Create Call Action');

        return $this->view('call-purpose.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CallFormRequest $request){
        $requestData = [
            'call_purpose_id'       => $request->call_purpose_id,
            'call_status_id'        => $request->call_status_id,
            'type'                  => $request->type,
            'description'           => $request->description,
            'created_by_staff_id'   => Auth::id()
        ];

        if($request->call_id){
            $parentCall = Call::findOrFail($request->call_id);
            $requestData['client_id'] = $parentCall->client_id;
            $requestData['parent_id'] = $parentCall->id;
        }else{
            $requestData['client_id'] = $request->client_id;
        }


        if($request->sign_type && $request->sign_id){
            switch ($request->sign_type){
                case 'property':
                    $check = App\Models\Property::find($request->sign_id);
                    $modal = 'App\Models\Property';
                    break;
                case 'request':
                    $check = App\Models\Request::find($request->sign_id);
                    $modal = 'App\Models\Request';
                    break;
                case 'client':
                    $check = App\Models\Client::find($request->sign_id);
                    $modal = 'App\Models\Client';
                    break;
                case 'leads':
                    $check = App\Models\ImporterData::find($request->sign_id);
                    $modal = 'App\Models\ImporterData';
                    break;
            }

            if(!$check){
                return $this->response(
                    false,
                    11001,
                    __('Unable to check :name',['name'=>$request->sign_type])
                );
            }

            $requestData['sign_type'] = $modal;
            $requestData['sign_id'] = $request->sign_id;

        }else{
            $requestData['sign_type']   = null;
            $requestData['sign_id']     = null;
        }

        $insertData = Call::create($requestData);
        if($insertData){

            if($request->remind_me == 'yes'){
                Reminder::create([
                    'staff_id'=> Auth::id(),
                    'sign_type'=> 'App\Models\Call',
                    'sign_id'=> $insertData->id,
                    'date_time'=> $request->remind_me_on,
                    'comment'=> $request->description
                ]);
            }

//            if($requestData['sign_type'] == 'App\Models\ImporterData'){
//                App\Models\ImporterData::find($request->sign_id)->update([
//                    'last_call_purpose_id' => $request->call_purpose_id,
//                    'last_call_status_id' => $request->call_status_id,
//                    'last_call_description' => $request->description
//                ]);
//
//                if(!empty(setting('request_notify_managers_groups_ids'))) {
//                    $managersGroupsIds = explode(',', setting('request_notify_managers_groups_ids'));
//
//                    $managersToNotify = array_column(  // notify Management group  only
//                        App\Models\Staff::whereIn('permission_group_id',$managersGroupsIds)->get(['id'])->toArray(),
//                        'id'
//                    );
//
//                    notifyStaff(   // notify  managers
//                        [
//                            'type' => 'staff',
//                            'ids' => $managersToNotify
//                        ],
//                        __('Leads Notification'),
//                        __('New Call Added To Lead Data'),
//                        route('system.lead-data.show', $request->sign_id)
//                    );
//
//                }
//            }

            // ----log
            //save_log(__('Create Call'),'App\Models\Call');
            // ----log

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'id'=> $insertData->id
                ]
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not add the data')
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Call $call){

        if(!staffCan('call-manage-all') && $call->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        $this->viewData['purposes'] = CallPurpose::get();
        $this->viewData['status'] = CallStatus::get();

        $this->viewData['result'] = $call;
        return $this->view('call.show',$this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(CallPurpose $call_purpose,Request $request){
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(CallPurposeFormRequest $request, CallPurpose $call_purpose)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Call $call,Request $request)
    {
        if(!staffCan('call-manage-all') && $call->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        $message = __('Call deleted successfully');
        $call->delete();
        return $this->response(true,200,$message);
    }

}