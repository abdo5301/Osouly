<?php

namespace App\Modules\System;

use App\Http\Requests\PaymentMethodFormRequest;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class PaymentMethodController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PaymentMethods::select([
                'id',
                'name',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.payment-methods.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.payment-methods.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.payment-methods.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  
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
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Payment Methods')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Payment Methods');
            }else{
                $this->viewData['pageTitle'] = __('Payment Methods');
            }

            return $this->view('payment-methods.index',$this->viewData);
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
            'text'=> __('Payment Methods'),
            'url'=> route('system.payment-methods.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Payment Method'),
        ];

        $this->viewData['pageTitle'] = __('Create Payment Method');

        return $this->view('payment-methods.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodFormRequest $request){
        $requestData = $request->all();

        $arr = [];
        foreach ($requestData['field_name'] as $key => $value) {
            $arr[$value] = $requestData['field_value'][$key];

        }
        $requestData['parameters'] = json_encode($arr);

        $insertData = PaymentMethods::create($requestData);

        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.payment-methods.index')
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
    public function show(PaymentMethods $payment_method,Request $request){
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Payment Methods'),
                'url' => route('system.payment-methods.index'),
            ],
            [
                'text' => __('Show Payment Method Data'),
            ]
        ];

        $this->viewData['pageTitle'] = __('Show Payment Method Data');

        $this->viewData['result'] = $payment_method;
        $this->viewData['parameters'] = !empty($payment_method->parameters) ? json_decode($payment_method->parameters) : array();


        return $this->view('payment-methods.show', $this->viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMethods $payment_method,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Payment Methods'),
            'url'=> route('system.payment-methods.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $payment_method->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Payment Method');
        $this->viewData['result'] = $payment_method;
        $this->viewData['parameters'] = !empty($payment_method->parameters) ? json_decode($payment_method->parameters) : array();

        return $this->view('payment-methods.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethodFormRequest $request, PaymentMethods $payment_method)
    {

        $requestData = $request->all();

        $arr = [];
        foreach ($requestData['field_name'] as $key => $value) {
            $arr[$value] = $requestData['field_value'][$key];

        }
        $requestData['parameters'] = json_encode($arr);

        $updateData = $payment_method->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.payment-methods.index')
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
    public function destroy(PaymentMethods $payment_method)
    {
        $message = __('Payment Method deleted successfully');
        //if(!empty($payment_methods->pay()))
         //   $payment_methods->pay()->delete();

        $payment_method->delete();

        return $this->response(true,200,$message);
    }

}
