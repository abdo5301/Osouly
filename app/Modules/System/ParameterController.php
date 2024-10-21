<?php

namespace App\Modules\System;

use App\Http\Requests\ParameterFormRequest;
use App\Models\Parameter;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Http\Requests\PropertyTypeFormRequest;
use Form;
use Auth;
use App;

class ParameterController extends SystemController
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

        $propertyType = PropertyType::findOrFail($request->property_type_id);

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property Types'),
            'url'=> route('system.property-type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> $propertyType->{'name_'.App::getLocale()},
            'url'=> route('system.property-type.show',$propertyType->id)
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Parameter'),
        ];

        $this->viewData['pageTitle'] = __('Create Parameter');
        $this->viewData['property_type'] = $propertyType;

        return $this->view('parameter.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParameterFormRequest $request){

        $propertyColumns = \DB::select("SHOW COLUMNS FROM `property_parameters`");
        $lastPropertyColumns = last(array_column($propertyColumns,'Field'));

        $requestColumns = \DB::select("SHOW COLUMNS FROM `request_parameters`");
        $lastRequestColumns = last(array_column($requestColumns,'Field'));

        $requestData = [
            'property_type_id'=> $request->property_type_id,
            'column_name'=> $request->column_name,
            'name_ar'=> $request->name_ar,
            'name_en'=> $request->name_en,
            'type'=> $request->type,
            'required'=> $request->required,
            'show_in_request'=> $request->show_in_request,
            'show_in_property'=> $request->show_in_property,
            'between_request'=> $request->between_request,
            'multi_request'=> $request->multi_request,
            'position'=> $request->position,
            'created_by_staff_id'=> Auth::id()
        ];

        switch ($request->type){
            case 'select':
            case 'multi_select':
            case 'radio':
            case 'checkbox':
                $options = [];
                foreach ($request->options['value'] as $key => $value){
                    $options[] = [
                        'value'     => $value,
                        'name_ar'   => $request->options['name_ar'][$key],
                        'name_en'   => $request->options['name_en'][$key]
                    ];
                }

                $requestData['options'] = $options;

                break;

            case 'number':
                $requestData['between_request']    = $request->between_request;
                break;

        }

        $insertData = Parameter::create($requestData);
        if($insertData){

            if($request->type == 'number'){
                \DB::statement("ALTER TABLE `property_parameters` ADD `$request->column_name` INT(10) NULL DEFAULT NULL AFTER `$lastPropertyColumns`;");
                if($request->show_in_request == 'yes'){
                    if($request->between_request == 'yes'){
                        \DB::statement("ALTER TABLE `request_parameters` ADD `".$request->column_name."_from` INT(10) NULL DEFAULT NULL AFTER `$lastRequestColumns`;");
                        \DB::statement("ALTER TABLE `request_parameters` ADD `".$request->column_name."_to` INT(10) NULL DEFAULT NULL AFTER `".$request->column_name."_from`;");
                    }else{
                        \DB::statement("ALTER TABLE `request_parameters` ADD `".$request->column_name."` INT(10) NULL DEFAULT NULL AFTER `$lastRequestColumns`;");
                    }
                }
            }else{
                if($request->show_in_property == 'yes'){
                    \DB::statement("ALTER TABLE `property_parameters` ADD `$request->column_name` TEXT NULL DEFAULT NULL AFTER `$lastPropertyColumns`;");
                }
                //\DB::statement("ALTER TABLE `property_parameters` ADD `$request->column_name` TEXT NULL DEFAULT NULL AFTER `$lastPropertyColumns`;");
                if($request->show_in_request == 'yes'){
                    \DB::statement("ALTER TABLE `request_parameters` ADD `$request->column_name` TEXT NULL DEFAULT NULL AFTER `$lastRequestColumns`;");
                }
            }


            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property-type.show',$request->property_type_id)
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
    public function edit(Parameter $parameter,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property Types'),
            'url'=> route('system.property-type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> $parameter->property_type->{'name_'.App::getLocale()},
            'url'=> route('system.property-type.show',$parameter->property_type->id)
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $parameter->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Parameter');
        $this->viewData['result'] = $parameter;
        $this->viewData['property_type'] = $parameter->property_type;

        return $this->view('parameter.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ParameterFormRequest $request, Parameter $parameter)
    {

        $requestData = [
            'name_ar'=> $request->name_ar,
            'name_en'=> $request->name_en,
            'type'=> $request->type,
            'required'=> $request->required,
            'show_in_request'=> $request->show_in_request,
            'show_in_property'=> $request->show_in_property,
            'between_request'=> $request->between_request,
            'multi_request'=> $request->multi_request,
            'position'=> $request->position,
            'created_by_staff_id'=> Auth::id()
        ];

        switch ($request->type){
            case 'select':
            case 'multi_select':
            case 'radio':
            case 'checkbox':
                $options = [];
                foreach ($request->options['value'] as $key => $value){
                    $options[] = [
                        'value'     => $value,
                        'name_ar'   => $request->options['name_ar'][$key],
                        'name_en'   => $request->options['name_en'][$key]
                    ];
                }

                $requestData['options'] = $options;

                break;

            case 'number':
                $requestData['between_request']    = $request->between_request;
                break;

        }

        $updateData = $parameter->update($requestData);
        if($updateData){


            return $this->response(
                true,
                200,
                __('Data updated successfully'),
                [
                    'url'=> route('system.property-type.show',$parameter->property_type_id)
                ]
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not update the data')
            );
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parameter $parameter,Request $request)
    {
        $message = __('Parameter deleted successfully');
        $parameter->delete();
        return $this->response(true,200,$message);
    }

}
