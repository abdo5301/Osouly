<?php

namespace App\Modules\System;

use App\Http\Requests\LockerFormRequest;
use App\Models\Locker;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Illuminate\Support\Facades\DB;

class LockerController extends SystemController
{


    private function createEditData(){
        $this->viewData['payment_methods'] = App\Models\PaymentMethods::get([
            'id',
            'name'
        ]);

        $this->viewData['income_reasons'] = App\Models\IncomeReason::get([
            'id',
            'name'
        ]);

        $this->viewData['outcome_reasons'] = App\Models\OutcomeReason::get([
            'id',
            'name'
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Locker::select([
                'id',
                'name',
                'amount',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('amount', function($data){
                    return $data->amount ? number_format($data->amount) : 00.00;
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
                                <a class="dropdown-item" href="'.route('system.locker.show',$data->id).'"><i class="la la-search-plus"></i> '.__('Log').'</a>
                                <a class="dropdown-item" href="'.route('system.locker.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.locker.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  
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
                __('Amount'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Lockers')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Lockers');
            }else{
                $this->viewData['pageTitle'] = __('Lockers');
            }

            return $this->view('locker.index',$this->viewData);
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
            'text'=> __('Lockers'),
            'url'=> route('system.locker.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Locker'),
        ];

        $this->viewData['pageTitle'] = __('Create Locker');

        return $this->view('locker.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LockerFormRequest $request){
        $requestData = $request->all();

        $insertData = Locker::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.locker.index')
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
    public function show(Locker $locker,Request $request){

        //print_r($locker->pay());die;
        if(!$locker || !$locker->id)
            abort(404);


        if($request->isDataTable){

            $eloquentData = App\Models\Pay::select([
                'id',
                'price',
                DB::raw('locker_id as locker_total'),
                'sign_type',
                'sign_id',
                'client_id',
                'staff_id',
                'date',
//                'created_at',
            ])->where('locker_id',$locker->id)->orderByDesc('id');


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData,'DATE(pay.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'DATE(pay.date)',$request->date1,$request->date2);
            whereBetween($eloquentData,'pay.price',$request->price1,$request->price2);

            if($request->id){
                $eloquentData->where('pay.id',$request->id);
            }

            if($request->payment_method_id){
                $eloquentData->where('pay.payment_method_id',$request->payment_method_id);
            }

            if($request->type){
                $eloquentData->where('pay.sign_type',$request->type);
            }

            if($request->reason_id){
                $eloquentData->where('pay.sign_id',$request->reason_id);
            }

            if($request->locker_id){
                $eloquentData->where('pay.locker_id',$request->locker_id);
            }


            if($request->client_id){
                $eloquentData->where('pay.client_id',$request->client_id);
            }

            if($request->staff_id){
                $eloquentData->where('pay.staff_id',$request->staff_id);
            }

            if($request->notes){
                $eloquentData->where('pay.note','LIKE','%'.$request->notes.'%');
            }


            if($request->downloadExcel){
                return exportXLS(
                    __('Locker Log'). ' ( ' .$locker->name. ' )',
                    [
                        __('ID'),
                        __('Amount Value'),
                        __('Amount Type'),
                        __('Amount Reason'),
                        __('Payment Method'),
                        __('Client'),
                        __('Staff'),
                        __('Date'),
                        __('notes'),
//                        __('Created At'),
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'price'=> function($data){
                            return $data->price ? number_format($data->price) : '--';
                        },
                        'sign_type'=> function($data){
                        if($data->sign_type == 'App\Models\IncomeReason'){
                            return  __('Income');
                        }elseif($data->sign_type == 'App\Models\OutcomeReason'){
                            return  __('Outcome');
                        }else{
                            return '--';
                        }
                        },
                        'sign_id'=> function($data){
                            return $data->sign ? $data->sign->name : '--';
                        },
                        'payment_method_id'=> function($data){
                            return $data->paymentMethod ? $data->paymentMethod->name : '--';
                        },
                        'client'=> function($data){
                            return $data->client ? $data->client->fullname : '--';
                        },
                        'staff'=> function($data){
                            return $data->staff ? $data->staff->fullname : '--';
                        },
                        'date'=> function($data){
                            return $data->date ? date('Y-m-d',strtotime($data->date)) : '--';
                        },
                        'note'=> function($data){
                            return $data->note ? $data->note : '--';
                        },
//                        'created_at'=> function($data){
//                            return $data->created_at->format('Y-m-d h:i A');
//                        },
                    ]
                );
            }

          //$tt =   lockerTotalLimited(1 , 4);
           // print_r($tt);die;

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('price',function($data){
                    return amount($data->price,true);
                })
                ->addColumn('locker_total',function($data){
                    $total = lockerTotalLimited($data->locker_total,$data->id);
                    return amount($total,true);
                })
                ->addColumn('sign_type',function($data){
                    if($data->sign_type == 'App\Models\IncomeReason'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Income').'</span>';
                    }elseif($data->sign_type == 'App\Models\OutcomeReason'){
                        return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('Outcome').'</span>';
                    }else{
                        return '--';
                    }
                })
                ->addColumn('sign_id',function($data){
                    return $data->sign ? $data->sign->name : '--';
                })
                ->addColumn('client_id',function($data){
                    return $data->client ? $data->client->fullname : '--';
                })
                ->addColumn('staff_id',function($data){
                    return $data->staff ? '<a target="_blank" href="'.route('system.staff.show',$data->staff_id).'">'.$data->staff->fullname.'</a>' : '--';
                })
                ->addColumn('date', function($data){
                    return $data->date ?  date('Y-m-d',strtotime($data->date)) : '--';
                })

                ->addColumn('action', function($data){
                    if($data->sign_type == 'App\Models\IncomeReason'){
                        $view_link =  route('system.income.show',$data->id);
                    }elseif($data->sign_type == 'App\Models\OutcomeReason'){
                        $view_link =  route('system.outcome.show',$data->id);
                    }else{
                        $view_link =  '#';
                    }
                    return ' <a class="dropdown-item" href="'.$view_link.'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Amount Value'),
                __('Total'),
                __('Amount Type'),
                __('Amount Reason'),
                __('Client'),
                __('Staff'),
                __('Date'),
//                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Lockers'),
                'url'=> route('system.locker.index')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Locker Log'). ' ( ' .$locker->name. ' )'
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Locker Log Data');
            }else{
                $this->viewData['pageTitle'] = __('Locker Log'). ' ( ' .$locker->name. ' )';
            }

            $this->viewData['result'] = $locker;

            $this->createEditData();

            return $this->view('locker.show',$this->viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Locker $locker,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Lockers'),
            'url'=> route('system.locker.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $locker->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Locker');
        $this->viewData['result'] = $locker;

        return $this->view('locker.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(LockerFormRequest $request, Locker $locker)
    {

        $requestData = $request->all();

        $updateData = $locker->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.locker.index')
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
    public function destroy(Locker $locker)
    {
        $message = __('Locker deleted successfully');
        if(!empty($locker->pay()))
        $locker->pay()->delete();

        $locker->delete();

        return $this->response(true,200,$message);
    }

}
