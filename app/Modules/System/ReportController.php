<?php

namespace App\Modules\System;

use App\Http\Requests\CreditsFormRequest;
use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use function foo\func;


class ReportController extends SystemController
{


    public function match(Request $request){

        if($request->isDataTable){

//            $date1 = date('Y-m-1');
//            $date2 = date('Y-m-31');

//            if($request->created_at1){
//                $date1 = $request->created_at1;
//            }
//
//            if($request->created_at2){
//                $date2 = $request->created_at2;
//            }


            $eloquentData = Invoice::select([
                'invoices.owner_id',
                'clients.first_name',
                'clients.second_name',
                'clients.third_name',
                'clients.last_name',
                  \DB::raw('sum(CASE WHEN invoices.status = "paid" AND transaction_id is not null THEN invoices.commission END) as total_commission'),
                \DB::raw('sum(CASE WHEN invoices.status = "paid" AND transaction_id is not null  THEN invoices.amount END) as total_paid'),

            ])->join('clients','invoices.owner_id','clients.id')->groupBy('invoices.owner_id');






            whereBetween($eloquentData,'date',$request->created_at1,$request->created_at2);


            if($request->owner_id){
                $eloquentData->where('invoices.owner_id',$request->owner_id);
            }



            return datatables()->eloquent($eloquentData)
                ->addColumn('owner_id','{{$owner_id}}')
                ->addColumn('client_name',function ($data){
                    return $data->first_name.' '.$data->second_name.' '.$data->third_name.' '.$data->last_name;
                })
                 ->addColumn('total_paid','{{$total_paid}}')
                ->addColumn('total_commission','{{$total_commission}}')
                ->addColumn('rest', function($data){
                    return $data->total_paid - $data->total_commission;
                })
                ->addColumn('total_transfare', function($data) use($request){

                    $total = App\Models\ClientTransaction::where('type','out')->where('client_id',$data->owner_id);
                    whereBetween($total,'Date(created_at)',$request->created_at1,$request->created_at2);
                     $total = $total->sum('amount');
                     return $total;
                })
                ->addColumn('transfare', function($data) use($request){

                    $total = App\Models\ClientTransaction::where('type','out')->where('client_id',$data->owner_id);
                    whereBetween($total,'Date(created_at)',$request->created_at1,$request->created_at2);
                    $total = $total->sum('amount');
                    return $data->total_paid - $data->total_commission - $total;
                })->addColumn('dates',function ($data) use($request){

                  return '<span>'.@$request->created_at1.'</span><br><span>'.@$request->created_at2.'</span>';
                })

                ->addColumn('action', function($data)use ($request){
                    return ' <a  href="'.route('system.invoice.index',['owner_id'=>$data->owner_id,'date1'=>$request->created_at1,'date2'=>$request->created_at2,'status'=>'paid']).'">  '.__('invoices').'</a> <br> <br> 
                             <a href="'.route('system.client-transaction.index',['client_id'=>$data->owner_id,'date1'=>$request->created_at1,'date2'=>$request->created_a2]).'">  '.__('Transactions').'</a>
';
                })->rawColumns(['dates', 'action'])
                 ->make(true);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('اجمالي المستحق'),
                __('عمولة الموقع '),
                __('صافي المستحق '),
                __('المبلغ المحول '),
                __('المطلوب تحويله'),
                __('التاريخ'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('تقرير تطابق الاصدة')
            ];
            $this->viewData['pageTitle'] = __('تقرير تطابق الاصدة');


            return $this->view('report.match',$this->viewData);
        }

    }

    public function credits(Request $request){ // download and upload client credits

        $eloquentData = Client::select([
            'id',
            'branch_code',
            'bank_code',
            'bank_account_number',
            \DB::raw('CONCAT(first_name," ",second_name," ", third_name, " ", last_name) as name'),
            'credit'
        ])->where('credit','>',0)->orderBy('id', 'desc');

        // print_r($eloquentData->get());die();

        //whereBetween($eloquentData,'DATE(requests.created_at)',$request->created_at1,$request->created_at2);

        if($request->downloadExcel && $request->downloadExcel == 'on_us'){
            $eloquentData->where('clients.bank_code','NBEGEGCXXXX');
            return exportXLS(
                __('ON US'),
                [
                    __('Branch Code'),
                    __('Bank Account Number'),
                    __('Name'),
                    __('Credit'),
                    __('Staff ID'),
                    __('Bank Code (Static)')
                ],
                $eloquentData->get(),
                [
                    'branch_code'=> 'branch_code',
                    'bank_account_number'=> 'bank_account_number',
                    'name'=> 'name',
                    'credit'=> function($data){
                        return $data->credit;
                    },
                ]
            );
        }

        if($request->downloadExcel && $request->downloadExcel == 'off_us'){
            $eloquentData->where('clients.bank_code','!=','NBEGEGCXXXX');
            return exportXLS(
                __('OFF US'),
                [
                    __('Bank Code'),
                    __('Branch Code'),
                    __('Bank Account Number'),
                    __('Name'),
                    __('Credit'),
                    __('Staff ID'),
                ],
                $eloquentData->get(),
                [
                    'bank_code'=> 'bank_code',
                    'branch_code'=> 'branch_code',
                    'bank_account_number'=> 'bank_account_number',
                    'name'=> 'name',
                    'credit'=> function($data){
                        return $data->credit;
                    },
                ]
            );
        }

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Credits')
        ];

