<?php

namespace App\Modules\System;

use App\Http\Requests\OutcomeReasonFormRequest;
use App\Models\OutcomeReason;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class OutcomeReasonController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = OutcomeReason::select([
                'id',
                'name',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.outcome-reasons.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.outcome-reasons.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Outcome Reasons')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Outcome Reasons');
            }else{
                $this->viewData['pageTitle'] = __('Outcome Reasons');
            }

            return $this->view('outcome-reasons.index',$this->viewData);
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
            'text'=> __('Outcome Reasons'),
            'url'=> route('system.outcome-reasons.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Outcome Reasons'),
        ];

        $this->viewData['pageTitle'] = __('Create Outcome Reasons');

        return $this->view('outcome-reasons.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutcomeReasonFormRequest $request){
        $requestData = $request->all();

        $insertData = OutcomeReason::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.outcome-reasons.index')
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
    public function show(OutcomeReason $outcome_reason,Request $request){
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(OutcomeReason $outcome_reason,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Outcome Reasons'),
            'url'=> route('system.outcome-reasons.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $outcome_reason->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Outcome Reasons');
        $this->viewData['result'] = $outcome_reason;

        return $this->view('outcome-reasons.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(OutcomeReasonFormRequest $request, OutcomeReason $outcome_reason)
    {

        $requestData = $request->all();

        $updateData = $outcome_reason->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.outcome-reasons.index')
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
    public function destroy(OutcomeReason $outcome_reason)
    {
        $message = __('Outcome Reason deleted successfully');
        if(!empty($outcome_reason->pay()))
        $outcome_reason->pay()->delete();

        $outcome_reason->delete();

        return $this->response(true,200,$message);
    }

}
