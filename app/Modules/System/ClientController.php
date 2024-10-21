<?php

namespace App\Modules\System;

use App\Models\Client;
use App\Libs\AreasData;
use App\Models\ClientJob;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\ClientFormRequest;
use Form;
use Auth;
use Spatie\Activitylog\Models\Activity;

class ClientController extends SystemController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
     $type = $request->segment(2);

        if($request->isDataTable){

            $eloquentData = Client::select([
                'id',
              // 'type',
                'first_name',
//                \DB::raw('CONCAT(first_name,second_name) as fullname'),
                'mobile',
                'credit',
                'status',
               // 'email',
                'created_at',
            ])->where('type','=',$type);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            /*
             * Start handling filter
             */

            whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'DATE(verified_at)',$request->verified_at1,$request->verified_at2);
            whereBetween($eloquentData,'DATE(updated_at)',$request->updated_at1,$request->updated_at2);
            whereBetween($eloquentData,'DATE(birth_date)',$request->birth_date1,$request->birth_date2);

            if($request->id){
                $eloquentData->where('id','=',$request->id);
            }

            if($request->created_by_staff_id){
                $eloquentData->where('created_by_staff_id','=',$request->created_by_staff_id);
            }

            if($request->id_number){
                $eloquentData->where('id_number','=',$request->id_number);
            }

            if($request->bank_account_number){
                $eloquentData->where('bank_account_number','=',$request->bank_account_number);
            }

            if($request->parent_id){
                $eloquentData->where('parent_id','=',$request->parent_id);
            }

