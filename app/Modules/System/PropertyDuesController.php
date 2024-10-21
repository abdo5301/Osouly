<?php

namespace App\Modules\System;

use App\Models\PropertyDues;
use Illuminate\Http\Request;
use App\Http\Requests\PropertyDuesFormRequest;
use Form;
use Auth;
use App;

class PropertyDuesController extends SystemController
{

    private function createEditData(){
        $this->viewData['dues'] = App\Models\Dues::get(['id','name']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PropertyDues::select([
                'id',
                'property_id',
                'name',
                'due_id',
                'value',
                'type',
                'duration',
                'created_at',
            ]);

            whereBetween($eloquentData,'DATE(property_dues.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'property_dues.value',$request->value1,$request->value2);

            if($request->id){
                $eloquentData->where('property_dues.id',$request->id);
            }

            if($request->property_id){
                $eloquentData->where('property_dues.property_id',$request->property_id);
            }

            if($request->due_id){
                $eloquentData->where('property_dues.due_id',$request->due_id);
            }

            if($request->type){
                $eloquentData->where('property_dues.type',$request->type);
            }

            if($request->name){
                $eloquentData->where('property_dues.name','LIKE','%'.$request->name.'%');
            }

            if($request->duration){
                $eloquentData->where('property_dues.duration',$request->duration);
            }





            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id',function($data){
                    return '<a target="_blank" href="'.route('system.property.show',$data->property_id).'">'.$data->property_id.'</a>';
                })
                ->addColumn('name','{{$name}}')
                ->addColumn('due_id',function($data){
                    return $data->dues ? '<a target="_blank" href="'.route('system.dues.show',$data->due_id).'">'.$data->dues->name.'</a>' : '--';
                })
                ->addColumn('value',function($data){
                    return $data->value ? amount($data->value,true) : '0.00';
                })
                ->addColumn('type', function($data) {
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->type)) . '</span>';

                })
                ->addColumn('duration', function($data) {
                    return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords(str_replace('_',' ',$data->duration))) . '</span>';
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
                                <a class="dropdown-item" href="'.route('system.property-dues.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.property-dues.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property-dues.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Property ID'),
                __('Name'),
                __('Due Name'),
                __('Value'),
                __('Type'),
                __('Duration'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Property Dues')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Property Dues');
            }else{
                $this->viewData['pageTitle'] = __('Property Dues');
            }

            $this->createEditData();

            return $this->view('property-dues.index',$this->viewData);
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
            'text'=> __('Property Dues'),
            'url'=> route('system.property-dues.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Property Dues'),
        ];

        $this->viewData['pageTitle'] = __('Create Property Dues');

        $this->createEditData();

        return $this->view('property-dues.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyDuesFormRequest $request){
        $requestData = $request->all();

        $insertData = PropertyDues::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property-dues.index')
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

    public function show(PropertyDues $property_due,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Property Dues'),
                'url' => route('system.property-dues.index'),
            ],
            [
                'text' =>  __('Show Property Dues Data'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Property Dues Data');


        $this->viewData['result'] = $property_due;

        return $this->view('property-dues.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyDues $property_due,Request $request){

//        $property_due = PropertyDues::find($id);
//        if(!$property_due){
//            abort(404);
//        }

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property Dues'),
            'url'=> route('system.property-dues.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $property_due->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Property Dues');
        $this->viewData['result'] = $property_due;
        $this->createEditData();

        return $this->view('property-dues.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyDuesFormRequest $request, PropertyDues $property_due)
    {
//        $property_due = PropertyDues::find($id);
//        if(!$property_due){
//            abort(404);
//        }

        $requestData = $request->all();

        $updateData = $property_due->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.property-dues.show',$property_due->id)
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
    public function destroy(PropertyDues $property_due,Request $request)
    {
        $message = __('Property Dues deleted successfully');

        $property_due->delete();

        return $this->response(true,200,$message);
    }

}
