<?php

namespace App\Modules\System;

use App\Models\ClientPackages;
use Illuminate\Http\Request;
use App\Http\Requests\ClientPackagesFormRequest;
use Form;
use Auth;
use App;

class ClientPackagesController extends SystemController
{

    private function createEditData(){
        $this->viewData['services'] = App\Models\Service::where('parent_id',0)->get([
            'id',
            'title_'.App::getLocale().' as name'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = ClientPackages::select([
                'id',
                'service_id',
                'client_id',
                'transaction_id',
                'date_from',
                'date_to',
                'status',
                'created_at',
            ])->orderBy('client_packages.id', 'desc');;

            whereBetween($eloquentData,'DATE(client_packages.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'DATE(client_packages.date_from)',$request->date_from1,$request->date_from2);
            whereBetween($eloquentData,'DATE(client_packages.date_to)',$request->date_to1,$request->date_to2);

            if($request->id){
                $eloquentData->where('client_packages.id',$request->id);
            }

            if($request->service_id){
                $eloquentData->where('client_packages.service_id',$request->service_id);
            }

            if($request->service_type){
                $eloquentData->where('client_packages.service_type',$request->service_type);
            }

            if($request->service_count){
                $eloquentData->where('client_packages.service_count',$request->service_count);
            }

            if($request->client_id){
                $eloquentData->where('client_packages.client_id',$request->client_id);
            }

            if($request->transaction_id){
                $eloquentData->where('client_packages.transaction_id',$request->transaction_id);
            }

            if($request->count_per_day){
                $eloquentData->where('client_packages.count_per_day',$request->count_per_day);
            }

            if($request->status){
                $eloquentData->where('client_packages.status',$request->status);
            }


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('service_id',function($data){
                    if($data->service && !empty($data->service->parent_id)){
                        $rout_name = 'package';
                    }else{
                        $rout_name = 'service';
                    }
                    return $data->service ? '<a target="_blank" href="'.route('system.'.$rout_name.'.show',$data->service_id).'">'.$data->service->{'title_'.lang()}.'<br>( '.__(ucwords($rout_name)).' )</a>' : '--';
                })
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a target="_blank" href="'.route('system.'.$data->client->type.'.show',$data->client_id).'">'.$data->client->fullname.'</a>' : '--';
                })
                ->addColumn('transaction_id',function($data){
                    return $data->transaction ? '<a href="'.route('system.transaction.show',$data->transaction_id).'" target="_blank"># '.$data->transaction_id .'</a>': '--';
                })
                ->addColumn('date_from', function($data){
                    return $data->date_from ? date('Y-m-d',strtotime($data->date_from))  : '--';
                })
                ->addColumn('date_to', function($data){
                    return $data->date_to ? date('Y-m-d',strtotime($data->date_to))  : '--';
                })
                ->addColumn('status', function($data) {
                    if($data->status =='active'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }elseif ($data->status =='pendding'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--info k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }else{
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords(str_replace('-',' ',$data->status))) . '</span>';
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
                                <a class="dropdown-item" href="'.route('system.client-package.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>                             
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.client-package.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Service'),
                __('Client'),
                __('Transaction ID'),
                __('Date From'),
                __('Date To'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Client Packages')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Client Packages');
            }else{
                $this->viewData['pageTitle'] = __('Client Packages');
            }

            $this->createEditData();

            return $this->view('client-package.index',$this->viewData);
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
            'text'=> __('Client Packages'),
            'url'=> route('system.client-package.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Client Package'),
        ];

        $this->viewData['pageTitle'] = __('Create Client Package');
        $this->createEditData();

        return $this->view('client-package.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientPackagesFormRequest $request){

        $requestData = [
            'service_id' => $request->service_id,
            'client_id'  => $request->client_id,
            'transaction_id'  => $request->transaction_id,
            'status'  => $request->status,
            //'service_count'  => $request->service_count,
            'date_from'  => $request->date_from,
            'date_to'  => $request->date_to,
            //'count_per_day'  => $request->count_per_day
        ];

        $service_data = getService($request->service_id);

        if($request->package_id && $request->package_id > 0){
            $requestData['service_id'] = $request->package_id;
            $service_data = getService($request->package_id);
        }


        if(!empty($service_data)){
            $requestData['service_details'] = json_encode($service_data);
            $requestData['service_type'] =  $service_data->type;
            $requestData['service_count'] =  $service_data->properties_count;
            $requestData['rest_count'] =  $service_data->properties_count;
             $requestData['count_per_day'] =  $service_data->type_count;
        }

        $insertData = ClientPackages::create($requestData);

        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.client-package.index')
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

    public function show(ClientPackages $client_package,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Client Packages'),
                'url' => route('system.client-package.index'),
            ],
            [
                'text' =>  __('Show Client Package'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Client Package');


        $this->viewData['result'] = $client_package;

        $this->viewData['service_details'] = $client_package->service_details ? json_decode($client_package->service_details) : '';
       // print_r($this->viewData['service_details']);die;


        return $this->view('client-package.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientPackages $client_package,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Client Packages'),
            'url'=> route('system.client-package.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> '# '.$client_package->id]),
        ];

        $this->viewData['pageTitle'] = __('Edit Client Package');
        $this->viewData['result'] = $client_package;

        $this->createEditData();

        return $this->view('client-package.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ClientPackagesFormRequest $request, ClientPackages $client_package)
    {
        $requestData = $request->all();

        $updateData = $client_package->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.client-package.show',$client_package->id)
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
    public function destroy(ClientPackages $client_package,Request $request)
    {
        $message = __('Client Package deleted successfully');

        $client_package->delete();

        return $this->response(true,200,$message);
    }

}