//            if($request->type){
//                $eloquentData->where('type','=',$request->type);
//            }

            if($request->name){
                $eloquentData->where('first_name','LIKE','%'.$request->name.'%');
                $eloquentData->orWhere('second_name','LIKE','%'.$request->name.'%');
                $eloquentData->orWhere('third_name','LIKE','%'.$request->name.'%');
                $eloquentData->orWhere('last_name','LIKE','%'.$request->name.'%');
            }

            if($request->email){
                $eloquentData->where('email','LIKE','%'.$request->email.'%');
            }

            if($request->description){
                $eloquentData->where('description','LIKE','%'.$request->description.'%');
            }

            if($request->address){
                $eloquentData->where('address','LIKE','%'.$request->address.'%');
            }

            if($request->phone){
                $eloquentData->where('phone','LIKE','%'.$request->phone.'%');
            }

            if($request->area_id){
                $eloquentData->whereIn('area_id',AreasData::getAreasDown($request->area_id));
            }

            if($request->mobile){
                $eloquentData->where(function($query) use ($request){
                    $query->where('mobile','LIKE','%'.$request->mobile.'%');
                });
            }

            if($request->status){
                $eloquentData->where('status','=',$request->status);
            }

            whereBetween($eloquentData,'credit',$request->credit1,$request->credit2);



            if($request->downloadExcel){
                return exportXLS(
                    __('Clients'),
                    [
                        __('ID'),
                  //      __('Type'),
                        __('Name'),
                        __('Mobile'),
                        __('Phone'),
                        __('E-Mail'),
                        __('Address'),
                        __('Description'),
                        __('Status'),
                        __('Created At')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                    //    'type'=> function($data){return ucfirst($data->type);},
                        'name'=> function($data){
                                return $data->fullname;
                        },
                        'mobile'=> 'mobile',
                        'phone'=> 'phone',
                        'email'=> 'email',
                        'credit'=> 'credit',
                        'address'=> 'address',
                        'description'=> 'description',
                        'status'=>  function($data){
                            if($data->status == 'active'){
                                return __('Active');
                            }elseif ($data->status == 'pending'){
                                return __('Pending');
                            }
                            return __('In-Active');
                        },
                        'created_at'=> function($data){
                            return $data->created_at->format('Y-m-d h:i A');
                        }
                    ]
                );
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
//                ->addColumn('type',function($data){
//                    if($data->type == 'owner'){
//                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Owner').'</span>';
//                    }else{
//                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Renter').'</span>';
//                    }
//                })
                ->addColumn('first_name', function($data){
                        return $data->fullname;
                })
                ->addColumn('mobile', function($data){
                    return '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })
                ->addColumn('credit','{{$credit}}')
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
                    }elseif ($data->status == 'pending'){
                        return '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__('Pending').'</span>';
                    }else{
                        return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('In-Active').'</span>';
                    }

                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data) use ($type){
                  if($data->status == 'in-active'){
                      $client_block_text = '<i class="la la-key"></i>'.__('Unblock');
                  }elseif($data->status == 'active'){
                      $client_block_text = '<i class="la la-ban"></i>'.__('Block');
                  }else{
                      $client_block_text = '<i class="la la-check-circle-o"></i>'.__('Activation');
                  }
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.'.$type.'.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.'.$type.'.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="blockClient(\''.route('system.'.$type.'.block',$data->id).'\')">'.$client_block_text.'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.'.$type.'.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>
                        </span>';
                })
                ->whitelist(['id','fullname','mobile'])
                ->escapeColumns(['type','email'])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
            //    __('Type'),
                __('Name'),
                __('Mobile'),
                __('Credit'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __(ucfirst($type).'s')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted '.ucfirst($type).'s');
            }else{
                $this->viewData['pageTitle'] = __(ucfirst($type).'s');
            }

            $this->viewData['type'] = $type;

            return $this->view('client.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $type = $request->segment(2);
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __(ucfirst($type).'s'),
            'url'=> route('system.'.$type.'.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create '.ucfirst($type)),
        ];

        $this->viewData['pageTitle'] =  __('Create '.ucfirst($type));

        $this->viewData['type'] = $type;

        return $this->view('client.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientFormRequest $request){
        $type = $request->segment(2);
        $requestData = $request->all();
        $requestData['password'] = bcrypt($requestData['password']);
        $requestData['created_by_staff_id'] = Auth::id();

        $insertData = Client::create($requestData);

        if($insertData){

            //upload files and images
            $files = $request->allFiles();
            if(!empty($files)){
                $custom_key = md5(rand().time());
                foreach ($files as $key => $val){
                    if($request->hasFile($key)){
                        $path = $request->file($key)->store(setting('system_path').'/'.date('Y/m/d'),'first_public');
                            Image::create([
                                'custom_key'=> $custom_key,
                                'path'=> $path,
                                'sign_id'=> $insertData->id,
                                'sign_type'=> 'App\Models\Client',
                                'image_name'=> $key
                            ]);
                    }
                }
            }

            if(!empty($request->post('job_title'))){

                $job_title = $request->post('job_title');
                $job_company = $request->post('company_name');
                $job_from = $request->post('from_date');
                $job_to = $request->post('to_date');
                $job_present = $request->post('present');

                foreach ($job_title as $key => $value){
                    if(empty($job_title[$key]) || empty($job_from[$key])){
                        continue;
                    }
                    if($job_present[$key] == 'no' && empty($job_to[$key])){
                        continue;
                    }
                    $job_data = array(
                        'client_id'=> $insertData->id,
                        'job_title'=> $job_title[$key],
                        'company_name'=> $job_company[$key],
                        'from_date'=> $job_from[$key],
                        'to_date'=> $job_to[$key],
                        'present'=>  $job_present[$key]
                    );
                    ClientJob::create($job_data);
                }
            }


            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'id'=> $insertData->id,
                    'name'=> $insertData->first_name,
                    'url'=> route('system.'.$type.'.show',$insertData->id)
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
    public function show($id,Request $request){
        $client = Client::find($id);
        $type = $request->segment(2);
        if(empty($client) || $type != $client->type ){
            abort(404, 'Unauthorized.');
        }

        if($request->isDataTable == 'call'){
            $eloquentData = $client->calls()->select([
                'id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'created_at'
            ])->orderByDesc('id');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

//            if(!staffCan('call-manage-all')){
//                $eloquentData->where('calls.created_by_staff_id',Auth::id());
//            }

            return datatables()->eloquent($eloquentData)
//                ->addColumn('id','{{$id}}')
                ->addColumn('id',function($data){
                    return '<a target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'">'.$data->id.'</a>';
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
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
//                ->addColumn('action', function($data){
//                    return '<span class="dropdown">
//                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
//                              <i class="la la-gear"></i>
//                            </a>
//                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
//                                <a class="dropdown-item" target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'"><i class="la la-search-plus"></i> '.__('View').'</a>
//                                <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
//                            </div>
//                        </span>';
//                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'property'){

            $eloquentData = $client->property()->select([
                'id',
                'title',
                'property_type_id',
                'purpose_id',
                'price',
                'space',
                'created_at'
            ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('title','{{$title}}')
                ->addColumn('property_type_id',function($data){
                    return $data->property_type ? $data->property_type->{'name_'.\App::getLocale()} : '--';
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose ? $data->purpose->{'name_'.\App::getLocale()} :'--';
                })
                ->addColumn('price',function($data){
                    return number_format($data->price);
                })
                ->addColumn('space',function($data){
                    return number_format($data->space);
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })

                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'log'){
            $eloquentData = Activity::with(['subject','causer'])
                ->where('subject_type','App\Models\Client')
                ->where('subject_id',$client->id)
                ->select([
                    'id',
                    'log_name',
                    'description',
                    'subject_id',
                    'subject_type',
                    'causer_id',
                    'causer_type',
                    'created_at',
                    'updated_at'
                ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('causer',function($data){
                    return $data->causer ? '<a target="_blank" href="'.route('system.staff.show',$data->causer->id).'">'.$data->causer->fullname.'</a>' : '--';
                })
                ->addColumn('created_at','{{$created_at}}')
//                ->addColumn('action',function($data){
//                    return '<span class="dropdown">
//                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
//                              <i class="la la-gear"></i>
//                            </a>
//                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
//                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
//                            </div>
//                        </span>';
//                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'client_packages'){

            $eloquentData = $client->packages()->select([
                'id',
                'service_id',
                'transaction_id',
                'date_from',
                'date_to',
                'status',
                'created_at',
            ])->orderByDesc('id');

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
                    }elseif ($data->status =='pending'){
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
                                <a class="dropdown-item" href="'.route('system.client-package.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>                             
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.client-package.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'client_transactions'){

            $eloquentData = $client->clientTransactions()->select([
                'id',
                'transaction_id',
                'amount',
                'type',
                'created_at'
            ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('transaction_id',function($data){
                    return $data->transaction_id ? '<a target="_blank" href="'.route('system.transaction.show',$data->transaction_id).'"> #'.$data->transaction_id.'</a>' : '--';
                })
                ->addColumn('amount',function($data){
                    return $data->amount ? amount($data->amount,true) : '0.00';
                })
                ->addColumn('type', function($data) {
                    if($data->type =='in'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords('From Client')) . '</span>';
                    }
                    return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords('To Client')) . '</span>';
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
                                <a class="dropdown-item" href="'.route('system.client-transaction.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.client-transaction.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.client-transaction.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'owner_installments'){

                $eloquentData = $client->owner_installments()->select([
                    'id',
                    'amount',
                    'renter_id',
                    'invoice_id',
                    'due_date',
                    'created_at'
                ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('amount',function($data){
                    return $data->amount ? amount($data->amount,true) : '0.00';
                })
                ->addColumn('renter_id',function($data){
                    return $data->renter ? '<a target="_blank" href="'.route('system.'.$data->renter->type.'.show',$data->renter_id).'">'.$data->renter->fullname.'</a>' : '--';
                })
                ->addColumn('invoice_id',function($data){
                    return $data->invoice ? '<a target="_blank" href="'.route('system.invoice.show',$data->invoice_id).'"> #'.$data->invoice_id.'</a>' : '--';
                })
                ->addColumn('due_date', function($data){
                    return $data->due_date ? date('Y-m-d',strtotime($data->due_date))  : '--';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'renter_installments'){

                $eloquentData = $client->renter_installments()->select([
                    'id',
                    'amount',
                    'owner_id',
                    'invoice_id',
                    'due_date',
                    'created_at'
                ])->orderByDesc('id');


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('amount',function($data){
                    return $data->amount ? amount($data->amount,true) : '0.00';
                })
                ->addColumn('owner_id',function($data){
                    return $data->owner ? '<a target="_blank" href="'.route('system.'.$data->owner->type.'.show',$data->owner_id).'">'.$data->owner->fullname.'</a>' : '--';
                })
                ->addColumn('invoice_id',function($data){
                    return $data->invoice ? '<a target="_blank" href="'.route('system.invoice.show',$data->invoice_id).'"> #'.$data->invoice_id.'</a>' : '--';
                })
                ->addColumn('due_date', function($data){
                    return $data->due_date ? date('Y-m-d',strtotime($data->due_date))  : '--';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'favorite'){

            $eloquentData = $client->favorite()->select([
                'id',
                'property_id',
                'created_at'
            ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id',function($data){
                    return $data->property_id ? '<a target="_blank" href="'.route('system.property.show',$data->property_id).'"> #'.$data->property_id.'</a>' : '--';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'request'){

            $eloquentData = $client->renterrequests()->select([
                'requests.id',
                'requests.property_id',
                'requests.status',
                'requests.created_at'
            ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id',function($data){
                    return $data->property_id;
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
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.request.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'invoice'){

            $eloquentData = $client->invoices()->select([
                'id',
                'property_id',
                'property_due_id',
                'installment_id',
                'amount',
                'date',
                'status',
                'created_at',
            ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id',function($data){
                    return $data->property_id ? '<a target="_blank" href="'.route('system.property.show',$data->property_id).'"> #'.$data->property_id.'</a>' : '--';
                })
                ->addColumn('property_due_id',function($data){
                    return $data->property_due ? '<a target="_blank" href="'.route('system.invoice.show',$data->property_due_id).'">'.$data->property_due->name.'</a>' : '--';
                })
                ->addColumn('installment_id',function($data){
                    return $data->installment ? '# '.$data->installment_id : '--';
                })
                ->addColumn('amount',function($data){
                    return $data->amount ? amount($data->amount,true) : '0.00';
                })
                ->addColumn('date', function($data){
                    return $data->date ? date('Y-m-d',strtotime($data->date))  : '--';
                })
                ->addColumn('status', function($data) {
                    if($data->status =='unpaid'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }
                    return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';

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
                                <a class="dropdown-item" href="'.route('system.invoice.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.invoice.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.invoice.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text'=> __(ucfirst($type).'s'),
                    'url'=> route('system.'.$type.'.index'),
                ],
                [
                    'text'=> $client->fullname,
                ]
            ];

            $this->viewData['pageTitle'] = __(ucfirst($type).' Profile');

            $this->viewData['result'] = $client;

            $this->viewData['type'] = $type;

            return $this->view('client.show',$this->viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request){
        $client = Client::with(['images'])->find($id);
        $type = $request->segment(2);
        if(empty($client) || $type != $client->type ){
            abort(404, 'Unauthorized.');
        }
//        if(!staffCan('client-manage-all') && $client->created_by_staff_id != Auth::id()){
//            abort(401, 'Unauthorized.');
//        }

        $images = $client->images;
        $images_array = array();
        foreach ($images as $image){
            $images_array[$image->image_name] = asset($image->path);
        }
        //print_r($images_array);die;

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __(ucfirst($type).'s'),
            'url'=> route('system.'.$type.'.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $client->fullname]),
        ];

        $this->viewData['pageTitle'] = __('Edit '.ucfirst($type));
        $this->viewData['result'] = $client;

        $this->viewData['type'] = $type;

        $this->viewData['client_jobs'] = ClientJob::Where('client_id',$id)->get();

        $this->viewData['images'] = $images_array;

        return $this->view('client.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ClientFormRequest $request, $id)
    {
        $type = $request->segment(2);

       // print_r($request->files('card_face'));die;

        $client = Client::find($id);

        $requestData = $request->all();

        if($requestData['password']){
            $requestData['password'] = bcrypt($requestData['password']);
        }else{
            unset($requestData['password']);
        }

        $updateData = $client->update($requestData);

        if($updateData){
            //upload files and images
           $files = $request->allFiles();
           if(!empty($files)){
            $custom_key = md5(rand().time());
            foreach ($files as $key => $val){
                if($request->hasFile($key)){
                $path = $request->file($key)->store(setting('system_path').'/'.date('Y/m/d'),'first_public');
                $old_image = Image::where(['image_name'=>$key,'sign_id'=>$id,'sign_type'=>'App\Models\Client'])->first();
                if (!$old_image){ //create image
                     Image::create([
                        'custom_key'=> $custom_key,
                        'path'=> $path,
                        'sign_id'=> $id,
                        'sign_type'=> 'App\Models\Client',
                        'image_name'=> $key
                    ]);
                }else{ // update image
                    if(is_file($old_image->path))
                        unlink($old_image->path);
                     $old_image->update([
                        'path'=> $path
                    ]);
                }
            }
            }
           }

            if(!empty($request->post('job_title'))){

                $client->jobs()->forceDelete();
                $job_title = $request->post('job_title');
                $job_company = $request->post('company_name');
                $job_from = $request->post('from_date');
                $job_to = $request->post('to_date');
                $job_present = $request->post('present');

                foreach ($job_title as $key => $value){
                    if(empty($job_title[$key]) || empty($job_from[$key])){
                        continue;
                    }
                    if($job_present[$key] == 'no' && empty($job_to[$key])){
                        continue;
                    }
                    $job_data = array(
                        'client_id'=> $id,
                        'job_title'=> $job_title[$key],
                        'company_name'=> $job_company[$key],
                        'from_date'=> $job_from[$key],
                        'to_date'=> $job_to[$key],
                        'present'=>  $job_present[$key]
                    );
                    ClientJob::create($job_data);
                }
            }

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.'.$type.'.show',$client->id)
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
     * Block Client.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function block($id,Request $request){
        $type = $request->segment(2);

        $client = Client::find($id);

        if(empty($client) || $type != $client->type ){
            abort(404);
        }

        if($client->status == 'in-active'){
            $status_update = ['status'=>'active'];
            $message = __('Client Unblocked successfully');
        }elseif($client->status == 'active'){
            $status_update = ['status'=>'in-active'];
            $message = __('Client Blocked successfully');
        }else{
            $status_update = ['status'=>'active'];
            $message = __('Client Activated successfully');
        }

        $client_block = $client->update($status_update);

        if($client_block){
            return $this->response(true,200,$message);
        }else{
            $message = __('Sorry, we could not change status of this client. pleas try again later !');
            return $this->response(
                false,
                11001,
                $message
            );
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $type = $request->segment(2);

        $client = Client::find($id);

        if(empty($client) || $type != $client->type ){
            abort(404);
        }

        $message = __('Client deleted successfully');

        /* start delete client relationships */

        $client->renterRequests()->delete();
        $client->sms()->delete();
        $client->comments()->delete();
        $client->jobs()->delete();
        $client->packages()->delete();
        $client->property()->delete();
        $client->calls()->delete();
        $client->reminders()->delete();

        $images = $client->images;
        if(!empty($images)){
            foreach ($images as $img){
                if(is_file($img->path)){
                    unlink($img->path);
                }
            }
            $client->images()->delete();
        }
        /* end delete client relationships */

        $client->delete();

        return $this->response(true,200,$message);
    }

}
