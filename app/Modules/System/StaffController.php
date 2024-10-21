<?php

namespace App\Modules\System;

use App\Models\{Staff,PermissionGroup};
use Illuminate\Http\Request;
use App\Http\Requests\StaffFormRequest;
use Form;
use Auth;
use Spatie\Activitylog\Models\Activity;

class StaffController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Staff::select([
                'id',
                'firstname',
                'mobile',
                'status',
                'permission_group_id',
            ]);
               // ->with('permission_group');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            /*
             * Start handling filter
             */

            whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('id','=',$request->id);
            }

            if($request->name){
                $eloquentData->where(function($query) use ($request){
                    $query->where('firstname','LIKE','%'.$request->name.'%')
                        ->orWhere('lastname','LIKE','%'.$request->name.'%');
                });
            }

            if($request->email){
                $eloquentData->where('email','LIKE','%'.$request->email.'%');
            }

            if($request->mobile){
                $eloquentData->where('mobile','LIKE','%'.$request->mobile.'%');
            }

            if($request->gender){
                $eloquentData->where('gender','=',$request->gender);
            }

            if($request->job_title){
                $eloquentData->where('job_title','LIKE','%'.$request->job_title.'%');
            }

            if($request->status){
                $eloquentData->where('status','=',$request->status);
            }

            if($request->permission_group_id){
                $eloquentData->where('permission_group_id','=',$request->permission_group_id);
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('firstname', function($data){
                    return $data->firstname;
                })
                ->addColumn('mobile', function($data){
                    return '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })
                ->addColumn('permission_group_id', function($data){
                    return $data->permission_group->name;
                })
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('In-Active').'</span>';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.staff.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.staff.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.staff.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
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
                __('Mobile'),
                __('Permission Group'),
                __('Status'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Staff')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Staff');
            }else{
                $this->viewData['pageTitle'] = __('Staff');
            }

            $this->viewData['PermissionGroup'] = array_column(PermissionGroup::get()->toArray(),'name','id');

            return $this->view('staff.index',$this->viewData);
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
            'text'=> __('Staff'),
            'url'=> route('system.staff.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Staff'),
        ];

        $this->viewData['pageTitle'] = __('Create Staff');

        $this->viewData['PermissionGroup'] = PermissionGroup::get();

        return $this->view('staff.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaffFormRequest $request){
        $requestData = $request->all();
        $requestData['password'] = bcrypt($requestData['password']);

        if($request->file('avatar')){
            $path = $request->file('avatar')->store(setting('system_path').'/avatar/'.date('Y/m/d'),'first_public');
            if($path){
                $requestData['avatar'] = $path;
            }
        }else{
            unset($requestData['avatar']);
        }


        $insertData = Staff::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.staff.show',$insertData->id)
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
    public function show(Staff $staff,Request $request){

        if($request->isDataTable == 'call'){
            $eloquentData = $staff->calls()->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'description',
                'created_by_staff_id',
                'created_at'
            ])
                ->orderByDesc('id')
                ->with([
                    'client',
                    'call_purpose',
                    'call_status',
                    'staff'
                ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            if(!staffCan('call-manage-all')){
                $eloquentData->where('calls.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    return '<a href="'.route('system.client.show',$data->client->id).'" target="_blank">'.$data->client->fullname.'</a>';
                })
                ->addColumn('call_purpose_id',function($data){
                    return '<b style="color: '.$data->call_purpose->color.'">'.$data->call_purpose->{'name_'.\App::getLocale()}.'</b>';
                })
                ->addColumn('call_status_id',function($data){
                    return '<b style="color: '.$data->call_status->color.'">'.$data->call_status->{'name_'.\App::getLocale()}.'</b>';
                })
                ->addColumn('type',function($data){
                    return __(strtoupper($data->type));
                })
                ->addColumn('created_by_staff_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>';
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
                                <a class="dropdown-item" target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }elseif($request->isDataTable == 'log'){
        $eloquentData = Activity::with(['subject','causer'])
            ->where('causer_type','App\Models\Staff')
            ->where('causer_id',$staff->id)
            ->select([
                'id',
                'log_name',
                'description',
                'subject_id',
                'subject_type',
                'causer_id',
                'causer_type',
                'created_at',
                'updated_at'
            ]);

        return datatables()->eloquent($eloquentData)
            ->addColumn('id','{{$id}}')
            ->addColumn('description','{{$description}}')
            ->addColumn('subject',function($data){
                return last(explode('\\',$data->subject_type)).' ('.$data->subject_id.')';
            })
            ->addColumn('created_at','{{$created_at}}')
//            ->addColumn('action',function($data){
//                return '<span class="dropdown">
//                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
//                              <i class="la la-gear"></i>
//                            </a>
//                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
//                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
//                            </div>
//                        </span>';
//            })
            ->escapeColumns([])
            ->make(false);

    }else{
            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Staff'),
                    'url' => route('system.staff.index'),
                ],
                [
                    'text' => $staff->fullname,
                ]
            ];

            $this->viewData['pageTitle'] = __('Staff Profile');


            $this->viewData['result'] = $staff;
            return $this->view('staff.show', $this->viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Staff $staff,Request $request){

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Staff'),
            'url'=> route('system.staff.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $staff->fullname]),
        ];

        $this->viewData['pageTitle'] = __('Edit Staff');
        $this->viewData['result'] = $staff;
        $this->viewData['PermissionGroup'] = PermissionGroup::get();

        return $this->view('staff.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(StaffFormRequest $request, Staff $staff)
    {

        $requestData = $request->all();

        if($requestData['password']){
            $requestData['password'] = bcrypt($requestData['password']);
        }else{
            unset($requestData['password']);
        }

        if($request->file('avatar')){
            $path = $request->file('avatar')->store(setting('system_path').'/avatar/'.date('Y/m/d'),'first_public');
            if($path){
                $requestData['avatar'] = $path;
            }
        }else{
            unset($requestData['avatar']);
        }

        $updateData = $staff->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.staff.show',$staff->id)
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


    public function changePassword(){

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Change Password'),
        ];
        $this->viewData['pageTitle'] = __('Change Password');

        return $this->view('staff.change-password',$this->viewData);

    }

    public function changePasswordPost(StaffFormRequest $request){

        if(!\Hash::check($request->currant_password, Auth::user()->password)){
            return $this->response(
                false,
                11001,
                __('Wrong Currant Password')
            );
        }elseif($request->currant_password == $request->password){
            return $this->response(
                false,
                11001,
                __('New password can\'t be currant password')
            );
        }


        $insertData = Staff::where('id',Auth::id())
            ->update([
                'password'=> bcrypt($request->password)
            ]);

        if($insertData){
            return $this->response(
                true,
                200,
                __('password updated successfully'),
                [
                    'url'=> route('system.dashboard')
                ]
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not update data')
            );
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
        $message = __('Staff deleted successfully');
        $staff->delete();
        return $this->response(true,200,$message);
    }

}
