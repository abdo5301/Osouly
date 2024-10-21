<?php

namespace App\Modules\System;

use App\Http\Requests\ImporterFormRequest;
use App\Http\Requests\LeadFormRequest;
use App\Models\Area;
use App\Models\Lead;
use App\Models\LeadData;
use App\Models\LeadStatus;
use App\Models\DataSource;
use App\Models\PropertyType;
use App\Models\CallPurpose;
use App\Models\CallStatus;
use App\Models\Purpose;
use App\Models\Client;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class LeadController extends SystemController
{

    private function createEditData(){

        $this->viewData['lead_status'] = LeadStatus::get([
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



        if($request->isDataTable){

            $eloquentData = Lead::select([
                'leads.id',
                'leads.name',
                'leads.created_by_staff_id',
                'leads.created_at'
            ])
                ->join('lead_data','leads.id','=','lead_data.lead_id')
                ->with([
                    'staff',
                    'data'
                ])->groupBy('lead_data.lead_id');
            //dd($eloquentData);


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            whereBetween($eloquentData,'DATE(leads.created_at)',$request->created_at1,$request->created_at2);


            if($request->id){
                $eloquentData->where('leads.id',$request->id);
            }


            if($request->name){
                $eloquentData->where('leads.name','LIKE','%'.$request->name.'%');
            }


            if($request->created_by_staff_id){
                $eloquentData->where('leads.created_by_staff_id',$request->created_by_staff_id);
            }


          if(!staffCan('lead-manage-all')){
              $eloquentData->where('lead_data.transfer_to_sales_id',Auth::id())
              ->orWhere('leads.created_by_staff_id',Auth::id());
              //$eloquentData->where('leads.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('lead_data_count', function($data){
                    if(!staffCan('lead-manage-all')){
                        return $data->data()->where('transfer_to_sales_id',Auth::id())->orWhere('created_by_staff_id',Auth::id())->count();
                    }
                    return $data->data()->count();
                })
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
                                <a class="dropdown-item" href="'.route('system.lead.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
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
                __('Count'),
                __('Created At'),
                __('Created By'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Leads')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Leads');
            }else{
                $this->viewData['pageTitle'] = __('Leads');
            }

            return $this->view('lead.index',$this->viewData);
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
            'text'=> __('Leads'),
            'url'=> route('system.lead.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Leads'),
        ];

        $this->viewData['pageTitle'] = __('Create Leads');

        $this->createEditData();

        return $this->view('lead.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeadFormRequest $request){

        $file = $request->file('file')->store(setting('system_path').'/lead/'.date('Y/m/d'),'first_public');


        $data = [
            'name'=> $request->name,
            'file'=> $file,
            'created_by_staff_id'=> Auth::id()
        ];
        try {
        $spreadsheet =  \PhpOffice\PhpSpreadsheet\IOFactory::load(base_path('public/'.$file))
            ->getActiveSheet()
            ->toArray(null,true,true,true);
        }catch (\Exception $e){
            return $this->response(
                false,
                11001,
                __('Please check the XLS file. Some columns are invalid') . ' :  ('.$e->getMessage().')'
            );
            // return $e;
        }

        if(count($spreadsheet) < 2){
            return $this->response(
                false,
                11001,
                __('Empty XLS file')
            );
        }

        $insertData = Lead::create($data);
        if($insertData){

            if($request->ignore_first_row == 'yes'){
                unset($spreadsheet[1]);
            }
            $double_mobiles = [];
            $message_ul = '<ul>';
            $i = 0;
            foreach ($spreadsheet as $key => $value) {
                if (
                    !isset($value[strtoupper($request->columns_data_name)]) ||
                    !isset($value[strtoupper($request->columns_data_mobile)])
                ) continue;

                $name = @$value[strtoupper($request->columns_data_name)];
                $mobile = @$value[strtoupper($request->columns_data_mobile)];
                $mobile = preg_replace("/[^0-9]/", "", $mobile);
                if (!preg_match("~^0\d+$~", $mobile)) {
                    $mobile = '0'.$mobile;
                }

                $email = @$value[strtoupper($request->columns_data_email)];
                $description = @$value[strtoupper($request->columns_data_description)];
                $project_name = @$value[strtoupper($request->columns_data_project_name)];
                $campaign_name = @$value[strtoupper($request->columns_data_campaign_name)];

                //check client mobile in leads
                $client_lead_check = LeadData::where('mobile',$mobile)->first();
                if($client_lead_check){
                    array_push($double_mobiles,$mobile);
                    $message_ul .= "<li><a target='_blank' href='".route('system.lead-data.show',$client_lead_check->id)."'>".$mobile."</a></li>";
                    continue;
                }

                //check client in database
                $client_check = Client::where('mobile1',$mobile)->orWhere('mobile2',$mobile)->first();
                //creat new client if not exist
                if(!$client_check){
                    $client_check =  Client::create([
                        'name'=>$name,
                        'mobile1'=>$mobile,
                        'email'=>$email,
                        'description'=>$description,
                        'created_notes'=> 'Created from Uploading leads Excel',
                        'created_by_staff_id' => Auth::id(),
                    ]);
                }

                LeadData::create([
                    'lead_id' => $insertData->id,
                    'name' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'project_name' => $project_name,
                    'campaign_name' => $campaign_name,
                    'description' => $description,
                    'client_id' =>  $client_check ? $client_check->id : '', // abdo edit
                    'data_source_id' => $request->data_source_id,
                    'created_by_staff_id' => Auth::id(),
                    'transfer_to_sales_id' => Auth::id(),
                    //'lead_status_id' => $request->lead_status_id ? $request->lead_status_id : '',
                    //'transfer_by_staff_id' => Auth::id()
                ]);



                $i++;
            }

            if(!empty($double_mobiles)){
                $message_ul .= '</ul>';
                $double_message_render = '<br>'.__('There Are ( :count ) Mobiles Already Exists in Leads',['count'=>count($double_mobiles)]).$message_ul /*implode(',',$double_mobiles)*/;
            }else{
                $double_message_render = '';
            }

            if(!$i){
                $insertData->delete();
                return $this->response(
                    false,
                    11001,
                    __('corrupted XLS file').' '.$double_message_render
                );
            }

            // ----log
            save_log(__('Upload Lead'),'App\Models\Lead',$insertData->id);
            // ----log

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
                    __('New Excel File Uploaded Contain ( :num ) Leads', ['num' => $i]),
                    route('system.lead.show', $insertData->id)
                );

            }


                return $this->response(
                    true,
                    11001,
                     __('Successfully added ( :num ) Data from ( :all )',['num'=>$i,'all'=>count($spreadsheet)]).' '.$double_message_render
                );


//            return $this->response(
//                true,
//                200,
//                __('Data added successfully'),
//                [
//                    'url'=> route('system.lead.show',$insertData->id)
//                ]
//            );
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
    public function show(Lead $lead,Request $request){

//        if(!staffCan('lead-manage-all') && $lead->created_by_staff_id != Auth::id() ){
//            abort(401, 'Unauthorized.');
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

        $eloquentData = [];




        if($request->isDataTable){

            //$eloquentData = $lead->data();

            $eloquentData = $lead->data()->select([
                'id',
                'lead_status_id',
                'last_call_purpose_id',
                'last_call_status_id',
                'last_call_description',
                'data_source_id',
                'client_id',
                'lead_id',
                'name',
                'mobile',
                'email',
                'description',
                'project_name',
                'campaign_name',
                'transfer_by_staff_id',
                'transfer_to_sales_id',
                'created_by_staff_id',
                'requested',
                'request_id'
            ])
                ->with([
                    'client',
                    'staff',
                    'lead',
                    'calls',
                    'data_source',
                    'last_call_purpose',
                    'last_call_status',
                    'lead_status',
                    'transfer_by_staff',
                    'transfer_to_sales'
                ])->orderBy('id','desc');

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
                    return '<b  class="more_info"  title="'.$data->description.'">'.\Illuminate\Support\Str::words($data->description, 3,'..').'</b>';
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
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                             <a class="dropdown-item" target="_blank" href="'. route('system.lead-data.show',$data->id).'"><i class="la la-search-plus"></i> '.__('Action').'</a>                        
                             <a class="dropdown-item" target="_blank" href="'. route('system.lead-data.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>           
                             <a class="dropdown-item" href="javascript:showModalCall('.$data->id.','.$client_id.','."'$client_name'".');"><i class="la la-phone"></i> '.__('Create Call').'</a>
                             <a class="dropdown-item" href="javascript:showCallsHistory('.$data->id.');"><i class="la la-book"></i> '.__('Call History').'</a>
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
                    'text' => __('Leads'),
                    'url' => route('system.lead.index'),
                ],
                [
                    'text' => __('#ID: :id', ['id' => $lead->id]),
                ]
            ];

            $this->viewData['pageTitle'] = __('#ID: :id', ['id' => $lead->id]);

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Last Action'),
                __('Last Status'),
                __('Last Description'),
                __('Client Name'),
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
            $this->viewData['result'] = $lead;
            return $this->view('lead.show', $this->viewData);
        }
    }



    /**
     * Display the specified resource for auth sales only.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function salesData(Lead $lead,Request $request){

        $eloquentData = [];

        if($request->isDataTable){

            $eloquentData = leadData::select([
                'id',
                'name',
                'mobile',
                'email',
                'description',
                'client_id',
                'requested',
                'request_id',
                'lead_id'

            ])
                ->where('transfer_to_sales_id',Auth::id())
                ->with([
                    'client',
                    'lead',
                ]);

            //$eloquentData = LeadData::where(['transfer_to_sales_id'=>Auth::id(),'lead_id' => $lead->id])->get();

            //$eloquentData = $lead->data();

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('mobile',function($data){
                    return '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })
                ->addColumn('email',function($data){
                    if(!$data->email) return '--';
                    return '<a href="mailto:'.$data->email.'">'.$data->email.'</a>';
                })
                ->addColumn('description',function($data){
                    if(!$data->description) return '--';
                    return $data->description;
                })
                ->addColumn('client',function($data){
                    if(!$data->client_id) return '--';
                    return '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a>';
                })
                ->addColumn('requested',function($data){
                    if(!$data->requested) return '--';
                    return  $data->requested == 'pending' ? '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst($data->requested)).'</span>' : '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst($data->requested)).'</span>';

                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" target="_blank" href="'.( $data->request_id ? route('system.request.show',$data->request_id) : route('system.request.create','lead_data='.$data->id) ).'"><i class="la la-search-plus"></i> '.__('Request').'</a>                        
                            </div>
                        </span>';


                })
                ->escapeColumns([])
                ->make(false);
        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Sales Leads'),
                ]
            ];

            $this->viewData['pageTitle'] = __('Sales Leads');

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Mobile'),
                __('E-mail'),
                __('Description'),
                __('Client'),
                __('Requested'),
                __('To Request')
            ];


            $this->viewData['result'] = $lead;
            return $this->view('lead.sales-data', $this->viewData);
        }
    }



    /**
     * Display the specified resource for auth sales only.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function createLeadData(Request $request){
        return $this->view('lead.create-lead-date');
    }

    public function storeLeadData(LeadDataFormRequest $request){
        echo 'sadsad';
    }

}