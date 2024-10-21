<?php

namespace App\Modules\System;

use App\Models\Maintenance;
use Illuminate\Http\Request;
//use App\Http\Requests\MaintenanceFormRequest;
use Form;
use Auth;
use App;

class MaintenanceController extends SystemController
{

    private function createEditData(){
        $this->viewData['categories'] = App\Models\MaintenanceCategory::get([
              'id',
              'name_'.App::getLocale().' as name'
            ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Maintenance::select([
                'id',
                'maintenance_category_id',
                'property_id',
                'client_id',
                'date',
                'status',
                'created_at',
            ]);

            whereBetween($eloquentData,'DATE(maintenance.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'DATE(maintenance.date)',$request->date1,$request->date2);

            if($request->id){
                $eloquentData->where('maintenance.id',$request->id);
            }

            if($request->property_id){
                $eloquentData->where('maintenance.property_id',$request->property_id);
            }

            if($request->client_id){
                $eloquentData->where('maintenance.client_id',$request->client_id);
            }

            if($request->maintenance_category_id){
                $eloquentData->where('maintenance.maintenance_category_id',$request->maintenance_category_id);
            }


            if($request->notes){
                $eloquentData->where('maintenance.notes','LIKE','%'.$request->notes.'%');
            }

            if($request->priority){
                $eloquentData->where('maintenance.priority',$request->priority);
            }

            if($request->status){
                $eloquentData->where('maintenance.status',$request->status);
            }


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('maintenance_category_id',function($data){
                    return $data->category ? '<a target="_blank" href="'.route('system.maintenance-category.index',['id'=>$data->category->id]).'">'.$data->category->{'name_'.lang()}.'</a>' : '--';
                })
                ->addColumn('property_id',function($data){
                    return $data->property_id ? '<a target="_blank" href="'.route('system.property.show',$data->property_id).'"> #'.$data->property_id.'</a>' : '--';
                })
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a target="_blank" href="'.route('system.'.$data->client->type.'.show',$data->client_id).'">'.$data->client->fullname.'</a>' : '--';
                })

                ->addColumn('date', function($data){
                    return $data->date ? date('Y-m-d',strtotime($data->date))  : '--';
                })
                ->addColumn('status', function($data) {
                    if($data->status =='open'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }elseif ($data->status =='inprogress'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--info k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }else{
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';

                    }
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
                                <a class="dropdown-item" href="'.route('system.maintenance.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Category'),
                __('Property ID'),
                __('Client'),
                __('Date'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Maintenance')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Maintenance');
            }else{
                $this->viewData['pageTitle'] = __('Maintenance');
            }

            $this->createEditData();

            return $this->view('maintenance.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        abort(404);
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Invoices'),
            'url'=> route('system.invoice.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Invoice'),
        ];

        $this->viewData['pageTitle'] = __('Create Invoice');

        $this->createEditData();

        return $this->view('invoice.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaintenanceFormRequest $request){
        abort(404);
        $requestData = $request->all();

        $insertData = Maintenance::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.maintenance.index')
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

    public function show(Maintenance $maintenance,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Maintenance'),
                'url' => route('system.maintenance.index'),
            ],
            [
                'text' =>  __('Show Maintenance Data'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Maintenance Data');


        $this->viewData['result'] = $maintenance;

        return $this->view('maintenance.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Maintenance $maintenance,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Maintenance'),
            'url'=> route('system.maintenance.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> '# '.$maintenance->id]),
        ];

        $this->viewData['pageTitle'] = __('Edit Maintenance');
        $this->viewData['result'] = $maintenance;
        $this->createEditData();

        return $this->view('maintenance.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(MaintenanceFormRequest $request, Maintenance $maintenance)
    {

        $requestData = $request->all();

        $updateData = $maintenance->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.maintenance.show',$maintenance->id)
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
    public function destroy(Maintenance $maintenance,Request $request)
    {
        $message = __('Maintenance deleted successfully');

        $maintenance->delete();

        return $this->response(true,200,$message);
    }

}
