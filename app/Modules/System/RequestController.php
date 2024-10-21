<?php

namespace App\Modules\System;
use App\Http\Requests\RequestFormRequest;
use App\Models\Request as RequestModal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;


class RequestController extends SystemController
{


    private function createEditData(){
        $this->viewData['request_status'] = ['new','pendding','accept','reject','cancel'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = RequestModal::select([
               'id',
               'renter_id',
               'property_id',
               'status',
               'created_at',
            ])->orderBy('requests.id', 'desc');

           // print_r($eloquentData->get());die();


            /*
             * Start handling filter
             */

            whereBetween($eloquentData,'DATE(requests.created_at)',$request->created_at1,$request->created_at2);


            if($request->id){
                $eloquentData->where('requests.id',$request->id);
            }


            if($request->renter_id){
                $eloquentData->where('requests.renter_id',$request->renter_id);
            }

            if($request->property_id){
                $eloquentData->where('requests.property_id',$request->property_id);
            }

            if($request->status){
                $eloquentData->where('requests.status',$request->status);
            }

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            if($request->downloadExcel){
                return exportXLS(
                    __('Requests'),
                    [
                        __('ID'),
                        __('Renter'),
                        __('Mobile'),
                        __('Property'),
                        __('Status'),
                        __('Created At')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'renter'=> function($data){
                            return  $data->renter ? $data->renter->Fullname : '--';
                        },
                        'mobile'=> function($data){
                            return  $data->renter ? $data->renter->mobile : '--';
                        },
                        'property'=> function($data){
                            return   $data->property ? $data->property->address : '--';
                        },
                        'status'=> function($data){
                            return __(ucfirst($data->status));
                        },
                        'created_at'=> function($data){
                            return $data->created_at->format('Y-m-d h:i A');
                        }
                    ]
                );
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('renter_id',function($data){
                    return '<a href="'.route('system.renter.show',$data->renter_id).'" target="_blank">'.$data->renter->Fullname.'</a><br /><a href="tel:'.$data->renter->mobile.'">'.$data->renter->mobile.'</a>';
                })
                ->addColumn('property_id',function($data){
                    return '<a href="'.route('system.property.show',$data->property_id).'" target="_blank">'.$data->property_id.'</a>';
                })

                ->addColumn('status',function($data){
                    return __(ucfirst($data->status));
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
                                <a class="dropdown-item" href="'.route('system.request.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.request.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.request.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->whitelist(['requests.id'])
                ->make(false);

        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Renter'),
                __("Property ID"),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Requests')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Requests');
            }else{
                $this->viewData['pageTitle'] = __('Requests');
            }

            $this->createEditData();


            return $this->view('request.index',$this->viewData);
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
            'text'=> __('Requests'),
            'url'=> route('system.request.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Request'),
        ];

        $this->viewData['pageTitle'] = __('Create Request');

        $this->createEditData();
        return $this->view('request.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestFormRequest $request){
        $requestDataInsert = [
            'renter_id' => $request->renter_id,
            'property_id' => $request->property_id,
            'status' => $request->status,
        ];

        $insertData = RequestModal::create($requestDataInsert);
        if($insertData){

            // --- Notification for sales related to added request
//            $sales_requests_count = RequestModal::where('sales_id',$insertData->sales_id)->count();
//            if($sales_requests_count) {
//                notifyStaff( // notify  Staff
//                    [
//                        'type' => 'staff',
//                        'ids' => [$insertData->sales_id]
//                    ],
//                    __('Request Notification'),
//                    __('Fresh request related to you. you have now :number requests', ['number' => $sales_requests_count]),
//                    route('system.request.show', $insertData->id)
//                );
//            }
            // --- Notification for sales related to added request

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.request.show',$insertData->id)
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
    public function show(RequestModal $request,Request $HTTPrequest){
        list($requestModal,$request) = [$request,$HTTPrequest];

        /*if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401);
        }*/

//        if(!staffCan('request-manage-all') &&  $requestModal->sales_id != Auth::id() && $requestModal->created_by_staff_id != Auth::id()){
//            abort(401);
//        }

       if($request->isDataTable == 'call'){
            $eloquentData = $requestModal->calls()->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'description',
                'created_by_staff_id',
                'created_at'
            ])
                ->orderByDesc('id')
                ->with([
                    'client',
                    'call_purpose',
                    'call_status',
                    'staff'
                ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


//            if(!staffCan('call-manage-all')){
//                $eloquentData->where('calls.created_by_staff_id',Auth::id());
//            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    return '<a href="'.route('system.renter.show',$data->client->id).'" target="_blank">'.$data->client->fullname.'</a>';
                })
                ->addColumn('call_purpose_id',function($data){
                    return '<b style="color: '.$data->call_purpose->color.'">'.$data->call_purpose->{'name_'.\App::getLocale()}.'</b>';
                })
                ->addColumn('call_status_id',function($data){
                    return '<b style="color: '.$data->call_status->color.'">'.$data->call_status->{'name_'.\App::getLocale()}.'</b>';
                })
                ->addColumn('type',function($data){
                    return __(strtoupper($data->type));
                })
                ->addColumn('created_by_staff_id', function($data){
                    return $data->staff ? '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>' : "--";
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
                                <a class="dropdown-item" target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }elseif($request->isDataTable == 'log'){
           $eloquentData = Activity::
           where('subject_type','App\Models\Request')
               ->where('subject_id',$requestModal->id)
               ->select([
                   'id',
                   'description',
                   'created_at',
               ]);

           return datatables()->eloquent($eloquentData)
               ->addColumn('id','{{$id}}')
               ->addColumn('description','{{$description}}')
               ->addColumn('created_at','{{$created_at}}')
               ->addColumn('action',function($data){
                   return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
               })
               ->escapeColumns([])
               ->make(false);

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Requests'),
                    'url' => route('system.request.index'),
                ],
                [
                    'text' => __('Request'),
                ]
            ];

            $this->viewData['pageTitle'] = $requestModal->name;

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Price'),
                __('Space'),
                __('Bed Rooms'),
                __('Bath Room'),
                __('Owner Name'),
                __('Action')
            ];

            $this->viewData['result'] = $requestModal;
//            $this->viewData['result']->property_model_array = PropertyModel::select('id','name_'.App::getLocale().' as name')->whereIn('id',explode(',',$requestModal->property_model_id))->get();

            // ----log
            save_log(__('View Request'),'App\Models\Request');
            // ----log

            return $this->view('request.show', $this->viewData);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestModal $request,Request $HTTPrequest){
        list($requestModal,$request) = [$request,$HTTPrequest];

        /*if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }*/
//        if(!staffCan('request-manage-all') &&  $requestModal->sales_id != Auth::id() && $requestModal->created_by_staff_id != Auth::id()){
//            abort(401);
//        }
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Requests'),
            'url'=> route('system.request.index')
        ];
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit Request'),
        ];

        $this->viewData['pageTitle'] = __('Edit Request');
        $this->viewData['result'] = $requestModal;

        $this->createEditData();
        return $this->view('request.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(RequestModal $request,RequestFormRequest $HTTPrequest)
    {
        list($requestModal,$request) = [$request,$HTTPrequest];
       /* if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }*/

//        if(!staffCan('request-manage-all') &&  $requestModal->sales_id != Auth::id() && $requestModal->created_by_staff_id != Auth::id()){
//            abort(401);
//        }

        $requestDataInsert = [
            'renter_id' => $request->renter_id,
            'property_id' => $request->property_id,
            'status' => $request->status
        ];
        $updateData = $requestModal->update($requestDataInsert);

        if($updateData){

            // --- Notification
//            $numProperties = $requestModal->property()->count();
//
//            if($numProperties){
//                $allStaffToNotify = array_column(
//                    App\Models\Staff::get(['id'])->toArray(),
//                    'id'
//                );
//                notifyStaff(
//                    [
//                        'type'  => 'staff',
//                        'ids'   => $allStaffToNotify
//                    ],
//                    __('Request Notification'),
//                    __('There are :number properties related to request',['number'=> $numProperties]),
//                    route('system.request.show',$requestModal->id)
//                );
//            }
            // --- Notification

            // ----log
           // save_log(__('Update Request'),'App\Models\Request');
            // ----log


            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.request.show',$requestModal->id)
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




    public function share(Request $request){
        $requestData = RequestModal::findOrFail($request->id);

        /*if(!staffCan('request-manage-all') && ( $requestData->created_by_staff_id != Auth::id() || $requestData->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }*/
//        if(!staffCan('request-manage-all') &&  $requestData->sales_id != Auth::id()){
//            abort(401);
//        }

        $hours = (int) $request->hours;
        if(!$hours){
            return [
                'status'=> false,
                'message'=> __('Please select valid hours')
            ];
        }

        $dataArray = [
            'sharing_until'=> Carbon::now()->addHours($hours)->format('Y-m-d H:i:s'),
            'sharing_staff_id'=> Auth::id()
        ];

        if(!$requestData->sharing_slug){
            $dataArray['sharing_slug'] = Str::random(5).$requestData->id.Str::random(5);
        }

        $requestData->update($dataArray);

        // ----log
        save_log(__('Share Request'),'App\Models\Request',$request->id);
        // ----log

        return [
            'status'=> true,
            'message'=> __('Done')
        ];

    }
    public function closeShare(Request $request){
        $requestData = RequestModal::findOrFail($request->id);

        $requestData->update([
            'sharing_slug'      => null,
            'sharing_until'     => null,
            'sharing_staff_id'  => null
        ]);

        // ----log
        save_log(__('Cancel Sharing Request'),'App\Models\Request',$request->id);
        // ----log

        return [
            'status'=> true,
            'message'=> __('Done')
        ];

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestModal $request,Request $HTTPrequest)
    {
        $message = __('Request deleted successfully');

        //RequestParameter::where('request_id',$requestModal->id)->delete();
        $request->calls()->delete();
        $request->delete();

        return $this->response(true,200,$message);
    }

}
