<?php

namespace App\Modules\System;

use App\Http\Requests\PermissionGroupFormRequest;
use App\Models\{
    Invoice, Permission, Staff, PermissionGroup
};
use Illuminate\Http\Request;
use App\Http\Requests\StaffFormRequest;
use Form;
use Auth;
use Hash;
use Illuminate\Support\Collection;

class PermissionGroupsController extends SystemController
{

    public function permissions($permission=false){
        $permissions = \Illuminate\Support\Facades\File::getRequire('../app/Modules/System/Permissions.php');
        return $permission ? isset($permissions[$permission]) ? $permissions[$permission] : false : $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = PermissionGroup::select([
                'permission_groups.id',
                'permission_groups.name',
                "permission_groups.updated_at",
                \DB::raw("(SELECT COUNT(*) FROM `staff` WHERE permission_group_id = `permission_groups`.`id`) as `count`")
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }



            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('count','{{$count}}')
                ->addColumn('updated_at','{{$updated_at}}')
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.permission-group.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
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
                __('Num. Staff'),
                __('Last Update'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Staff Permission')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Staff Permission');
            }else{
                $this->viewData['pageTitle'] = __('Staff Permission');
            }

            return $this->view('permission-group.index',$this->viewData);
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
            'text'=> __('Staff Permission'),
            'url'=> route('system.permission-group.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Staff Permission'),
        ];

        $this->viewData['pageTitle'] = __('Create Staff Permission');

        $this->viewData['permissions'] = $this->permissions();

        return $this->view('permission-group.create',$this->viewData);
    }


    public function store(PermissionGroupFormRequest $request)
    {
        $permissions = array();
        $perms = recursiveFind($this->permissions(),'permissions');
        foreach($perms as $val){
            foreach($val as $key=>$oneperm){
                $permissions[$key] = $oneperm;
            }
        }

        $coll = new Collection();

        $requestData = $request->all();



        if($row = PermissionGroup::create($requestData)){
            array_map(function($oneperm) use ($permissions,$row,&$coll){
                foreach ($permissions[$oneperm] as $oneroute){
                    $coll->push(new Permission(['route_name'=>$oneroute,'permission_group_id'=>$row->id]));
                }
            },$request->all()['permissions']);
            $row->permission()->insert($coll->toArray());

            return redirect()
                ->route('system.permission-group.create')
                ->with('status', 'success')
                ->with('msg', __('Permission Group added'));
        } else{
            return redirect()
                ->route('system.permission-group.create')
                ->with('status','danger')
                ->with('msg',__('Sorry Couldn\'t add Permission Group'));
        }

    }


    public function show(PermissionGroup $permission_group)
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(PermissionGroup $permission_group)
    {
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Staff Permission'),
            'url'=> route('system.permission-group.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit Staff Permission'),
        ];

        $this->viewData['pageTitle'] = __('Edit Staff Permission');

        $this->viewData['permission_group'] = $permission_group;
        $this->viewData['permissions'] = $this->permissions();
        $this->viewData['currentpermissions'] = $permission_group->permission()->get()->pluck('route_name')->toArray();

        return $this->view('permission-group.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionGroupFormRequest $request, PermissionGroup $permission_group)
    {
        $permissions = array();
        $perms = recursiveFind($this->permissions(),'permissions');
        foreach($perms as $val){
            foreach($val as $key=>$oneperm){
                $permissions[$key] = $oneperm;
            }
        }

        $requestData = $request->all();


        if($request->only(['permissions'])['permissions'] !== null){
            $coll = new Collection();
            array_map(function($oneperm) use ($permissions,&$coll,$permission_group){
                foreach ($permissions[$oneperm] as $oneroute){
                    $coll->push(new Permission(['route_name'=> $oneroute,'permission_group_id'=> $permission_group->id]));
                }
            },$request->all()['permissions']);
        }


        if($permission_group->update($requestData)) {
            $permission_group->permission()->delete();
            if(isset($coll) && $coll->count()) {
                $permission_group->permission()->insert($coll->toArray());
            }

            return redirect()
                ->route('system.permission-group.edit',$permission_group->id)
                ->with('status','success')
                ->with('msg',__('Successfully edit Permissions Group'));
        }else{
            return redirect()
                ->route('system.permission-group.edit')
                ->with('status','success')
                ->with('msg',__('Sorry Couldn\'t Edit Permissions Group'));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff,Request $request)
    {
        return back();
    }

}
