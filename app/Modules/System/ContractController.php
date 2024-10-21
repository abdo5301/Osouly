<?php

namespace App\Modules\System;

use App\Http\Requests\ContractFormRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class ContractController extends SystemController
{
    private function createEditData(){
        $this->viewData['vars'] = ['renter_name'=>'%renter_name%','date_from'=>'%date_from%','date_to'=>'%date_to%','price'=>'%price%',
            'contract_type'=>'%contract_type%','insurance_price'=>'%insurance_price%','deposit_rent'=>'%deposit_rent%',
            'pay_from'=>'%pay_from%','pay_to'=>'%pay_to%','increase_value'=>'%increase_value%','increase_percentage'=>'%increase_percentage%',
            'increase_from'=>'%increase_from%','pay_every'=>'%pay_every%','pay_at'=>'%pay_at%','calendar'=>'%calendar%','limit_to_pay'=>'%limit_to_pay%','contract_date'=>'%contract_date%',
            'owner_name'=>'%owner_name%','owner_qysm'=>'%owner_qysm%','owner_gev'=>'%owner_gev%','owner_id_num'=>'%owner_id_num%','owner_address'=>'%owner_address%','renter_address'=>'%renter_address%',
            'renter_qysm'=>'%renter_qysm%','renter_gev'=>'%renter_gev%','renter_id_num'=>'%renter_id_num%','property_number'=>'%property_number%','property_address'=>'%property_address%','contract_period'=>'%contract_period%'
        ];
        $this->viewData['contract_templates'] = App\Models\ContractTemplate::get(['id','name']);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Contract::select([
                'id',
                'property_id',
                'renter_id',
                'contract_type',
                'price',
                'insurance_price',
                'deposit_rent',
                'status',
                'created_at'
            ])->orderBy('id', 'desc');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData,'DATE(contract.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'contract.price',$request->price1,$request->price2);
            whereBetween($eloquentData,'contract.insurance_price',$request->insurance_price1,$request->insurance_price2);
            whereBetween($eloquentData,'contract.deposit_rent',$request->deposit_rent1,$request->deposit_rent2);

            if($request->id){
                $eloquentData->where('contract.id',$request->id);
            }

            if($request->property_id){
                $eloquentData->where('contract.property_id',$request->property_id);
            }

            if($request->renter_id){
                $eloquentData->where('contract.renter_id',$request->renter_id);
            }


            if($request->contract_type){
                $eloquentData->where('contract.contract_type',$request->contract_type);
            }

            if($request->status){
                $eloquentData->where('contract.status',$request->status);
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_id', function($data){
                    return $data->property ? '<a  href="'.route('system.property.show',$data->property_id).'" target="_blank" >'.$data->property_id.'</a>' : '--';
                })
                ->addColumn('renter_id', function($data){
                    return $data->renter ? '<a  href="'.route('system.renter.show',$data->renter_id).'" target="_blank" >'.$data->renter->fullname.'</a>' : '--';
                })

                ->addColumn('contract_type', function($data) {
                    if ($data->contract_type == 'month') {
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __('Month') . '</span>';
                    } elseif ($data->contract_type == 'year'){
                        return '<span  class="k-badge  k-badge--info k-badge--inline k-badge--pill">' . __('Year') . '</span>';
                    }else{
                        return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('Day').'</span>';
                    }
                })
                ->addColumn('price',function($data){
                    return $data->price ? number_format($data->price) : '0.00';
                })
                ->addColumn('insurance_price',function($data){
                    return $data->insurance_price ? number_format($data->insurance_price) : '0.00';
                })
                ->addColumn('deposit_rent',function($data){
                    return $data->deposit_rent ? number_format($data->deposit_rent) : '0.00';
                })

                ->addColumn('status', function($data) {
                    if ($data->status == 'active') {
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __('Active') . '</span>';
                    } elseif ($data->status == 'pendding'){
                        return '<span  class="k-badge  k-badge--info k-badge--inline k-badge--pill">' . __('Pending') . '</span>';
                    }else{
                        return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('Canceled').'</span>';
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
                                <a class="dropdown-item" href="'.route('system.contract.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <!--<a class="dropdown-item" href="'.route('system.contract.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a> -->
                               <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.contract.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                              
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
                __('Renter'),
                __('Contract Type'),
                __('Price'),
                __('Insurance Price'),
                __('Deposit Rent'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Contracts')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Contracts');
            }else{
                $this->viewData['pageTitle'] = __('Contracts');
            }

            $this->createEditData();

            return $this->view('contract.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Contracts'),
            'url'=> route('system.contract.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Contract'),
        ];

        $this->viewData['pageTitle'] = __('Create Contract');

        $this->createEditData();

        return $this->view('contract.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractFormRequest $request){

        $contractDataInsert = $request->all();
        $contract_template_id = $contractDataInsert['contract_template_id'];
        unset($contractDataInsert['contract_template_id']);

        $contract_template = App\Models\ContractTemplate::find($contract_template_id);
        if(!$contract_template){
            $contractDataInsert['contract_content'] = ' لم يتم العثور على قالب العقد !';
        }else{
            $contractDataInsert['contract_content'] = $contract_template->template_content;
        }

        $insertData = Contract::create($contractDataInsert);

        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.contract.show',$insertData->id)
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
    public function show(Contract $contract,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Contracts'),
                'url' => route('system.contract.index'),
            ],
            [
                'text' =>  __('Show Contract Data'),
            ]
        ];

        $this->viewData['pageTitle'] =  __('Show Contract Data');


        $contract_content = $contract->contract_content;

        if(strpos($contract_content, '{{') !== false){

            $vars =  ['renter_name'=>'%renter_name%','date_from'=>'%date_from%','date_to'=>'%date_to%','price'=>'%price%',
                'contract_type'=>'%contract_type%','insurance_price'=>'%insurance_price%','deposit_rent'=>'%deposit_rent%',
                'pay_from'=>'%pay_from%','pay_to'=>'%pay_to%','increase_value'=>'%increase_value%','increase_percentage'=>'%increase_percentage%',
                'increase_from'=>'%increase_from%','pay_every'=>'%pay_every%','pay_at'=>'%pay_at%','calendar'=>'%calendar%','limit_to_pay'=>'%limit_to_pay%','contract_date'=>'%contract_date%',
                'owner_name'=>'%owner_name%','owner_qysm'=>'%owner_qysm%','owner_gev'=>'%owner_gev%','owner_id_num'=>'%owner_id_num%','owner_address'=>'%owner_address%','renter_address'=>'%renter_address%',
                'renter_qysm'=>'%renter_qysm%','renter_gev'=>'%renter_gev%','renter_id_num'=>'%renter_id_num%','property_number'=>'%property_number%','property_address'=>'%property_address%','contract_period'=>'%contract_period%'
            ];

            foreach ($vars as $key => $value) {
                //replace vars here
                if ($value == '{{contract_date}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,date('d-m-Y',strtotime($contract->created_at)),$contract_content);
                }

                if ($value == '{{pay_from}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,date('d-m-Y',strtotime($contract->pay_from)),$contract_content);
                }

                if ($value == '{{pay_to}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,date('d-m-Y',strtotime($contract->pay_to)),$contract_content);
                }

                if ($value == '{{increase_value}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,amount($contract->increase_value,true),$contract_content);
                }

                if ($value == '{{increase_percentage}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,$contract->increase_percentage.' %',$contract_content);
                }

                if ($value == '{{increase_from}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,date('d-m-Y',strtotime($contract->increase_from)),$contract_content);
                }

                if ($value == '{{pay_every}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,$contract->pay_every,$contract_content);
                }

                if ($value == '{{pay_at}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,__(ucwords($contract->pay_at)),$contract_content);
                }

                if ($value == '{{calendar}}' && strpos($contract_content, $value) !== false){
                    if($contract->calendar == 'm'){
                        $contract_content = str_replace($value,__('Gregorian'),$contract_content);
                    }else{
                        $contract_content = str_replace($value,__('Hijri'),$contract_content);
                    }
                }

                if ($value == '{{limit_to_pay}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,$contract->limit_to_pay,$contract_content);
                }

                if ($value == '{{renter_id}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,$contract->renter->fullname,$contract_content);
                }

                if ($value == '{{date_from}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,date('d-m-Y',strtotime($contract->date_from)),$contract_content);
                }

                if ($value == '{{date_to}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,date('d-m-Y',strtotime($contract->date_to)),$contract_content);
                }

                if ($value == '{{price}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,amount($contract->price,true),$contract_content);
                }

                if ($value == '{{insurance_price}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,amount($contract->insurance_price,true),$contract_content);
                }

                if ($value == '{{deposit_rent}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,amount($contract->deposit_rent,true),$contract_content);
                }

                if ($value == '{{contract_type}}' && strpos($contract_content, $value) !== false){
                    $contract_content = str_replace($value,__(ucwords($contract->contract_type)),$contract_content);
                }

            }
        }

        $this->viewData['result'] = $contract;

        $this->viewData['contract_content'] = $contract_content;

        return $this->view('contract.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Contracts'),
            'url'=> route('system.contract.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit Contract Data'),
        ];

        $this->viewData['pageTitle'] = __('Edit Contract Data');
        $this->viewData['result'] = $contract;

        $this->createEditData();

        return $this->view('contract.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ContractFormRequest $request, Contract $contract)
    {

        $contractDataUpdate = $request->all();

        $updateData = $contract->update($contractDataUpdate);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.contract.show',$contract->id)
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
    public function destroy(Contract $contract)
    {
        $message = __('Data deleted successfully');

        $contract->delete();

        return $this->response(true,200,$message);
    }



}