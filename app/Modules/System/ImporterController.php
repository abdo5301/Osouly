<?php

namespace App\Modules\System;

use App\Http\Requests\ImporterFormRequest;
use App\Models\Area;
use App\Models\Importer;
use App\Models\Property;
use App\Models\ImporterData;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;
use test\Mockery\ReturnTypeObjectTypeHint;

class ImporterController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Importer::select([
                'id',
                'connector',
                'query_name',
                'area_id',
                'property_type_id',
                'purpose_id',
                'status',
                'success',
                'created_at'
            ]);



            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            if(!staffCan('importer-manage-all')){
                $eloquentData->where('calls.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('connector','{{$connector}}')
                ->addColumn('query_name',function($data){
                    return ($data->query_name) ? $data->query_name : '--';
                })
                ->addColumn('area_id',function($data){
                    return implode(' -> ',\App\Libs\AreasData::getAreasUp($data->area_id,true));
                })
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.App::getLocale()};
                })
                ->addColumn('status','{{$status}}')
                ->addColumn('success','{{$success}}')

                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.importer.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" onclick="set_importer(this.rel)" data-toggle="modal" data-target="#sales-modal" href="javascript:void(0);" rel="'.$data->id.'"><i class="la la-users"></i> '.__('Distribute to Staff').'</a>
                                <a class="dropdown-item"  href="'.route('system.importer.show',$data->id).'?is_total=true&isDataTable=true&downloadExcel=true" ><i class="la la-file-excel-o"></i> '.__('Download Excel').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Connector'),
                __('Query'),
                __('Area'),
                __('Type'),
                __('Purpose'),
                __('Status'),
                __('Success'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Importer')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Importer');
            }else{
                $this->viewData['pageTitle'] = __('Importer');
            }

            return $this->view('importer.index',$this->viewData);
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
            'text'=> __('Importer'),
            'url'=> route('system.importer.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Import Data'),
        ];

        $this->viewData['pageTitle'] = __('Import Data');

        $this->viewData['property_types'] = PropertyType::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['purposes'] = Purpose::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        return $this->view('importer.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ImporterFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();


        switch ($requestData['connector']){
            case 'OLX':
                $areaInfo = Area::where('id',$request->area_id)
                    ->whereNotNull('olx_id')
                    ->first();

                $propertyTypeInfo = PropertyType::where('id',$request->property_type_id)
                    ->whereNotNull('olx_id')
                    ->first();

                $purposeInfo = Purpose::where('id',$request->purpose_id)
                    ->whereNotNull('olx_id')
                    ->first();

                if(!$areaInfo || !$propertyTypeInfo || !$purposeInfo){
                    return $this->response(
                        false,
                        11001,
                        __('Sorry, we could import date from OLX')
                    );
                }

                break;

            case 'Aqarmap':

                $areaInfo = Area::where('id',$request->area_id)
                    ->whereNotNull('aqarmap_id')
                    ->first();

                $propertyTypeInfo = PropertyType::where('id',$request->property_type_id)
                    ->whereNotNull('aqarmap_id')
                    ->first();

                $purposeInfo = Purpose::where('id',$request->purpose_id)
                    ->whereNotNull('aqarmap_id')
                    ->first();

                if(!$areaInfo || !$propertyTypeInfo || !$purposeInfo){
                    return $this->response(
                        false,
                        11001,
                        __('Sorry, we could import date from Aqarmap')
                    );
                }

                break;
        }

        $insertData = Importer::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.importer.show',$insertData->id)
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
    public function show(Importer $importer,Request $request){

        if(!staffCan('importer-manage-all') && $importer->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

       // dd($importer);

        if($request->isDataTable){



           /* $eloquentData = $importer->data();/*->select([
                'mobile',
                \DB::raw('COUNT(*) as `counter`')
            ])
                ->groupBy('mobile')*/

            $eloquentData = $importer->data()->select('*',\DB::raw('COUNT(*) as `counter`' ))->groupBy('mobile')
                ->orderBy('counter','ASC');

            if($request->id){
                $eloquentData->where('id',$request->id);
            }

            if($request->mobile){
                $eloquentData->where('mobile',$request->mobile);
            }

            if($request->count_from && $request->count_to){
                $countWhere = $importer->data()->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $eloquentData->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            if($request->downloadExcel){
                return exportXLS(
                    __('Importer Data').' #'.$importer->id,
                    [
                        __('ID'),
                        __('Name'),
                        __('Price'),
                        __('Space'),
                        __('Bed Rooms'),
                        __('Bath Room'),
                        __('Owner Name'),
                        __('Phone')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'name'=> function($data){
                            return '<a href="'.$data->url.'" target="_blank">'.$data->name.'</a>';
                        },
                        'price'=> function($data){
                            return $data->price ? number_format($data->price) : '--';
                        },
                        'space'=> function($data){
                            return $data->space ? number_format($data->space) : '--';
                        },
                        'bed_rooms'=> function($data){
                            return $data->bed_rooms;
                        },
                        'bath_room'=> function($data){
                            return $data->bath_room;
                        },
                        'owner_name'=> function($data){
                            return '('.$data->counter.')'.$data->owner_name;
                        },
                        'mobile'=> function($data){
                            return $data->mobile;
                        },
                    ]
                );
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','<a href="{{$url}}" target="_blank">{{$name}}</a>')
                ->addColumn('price',function($data){
                    return amount($data->price,true);
                })
                ->addColumn('space','{{$space}}')
                ->addColumn('bed_rooms',function($data){
                    return $data->bed_rooms;
                })
                ->addColumn('bath_room',function($data){
                    return $data->bath_room;
                })
                ->addColumn('owner_name',function($data){
                    return  '('.$data->counter.')'.$data->owner_name;
                })
                ->addColumn('mobile',function($data){
                    return   '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })

                ->addColumn('action', function($data){
                    return '  <a class="dropdown-item" href="javascript:viewProperty('.$data->id.')"><i class="fa fa-eye"></i> '.__('View').'</a>';
                })
                ->escapeColumns([])
                ->make();
        }elseif($request->propertyData){
            $propertyDataID = $request->propertyData;

            $propertyData = ImporterData::where([
                ['id',$propertyDataID],
                ['importer_id',$importer->id]
            ])->first();

            //dd($propertyData);

            if(!$propertyData) return ['status'=> false];

            $next = ImporterData::where([
                ['id','>',$propertyDataID],
                ['importer_id',$importer->id]
            ])
                ->orderBy('id','ASC');


            if($request->mobile){
                $next->where('mobile',$request->mobile);
            }
            if($request->count_from && $request->count_to){
                $countWhere = $importer->data()->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $next->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            $next = $next->first(['id']);


            $previous = ImporterData::where([
                ['id','<',$propertyDataID],
                ['importer_id',$importer->id]
            ])
                ->orderBy('id','DESC');

            if($request->mobile){
                $previous->where('mobile',$request->mobile);
            }
            if($request->count_from && $request->count_to){
                $countWhere = $importer->data()->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $previous->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            $previous = $previous->first(['id']);

            $systemProperty = '';
            if($propertyData->property_id){
                $systemProperty = ' <tr>
                        <td>'.__('System Property').'</td>
                        <td><a href="'.route('system.property.show',$propertyData->property_id).'" target="_blank">#ID: '.$propertyData->property_id.'</a></td>
                    </tr>';
            }



                $owner =   $propertyData->owner_name. '( <a href="tel:'.$propertyData->mobile.'">'.$propertyData->mobile.'  </a> )';
                $staff = $propertyData->staff_id ? $propertyData->staff->fullname : '--';


            $table = '<table class="table">
                <thead>
                    <tr>
                        <th>'.__('Key').'</th>
                        <th>'.__('Value').'</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>'.__('ID').'</td>
                        <td>'.$propertyData->id.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Connector').'</td>
                        <td>'.$propertyData->connector_id.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Status').'</td>
                        <td>'.__(ucfirst($propertyData->status)).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Staff').'</td>
                        <td>'.$staff.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Name').'</td>
                        <td>'.$propertyData->name.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Description').'</td>
                        <td>'.$propertyData->description.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Price').'</td>
                        <td>'.amount($propertyData->price,true).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Space').'</td>
                        <td>'.number_format($propertyData->space).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Bed Rooms').'</td>
                        <td>'.$propertyData->bed_rooms.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Bath Room').'</td>
                        <td>'.$propertyData->bath_room.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Owner').'</td>
                        <td>'.$owner.'</td>
                    </tr>
                     '.$systemProperty.'

                </tbody>
            </table>

            ';

            return [
                'status'    => true,
                'table'     => $table,
                'next'      => ($next) ? $next->id : false,
                'previous'  => ($previous) ? $previous->id : false,
                'property_id'=> $propertyData->property_id
            ];

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Importer'),
                    'url' => route('system.importer.index'),
                ],
                [
                    'text' => __('#ID: :id', ['id' => $importer->id]),
                ]
            ];

            $this->viewData['pageTitle'] = __('#ID: :id', ['id' => $importer->id]);

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Price'),
                __('Space'),
                __('Bed Rooms'),
                __('Bath Room'),
                __('Owner Name'),
                __('Phone'),
                __('Action')
            ];


            $ownersData = $importer->data()->select([
                'owner_name',
                'mobile',
                \DB::raw('COUNT(*) as `count`')
            ])
                ->groupBy('mobile')
                ->orderBy('count','ASC')
                ->get();

            $this->viewData['ownersData'] = $ownersData;

            $this->viewData['result'] = $importer;
            return $this->view('importer.show', $this->viewData);
        }
    }


    public function staff_data(Request $request){


        if($request->isDataTable){

            /* $eloquentData = $importer->data();/*->select([
                 'mobile',
                 \DB::raw('COUNT(*) as `counter`')
             ])
                 ->groupBy('mobile')*/

            $eloquentData = ImporterData::Where('staff_id',Auth::id());

            if($request->id){
                $eloquentData->where('id',$request->id);
            }

            if($request->mobile){
                $eloquentData->where('mobile',$request->mobile);
            }

            if($request->count_from && $request->count_to){
                $countWhere = ImporterData::Where('staff_id',Auth::id())->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $eloquentData->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('importer_id','{{$importer_id}}')
                ->addColumn('name','<a href="{{$url}}" target="_blank">{{$name}}</a>')
                ->addColumn('price',function($data){
                    return amount($data->price,true);
                })
                ->addColumn('space','{{$space}}')
                ->addColumn('bed_rooms',function($data){
                    return $data->bed_rooms;
                })
                ->addColumn('bath_room',function($data){
                    return $data->bath_room;
                })
                ->addColumn('owner_name',function($data){
                    //return  '('.$data->counter.')'.$data->owner_name;
                    return  $data->owner_name;
                })
                ->addColumn('mobile',function($data){
                    return   '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })

                ->addColumn('action', function($data){
                    return '  <a class="dropdown-item" href="javascript:viewProperty('.$data->id.')"><i class="fa fa-eye"></i> '.__('View').'</a>
';
                })
                ->escapeColumns([])
                ->make();
        }elseif($request->propertyData){
            $propertyDataID = $request->propertyData;
            $importer_id = $request->importer_id;

            $propertyData = ImporterData::where([
                ['id',$propertyDataID],
                ['staff_id',Auth::id()]
            ])->first();

            //dd($propertyData);

            if(!$propertyData) return ['status'=> false];

            $next = ImporterData::where([
                ['id','>',$propertyDataID],
                ['staff_id',Auth::id()]
            ])
                ->orderBy('id','ASC');


            if($request->mobile){
                $next->where('mobile',$request->mobile);
            }
            if($request->count_from && $request->count_to){
                $countWhere = ImporterData::Where('staff_id',Auth::id())->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $next->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            $next = $next->first(['id']);


            $previous = ImporterData::where([
                ['id','<',$propertyDataID],
                ['staff_id',Auth::id()]

            ])
                ->orderBy('id','DESC');

            if($request->mobile){
                $previous->where('mobile',$request->mobile);
            }
            if($request->count_from && $request->count_to){
                $countWhere = ImporterData::Where('staff_id',Auth::id())->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $previous->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            $previous = $previous->first(['id']);

            $systemProperty = '';
            if($propertyData->property_id){
                $systemProperty = ' <tr>
                        <td>'.__('System Property').'</td>
                        <td><a href="'.route('system.property.show',$propertyData->property_id).'" target="_blank">#ID: '.$propertyData->property_id.'</a></td>
                    </tr>';
            }



            $owner =   $propertyData->owner_name. '( <a href="tel:'.$propertyData->mobile.'">'.$propertyData->mobile.'  </a> )';



            $table = '<table class="table">
                <thead>
                    <tr>
                        <th>'.__('Key').'</th>
                        <th>'.__('Value').'</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>'.__('ID').'</td>
                        <td>'.$propertyData->id.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Connector').'</td>
                        <td>'.$propertyData->connector_id.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Status').'</td>
                        <td>'.__(ucfirst($propertyData->status)).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Name').'</td>
                        <td>'.$propertyData->name.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Description').'</td>
                        <td>'.$propertyData->description.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Price').'</td>
                        <td>'.amount($propertyData->price,true).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Space').'</td>
                        <td>'.number_format($propertyData->space).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Bed Rooms').'</td>
                        <td>'.$propertyData->bed_rooms.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Bath Room').'</td>
                        <td>'.$propertyData->bath_room.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Owner').'</td>
                        <td>'.$owner.'</td>
                    </tr>
                     '.$systemProperty.'

                </tbody>
            </table>

            ';

            return [
                'status'    => true,
                'table'     => $table,
                'next'      => ($next) ? $next->id : false,
                'previous'  => ($previous) ? $previous->id : false,
                'property_id'=> $propertyData->property_id
            ];

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Importer'),
                    'url' => route('system.importer.index'),
                ],
                [
                    'text' => __('Staff Importer Data'),
                ]
            ];

            $this->viewData['pageTitle'] = __('Staff Importer Data');

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Importer ID'),
                __('Name'),
                __('Price'),
                __('Space'),
                __('Bed Rooms'),
                __('Bath Room'),
                __('Owner Name'),
                __('Phone'),
                __('Action')
            ];

            $ownersData =  ImporterData::Where('staff_id',Auth::id())->select([
                'owner_name',
                'mobile',
                \DB::raw('COUNT(*) as `count`')
            ])
                ->groupBy('mobile')
                ->orderBy('count','ASC')
                ->get();

            $this->viewData['ownersData'] = $ownersData;

            return $this->view('importer.staff', $this->viewData);
        }

    }


    public function distribute(Request $request){
    //    dd($importer);
        $data = array();
        if($request->importer_id){
            $sales_arr = $request->sales_ids; //explode(',',$request->sales_ids);
            $importer_data = ImporterData::Where('importer_id',(int)$request->importer_id)->get();
            if(!count($importer_data)) {
                $data['error'] = __('Importer Data not found');
            }elseif(!count($sales_arr)){
                $data['error'] = __('You must select sales to distribute');
            }elseif(count($importer_data) < count($sales_arr)){
                $data['error'] = __('The importer data count must be more than selected sales count');
            }else{
                $div_value = round(count($importer_data) / count($sales_arr));

                $data['staff_count'] = count($sales_arr);
                $data['importer_count'] = count($importer_data);
                $data['count_for_one'] = $div_value;
                $data['importer_id'] = $request->importer_id;

                $offset = 0;
                $div_arr = array();
                $counter = 1;
                foreach ($sales_arr as $key => $value){
                    if($counter == count($sales_arr)){ // at last loop add plus 1 to data limit to avoid rounding
                        $div_value += 1;
                    }

                    $update_data = array(
                        'staff_id' => $value,
                        'status' => 'assigned'
                    );
                    $importer_data = new ImporterData();
                    $m_arr = $importer_data->where('importer_id',$request->importer_id)->orderBy('id','DESC')->offset($offset)->limit($div_value)->get();
                    foreach ($m_arr as $item){
                         ImporterData::find($item->id)->update($update_data);
                    }

                   $div_arr[] = ['staff_id'=>$value,'importer_count'=>$div_value,'offset'=>$offset];
                   $offset += $div_value;
                   $counter++;
                }
                $data['success'] = __('importer data has been distributed');
                $data['div_arr'] = $div_arr;

            }

        }else{
            $data['error'] = __('Importer not found');
        }

       return json_encode($data);


    }


}