        $path    = setting('system_path').'/credits';
        if(is_dir($path)){
            $files = array_diff(scandir($path), array('.', '..'));
            $this->viewData['old_files'] =  array_values($files);
        }



        $this->viewData['pageTitle'] = __('Credits');

        return $this->view('report.credit',$this->viewData);
    }


    function creditUpload(CreditsFormRequest $request){

        $file = $request->file('file')->store(setting('system_path').'/credits','first_public');

        try {
            $spreadsheet =  \PhpOffice\PhpSpreadsheet\IOFactory::load(base_path('public/'.$file))
                ->getActiveSheet()
                ->toArray(null,true,true,true);
        }catch (\Exception $e){
            if(is_file($file)){
                unlink($file);
            }
            return $this->response(
                false,
                11001,
                __('Please check the XLS file. Some columns are invalid') . ' :  ('.$e->getMessage().')'
            );
            // return $e;
        }

        if(count($spreadsheet) < 2){
            if(is_file($file)){
                unlink($file);
            }
            return $this->response(
                false,
                11001,
                __('Empty XLS file')
            );
        }

        if($request->ignore_first_row == 'yes'){
            unset($spreadsheet[1]);
        }

        $i = 0;
        foreach ($spreadsheet as $key => $value) {
            if (
                !isset($value[strtoupper($request->columns_data_transaction_id)]) ||
                !isset($value[strtoupper($request->columns_data_client_id)]) ||
                !isset($value[strtoupper($request->columns_data_client_name)]) ||
                !isset($value[strtoupper($request->columns_data_amount)])
            ) continue;

            $transaction_id = @$value[strtoupper($request->columns_data_transaction_id)];
            $client_id = @$value[strtoupper($request->columns_data_client_id)];
            $client_name = @$value[strtoupper($request->columns_data_client_name)];
            $amount = @$value[strtoupper($request->columns_data_amount)];

            //create client transaction
            App\Models\ClientTransaction::create([
                'type' => 'out',
                'client_id' => $client_id,
                'transaction_id' => $transaction_id,
                'amount' => $amount,
            ]);

            //update client credit
            Client::where('id',$client_id)->decrement('credit', $amount);

            $i++;
        }

        if(!$i){
            if(is_file($file)){
                unlink($file);
            }
            return $this->response(
                false,
                    11001,
                __('corrupted XLS file')
            );
        }


//        if(is_file($file)){ // remove file to empty space
//            unlink($file);
//        }
        return $this->response(
            true,
            11001,
            __('Successfully added ( :num ) Data from ( :all )',['num'=>$i,'all'=>count($spreadsheet)])
        );
    }


    function totalDues(Request $request){

        $dues = App\Models\Dues::where('status','active')->get();

        $this->viewData['due_data'] = array();

        if(count($dues) > 0){

        if($request->custom_date){ // date => custom
            //print_r($_GET);die;
            foreach ($dues as $due){
                $eloquentData = App\Models\Invoice::select(\DB::raw('sum(amount) as sum'))
                    ->whereIn('property_due_id',App\Models\PropertyDues::where('due_id',$due->id)->pluck('id'));
                 whereBetween($eloquentData,'DATE(invoices.date)',$request->date1,$request->date2);

                 $total =  $eloquentData->first()->sum;

                $this->viewData['due_data'][] = array(
                    'name' =>  $due->name,
                    'total'=>  $total ? $total : 0,
                    'filter'=> ['status'=>'paid','due_id'=>$due->id,'date1'=>$request->date1,'date2'=>$request->date2]
                );
            }

        }else{ // date => today
            foreach ($dues as $due){
                $eloquentData = App\Models\Invoice::select(\DB::raw('sum(amount) as sum'))
                    ->whereIn('property_due_id',App\Models\PropertyDues::where('due_id',$due->id)->pluck('id'));
                whereBetween($eloquentData,'DATE(invoices.date)',date('Y-m-01'),date('Y-m-31'));

                $total =  $eloquentData->first()->sum;
                $this->viewData['due_data'][] = array(
                    'name' =>  $due->name,
                    'total'=>  $total ? $total : 0,
                    'filter'=> ['status'=>'paid','due_id'=>$due->id,'date1'=>date('Y-m-1'),'date2'=>date('Y-m-31')]
                );
            }
        }
        }

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Dues Total')
        ];



        $this->viewData['pageTitle'] = __('Dues Total');

        return $this->view('report.total-dues',$this->viewData);


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
      abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request){
        abort(404);
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Requests'),
            'url'=> route('system.request.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Request'),
        ];

        $this->viewData['pageTitle'] = __('Create Request');

        return $this->view('request.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestFormRequest $request){
        abort(404);
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
        abort(404);
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
                    'text' => $requestModal->name,
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
        abort(404);
        list($requestModal,$request) = [$request,$HTTPrequest];

        /*if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }*/
//        if(!staffCan('request-manage-all') &&  $requestModal->sales_id != Auth::id() && $requestModal->created_by_staff_id != Auth::id()){
//            abort(401);
//        }
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Request'),
            'url'=> route('system.request.index')
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
        abort(404);
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
        abort(404);
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
        abort(404);
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
    public function destroy(RequestModal $requestModal)
    {
        abort(404);
        $message = __('Request deleted successfully');

        RequestParameter::where('request_id',$requestModal->id)->delete();
        $requestModal->delete();

        return $this->response(true,200,$message);
    }

}
