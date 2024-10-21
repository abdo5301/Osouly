<?php

namespace App\Modules\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Auth;
use  App\Models\ImporterData;
use  App\Models\Importer;
use  App\Models\LeadData;
use  App\Models\PropertyParameter;
use  App\Models\RequestParameter;
use Jenssegers\Agent\Agent;

class ActivityController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        //  $all = Activity::with(['causer'])->get();
        // foreach($all as $a=>$rr){
        //     if(!$rr->causer){
        //         Activity::where('id',$rr->id)->update(['causer_id'=>1]);
        //     }else{
        //         continue;
        //     }

        // }

        if($request->isDataTable){
            $eloquentData = Activity::with(['subject','causer'])
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

            whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('id', '=',$request->id);
            }

            if($request->description){
                $eloquentData->where('description', '=',$request->description);
            }

            if($request->subject_type){
                $eloquentData->where('subject_type', '=',$request->subject_type);
            }

            if($request->subject_id){
                $eloquentData->where('subject_id', '=',$request->subject_id);
            }

            if($request->causer_type){
                $eloquentData->where('causer_type', '=',$request->causer_type);
            }

            if($request->causer_id){
                $eloquentData->where('causer_id', '=',$request->causer_id);
            }



            if ($request->downloadExcel == "true") {
                if (staffCan('download.activity-log.excel')) {
                    $excelData = $eloquentData;
                    $excelData = $excelData->get();
                    exportXLS(__('Activity Log'),
                        [
                            'ID',
                            'Status',
                            'Model',
                            'User',
                            'Created At'

                        ],
                        $excelData,
                        [
                            'id' => 'id',
                            'description' => 'description',
                            'subject' => function($data){
                                return $data->subject_type.' ('.$data->subject_id.')';
                            },
                            'causer'=>function($data){
                                return $data->causer_type.' ('.$data->causer_id.')';
                            },
                            'created_at' =>'created_at',
                        ]
                    );
                }
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('causer',function($data){
                    if($data->causer) {
                            return '<a target="_blank" href="'.route('system.staff.show',$data->causer->id).'">'.$data->causer->fullname.'</a>';
                    }elseif(last(explode('\\',$data->subject_type)) == 'ImporterData'){
                            $importer_data = ImporterData::where('id',$data->subject_id)->first();
                            $importer = Importer::where('id',$importer_data->importer_id)->first();
                        return  $importer->staff ? '<a target="_blank" href="'.route('system.staff.show',$importer->staff->id).'">'.$importer->staff->fullname.'</a>' : "--";
                    }else{
                     return '--';
                    }

                })
                ->addColumn('subject',function($data){
                    $subject = last(explode('\\',$data->subject_type));
                    if($data->url){
                     return '<a href="'.$data->url.'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                    }elseif($subject == 'ImporterData'){

                    $importer = ImporterData::where('id',$data->subject_id)->first();

                return '<a href="'.route('system.importer.show',$importer->importer_id.'?id='.$data->subject_id).'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                    }elseif($subject == 'LeadData'){

                  //  $lead = LeadData::where('id',$data->subject_id)->first();

                return '<a href="'.route('system.lead-data.show',$data->subject_id).'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                   }elseif($subject == 'PropertyParameter'){

                    $property = PropertyParameter::where('id',$data->subject_id)->first();

                return '<a href="'.route('system.property.show',$property->property_id).'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                   }elseif($subject == 'RequestParameter') {

                        $request = RequestParameter::where('id', $data->subject_id)->first();

                        return '<a href="' . route('system.request.show', $request->request_id) . '" target="_blank">' . $subject . ' (' . $data->subject_id . ')' . '</a>';
                    }else{
                        if(in_array($subject,['RequestStatus','LeadStatus','PropertyStatus','PropertyType','PropertyModel','PermissionGroups','DataSource','CallPurpose','CallStatus'])){
                            preg_match( '/[A-Z]/', lcfirst($subject), $matches, PREG_OFFSET_CAPTURE );
                            $first_word_subject_length =  $matches[0][1];
                            $dash_subject = substr_replace(strtolower($subject),"-",$first_word_subject_length,0);
                            if(\Route::has(('system.'.strtolower($dash_subject).'.index'))){
                                return '<a href="'.route('system.'.strtolower($dash_subject).'.index').'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                            }
                        }

                        if(\Route::has(('system.'.strtolower($subject).'.show'))){
                            return '<a href="'.route('system.'.strtolower($subject).'.show',$data->subject_id).'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                        }elseif (\Route::has(('system.'.strtolower($subject).'.index'))){
                       return '<a href="'.route('system.'.strtolower($subject).'.index').'" target="_blank">'.$subject. ' ('.$data->subject_id.')'.'</a>';
                         }else{
                       return $subject. ' ('.$data->subject_id.')';
                    }}

                    //return $subject. ' ('.$data->subject_id.')';

                })
                ->addColumn('created_at','{{$created_at}}')
                ->addColumn('action',function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{

            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Status'),
                __('Staff'),
                __('Model ID'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Activity Log')
            ];
            $this->viewData['pageTitle'] = __('Activity Log');


            return $this->view('activity-log.index',$this->viewData);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($ID){
        $result = Activity::findOrFail($ID);

        $agent = new Agent();
        $agent->setUserAgent($result->user_agent);
        $result->agent = $agent;

        $location = @json_decode(file_get_contents('http://ip-api.com/json/'.$result->ip));
        if($location->status!='fail')
            $result->location = $location;


        $this->viewData['result'] = $result;
        return $this->view('activity-log.show',$this->viewData);
    }

}
