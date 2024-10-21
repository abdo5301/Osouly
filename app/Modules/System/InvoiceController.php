<?php

namespace App\Modules\System;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Requests\InvoiceFormRequest;
use Form;
use Auth;
use App;

class InvoiceController extends SystemController
{

    private function createEditData(){
        $this->viewData['dues'] = App\Models\Dues::get(['id','name']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Invoice::select([
                'id',
                'property_id',
                'property_due_id',
//                'installment_id',
                'client_id',
                'amount',
                'date',
                'status',
                'created_at',
            ])->orderBy('invoices.id', 'desc');

            whereBetween($eloquentData,'DATE(invoices.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'invoices.amount',$request->amount1,$request->amount2);
            whereBetween($eloquentData,'DATE(invoices.date)',$request->date1,$request->date2);

            if($request->id){
                $eloquentData->where('invoices.id',$request->id);
            }

            if($request->property_id){
                $eloquentData->where('invoices.property_id',$request->property_id);
            }

            if($request->client_id){
                $eloquentData->where('invoices.client_id',$request->client_id);
            }

            if($request->owner_id){
                $eloquentData->where('invoices.owner_id',$request->owner_id);
            }

            if($request->property_due_id){
                $eloquentData->where('invoices.property_due_id',$request->property_due_id);
            }

            if($request->due_id){
                $eloquentData->whereIn('invoices.property_due_id',App\Models\PropertyDues::where('due_id',$request->due_id)->pluck('id'));
            }

            if($request->installment_id){
                $eloquentData->where('invoices.installment_id',$request->installment_id);
            }

            if($request->notes){
                $eloquentData->where('invoices.notes','LIKE','%'.$request->notes.'%');
            }

            if($request->status){
                $eloquentData = $eloquentData->where('invoices.status',$request->status);
            }


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id',function($data){
                    return $data->property_id ? '<a target="_blank" href="'.route('system.property.show',$data->property_id).'"> #'.$data->property_id.'</a>' : '--';
                })
                ->addColumn('property_due_id',function($data){
                    return $data->property_due_id ? '<a target="_blank" href="'.route('system.invoice.show',$data->property_due_id).'">'.@$data->property_due->dues->name.'('.$data->property_due_id.')'.'</a>' : '--';
                })
//                ->addColumn('installment_id',function($data){
//                    return $data->installment ? '# '.$data->installment_id : '--';
//                })
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a target="_blank" href="'.route('system.'.$data->client->type.'.show',$data->client_id).'">'.$data->client->fullname.'</a>' : '--';
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
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Property ID'),
                __('Due Name'),
//                __('Installment ID'),
                __('Client'),
                __('Amount Value'),
                __('Date'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Invoices')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Invoices');
            }else{
                $this->viewData['pageTitle'] = __('Invoices');
            }

            $this->createEditData();

            return $this->view('invoice.index',$this->viewData);
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
            'text'=> __('Invoices'),
            'url'=> route('system.invoice.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Invoice'),
        ];

        $this->viewData['pageTitle'] = __('Create Invoice');

        $this->createEditData();

        return $this->view('invoice.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceFormRequest $request){
        $requestData = $request->all();
        if($request->notes){
            $requestData['notes'] = uploadImagesByTextEditor($request->notes);
        }

        $insertData = Invoice::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.invoice.index')
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

    public function show(Invoice $invoice,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Invoices'),
                'url' => route('system.invoice.index'),
            ],
            [
                'text' =>  __('Show Invoice Data'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Invoice Data');


        $this->viewData['result'] = $invoice;

        return $this->view('invoice.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Invoices'),
            'url'=> route('system.invoice.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> '# '.$invoice->id]),
        ];

        $this->viewData['pageTitle'] = __('Edit Invoice');
        $this->viewData['result'] = $invoice;
        $this->createEditData();

        return $this->view('invoice.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceFormRequest $request, Invoice $invoice)
    {
//        $invoice = Invoice::find($id);
//        if(!$invoice){
//            abort(404);
//        }

        $requestData = $request->all();
        if($request->notes){
            $requestData['notes'] = uploadImagesByTextEditor($request->notes);
        }

        $updateData = $invoice->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.invoice.show',$invoice->id)
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
    public function destroy(Invoice $invoice,Request $request)
    {
        $message = __('Invoice deleted successfully');

        $invoice->delete();

        return $this->response(true,200,$message);
    }

}
