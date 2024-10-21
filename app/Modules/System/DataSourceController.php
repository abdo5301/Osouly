<?php

namespace App\Modules\System;

use App\Models\DataSource;
use Illuminate\Http\Request;
use App\Http\Requests\DataSourceFormRequest;
use Form;
use Auth;
use App;

class DataSourceController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = DataSource::select([
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
                                <a class="dropdown-item" href="'.route('system.data-source.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.data-source.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
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
                'text'=> __('Data Sources')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Data Sources');
            }else{
                $this->viewData['pageTitle'] = __('Data Sources');
            }

            return $this->view('data-source.index',$this->viewData);
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
            'text'=> __('Data Sources'),
            'url'=> route('system.data-source.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Data Source'),
        ];

        $this->viewData['pageTitle'] = __('Create Data Source');

        return $this->view('data-source.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DataSourceFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();

        $insertData = DataSource::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.data-source.index')
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
    public function show(DataSource $data_source,Request $request){
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(DataSource $data_source,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Data Sources'),
            'url'=> route('system.data-source.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $data_source->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Data Source');
        $this->viewData['result'] = $data_source;

        return $this->view('data-source.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(DataSourceFormRequest $request, DataSource $data_source)
    {

        $requestData = $request->all();

        $updateData = $data_source->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.data-source.index')
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
    public function destroy(DataSource $data_source,Request $request)
    {
        $message = __('Data Source deleted successfully');
        $data_source->delete();
        return $this->response(true,200,$message);
    }

}
