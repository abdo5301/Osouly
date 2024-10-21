<?php

namespace App\Modules\System;

use App\Http\Requests\LeadDataFormRequest;
use App\Models\LeadData;
use App\Models\DataSource;
use App\Models\CallPurpose;
use App\Models\CallStatus;
use App\Models\Client;
use App\Models\Call;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class LeadDataController extends SystemController
{

    private function createEditData(){

        $this->viewData['lead_status'] = CallPurpose::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        $this->viewData['data_source'] = DataSource::get([
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

//        if(!staffCan('lead-manage-all') && $lead->created_by_staff_id != Auth::id() ){
//            abort(401, 'Unauthorized.');
//        }

//        $all = LeadData::with(['calls'])->get();
//        foreach ($all as $ii => $rr){
//            if(!$rr->calls()){
//                continue;
//            }else{
//               // $last_call = LeadData::where()->with(['calls'])->get();$rr->calls()->latest()->first();
//                LeadData::where('id',$rr->id)->update([
//                    'last_call_status_id' => $last_call->call_status->id
//                ]);
//            }
//        }



        if ($request->to_sales == 'true'){
            if(!empty($request->to_sales_id) && !empty($request->data_ids)){

                $new_sales = App\Models\Staff::where('id',$request->to_sales_id)->first();
                $data_ids = explode(',',$request->data_ids);

                $numLeads = 0;
                foreach ($data_ids as $data_id){
                    $lead_data = LeadData::where('id',$data_id)->first();
                    $old_sales = App\Models\Staff::where('id',$lead_data->transfer_to_sales_id)->first();
                   // $old_sales_leads_count = LeadData::where('transfer_to_sales_id',$lead_data->to_sales_id)->count();
                   //$new_sales_leads_count = LeadData::where('sales_id',$request->to_sales_id)->count();
                    if($lead_data && $old_sales && $new_sales && $new_sales->id != $old_sales->id ){
                        LeadData::where('id',$data_id)->update([
                            'transfer_to_sales_id'=>$request->to_sales_id,
                            'transfer_by_staff_id'=> Auth::id()
                        ]);
                    // ----log
                    save_log(__('Transfer From ('.$old_sales->fullname.') To ('.$new_sales->fullname.')'),'App\Models\LeadData',$data_id);
                    // ----log
                        $numLeads++;
                    }

                }


                // --- Notification
                //$numLeads = LeadData::whereIn('id',$data_ids)->count();

                if($numLeads){
                    $allStaffToNotify = array_column(
                        App\Models\Staff::where('id',$request->to_sales_id)->get(['id'])->toArray(),
                        'id'
                    );
                    notifyStaff(
                        [
                            'type'  => 'staff',
                            'ids'   => $allStaffToNotify
                        ],
                        __('There are :number leads need to action',['number'=> $numLeads]),
                        __('There are :number leads need to action',['number'=> $numLeads]),
                        route('system.lead-data.index')
                    );
                }
                // --- Notification

                // ----log
               // save_log(__('Transfer leads ('.$request->data_ids.') to sales ('.$request->to_sales_id.')'),'App\Models\LeadData');
                // ----log

            }
        }



        if ($request->to_archive == 'true'){
            if(!empty($request->data_ids)){
                $data_ids = explode(',',$request->data_ids);
                $numLeads = 0;
                foreach ($data_ids as $data_id){
                    $lead_data = LeadData::where('id',$data_id)->first();
                    if($lead_data){
                        $lead_data->delete();
//                        // ----log
//                        save_log(__('Archive Leads'),'App\Models\LeadData',$data_id);
//                        // ----log
                        $numLeads++;
                    }

                }

                // ---log
                // save_log(__('Transfer leads ('.$request->data_ids.') to sales ('.$request->to_sales_id.')'),'App\Models\LeadData');
                // ----log

            }
        }




        $eloquentData = [];


        if($request->isDataTable){

            $eloquentData = LeadData::select([
                'lead_data.id',
                'lead_data.client_id',
                'lead_data.lead_id',
                'lead_data.last_call_purpose_id',
                'lead_data.last_call_status_id',
                'lead_data.last_call_description',
                'lead_data.data_source_id',
                'lead_data.name',
                'lead_data.mobile',
                'lead_data.email',
                'lead_data.description',
                'lead_data.project_name',
                'lead_data.campaign_name',
                'lead_data.transfer_by_staff_id',
                'lead_data.transfer_to_sales_id',
                'lead_data.requested',
                'lead_data.request_id',
                'lead_data.created_by_staff_id',
                'lead_data.lead_status_id',
                'lead_data.deleted_at'
            ])//->whereNull('lead_id')
            //->join('calls','calls.sign_type','=','App\Models\LeadData')
                ->with([
                    'client',
                    'staff',
                    'last_call_purpose',
                    'last_call_status',
                    'data_source',
                    'lead_status',
                    'lead',
                    'calls',
                    'transfer_by_staff',
                    'transfer_to_sales'
                ])->orderBy('id','desc');


            if($request->id){
                $eloquentData->where('id',$request->id);
            }

            whereBetween($eloquentData,'DATE(lead_data.created_at)',$request->created_at1,$request->created_at2);

            if($request->client_id){
                $eloquentData->where('client_id',$request->client_id);
            }

            if($request->data_source_id){
                $eloquentData->where('data_source_id',$request->data_source_id);
            }

            if($request->created_by_staff_id){
                $eloquentData->where('created_by_staff_id',$request->created_by_staff_id);
            }

            if($request->transfer_by_staff_id){
                $eloquentData->where('transfer_by_staff_id',$request->transfer_by_staff_id);
            }

            if($request->transfer_to_sales_id){
                $eloquentData->where('transfer_to_sales_id',$request->transfer_to_sales_id);
            }

            if($request->leads_type){
                if($request->leads_type == 'leads_manuel'){
                   $eloquentData->whereNull('lead_id');
                }elseif($request->leads_type == 'leads_Excel'){
                   $eloquentData->whereNotNull('lead_id');
                }
            }

            if($request->email){
                $eloquentData->where('email',$request->email);
            }

            if($request->description){
                $eloquentData->where('description','like',"%".$request->description."%");
            }

            if($request->last_call_description){
                $eloquentData->where('last_call_description','like',"%".$request->last_call_description."%");
            }

            if($request->campaign_name){
                $eloquentData->where('campaign_name',$request->campaign_name);
            }

            if($request->project_name){
                $eloquentData->where('project_name',$request->project_name);
            }

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            if($request->manuel_leads){
                $eloquentData->whereNull('lead_id');
            }

            if($request->archive){
                $eloquentData->onlyTrashed();
            }

            if($request->lead_status_id){
                if($request->lead_status_id == 'fresh_lead'){
                    $eloquentData->doesntHave('calls');
                }else{
                    $eloquentData->where('last_call_purpose_id',$request->lead_status_id);
                }

            }


            if($request->last_call_status){
                    $eloquentData->where('last_call_status_id',$request->last_call_status);
            }

            if(!staffCan('lead-manage-all')){
                $eloquentData->where('lead_data.transfer_to_sales_id',Auth::id())
                ->orWhere('lead_data.created_by_staff_id',Auth::id());
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('select','')
                ->addColumn('id','{{$id}}')
                ->addColumn('last_call_purpose_id',function($data){
                     return $data->last_call_purpose ? '<b style="color:'.$data->last_call_purpose->color.'">'.$data->last_call_purpose->{'name_'.App::getLocale()}.'</b>' : '<b style="color:green">'.__('Fresh Lead').'</b>';
//                    $last_call = $data->calls()->orderBy('id','desc')->first();
//                    return $last_call ? '<b style="color:'.$last_call->call_purpose->color.'">'.$last_call->call_purpose->{'name_'.App::getLocale()}.'</b>' : '<b style="color:green">'.__('Fresh Lead').'</b>';
                    // if(!$data->lead_status) return '--';
                    //   return '<b style="color:'.$data->lead_status->color.'">'.$data->lead_status->{'name_'.App::getLocale()}.'</b>';
                    //if($last_call) // script edit last call
                    // LeadData::where('id',$data->id)->update(['last_call_status_id' => $last_call->call_status->id,'last_call_description'=>$last_call->description]);

                })
                ->addColumn('last_call_status_id',function($data){
                    return $data->last_call_status ? '<b style="color:'.$data->last_call_status->color.'">'.$data->last_call_status->{'name_'.App::getLocale()}.'</b>' : '--';
                })
                ->addColumn('last_call_description',function($data){
                    return $data->last_call_description ?  '<b  class="more_info"  title="'.$data->last_call_description.'" >'.\Illuminate\Support\Str::words($data->last_call_description, 3,'..').'</b>' : '--';
                })
                ->addColumn('name',function($data){
                    if(!$data->client_id) { return $data->name;}
                    return $data->client ? '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a>' : $data->name;
                })
                ->addColumn('mobile',function($data){
                    return '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })
                ->addColumn('email',function($data){
                    if(!$data->email) return '--';
                    return '<a href="mailto:'.$data->email.'">'.$data->email.'</a>';
                })
                ->addColumn('description',function($data){
                    if(!$data->description) return '--';
                    return '<b   class="more_info"  title="'.$data->description.'">'.\Illuminate\Support\Str::words($data->description, 3,'..').'</b>';
                })
                ->addColumn('project_name',function($data){
                    if(!$data->project_name) return '--';
                    return $data->project_name;
                })
                ->addColumn('campaign_name',function($data){
                    if(!$data->campaign_name) return '--';
                    return $data->campaign_name;
                })
                ->addColumn('client_id',function($data){
                    if(!$data->client_id) return '--';
                    return '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a>';
                })
                ->addColumn('transfer_by_staff_id',function($data){
                    if(!$data->transfer_by_staff_id) return '--';
                    return '<a href="'.route('system.staff.show',$data->transfer_by_staff_id).'" target="_blank">'.$data->transfer_by_staff->fullname.'</a>';
                })
                ->addColumn('transfer_to_sales_id',function($data){
                    if(!$data->transfer_to_sales_id) return '--';
                    return '<a href="'.route('system.staff.show',$data->transfer_to_sales_id).'" target="_blank">'.$data->transfer_to_sales->fullname.'</a>';
                })
                ->addColumn('created_by_staff_id',function($data){
                    if(!$data->staff) return '--';
                    return '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>';
                })
                ->addColumn('requested',function($data){
                    if(!$data->requested) return '--';
                    return  $data->requested == 'pending' ? '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst($data->requested)).'</span>' : '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst($data->requested)).'</span>';
                })
                ->addColumn('data_source_id',function($data){
                    if(!$data->data_source) return '--';
                    return $data->data_source->{'name_'.App::getLocale()};
                })
                ->addColumn('action', function($data){
                    $client_name = 0;
                    if($data->client){
                        $client_name = $data->client->name;
                    }
                    $client_id = 0;
                    if($data->client_id){
                        $client_id = $data->client_id;
                    }

                    if(staffCan('system.lead-data.destroy') && !$data->deleted_at){
                        $delete_link = '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.lead-data.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Archive Lead Data').'</a>';
                    }else{
                        $delete_link ='';
                    }

                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                             <a class="dropdown-item" target="_blank" href="'. route('system.lead-data.show',$data->id).'"><i class="la la-search-plus"></i> '.__('Action').'</a>                        
                             <a class="dropdown-item" target="_blank" href="'. route('system.lead-data.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>           
                             <a class="dropdown-item" href="javascript:showModalCall('.$data->id.','.$client_id.','."'$client_name'".');"><i class="la la-phone"></i> '.__('Create Call').'</a>
                             <a class="dropdown-item" href="javascript:showCallsHistory('.$data->id.');"><i class="la la-book"></i> '.__('Call History').'</a>
                            '.$delete_link.'
                            </div>
                        </span>';
                    //                                <a class="dropdown-item" target="_blank" href="'.(!$data->request_id ? route('system.request.create','lead_data='.$data->id) : route('system.request.show',$data->request_id)).'"><i class="la la-search-plus"></i> '.__('Request').'</a>
                })
                ->whitelist(['id','last_call_status.id','last_call_purpose.id','last_call_description','mobile','name','email','description','project_name','campaign_name'])
                ->escapeColumns([])
                ->make(false);
        }else{
            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Leads')
                ]
            ];

            $this->viewData['pageTitle'] = __('Leads');

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Last Action'),
                __('Last Status'),
                __('Last Description'),
                __('Name'),
                __('Mobile'),
                __('E-mail'),
                __('Description'),
                __('Project Name'),
                __('Campaign Name'),
                __('Client'),
                __('Transfer By'),
                __('To Sales'),
                __('Created By'),
                __('Requested'),
                __('Data Source'),
                __('Action')

            ];

            $this->viewData['lead_status'] = CallPurpose::get();
            $this->viewData['status']   = CallStatus::get();
            $this->viewData['data_source']   = DataSource::get();


            //$this->viewData['result'] = $lead;
            return $this->view('lead-data.index', $this->viewData);
        }
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Lead Data'),
            'url'=> route('system.lead-data.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Lead Data'),
        ];


        $this->viewData['pageTitle'] = __('Create Lead Data');

        $this->createEditData();

        return $this->view('lead-data.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeadDataFormRequest $request){
        $leadData = $request->all();
        $leadData['created_by_staff_id'] = Auth::id();
        $leadData['transfer_to_sales_id'] = Auth::id();
        //$leadData['transfer_by_staff_id'] = Auth::id();

        //check client in database
        $client_check = Client::where('mobile1',$request->mobile)->orWhere('mobile2',$request->mobile)->first();
        //creat new client if not exist
        if(!$client_check){
            $client_check =  Client::create([
                'name'=>$request->name,
                'mobile1'=>$request->mobile,
                'email'=>$request->email,
                'description'=>$request->description,
                'created_notes'=> 'Created from Adding leads Manuel',
                'created_by_staff_id' => Auth::id()
            ]);
        }

        if($client_check)
        $leadData['client_id'] = $client_check->id;
        else
        $leadData['client_id'] = '';


        $insertData = LeadData::create($leadData);

        if($insertData){

            if(!empty(setting('request_notify_managers_groups_ids'))) {
                $managersGroupsIds = explode(',', setting('request_notify_managers_groups_ids'));

                $managersToNotify = array_column(  // notify Management group  only
                    App\Models\Staff::whereIn('permission_group_id',$managersGroupsIds)->get(['id'])->toArray(),
                    'id'
                );

                notifyStaff(   // notify  managers
                    [
                        'type' => 'staff',
                        'ids' => $managersToNotify
                    ],
                    __('Leads Notification'),
                    __('Fresh Lead Added'),
                    route('system.lead-data.show', $insertData->id)
                );

            }


            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.lead-data.index')
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

        $lead_data = LeadData::findOrFail($id);

        if(!staffCan('lead-manage-all') && $lead_data->transfer_to_sales_id != Auth::id() && $lead_data->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }


        if ($request->isDataTable == 'call') {
            $eloquentData = $lead_data->calls()->select([
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

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }


            if (!staffCan('call-manage-all')) {
                $eloquentData->where('calls.created_by_staff_id', Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('client_id', function ($data) {
                    return '<a href="' . route('system.client.show', $data->client->id) . '" target="_blank">' . $data->client->name . '</a>';
                })
                ->addColumn('call_purpose_id', function ($data) {
                    return '<b style="color: ' . $data->call_purpose->color . '">' . $data->call_purpose->{'name_' . \App::getLocale()} . '</b>';
                })
                ->addColumn('call_status_id', function ($data) {
                    return '<b style="color: ' . $data->call_status->color . '">' . $data->call_status->{'name_' . \App::getLocale()} . '</b>';
                })
                ->addColumn('type', function ($data) {
                    return __(strtoupper($data->type));
                })
                ->addColumn('description',function($data){
                    if(!$data->description) return '--';
                    return '<b class="more_info" title="'.$data->description.'">'.\Illuminate\Support\Str::words($data->description, 3,'..').'</b>';
                })
                ->addColumn('created_by_staff_id', function ($data) {
                    return '<a href="' . route('system.staff.show', $data->staff->id) . '" target="_blank">' . $data->staff->fullname . '</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> (' . $data->created_at->diffForHumans() . ')';
                })
                ->addColumn('action', function ($data) {
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu ' . ((\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right') . '" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:showCall('.$data->id.');"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';

//                    <a class="dropdown-item" target="_blank" href="' . route('system.call.index', ['call_id' => $data->id]) . '"><i class="la la-search-plus"></i> ' . __('View') . '</a>

                })
                ->escapeColumns([])
                ->make(false);
        } elseif ($request->isDataTable == 'log') {
            $eloquentData = Activity::with(['subject', 'causer'])
                ->where('subject_type', 'App\Models\LeadData')
                ->where('subject_id', $lead_data->id)
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
                ->addColumn('id', '{{$id}}')
                ->addColumn('description', '{{$description}}')
                ->addColumn('causer', function ($data) {
                    return '<a target="_blank" href="' . route('system.staff.show', $data->causer->id) . '">' . $data->causer->fullname . '</a>';
                })
                ->addColumn('created_at', '{{$created_at}}')
                ->addColumn('action', function ($data) {
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu ' . ((\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right') . '" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:urlIframe(\'' . route('system.activity-log.show', $data->id) . '\')"><i class="la la-search-plus"></i> ' . __('View') . '</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        } else {

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Leads'),
                    'url' => route('system.lead-data.index'),
                ],
                [
                    'text' => __('Lead Data').' #'.$lead_data->id,
                ]
            ];

            $this->viewData['pageTitle'] = __('Lead Data');
            $this->viewData['purposes'] = CallPurpose::get();
            $this->viewData['status']   = CallStatus::get();

            $this->viewData['result'] = $lead_data;
           // save_log(__('View Lead'), 'App\Models\LeadData',$lead_data->id);
            return $this->view('lead-data.show', $this->viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request){

        $lead_data = LeadData::findOrFail($id);

        if(!staffCan('lead-manage-all') && $lead_data->transfer_to_sales_id != Auth::id() && $lead_data->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Lead Data'),
            'url'=> route('system.lead-data.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit Lead Data').' #'.$id,
        ];

        $this->viewData['pageTitle'] = __('Edit Lead Data');
        $this->viewData['result'] = $lead_data;
        //print_r($lead_data);die;
         $this->createEditData();

        return $this->view('lead-data.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(LeadDataFormRequest $request, $id){

        $requestData = $request->all();

        $lead_data = LeadData::findOrFail($id);

        if($request->name || $request->mobile || $request->email || $request->description){
            Client::where('id',$lead_data->client_id)->update([
                'mobile1'=>$request->mobile,
                'email'=>$request->email,
                'name'=>$request->name,
                'description'=>$request->description,
            ]);
        }

        $updateData = $lead_data->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.lead-data.show',$lead_data->id)
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
    public function destroy($id){
        $lead_data = LeadData::findOrFail($id);
        if(!staffCan('lead-manage-all') && $lead_data->transfer_to_sales_id	!= Auth::id() && $lead_data->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }
        $message = __('Lead Data Archived successfully');
        //Call::where(['sign_type'=>'App\Models\LeadData','sign_id'=>$lead_data->id])->delete();
        $lead_data->delete();
        return $this->response(true,200,$message);
    }

}
