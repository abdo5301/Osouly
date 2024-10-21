<?php

namespace App\Modules\System;

use App\Models\Cloud;
use App\Models\Setting;
use Illuminate\Http\Request;
use Form;
use Auth;
use DB;
use Spatie\Activitylog\Models\Activity;

class CloudController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Cloud::select([
                'id',
                'name',
                'database_name',
                'created_at'
            ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('database_name','{{$database_name}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA'). '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.cloud.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
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
                __('Database'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Cloud Systems')
            ];

            $this->viewData['pageTitle'] = __('Systems');

            return $this->view('cloud.index',$this->viewData);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Cloud $cloud,Request $request){
        \Config::set('database.connections.cloud.database',$cloud->database_name);

        if($request->isDataTable){

            $activity = new Activity();
            $activity->setConnection('cloud');


            $eloquentData = $activity->leftJoin('staff','staff.id','=','activity_log.causer_id')
                ->select([
                    'activity_log.id',
                    'activity_log.log_name',
                    'activity_log.description',
                    'activity_log.subject_id',
                    'activity_log.subject_type',
                    'activity_log.causer_id',
                    'activity_log.causer_type',
                    'activity_log.created_at',
                    'activity_log.updated_at',
                    DB::raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")
                ]);

            return datatables()->of($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('causer',function($data){
                    return '#ID:'.$data->causer_id.' - '.$data->staff_name;
                })
                ->addColumn('subject',function($data){
                    return last(explode('\\',$data->subject_type)).' ('.$data->subject_id.')';
                })
                ->addColumn('created_at','{{$created_at}}')
                ->escapeColumns([])
                ->make(false);
        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text'=> __('Cloud Systems'),
                    'url'=> route('system.cloud.index'),
                ],
                [
                    'text'=> $cloud->name,
                ]
            ];

            $this->viewData['pageTitle'] = $cloud->name;

            $this->viewData['result'] = $cloud;


            $this->viewData['count'] = [
                'staff'=> DB::table($cloud->database_name.'.staff')->count(),
                'properties'=> DB::table($cloud->database_name.'.properties')->count(),
                'clients'=> DB::table($cloud->database_name.'.clients')->count(),
                'requests'=> DB::table($cloud->database_name.'.requests')->count(),
                'importer'=> DB::table($cloud->database_name.'.importer')->count(),
                'importer_data'=> DB::table($cloud->database_name.'.importer_data')->count()
            ];


            $this->viewData['systemStatus'] = DB::table($cloud->database_name.'.settings')->where('name','system_status')->first()->value;


            return $this->view('cloud.show',$this->viewData);
        }
    }

    public function setting(Cloud $cloud,Request $request){
        \Config::set('database.connections.cloud.database',$cloud->database_name);
        $setting = new Setting();
        $setting->setConnection('cloud');

        $setting->where('name',$request->name)->update([
            'value'=> $request->value
        ]);

        return back();
    }

}
