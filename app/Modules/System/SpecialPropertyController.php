<?php

namespace App\Modules\System;

use App\Models\PropertyAds;
use Illuminate\Http\Request;
use App\Http\Requests\SpecialPropertyFormRequest;
use Form;
use Auth;
use App;

class SpecialPropertyController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PropertyAds::select([
                'id',
                'property_id',
                'client_package_id',
                'start_date',
                'end_date',
                'created_by',
                'created_at',
            ]);

            whereBetween($eloquentData,'DATE(property_ads.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'DATE(property_ads.start_date)',$request->start_date1,$request->start_date2);
            whereBetween($eloquentData,'DATE(property_ads.end_date)',$request->end_date1,$request->end_date2);

            if($request->id){
                $eloquentData->where('property_ads.id',$request->id);
            }

            if($request->property_id){
                $eloquentData->where('property_ads.property_id',$request->property_id);
            }

            if($request->client_package_id){
                $eloquentData->where('property_ads.client_package_id',$request->client_package_id000);
            }

            if($request->created_by){
                $eloquentData->where('property_ads.created_by',$request->created_by);
            }

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id',function($data){
                    return $data->property_id ? '<a target="_blank" href="'.route('system.property.show',$data->property_id).'"> #'.$data->property_id.'</a>' : '--';
                })
                ->addColumn('client_package_id',function($data){
                    return $data->client_package ? '<a target="_blank" href="'.route('system.client-package.show',$data->client_package_id).'"> #'.$data->client_package_id.'</a>' : '--';
                })

                ->addColumn('start_date', function($data){
                    return $data->start_date ? date('Y-m-d',strtotime($data->start_date))  : '--';
                })
                ->addColumn('end_date', function($data){
                    return $data->end_date ? date('Y-m-d',strtotime($data->end_date))  : '--';
                })
                ->addColumn('created_by',function($data){
                    return $data->created_by_client ? '<a target="_blank" href="'.route('system.'.$data->created_by_client->type.'.show',$data->created_by).'">'.$data->created_by_client->fullname.'</a>' : '--';
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
                                <a class="dropdown-item" href="'.route('system.special-property.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.special-property.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.special-property.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
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
                __('Client Package ID'),
                __('Start Date'),
                __('End Date'),
                __('Created By'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Special Properties')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Special Properties');
            }else{
                $this->viewData['pageTitle'] = __('Special Properties');
            }

            return $this->view('special-property.index',$this->viewData);
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
            'text'=> __('Special Properties'),
            'url'=> route('system.special-property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Special Property'),
        ];

        $this->viewData['pageTitle'] = __('Create Special Property');

        return $this->view('special-property.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpecialPropertyFormRequest $request){
        $requestData = $request->all();

        $insertData = PropertyAds::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.special-property.index')
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

    public function show(PropertyAds $special_property,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Special Properties'),
                'url' => route('system.special-property.index'),
            ],
            [
                'text' =>  __('Show Special Property'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Special Property');


        $this->viewData['result'] = $special_property;

        return $this->view('special-property.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyAds $special_property,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Special Properties'),
            'url'=> route('system.special-property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> '# '.$special_property->id]),
        ];

        $this->viewData['pageTitle'] = __('Edit Special Property');

        $this->viewData['result'] = $special_property;

        return $this->view('special-property.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(SpecialPropertyFormRequest $request, PropertyAds $special_property)
    {
        $requestData = $request->all();

        $updateData = $special_property->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.special-property.show',$special_property->id)
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
    public function destroy(PropertyAds $special_property,Request $request)
    {
        $message = __('Special Property deleted successfully');

        $special_property->delete();

        return $this->response(true,200,$message);
    }

}
