<?php

namespace App\Modules\System;

use App\Http\Requests\DueFormRequest;
use App\Models\Dues;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class DueController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Dues::select([
                'id',
                'name',
                'image',
                'type',
                'status',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData,'DATE(dues.created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('dues.id',$request->id);
            }


            if($request->status){
                $eloquentData->where('dues.status',$request->status);
            }

            if($request->type){
                $eloquentData->where('dues.type',$request->type);
            }

            if($request->name){
                $eloquentData->where('dues.name','LIKE','%'.$request->name.'%');
            }

            if($request->description){
                $eloquentData->where('dues.description','LIKE','%'.$request->description.'%');
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('image', function($data){
                    return ( $data->image && is_file($data->image) ) ? '<a target="_blank" href="'.asset($data->image).'"><img style="width:70px;height: 70px;" src="'.asset($data->image).'"></a>' : '--';
                })
                ->addColumn('type', function($data){
                    if($data->type == 'service'){
                        return  '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst('Deductions')).'</span>';
                    }
                    return  '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__(ucfirst('Dues')).'</span>';
                })
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('In-Active').'</span>';
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
                                <a class="dropdown-item" href="'.route('system.dues.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.dues.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.dues.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                              
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
                __('Image'),
                __('Type'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Dues and Deductions')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Dues and Deductions');
            }else{
                $this->viewData['pageTitle'] = __('Dues and Deductions');
            }

            return $this->view('dues.index',$this->viewData);
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
            'text'=> __('Dues and Deductions'),
            'url'=> route('system.dues.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Dues or Deductions'),
        ];

        $this->viewData['pageTitle'] = __('Create Dues or Deductions');

        return $this->view('dues.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DueFormRequest $request){

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
        }
        $dueDataInsert = [
            'name'=>$request->name,
            'type'=>$request->type,
            'description'=>$request->description,
            'image'=> isset($image) ? $image : '',
            'status'=>$request->status,
        ];


        $insertData = Dues::create($dueDataInsert);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.dues.show',$insertData->id)
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
    public function show(Dues $due,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Dues and Deductions'),
                'url' => route('system.dues.index'),
            ],
            [
                'text' => $due->name,
            ]
        ];

        $this->viewData['pageTitle'] =  $due->name;

        $this->viewData['result'] = $due;

        return $this->view('dues.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Dues $due,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Dues and Deductions'),
            'url'=> route('system.dues.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $due->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Dues Or Deductions Data');
        $this->viewData['result'] = $due;

        return $this->view('dues.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(DueFormRequest $request, Dues $due)
    {

        $dueDataUpdate = [
            'name'=>$request->name,
            'type'=>$request->type,
            'description'=>$request->description,
            'status'=>$request->status,
        ];

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
            $dueDataUpdate['image'] = $image;
            if(!empty($due->image) && is_file($due->image)){ // remove old image
                unlink($due->image);
            }
        }

        $updateData = $due->update($dueDataUpdate);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.dues.show',$due->id)
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
    public function destroy(Dues $due)
    {
        $message = __('Data deleted successfully');
        if(!empty($due->image) && is_file($due->image)){ // remove image
            unlink($due->image);
        }

        $due->delete();

        return $this->response(true,200,$message);
    }



}