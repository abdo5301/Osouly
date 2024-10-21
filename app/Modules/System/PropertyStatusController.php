<?php

namespace App\Modules\System;

use App\Http\Requests\PropertyStatusFormRequest;
use App\Models\PropertyStatus;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class PropertyStatusController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PropertyStatus::select([
                'id',
                'name_ar',
                'name_en',
                'created_at',
                'created_by_staff_id'
            ])
                ->with('staff');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name_ar','{{$name_ar}}')
                ->addColumn('name_en','{{$name_en}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('created_by_staff_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property-status.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property-status.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Created At'),
                __('Created By'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Property Status')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Property Status');
            }else{
                $this->viewData['pageTitle'] = __('Property Status');
            }

            return $this->view('property-status.index',$this->viewData);
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
            'text'=> __('Property Status'),
            'url'=> route('system.property-status.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Property Status'),
        ];

        $this->viewData['pageTitle'] = __('Create Property Status');

        return $this->view('property-status.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyStatusFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();

        $insertData = PropertyStatus::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property-status.index')
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
    public function show(PropertyStatus $property_status,Request $request){
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyStatus $property_status,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property Status'),
            'url'=> route('system.property-status.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $property_status->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Property Status');
        $this->viewData['result'] = $property_status;

        return $this->view('property-status.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyStatusFormRequest $request, PropertyStatus $property_status)
    {

        $requestData = $request->all();

        $updateData = $property_status->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.property-status.index')
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
    public function destroy(PropertyStatus $property_status,Request $request)
    {
        $message = __('Property Status deleted successfully');
        $property_status->delete();
        return $this->response(true,200,$message);
    }

}
