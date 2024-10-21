<?php

namespace App\Modules\System;

use App\Models\FacilityCompanies;
use Illuminate\Http\Request;
use App\Http\Requests\FacilityCompaniesFormRequest;
use Form;
use Auth;
use App;

class FacilityCompaniesController extends SystemController
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

            $eloquentData = FacilityCompanies::select([
                'id',
                'name',
                'due_id',
                'company_pay_id',
                'created_at',
            ]);

            whereBetween($eloquentData,'DATE(facility_companies.created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('facility_companies.id',$request->id);
            }

            if($request->due_id){
                $eloquentData->where('facility_companies.due_id',$request->due_id);
            }

            if($request->company_pay_id){
                $eloquentData->where('facility_companies.company_pay_id',$request->company_pay_id);
            }

            if($request->name){
                $eloquentData->where('facility_companies.name','LIKE','%'.$request->name.'%');
            }


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('due_id',function($data){
                    return $data->dues ? '<a target="_blank" href="'.route('system.dues.show',$data->due_id).'">'.$data->dues->name.'</a>' : '--';
                })
                ->addColumn('company_pay_id',function($data){
                    return  $data->company_pay_id ? $data->company_pay_id : '--';
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
                                <a class="dropdown-item" href="'.route('system.facility-companies.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.facility-companies.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.facility-companies.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
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
                __('Due Name'),
                __('Company Pay ID'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Facility Companies')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Facility Companies');
            }else{
                $this->viewData['pageTitle'] = __('Facility Companies');
            }

            $this->createEditData();

            return $this->view('facility-companies.index',$this->viewData);
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
            'text'=> __('Facility Companies'),
            'url'=> route('system.facility-companies.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Facility Company'),
        ];

        $this->viewData['pageTitle'] = __('Create Facility Company');

        $this->createEditData();

        return $this->view('facility-companies.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacilityCompaniesFormRequest $request){
        $requestData = $request->all();
        if(!empty($requestData['area_ids'])){
            $requestData['area_ids'] = implode(',',$requestData['area_ids']);
        }

        $insertData = FacilityCompanies::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.facility-companies.index')
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

    public function show(FacilityCompanies $facility_company,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Facility Companies'),
                'url' => route('system.facility-companies.index'),
            ],
            [
                'text' =>  $facility_company->name,
            ]
        ];

        $this->viewData['pageTitle'] = $facility_company->name;


        $this->viewData['result'] = $facility_company;

        return $this->view('facility-companies.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(FacilityCompanies $facility_company,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Facility Companies'),
            'url'=> route('system.facility-companies.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $facility_company->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Facility Company');
        $this->viewData['result'] = $facility_company;
        $this->createEditData();

        return $this->view('facility-companies.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(FacilityCompaniesFormRequest $request, FacilityCompanies $facility_company)
    {

        $requestData = $request->all();
        if(!empty($requestData['area_ids'])){
            $requestData['area_ids'] = implode(',',$requestData['area_ids']);
        }
        $updateData = $facility_company->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.facility-companies.show',$facility_company->id)
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
    public function destroy(FacilityCompanies $facility_company,Request $request)
    {
        $message = __('Facility Company deleted successfully');

        $facility_company->delete();

        return $this->response(true,200,$message);
    }

}
