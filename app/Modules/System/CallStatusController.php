<?php

namespace App\Modules\System;

use App\Http\Requests\CallStatusFormRequest;
use App\Models\CallStatus;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class CallStatusController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = CallStatus::select([
                'id',
                'name_ar',
                'name_en',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name_ar',function($data){
                    return '<b style="color: '.$data->color.'">'.$data->name_ar.'</b>';
                })
                ->addColumn('name_en',function($data){
                    return '<b style="color: '.$data->color.'">'.$data->name_en.'</b>';
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
                                <a class="dropdown-item" href="'.route('system.call-status.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call-status.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns(['action'])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Call Purpose')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Call Status');
            }else{
                $this->viewData['pageTitle'] = __('Call Status');
            }

            return $this->view('call-status.index',$this->viewData);
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
            'text'=> __('Call Status'),
            'url'=> route('system.call-status.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Call Status'),
        ];

        $this->viewData['pageTitle'] = __('Create Call Status');

        return $this->view('call-status.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CallStatusFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();

        $insertData = CallStatus::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.call-status.index')
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
    public function show(){
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(CallStatus $call_status,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Call Status'),
            'url'=> route('system.call-status.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $call_status->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Call Status');
        $this->viewData['result'] = $call_status;

        return $this->view('call-status.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(CallStatusFormRequest $request, CallStatus $call_status)
    {

        $requestData = $request->all();

        $updateData = $call_status->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.call-status.index')
                ]
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not edit the data')
            );
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(CallStatus $call_status,Request $request)
    {
        $message = __('Call Status deleted successfully');
        $call_status->delete();
        return $this->response(true,200,$message);
    }

}