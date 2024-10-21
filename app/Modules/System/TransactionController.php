<?php

namespace App\Modules\System;

use App\Models\Transaction;
use Illuminate\Http\Request;
//use App\Http\Requests\TransactionFormRequest;
use Form;
use Auth;
use App;

class TransactionController extends SystemController
{

    private function createEditData(){
        $this->viewData['payment_methods'] = App\Models\PaymentMethods::get([
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

            $eloquentData = Transaction::select([
                'id',
                'client_id',
                'invoice_id',
                'service_id',
                'amount',
                'payment_method_id',
                'status',
                'created_at',
            ])->orderBy('transactions.id', 'desc');

            whereBetween($eloquentData,'DATE(transactions.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'transactions.amount',$request->amount1,$request->amount2);

            if($request->id){
                $eloquentData->where('transactions.id',$request->id);
            }

            if($request->transaction_type){
                if($request->transaction_type == 'invoice'){
                    $eloquentData->whereNull('transactions.service_id');
                }
                if($request->transaction_type == 'service'){
                    $eloquentData->whereNull('transactions.invoice_id');
                }
            }

            if($request->client_id){
                $eloquentData->where('transactions.client_id',$request->client_id);
            }

            if($request->payment_method_id){
                $eloquentData->where('transactions.payment_method_id',$request->payment_method_id);
            }

            if($request->invoice_id){
                $eloquentData->where('transactions.invoice_id',$request->invoice_id);
            }

            if($request->service_id){
                $eloquentData->where('transactions.service_id',$request->service_id);
            }

            if($request->notes){
                $eloquentData->where('transactions.notes','LIKE','%'.$request->notes.'%');
            }

            if($request->status){
                $eloquentData->where('transactions.status',$request->status);
            }


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a target="_blank" href="'.route('system.'.$data->client->type.'.show',$data->client_id).'">'.$data->client->fullname.'</a>' : '--';
                })
                ->addColumn('invoice_id',function($data){
                    return $data->invoice ? '<a target="_blank" href="'.route('system.invoice.show',$data->invoice_id).'"> #'.$data->invoice_id.'</a>' : '--';
                })
                ->addColumn('service_id',function($data){
                    return $data->service ? '<a target="_blank" href="'.route('system.service.show',$data->service_id).'">'.$data->service->{'title_'.lang()}.'</a>' : '--';
                })
                ->addColumn('amount',function($data){
                    return $data->amount ? amount($data->amount,true) : '0.00';
                })
                ->addColumn('payment_method_id',function($data){
                    return $data->payment_method ? '<a target="_blank" href="'.route('system.payment-methods.show',$data->payment_method_id).'">'.$data->payment_method->name.'</a>' : '--';
                })
                ->addColumn('status', function($data) {
                    if($data->status =='pending'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--info k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }elseif ($data->status =='fail'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }else{
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
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
                                <a class="dropdown-item" href="'.route('system.transaction.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Client'),
                __('Invoice ID'),
                __('Service/Package'),
                __('Amount Value'),
                __('Payment Method'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Transactions')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Transactions');
            }else{
                $this->viewData['pageTitle'] = __('Transactions');
            }

            $this->createEditData();

            return $this->view('transaction.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
       abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    public function show(Transaction $transaction,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Transactions'),
                'url' => route('system.transaction.index'),
            ],
            [
                'text' =>  __('Show Transaction Data'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Transaction Data');


        $this->viewData['result'] = $transaction;

        return $this->view('transaction.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction,Request $request){
        abort(404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction,Request $request)
    {
        abort(404);
    }

}
