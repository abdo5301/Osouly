<?php

namespace App\Modules\System;

use App\Models\{
    Invoice, Staff, PermissionGroup
};
use Illuminate\Http\Request;
use App\Http\Requests\StaffFormRequest;
use Form;
use Auth;
use Hash;
use App\Models\AuthSession;

class AuthSessionController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = AuthSession::select([
                'id',
                'ip',
                'guard_name',
                'user_id',
                'user_agent',
                'created_at',
                'updated_at',
            ])
                ->orderBy('updated_at','desc');



            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('user_id',function($data) {
                    return '<a target="_blank" href="'.route('system.staff.show',$data->user_id).'">'.$data->user->fullname.'</a>';
                })
                ->addColumn('ip','{{$ip}}')
                ->addColumn('user_agent','{{$user_agent}}')
                ->addColumn('created_at',function($data){
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('updated_at',function($data){
                    return $data->updated_at->diffForHumans();
                })
                ->addColumn('action',function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.staff.delete-auth-sessions',['id'=>$data->id]).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('User'),
                __('Ip'),
                __('User Agent'),
                __('Created At'),
                __('Updated At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Auth Sessions')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Auth Sessions');
            }else{
                $this->viewData['pageTitle'] = __('Auth Sessions');
            }

            $this->viewData['PermissionGroup'] = array_column(PermissionGroup::get()->toArray(),'name','id');

            return $this->view('auth-session.index',$this->viewData);
        }
    }

    public function deleteAuthSession(Request $request){
        if(empty($request->id))
            return ['status'=>false,'msg'=>__('ID is Required')];

        $auth_session = AuthSession::where(['id'=>$request->id])->find($request->id);
        if(empty($auth_session))
            return ['status'=>false,'msg'=>__('Session Not Found')];

        if($auth_session->delete()){
            return ['status'=>true,'msg'=>__('Session Deleted')];
        }

        return ['status'=>false,'msg'=>__('Session Not Deleted')];

    }


}
