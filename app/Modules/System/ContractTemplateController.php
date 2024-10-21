<?php

namespace App\Modules\System;

use App\Http\Requests\ContractTemplateFormRequest;
use App\Models\ContractTemplate;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class ContractTemplateController extends SystemController
{
    private function createEditData(){
        $this->viewData['vars'] = ['renter_name'=>'%renter_name%','date_from'=>'%date_from%','date_to'=>'%date_to%','price'=>'%price%',
            'contract_type'=>'%contract_type%','insurance_price'=>'%insurance_price%','deposit_rent'=>'%deposit_rent%',
            'pay_from'=>'%pay_from%','pay_to'=>'%pay_to%','increase_value'=>'%increase_value%','increase_percentage'=>'%increase_percentage%',
            'increase_from'=>'%increase_from%','pay_every'=>'%pay_every%','pay_at'=>'%pay_at%','calendar'=>'%calendar%','limit_to_pay'=>'%limit_to_pay%','contract_date'=>'%contract_date%',
            'owner_name'=>'%owner_name%','owner_qysm'=>'%owner_qysm%','owner_gev'=>'%owner_gev%','owner_id_num'=>'%owner_id_num%','owner_address'=>'%owner_address%','renter_address'=>'%renter_address%',
            'renter_qysm'=>'%renter_qysm%','renter_gev'=>'%renter_gev%','renter_id_num'=>'%renter_id_num%','property_number'=>'%property_number%','property_address'=>'%property_address%','contract_period'=>'%contract_period%'
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  ContractTemplate::select([
                'id',
                'name',
                'staff_id',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData,'DATE(contract_templates.created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('contract_templates.id',$request->id);
            }

            if($request->staff_id){
                $eloquentData->where('contract_templates.staff_id',$request->staff_id);
            }

            if($request->name){
                $eloquentData->where('contract_templates.name','LIKE','%'.$request->name.'%');
            }

            if($request->temp_content){
                $eloquentData->where('contract_templates.template_content','LIKE','%'.$request->temp_content.'%');
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('staff_id', function($data){

                    return $data->staff ? $data->staff->fullname : '--';
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
                                <a class="dropdown-item" href="'.route('system.contract-template.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.contract-template.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.contract-template.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                              
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
                __('Staff'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Contract Templates')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Contract Templates');
            }else{
                $this->viewData['pageTitle'] = __('Contract Templates');
            }

            return $this->view('contract-template.index',$this->viewData);
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
            'text'=> __('Contract Templates'),
            'url'=> route('system.contract-template.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Contract Template'),
        ];

        $this->viewData['pageTitle'] = __('Create Contract Template');

        $this->createEditData();

        return $this->view('contract-template.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractTemplateFormRequest $request){

        $tempDataInsert = [
            'name'=>$request->name,
            'template_content'=>$request->temp_content ? $request->temp_content : '',
            'staff_id'=> Auth::id(),
        ];


        $insertData = ContractTemplate::create($tempDataInsert);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.contract-template.show',$insertData->id)
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
    public function show(ContractTemplate $contract_template,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Contract Templates'),
                'url' => route('system.contract-template.index'),
            ],
            [
                'text' => $contract_template->name,
            ]
        ];

        $this->viewData['pageTitle'] =  $contract_template->name;

        $this->viewData['result'] = $contract_template;

        return $this->view('contract-template.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(ContractTemplate $contract_template,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Contract Templates'),
            'url'=> route('system.contract-template.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $contract_template->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Contract Template Data');
        $this->viewData['result'] = $contract_template;

        $this->createEditData();

        return $this->view('contract-template.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ContractTemplateFormRequest $request, ContractTemplate $contract_template)
    {

        $tempDataUpdate = [
            'name'=>$request->name,
            'template_content'=>$request->temp_content ? $request->temp_content : '',
        ];


        $updateData = $contract_template->update($tempDataUpdate);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.contract-template.show',$contract_template->id)
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
    public function destroy(ContractTemplate $contract_template)
    {
        $message = __('Data deleted successfully');

        $contract_template->delete();

        return $this->response(true,200,$message);
    }



}