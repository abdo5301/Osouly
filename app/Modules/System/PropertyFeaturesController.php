<?php

namespace App\Modules\System;

use App\Models\PropertyFeatures;
use Illuminate\Http\Request;
use App\Http\Requests\PropertyFeaturesFormRequest;
use Form;
use Auth;
use App;

class PropertyFeaturesController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PropertyFeatures::select([
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
                ->addColumn('name_ar','{{$name_ar}}')
                ->addColumn('name_en','{{$name_en}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property-features.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property-features.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
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
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Property Features')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Property Features');
            }else{
                $this->viewData['pageTitle'] = __('Property Features');
            }



            return $this->view('property-features.index',$this->viewData);
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
            'text'=> __('Property Features'),
            'url'=> route('system.property-features.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Property Feature'),
        ];

        $this->viewData['pageTitle'] = __('Create Property Features');

        return $this->view('property-features.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyFeaturesFormRequest $request){
        $requestData = $request->all();

        $insertData = PropertyFeatures::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property-features.index')
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

    public function show(PropertyFeatures $property_features,Request $request){
        abort(404);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request){

        $property_features = PropertyFeatures::find($id);
        if(!$property_features){
            abort(404);
        }

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property Features'),
            'url'=> route('system.property-features.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $property_features->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Property Feature');
        $this->viewData['result'] = $property_features;

        return $this->view('property-features.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyFeaturesFormRequest $request, $id)
    {
        $property_features = PropertyFeatures::find($id);
        if(!$property_features){
            abort(404);
        }

        $requestData = $request->all();

        $updateData = $property_features->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.property-features.index')
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
    public function destroy(PropertyFeatures $property_feature,Request $request)
    {
        $message = __('Property Features deleted successfully');

        $property_feature->delete();

        return $this->response(true,200,$message);
    }

}
