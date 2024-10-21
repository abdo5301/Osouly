<?php

namespace App\Modules\System;

use App\Http\Requests\AreaTypeFormRequest;
use App\Libs\AreasData;
use App\Models\AreaType;
use App\Models\Parameter;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Http\Requests\PropertyTypeFormRequest;
use Form;
use Auth;
use App;

class AreatypesController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = AreaType::select([
                'id',
                'name_'.App::getLocale().' as name',
                //'parent_id',
                //'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name',function($data){
                    return implode(' -> ',AreasData::getAreaTypesUp($data->id,App::getLocale()));
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.area.index',['area_type'=>$data->id]).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.area-type.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.area-type.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
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
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Area Types')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Area Types');
            }else{
                $this->viewData['pageTitle'] = __('Area Types');
            }

            return $this->view('area-type.index',$this->viewData);
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
            'text'=> __('Area Types'),
            'url'=> route('system.area-type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Area Type'),
        ];

        $this->viewData['pageTitle'] = __('Create Area Type');

        return $this->view('area-type.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaTypeFormRequest $request){
        $requestData = $request->all();

        $insertData = AreaType::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.area-type.index')
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

    public function show(AreaType $area_type,Request $request){
        abort(404);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(AreaType $area_type,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Area Types'),
            'url'=> route('system.area-type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $area_type->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Area Type');
        $this->viewData['result'] = $area_type;

        return $this->view('area-type.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(AreaTypeFormRequest $request, AreaType $area_type)
    {

        $requestData = $request->all();

        $updateData = $area_type->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.area-type.index')
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
    public function destroy(AreaType $area_type,Request $request)
    {
        $message = __('Area Type deleted successfully');
        $area_type->delete();
        return $this->response(true,200,$message);
    }

}
