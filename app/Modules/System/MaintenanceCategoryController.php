<?php

namespace App\Modules\System;

use App\Http\Requests\MaintenanceCategoryFormRequest;
use App\Models\MaintenanceCategory;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class MaintenanceCategoryController extends SystemController
{
    private function createEditData(){
        $this->viewData['categories'] = App\Models\MaintenanceCategory::where('parent_id',0)->get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = MaintenanceCategory::select([
                'id',
                'parent_id',
                'name_ar',
                'name_en',
                'created_at',
            ]);

            whereBetween($eloquentData,'DATE(maintenance_categories.created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('id',$request->id);
            }

            if($request->parent_id){
                $eloquentData->where('parent_id',$request->parent_id);
            }

            if($request->name){
                $eloquentData->where('maintenance_categories.name_ar','LIKE','%'.$request->name.'%')
                ->orWhere('maintenance_categories.name_en','LIKE','%'.$request->name.'%');
            }




            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('parent_id',function($data){
                    $category = getMainCategory($data->parent_id);
                    if($category){
                       // return '<a target="_blank" href="'.route('system.maintenance-category.index',['parent_id'=>$data->parent_id]).'">'.$category->{'name_'.lang()}.'</a>';
                        return '<a  href="javascript:void(0)" onclick="showSubCategories('.$data->parent_id.')">'.$category->{'name_'.lang()}.'</a>';
                    }
                    return '--';
                })
                ->addColumn('name_ar','{{$name_ar}}')
                ->addColumn('name_en','{{$name_en}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.maintenance-category.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.purpose.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Parent Category'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Maintenance Categories')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Maintenance Categories');
            }else{
                $this->viewData['pageTitle'] = __('Maintenance Categories');
            }

            $this->createEditData();

            return $this->view('maintenance-category.index',$this->viewData);
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
            'text'=> __('Maintenance Categories'),
            'url'=> route('system.maintenance-category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Maintenance Category'),
        ];

        $this->viewData['pageTitle'] = __('Create Maintenance Category');

        $this->createEditData();

        return $this->view('maintenance-category.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaintenanceCategoryFormRequest $request){
        $requestData = $request->all();

        $insertData = MaintenanceCategory::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.maintenance-category.index')
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
    public function show(Purpose $data_source,Request $request){
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(MaintenanceCategory $maintenance_category,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Maintenance Categories'),
            'url'=> route('system.maintenance-category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $maintenance_category->{'name_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Maintenance Category');
        $this->viewData['result'] = $maintenance_category;

        $this->createEditData();

        return $this->view('maintenance-category.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(MaintenanceCategoryFormRequest $request, MaintenanceCategory $maintenance_category)
    {

        $requestData = $request->all();

        $updateData = $maintenance_category->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.maintenance-category.index')
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
    public function destroy(MaintenanceCategory $maintenance_category,Request $request)
    {
        $message = __('Maintenance Category deleted successfully');
        $maintenance_category->delete();
        return $this->response(true,200,$message);
    }

}
