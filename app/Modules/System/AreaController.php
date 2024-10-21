<?php

namespace App\Modules\System;

use App\Http\Requests\AreaFormRequest;
use App\Models\Area;
use App\Models\AreaType;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class AreaController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Area::select([
                'id',
                'name_'.App::getLocale().' as name',
                //'has_property_model',
                //'olx_id',
               // 'aqarmap_id',
               // 'propertyfinder_id',
            ]);

            $area_type = $request->area_type;
            if($area_type){
                $areaTypeData = AreaType::findOrFail($area_type);
                $this->viewData['area_type'] = $areaTypeData;
                $eloquentData->where('area_type_id',$area_type);
            }elseif($area_id = $request->area_id){
                $area = Area::findOrFail($area_id);
                $this->viewData['area'] = $area;
                $eloquentData->where('parent_id',$area_id);
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name',function($data){
                    $result = $data->name;

                    if($data->olx_id){
                        $result.= ' <span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('OLX').'</span>';
                    }

                    if($data->aqarmap_id){
                        $result.= ' <span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__('Aqarmap').'</span>';
                    }

                    if($data->propertyfinder_id){
                        $result.= ' <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('Property Finder').'</span>';
                    }

                    return $result;
                })
//                ->addColumn('has_property_model',function($data){
//                    if($data->has_property_model){
//                        return '<img src="https://cdn3.iconfinder.com/data/icons/simple-web-navigation/165/tick-512.png" width="30px" >';
//                    }else{
//                        return '<img src="https://cdn3.iconfinder.com/data/icons/shadcon/512/circle_-_corss-512.png" width="30px" >';
//                    }
//                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.area.index',['area_id'=>$data->id]).'"><i class="la la-edit"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.area.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="'.route('system.area.create',['area_id'=>$data->id]).'"><i class="la la-edit"></i> '.__('Add Sub Area').'</a>
                                <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.area.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
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
              //  __('Has P.M'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Areas')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Areas');
            }else{
                $this->viewData['pageTitle'] = __('Areas');
            }

            return $this->view('area.index',$this->viewData);
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
            'text'=> __('Area'),
            'url'=> route('system.area.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Area'),
        ];


        if($area_id = $request->area_id){
            $area = Area::findOrFail($area_id);
            $areaType = AreaType::where('id','>',$area->area_type_id)->first();
            if(!$areaType){
                abort(404);
            }

            $this->viewData['area_type'] = $areaType;
            $this->viewData['area'] = $area;
        }else{
            $area_type_id = $request->area_type_id;
            $areaType = AreaType::orderBy('id','ASC')->first();
            if(!$areaType || $area_type_id != $areaType->id){
                abort(404);
            }
            $this->viewData['area_type'] = $areaType;
        }


        $this->viewData['pageTitle'] = __('Create Area');

        return $this->view('area.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaFormRequest $request){





        if($area_id = $request->area_id){
            $area = Area::findOrFail($area_id);
            $areaType = AreaType::where('id','>',$area->area_type_id)->first();
            if(!$areaType){
                abort(404);
            }
            $request['parent_id']    = $area->id;
            $request['area_type_id'] = $areaType->id;
        }else{
            $area_type_id = $request->area_type_id;
            $areaType = AreaType::orderBy('id','ASC')->first();
            if(!$areaType || $area_type_id != $areaType->id){
                abort(404);
            }
        }

        if(Area::create($request->all())){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.area.index')
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
    public function edit(Area $area){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Area'),
            'url'=> route('system.area.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $area->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Area');
        $this->viewData['result'] = $area;

        return $this->view('area.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(AreaFormRequest $request, Area $area)
    {

        $requestData = $request->all();


        $updateData = $area->update($request->only([
            'name_ar',
            'name_en',
            'latitude',
            'longitude',
            'has_property_model',
            'olx_id',
            'aqarmap_id',
            'propertyfinder_id'
        ]));
        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.area.index')
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
    public function destroy(Area $area)
    {
        $message = __('Area deleted successfully');
        $area->delete();
        return $this->response(true,200,$message);
    }

}
