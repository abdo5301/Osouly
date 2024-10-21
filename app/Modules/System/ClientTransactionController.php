<?php

namespace App\Modules\System;

use App\Models\ClientTransaction;
use Illuminate\Http\Request;
use App\Http\Requests\ClientTransactionFormRequest;
use Form;
use Auth;
use App;

class ClientTransactionController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = ClientTransaction::select([
                'id',
                'client_id',
                'transaction_id',
                'amount',
                'type',
                'created_at',
            ])->orderBy('client_transactions.id', 'desc');

            whereBetween($eloquentData,'DATE(client_transactions.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'client_transactions.amount',$request->amount1,$request->amount2);

            if($request->id){
                $eloquentData->where('client_transactions.id',$request->id);
            }

            if($request->client_id){
                $eloquentData->where('client_transactions.client_id',$request->client_id);
            }

            if($request->transaction_id){
                $eloquentData->where('client_transactions.transaction_id',$request->transaction_id);
            }

            if($request->type){
                $eloquentData->where('client_transactions.type',$request->type);
            }

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a target="_blank" href="'.route('system.'.$data->client->type.'.show',$data->client_id).'">'.$data->client->fullname.'</a>' : '--';
                })
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
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Client'),
                __('Transaction ID'),
                __('Amount Value'),
                __('Type'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Client Transactions')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Client Transactions');
            }else{
                $this->viewData['pageTitle'] = __('Client Transactions');
            }

            return $this->view('client-transaction.index',$this->viewData);
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
            'text'=> __('Client Transactions'),
            'url'=> route('system.client-transaction.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Client Transaction'),
        ];

        $this->viewData['pageTitle'] = __('Create Client Transaction');

        return $this->view('client-transaction.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientTransactionFormRequest $request){
        $requestData = $request->all();

        $insertData = ClientTransaction::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.client-transaction.index')
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

    public function show(ClientTransaction $client_transaction,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Client Transactions'),
                'url' => route('system.client-transaction.index'),
            ],
            [
                'text' =>  __('Show Invoice Data'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Client Transaction Data');


        $this->viewData['result'] = $client_transaction;

        return $this->view('client-transaction.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientTransaction $client_transaction,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Client Transactions'),
            'url'=> route('system.client-transaction.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> '# '.$client_transaction->id]),
        ];

        $this->viewData['pageTitle'] = __('Edit Client Transaction');
        $this->viewData['result'] = $client_transaction;

        return $this->view('client-transaction.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ClientTransactionFormRequest $request, ClientTransaction $client_transaction)
    {

        $requestData = $request->all();

        $updateData = $client_transaction->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.client-transaction.show',$client_transaction->id)
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
    public function destroy(ClientTransaction $client_transaction,Request $request)
    {
        $message = __('Client Transaction deleted successfully');

        $client_transaction->delete();

        return $this->response(true,200,$message);
    }

}
