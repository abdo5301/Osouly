<?php

namespace App\Modules\System;

use App\Models\Parameter;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Http\Requests\PropertyTypeFormRequest;
use Form;
use Auth;
use App;

class PropertyTypeController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PropertyType::select([
                'id',
                'image',
                'name_ar',
                'name_en',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('image', function($data){
                    return ( $data->image && is_file($data->image) ) ? '<a target="_blank" href="'.asset($data->image).'"><img style="width:70px;height: 70px;" src="'.asset($data->image).'"></a>' : '--';
                })
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
                                <a class="dropdown-item" href="'.route('system.property-type.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.property-type.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property-type.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Image'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Property Types')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Property Types');
            }else{
                $this->viewData['pageTitle'] = __('Property Types');
            }



            return $this->view('property-type.index',$this->viewData);
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
            'text'=> __('Property Types'),
            'url'=> route('system.property-type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Property Type'),
        ];

        $this->viewData['pageTitle'] = __('Create Property Type');

        return $this->view('property-type.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyTypeFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();
        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
            $requestData['image'] = $image;
        }

        $insertData = PropertyType::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property-type.index')
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

    public function show(PropertyType $property_type,Request $request){

        if($request->isDataTable){

            $eloquentData = Parameter::select([
                'id',
                'column_name',
                'name_ar',
                'name_en',
                'type',
                'created_at',
                'created_by_staff_id'
            ])
                ->where('property_type_id',$property_type->id)
                ->with('staff');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('column_name','{{$column_name}}')
                ->addColumn('type','{{$type}}')
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
                                <a class="dropdown-item" href="'.route('system.parameter.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.parameter.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Column Name'),
                __('Type'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Created At'),
                __('Created By'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Property Types'),
                'url'=> route('system.property-type.index')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> $property_type->{'name_'.App::getLocale()}
            ];


            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Property Types');
            }else{
                $this->viewData['pageTitle'] = __(':name\'s parameters',['name'=> $property_type->{'name_'.App::getLocale()}]);
            }

            $this->viewData['result'] = $property_type;


            return $this->view('property-type.show',$this->viewData);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyType $property_type,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property Types'),
            'url'=> route('system.property-type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $property_type->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Property Type');
        $this->viewData['result'] = $property_type;

        return $this->view('property-type.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyTypeFormRequest $request, PropertyType $property_type)
    {

        $requestData = $request->all();

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
            $requestData['image'] = $image;
            if(!empty($property_type->image) && is_file($property_type->image)){ // remove old image
                unlink($property_type->image);
            }
        }
      //  print_r($requestData);die;
        $updateData = $property_type->update($requestData);



        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.property-type.index')
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
    public function destroy(PropertyType $property_type,Request $request)
    {
        $message = __('Property Type deleted successfully');
        if(!empty($property_type->image) && is_file($property_type->image)){ // remove image
            unlink($property_type->image);
        }

        $property_type->delete();

        return $this->response(true,200,$message);
    }

}
