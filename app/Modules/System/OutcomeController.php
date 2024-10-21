<?php

namespace App\Modules\System;

use App\Http\Requests\PayFormRequest;
use App\Models\Pay;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class OutcomeController extends SystemController
{

    private function createEditData(){
        $this->viewData['payment_methods'] = App\Models\PaymentMethods::get([
            'id',
            'name'
        ]);

        $this->viewData['reasons'] = App\Models\OutcomeReason::get([
            'id',
            'name'
        ]);

        $this->viewData['lockers'] = App\Models\Locker::get([
            'id',
            'name'
        ]);

        //$this->viewData['randKey'] = md5(rand().time());

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Pay::select([
                'id',
                'price',
                'sign_id',
                'locker_id',
                'date',
                'created_at',
            ])->where('sign_type','App\Models\OutcomeReason')->orderByDesc('id');

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


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('price', function($data){
                    return $data->price ? number_format($data->price) : '0.00';
                })
                ->addColumn('sign_id', function($data){
                    $reason = getPayReason($data->sign_id,'outcome');
                    return $reason ? '<a href="javascript:void(0)" onclick="showReasonPays('.$reason->id.')">'.$reason->name.'</a>' : '--';
                })
                ->addColumn('locker_id', function($data){
                    return $data->locker ? $data->locker->name : '--';
                })
                ->addColumn('date', function($data){
                    return $data->date ? date('Y-m-d',strtotime($data->date))  : '--';
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
                                <a class="dropdown-item" href="'.route('system.outcome.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <!-- <a class="dropdown-item" href="'.route('system.outcome.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a> -->
                                <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.outcome.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Outcome Price'),
                __('Outcome Reason'),
                __('Locker'),
                __('Date'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Outcomes')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Outcomes');
            }else{
                $this->viewData['pageTitle'] = __('Outcomes');
            }

            $this->createEditData();

            return $this->view('outcome.index',$this->viewData);
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
            'text'=> __('Outcomes'),
            'url'=> route('system.outcome.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Outcome'),
        ];

        $this->viewData['pageTitle'] = __('Create Outcome');

        $this->createEditData();

        return $this->view('outcome.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayFormRequest $request){
        $requestData = array(
            'sign_type'  => 'App\Models\OutcomeReason',
            'sign_id'  =>  $request->reason_id,
            'client_id' => $request->client_id ? $request->client_id : null,
            'staff_id' =>  $request->staff_id ? $request->staff_id : Auth::id(),
            'locker_id' =>  $request->locker_id,
            'payment_method_id' =>  $request->payment_method_id,
            'price' =>  $request->price,
            'note' =>  $request->note,
            'date' =>  $request->date,
        );

        $insertData = Pay::create($requestData);
        if($insertData){

            $new_pay = Pay::find($insertData->id);
            if($new_pay)
               $new_pay->locker()->decrement('amount', $request->price);

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.outcome.show',$insertData->id)
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

        $outcome = Pay::find($id);
        if(!$outcome){
            abort(404);
        }

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Outcomes'),
                'url' => route('system.outcome.index'),
            ],
            [
                'text' => __('Show Outcome Data'),
            ]
        ];

        $this->viewData['pageTitle'] = __('Show Outcome Data');

        $this->viewData['result'] = $outcome;

        return $this->view('outcome.show', $this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request){

        abort(404);

        $outcome = Pay::find($id);
        if(!$outcome){
            abort(404);
        }

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Outcomes'),
            'url'=> route('system.outcome.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit Outcome Data'),
        ];

        $this->viewData['pageTitle'] = __('Edit Outcome Data');
        $this->viewData['result'] = $outcome;

        $this->createEditData();

        return $this->view('outcome.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PayFormRequest $request, $id)
    {

        $outcome = Pay::find($id);
        if(!$outcome){
            abort(404);
        }

        $outcome->locker()->increment('amount', $outcome->price);

        $requestData = array(
            'sign_type'  => 'App\Models\OutcomeReason',
            'sign_id'  =>  $request->reason_id,
            'client_id' => $request->client_id ? $request->client_id : null,
            'staff_id' =>  $request->staff_id ? $request->staff_id : Auth::id(),
            'locker_id' =>  $request->locker_id,
            'payment_method_id' =>  $request->payment_method_id,
            'price' =>  $request->price,
            'note' =>  $request->note,
            'date' =>  $request->date,
        );

        $updateData = $outcome->update($requestData);

        if($updateData){

            $outcome->locker()->decrement('amount', $request->price);

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.outcome.show',$outcome->id)
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
    public function destroy($id)
    {
        $outcome = Pay::find($id);
        if(!$outcome){
            abort(404);
        }

        $message = __('Outcome deleted successfully');

        $outcome->locker()->increment('amount', $outcome->price);

        $outcome->delete();

        return $this->response(true,200,$message);
    }

}
