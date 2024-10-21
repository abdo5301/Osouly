<?php

namespace App\Modules\System;

use App\Http\Requests\PropertyFormRequest;
use App\Http\Requests\PropertyUploadExcelFormRequest;
use App\Libs\AreasData;
use App\Libs\CheckKeyInArray;
use App\Models\Call;
use App\Models\Client;
use App\Models\DataSource;
use App\Models\Image;
use App\Models\ImporterData;
use App\Models\Parameter;
use App\Models\Property;
use App\Models\PropertyModel;
//use App\Models\Activitylog as Activity;
use App\Models\PropertyParameter;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class PropertyController extends SystemController
{

    public function publish($id,Request $request){ //change property publish status from index table

        $property = Property::find($id);

        if((int)$property->publish == 1){
            $publish_update = ['publish'=>0];
            $message = __('Property UnPublished successfully');
        }else{
            $publish_update = ['publish'=>1];
            $message = __('Property Published successfully');
        }

        $property_publish = $property->update($publish_update);

        if($property_publish){
            return $this->response(true,200,$message);
        }else{
            $message = __('Sorry, we could not change status of this property. pleas try again later !');
            return $this->response(
                false,
                11001,
                $message
            );
        }

    }


    private function createEditData(){
        $this->viewData['property_types'] = PropertyType::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['purposes'] = Purpose::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['data_sources'] = DataSource::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['countries'] = App\Models\Area::Where('area_type_id',1)->get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['governments'] = App\Models\Area::Where('area_type_id',2)->get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['cities'] = App\Models\Area::Where('area_type_id',3)->get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        $this->viewData['features'] = App\Models\PropertyFeatures::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
       // $this->viewData['rent_ids'] = !empty(setting('prop_rent_types_ids')) && in_array($result->purpose_id,explode(',',setting('prop_rent_types_ids')));

        $this->viewData['randKey'] = md5(rand().time());

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){
            $eloquentData = Property::select([
                'id',
                'owner_id',
                'property_type_id',
                'status',
                'publish',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            /*
            * Start handling filter
            */

            if($request->features){
                $eloquentData->whereRaw('FIND_IN_SET(?,property_features)', [$request->features]);
            }

            whereBetween($eloquentData,'DATE(properties.created_at)',$request->created_at1,$request->created_at2);


            if($request->id){
                $eloquentData->where('properties.id',$request->id);
            }

            if($request->status){
                $eloquentData->where('properties.status',$request->status);
            }

            if($request->publish){
                $eloquentData->where('properties.publish',$request->publish);
            }

            if($request->property_type_id){
                $eloquentData->where('properties.property_type_id',$request->property_type_id);
            }


            if($request->purpose_id){
                $eloquentData->where('properties.purpose_id',$request->purpose_id);
            }


            if($request->data_source_id){
                $eloquentData->where('properties.data_source_id',$request->data_source_id);
            }


            if($request->owner_id){
                $eloquentData->where('properties.owner_id',$request->owner_id);
            }

            if($request->renter_id){
                $eloquentData->where('properties.owner_id',$request->renter_id);
            }

            if($request->area_id){
                $eloquentData->whereIn('properties.local_id',AreasData::getAreasDown($request->area_id));
            }


            if($request->building_number){
                $eloquentData->where('properties.building_number',$request->building_number);
            }


            if($request->flat_number){
                $eloquentData->where('properties.flat_number',$request->flat_number);
            }

            if($request->floor){
                if(is_array($request->floor)){
                    $eloquentData->Where('properties.floor','like','%'.implode(',',$request->floor).'%');
                }else{
                    $eloquentData->where('properties.floor','like','%'.$request->floor.'%');
                }

            }


            if($request->title){
                $eloquentData->where('properties.name','LIKE','%'.$request->title.'%');
            }

            if($request->description){
                $eloquentData->where('properties.description','LIKE','%'.$request->description.'%');
            }



            if($request->contract_type){
                $eloquentData->where('properties.contract_type',$request->contract_type);
            }


            whereBetween($eloquentData,'properties.price',$request->price1,$request->price2);
            whereBetween($eloquentData,'properties.insurance_price',$request->insurance_price1,$request->insurance_price2);
            whereBetween($eloquentData,'properties.contract_period',$request->contract_period1,$request->contract_period2);
            whereBetween($eloquentData,'properties.deposit_rent',$request->deposit_rent1,$request->deposit_rent2);
            whereBetween($eloquentData,'properties.space',$request->space1,$request->space2);

            if($request->space){ // abdo
                $eloquentData->where('properties.space','LIKE','%'.$request->space.'%');
            }

            if($request->price){ //abdo
                $eloquentData->where('properties.price','LIKE','%'.$request->price.'%');
            }

            if($request->address){
                $eloquentData->where('properties.address','LIKE','%'.$request->address.'%');
            }

            if($request->created_by_staff_id){
                $eloquentData->where('properties.created_by_staff_id',$request->created_by_staff_id);
            }

            if($request->downloadExcel){
                return exportXLS(
                    __('Properties'),
                    [
                        __('ID'),
                        __('Owner'),
                        __('Mobile'),
                        __('Phone'),
                        __('Type'),
                        __('Purpose'),
                        __('Status'),
                        __('Data Source'),
                        __('Area'),
                        __('Address'),
                        __('Description'),
                        __('Price'),
                        __('Space'),
                        __('Created At')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'owner'=> function($data){
                            return $data->owner ? $data->owner->fullname : '--';
                        },
                        'mobile'=> function($data){
                            return $data->owner ? $data->owner->mobile : '--';
                        },
                        'phone'=> function($data){
                            return $data->owner ? $data->owner->phone : '--';
                        },

                        'property_type_id'=> function($data){
                            return $data->property_type ? $data->property_type->{'name_'.App::getLocale()} : '--';
                        },

                        'purpose_id'=> function($data){
                            return $data->purpose ? $data->purpose->{'name_'.App::getLocale()} : '--';
                        },
                        'status'=> function($data){
                            return  __(ucwords(str_replace('_',' ',$data->status)));
                        },
                        'data_source_id'=> function($data){
                            return  $data->data_source ? $data->data_source->{'name_'.App::getLocale()} : '--';
                        },
                        'area'=> function($data){
                            return $data->area ? $data->area->{'name_'.\App::getLocale()}.'<br /><small>'.$data->address.'</small>' : '--';
                        },

                        'address'=> 'address',

                        'description'=> 'description',

                        'price'=> function($data){
                            return amount($data->price,true);
                        },

                        'space'=> function($data){
                            return number_format($data->space);
                        },

                        'created_at'=> function($data){
                            return $data->created_at->format('Y-m-d h:i A');
                        }
                    ]
                );
            }

            $eloquentData->orderBy('id','DESC');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('owner_id',function($data){
                    return $data->owner ? '<a href="'.route('system.owner.show',$data->owner_id).'" target="_blank">'.$data->owner->first_name.'</a>'.'<br /><a href="tel:'.$data->owner->mobile.'">'.$data->owner->mobile.'</a>' : __('Owner not found');
                })
//                ->addColumn('renter_id',function($data){
//                    if($data->renter_id && $data->renter){
//                        return '<a href="'.route('system.renter.show',$data->renter_id).'" target="_blank">'.$data->renter->first_name.'</a>'.'<br /><a href="tel:'.$data->renter->mobile.'">'.$data->renter->mobile.'</a>';
//                    }else{
//                        return __('No Renter');
//                    }
//                })
                ->addColumn('property_type_id',function($data){
                    return $data->property_type ? $data->property_type->{'name_'.App::getLocale()} : '--';
                })
                ->addColumn('status',function($data){
                    if($data->status == 'rented'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords(str_replace('_',' ',$data->status))) . '</span>';
                    }else{
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords(str_replace('_',' ',$data->status))) . '</span>';
                    }
                    //return __(ucwords(str_replace('_',' ',$data->status)));
                })
                ->addColumn('publish',function($data){
                    if($data->publish){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' .  __('Active') . '</span>';
                    }else{
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' .  __('In-Active') . '</span>';
                    }
                   // return $data->publish ? __('Active') : __('In-Active');
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                   // return 'yes';
                   // $delete_link = '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>';
                    //  '.$delete_link.'

                    if((int)$data->publish == 1){
                        $property_publish_text =  '<i class="la la-ban"></i>'.__('UnPublish');
                    }else{
                        $property_publish_text = '<i class="la la-laptop"></i>'.__('Publish');
                    }

                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.property.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="publishProperty(\''.route('system.property.publish',$data->id).'\')">'.$property_publish_text.'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>                                   
                              
                            </div>
                            
                        </span>';
                })
                ->whitelist(['properties.id','properties.title','clients.first_name','clients.mobile'])
                ->escapeColumns(['action'])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Owner'),
                __('Type'),
                __('Status'),
                __('Publish'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Properties')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Properties');
            }else{
                $this->viewData['pageTitle'] = __('Properties');
            }

            $this->createEditData();

            return $this->view('property.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        $this->viewData['importer_data'] = null;

        if($request->importer_data_id){
            $importerData = ImporterData::where('id',$request->importer_data_id)
                ->whereNull('property_id')
                ->firstOrFail();

            $this->viewData['importer_data'] = $importerData;
        }

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property'),
            'url'=> route('system.property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Property'),
        ];

        $this->viewData['pageTitle'] = __('Create Property');

        $this->createEditData();
        return $this->view('property.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyFormRequest $request){

        if($request->importer_data_id){
            $importerData = ImporterData::where('id',$request->importer_data_id)
                ->whereNull('property_id')
                ->firstOrFail();

            $clientData = Client::where('mobile',$importerData->mobile)
                ->first();

            if(!$clientData){
                $clientData = Client::create([
                    'type'=> 'owner',
                    'first_name'=> $importerData->owner_name,
                    'mobile'=> $importerData->mobile
                ]);
            }


            $clientID = $clientData->id;
        }else{
            $clientID = $request->owner_id;
        }

        $propertyDataInsert = [
            'property_type_id'=> $request->property_type_id,
            'purpose_id'=> $request->purpose_id,
            'data_source_id'=> $request->data_source_id,
            'owner_id'=> $clientID,
            'local_id'=> $request->area_id,
            'building_number'=> $request->building_number,
            'flat_number'=> $request->flat_number,
            'floor'=> ($request->floor && !in_array($request->property_type_id,[6,7,12,16])) ? implode(',',$request->floor): null,
            'title'=> $request->title,
            'description'=> $request->description,
            'contract_period'=> $request->contract_period,
            'contract_type'=> $request->contract_type,
            'insurance_price'=> $request->insurance_price,
            'deposit_rent'=> $request->deposit_rent,
            'price'=> $request->price,
            'space'=> $request->space,
            'address'=> $request->address,
            'latitude'=> $request->latitude,
            'longitude'=> $request->longitude,
            'video_url'=> $request->video_url,
            'mobile'=> $request->mobile,
            'street_name'=> $request->street_name,
            'country_id'=> $request->country_id,
            'government_id'=> $request->government_id,
            'city_id'=> $request->city_id,
            'area_type'=> $request->area_type,
            'mogawra'=> $request->mogawra,
            'room_number'=> $request->room_number,
            'bathroom_number'=> $request->bathroom_number,
            'features'=> $request->features ? implode(',',$request->features) : null,
            'meta_key'=> $request->meta_key,
            'meta_description'=> $request->meta_description,
            'status'=>$request->status,
            'publish'=>$request->publish,

        ];


        $insertData = Property::create($propertyDataInsert);
        if($insertData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $insertData->id,
                'sign_type'=> 'App\Models\Property'
            ]);

            if($request->importer_data_id){
                ImporterData::where('id',$request->importer_data_id)
                    ->whereNull('property_id')
                    ->update([
                        'property_id'=> $insertData->id
                    ]);
            }


           // ----log
         //   save_log(__('Create property'),'App\Models\Property',$insertData->id);
          // ----log

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property.show',$insertData->id)
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
    public function show(Property $property,Request $request){

//        if(!staffCan('property-manage-all') && ( $property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id() )){
//            abort(401, 'Unauthorized.');
//        }

//        if(!staffCan('property-manage-all') && $property->sales_id != Auth::id()){
//            abort(401, 'Unauthorized.');
//       }

        if($request->isDataTable == 'facilities'){
            $eloquentData = $property->facilities()->select([
                'id',
                'facility_company_id',
                'number',
                'created_at',
            ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('facility_company_id',function($data){
                    return $data->company ? '<a target="_blank" href="'.route('system.facility-companies.show',$data->facility_company_id).'">'.$data->company->name.'</a>' : '--';
                })
                ->addColumn('number',function($data){
                    return $data->number ? $data->number : '--';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->escapeColumns([])
                ->make(false);


        }elseif($request->isDataTable == 'invoice'){
            $eloquentData = $property->invoices()->select([
                'id',
                'property_due_id',
                'installment_id',
                'client_id',
                'amount',
                'date',
                'status',
                'created_at',
            ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('property_due_id',function($data){
                    return $data->property_due ? '<a target="_blank" href="'.route('system.invoice.show',$data->property_due_id).'">'.$data->property_due->name.'</a>' : '--';
                })
                ->addColumn('installment_id',function($data){
                    return $data->installment ? '# '.$data->installment_id : '--';
                })
                ->addColumn('client_id',function($data){
                    return $data->client ? '<a target="_blank" href="'.route('system.'.$data->client->type.'.show',$data->client_id).'">'.$data->client->fullname.' <br>( ' .__(ucwords($data->client->type)).' ) </a>' : '--';
                })

                ->addColumn('amount',function($data){
                    return $data->amount ? amount($data->amount,true) : '0.00';
                })
                ->addColumn('date', function($data){
                    return $data->date ? '<span style="white-space: nowrap;">'.date('Y-m-d',strtotime($data->date)).'</span>'  : '--';
                })
                ->addColumn('status', function($data) {
                    if($data->status =='unpaid'){
                        return '<span  style="white-space: nowrap;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';
                    }
                    return '<span  style="white-space: nowrap;" class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->status)) . '</span>';

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
                                <a class="dropdown-item" href="'.route('system.invoice.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.invoice.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.invoice.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);


        }elseif($request->isDataTable == 'dues'){
            $eloquentData = $property->dues()->select([
                'id',
                'name',
                'due_id',
                'value',
                'type',
                'duration',
                'created_at',
            ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('due_id',function($data){
                    return $data->dues ? '<a target="_blank" href="'.route('system.dues.show',$data->due_id).'">'.$data->dues->name.'</a>' : '--';
                })
                ->addColumn('value',function($data){
                    return $data->value ? amount($data->value,true) : '0.00';
                })
                ->addColumn('type', function($data) {
                    return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords($data->type)) . '</span>';

                })
                ->addColumn('duration', function($data) {
                    return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">' . __(ucwords(str_replace('_',' ',$data->duration))) . '</span>';
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
                                <a class="dropdown-item" href="'.route('system.property-dues.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>   
                                <a class="dropdown-item" href="'.route('system.property-dues.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property-dues.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);


        }elseif($request->isDataTable == 'call'){
            $eloquentData = $property->calls()->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
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
               // ->addColumn('id','{{$id}}')
                ->addColumn('id',function($data){
                    return '<a target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'">'.$data->id.'</a>';
                })
                ->addColumn('client_id',function($data){
                    return '<a href="'.route('system.'.$data->client->type.'.show',$data->client->id).'" target="_blank">'.$data->client->fullname.'</a>';
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
                    return $data->staff ? '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>' : "--";
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
//                ->addColumn('action', function($data){
//                    return '<span class="dropdown">
//                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
//                              <i class="la la-gear"></i>
//                            </a>
//                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
//                                <a class="dropdown-item" target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'"><i class="la la-search-plus"></i> '.__('View').'</a>
//                                <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
//                            </div>
//                        </span>';
//                })
                ->escapeColumns([])
                ->make(false);
        }elseif($request->isDataTable == 'true'){

            $eloquentData = $property->requests()->select([
                'requests.id',
                'requests.renter_id',
                'requests.status',
                'requests.created_at'
            ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('renter_id',function($data){
                    return '<a href="'.route('system.renter.show',$data->renter_id).'" target="_blank">'.$data->renter->Fullname.'</a><br /><a href="tel:'.$data->renter->mobile.'">'.$data->renter->mobile.'</a>';
                })

                ->addColumn('status',function($data){
                    return __(ucfirst($data->status));
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
                                <a class="dropdown-item" target="_blank" href="'.route('system.request.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'log'){
            $eloquentData = Activity::
                  where('subject_type','App\Models\Property')
                ->where('subject_id',$property->id)
                ->select([
                    'id',
                    'description',
                    'created_at',
                ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('created_at','{{$created_at}}')
                ->addColumn('action',function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Properties'),
                    'url' => route('system.property.index'),
                ],
                [
                    'text' => '#'.$property->id,
                ]
            ];

            $this->viewData['pageTitle'] = $property->name;

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Price'),
                __('Space'),
                __('Bed Rooms'),
                __('Bath Room'),
                __('Owner Name'),
                __('Action')
            ];

            $this->viewData['result'] = $property;
            save_log(__('View property'),'App\Models\Property');
            return $this->view('property.show', $this->viewData);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property,Request $request){
        /*if(!staffCan('property-manage-all') && ($property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id())){
            abort(401, 'Unauthorized.');
        }*/

//        if(!staffCan('property-manage-all') && $property->sales_id != Auth::id() && $property->created_by_staff_id != Auth::id()){
//            abort(401, 'Unauthorized.');
//        }
        // Main View Vars
        $this->viewData['importer_data'] = null;

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property'),
            'url'=> route('system.property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> '#'.$property->id]),
        ];

        $this->viewData['pageTitle'] = __('Edit Property');
        $this->viewData['result'] = $property;

        $this->createEditData();
        return $this->view('property.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyFormRequest $request, Property $property)
    {
        /*if(!staffCan('property-manage-all') && ($property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id())){
            abort(401, 'Unauthorized.');
        }*/

//        if(!staffCan('property-manage-all') && $property->sales_id != Auth::id() && $property->created_by_staff_id != Auth::id()){
//            abort(401, 'Unauthorized.');
//        }
        $propertyDataUpdate = [
            'property_type_id'=> $request->property_type_id,
            'purpose_id'=> $request->purpose_id,
            'data_source_id'=> $request->data_source_id,
            'owner_id'=> $request->owner_id,
            'local_id'=> $request->area_id,
            'building_number'=> $request->building_number,
            'flat_number'=> $request->flat_number,
            'floor'=> ($request->floor && !in_array($request->property_type_id,[6,7,12,16])) ? implode(',',$request->floor): null,
            'title'=> $request->title,
            'description'=> $request->description,
            'contract_period'=> $request->contract_period,
            'contract_type'=> $request->contract_type,
            'insurance_price'=> $request->insurance_price,
            'deposit_rent'=> $request->deposit_rent,
            'price'=> $request->price,
            'space'=> $request->space,
            'address'=> $request->address,
            'latitude'=> $request->latitude,
            'longitude'=> $request->longitude,
            'video_url'=> $request->video_url,
            'mobile'=> $request->mobile,
            'street_name'=> $request->street_name,
            'country_id'=> $request->country_id,
            'government_id'=> $request->government_id,
            'city_id'=> $request->city_id,
            'area_type'=> $request->area_type,
            'mogawra'=> $request->mogawra,
            'building_type'=> $request->building_type,
            'room_number'=> $request->room_number,
            'bathroom_number'=> $request->bathroom_number,
            'features'=> $request->features ? implode(',',$request->features) : null,
            'meta_key'=> $request->meta_key,
            'meta_description'=> $request->meta_description,
            'status'=>$request->status,
            'publish'=>$request->publish,

        ];


        $updateData = $property->update($propertyDataUpdate);

        if($updateData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $property->id,
                'sign_type'=> 'App\Models\Property'
            ]);

            // ----log
            //save_log(__('Update property'),'App\Models\Property');
            // ----log

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.property.show',$property->id)
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



    public function imageUpload(Request $request){
        $request->validate([
            'images.0' => 'required|image',
            'key'      => 'required|string'
        ]);

        $path = $request->file('images.0')->store(setting('system_path').'/'.date('Y/m/d'),'first_public');

        if($path){
            addWaterMarker($path);
            $image = Image::create([
                'custom_key'=> $request->key,
                'path'=> $path,
                'sign_id'=> !empty($request->property_id) ? $request->property_id : NULL ,
                'image_name'=> $request->file('images.0')->getClientOriginalName()
            ]);

            return [
                'status'=> true,
                'path'=>asset($path),
                'id'=> $image->id
            ];
        }

    }

    public function removeImage(Request $request){
        $request->validate([
            'name' => 'required|string',
            'key'  => 'required|string'
        ]);

        $image = Image::where([
            'custom_key'=> $request->key,
            'image_name'=> $request->name
        ])->firstOrFail();

        //unlink(storage_path('app/'.$image->path));
        if(is_file($image->path))
        unlink($image->path);

        $image->delete();


        return [
            'status'=> true
        ];

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Property $property)
    {
//        if(!staffCan('property-manage-all') && $property->sales_id != Auth::id() && $property->created_by_staff_id != Auth::id()){
//            abort(401, 'Unauthorized.');
//        }

        $message = __('Property deleted successfully');

        $property->dues()->delete();
        $property->rate()->delete();
        $property->facilities()->delete();
        $property->contracts()->delete();
        $property->requests()->delete();
        $property->requests()->delete();
        $property->calls()->delete();
        $property->ads()->delete();
        $property->reminders()->delete();

        $images = $property->images;
        if(!empty($images)){
            foreach ($images as $img){
                if(is_file($img->path)){
                    unlink($img->path);
                }
            }
            $property->images()->delete();
        }


        $property->delete();

        return $this->response(true,200,$message);
    }


    public function uploadExcel(Request $request){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property'),
            'url'=> route('system.property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Upload Excel'),
        ];

        $this->viewData['pageTitle'] = __('Upload Excel');
        $this->viewData['importer_data'] = null;

        $this->createEditData();

        $propertyTypes = Parameter::select('property_type_id')
            ->where('show_in_property','yes')
            ->whereNotIn('type', ['multi_select','checkbox'])
            ->groupBy('property_type_id')->with(['property_type'])
            ->get();

      //  dd($propertyTypes);

       $parameters = [];
        foreach ($propertyTypes as $key => $value) {
            $parameters[] = Parameter::where('property_type_id',$value->property_type_id)->whereNotIn('type', ['multi_select','checkbox'])->where('show_in_property','yes')->get();
        }

      //  dd($parameters);

        $this->viewData['propertyTypes'] = $propertyTypes;
        $this->viewData['parameters'] = $parameters;


        return $this->view('property.upload-excel',$this->viewData);
    }

    public function uploadExcelStore(PropertyUploadExcelFormRequest $request){

        // Start Handle XLS file

        $file = $request->file('excel_file')->store(setting('system_path').'/properties-excel/'.date('Y/m/d'));

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/'.$file))
            ->getActiveSheet()
            ->toArray(null,true,true,true);

        if(count($spreadsheet) < 2){
            return $this->response(
                false,
                11001,
                __('Empty XLS file')
            );
        }

        if($request->ignore_first_row == 'yes'){
            unset($spreadsheet[1]);
            $i = 2;
        }else{
            $i = 1;
        }


        $data   = [];
        $errors = [];

        foreach ($spreadsheet as $key => $value){
            CheckKeyInArray::setArray($value);
            $row = [];

            // Main Data
            $row['property_type'] = CheckKeyInArray::check(strtoupper($request->property_type));
            $row['payment_type'] = CheckKeyInArray::check(strtoupper($request->payment_type));
            $row['purpose_id'] = CheckKeyInArray::check(strtoupper($request->purpose_id));

            $row['client_name']            = CheckKeyInArray::check(strtoupper($request->client_name));
            $row['client_mobile']          = CheckKeyInArray::check(strtoupper($request->client_mobile));
            $row['client_company_name']    = CheckKeyInArray::check(strtoupper($request->client_company_name)); // Not required

            $row['model']          = CheckKeyInArray::check(strtoupper($request->model)); // Not required
            $row['name']           = CheckKeyInArray::check(strtoupper($request->name)); // Not required
            $row['description']    = CheckKeyInArray::check(strtoupper($request->description)); // Not required
            $row['remarks']        = CheckKeyInArray::check(strtoupper($request->remarks)); // Not required

            $row['years_of_installment']   = CheckKeyInArray::check(strtoupper($request->years_of_installment)); // Not required
            $row['deposit']                = CheckKeyInArray::check(strtoupper($request->deposit)); // Not required

            $row['price']      = CheckKeyInArray::check(strtoupper($request->price));
            $row['space']      = CheckKeyInArray::check(strtoupper($request->space));
            $row['address']    = CheckKeyInArray::check(strtoupper($request->address));

            $row['building_number']    = CheckKeyInArray::check(strtoupper($request->building_number));
            $row['flat_number']    = CheckKeyInArray::check(strtoupper($request->flat_number));

            // Parameters
            $property_type_check = propertyType::where('name_ar',$row['property_type'])->orWhere('name_en',$row['property_type'])->first();
            $rowParameters = [];
            if($property_type_check){
            $parametersData = Parameter::where(['property_type_id'=>$property_type_check->id,'show_in_property'=>'yes'])->get([
                    'column_name',
                    'name_ar',
                    'name_en',
                    'type',
                    'options',
                    'required'
                ]);

            if($parametersData->isNotEmpty()){
                foreach ($parametersData as $pKey => $pValue){
                    $parameterValue = $request->{'p_'.$pValue->column_name};
                    if($parameterValue){
                        $rowParameters[$pValue->column_name] = CheckKeyInArray::check(strtoupper($parameterValue));
                        switch ($pValue->type){
                            case 'number':
                                // check invalid parameter
                                //if($pValue->required == 'yes' && !$rowParameters[$pValue->column_name]){
                                  //  $errors[$i][$pValue->column_name] = __(':name required',['name'=> $pValue->{'name_'.\App::getLocale()}]);
                                //}
                                if(!is_numeric($rowParameters[$pValue->column_name])){
                                    $errors[$i][$pValue->column_name] = __(':name should be numeric',['name'=> $pValue->{'name_'.\App::getLocale()}]);
                                }else{
                                    $rowParameters[$pValue->column_name] = (int)str_replace(',','',$rowParameters[$pValue->column_name]);
                                }
                                break;
                            case 'select':
                            case 'radio':
                            if($rowParameters[$pValue->column_name]) {
                                $options_array_ar = array_column($pValue->options, 'name_ar');
                                $options_array_en = array_column($pValue->options, 'name_en');
                                if (!in_array($rowParameters[$pValue->column_name], $options_array_ar) && !in_array($rowParameters[$pValue->column_name], $options_array_en)) {
                                    $errors[$i][$pValue->column_name] = __('Invalid :name', ['name' => $pValue->{'name_' . \App::getLocale()}]);
                                } else {
                                    // handel select value
                                    foreach ($pValue->options as $oValue) {
                                        if ($rowParameters[$pValue->column_name] == $oValue['name_ar'] || $rowParameters[$pValue->column_name] == $oValue['name_en']) {
                                            $rowParameters[$pValue->column_name] = $oValue['value'];
                                        }
                                    }
                                }
                            }

                            break;

                            default:
                                $rowParameters[$pValue->column_name] = CheckKeyInArray::check(strtoupper($parameterValue));
                               // $rowParameters[$pValue->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                                break;

                        }

                    }
                }
            }
            }

            // check invalid parameter

            $purpose_check = Purpose::where('name_ar',$row['purpose_id'])->orWhere('name_en',$row['purpose_id'])->first();


            if(!$row['property_type'] || !$property_type_check){
                $errors[$i]['property_type'] = __('Invalid Property Type');
            }else{
                $row['property_type'] = $property_type_check->id;
            }

            if(!$row['purpose_id'] || !$purpose_check){
                $errors[$i]['purpose_id'] = __('Invalid Purpose');
            }else{
                $row['purpose_id'] = $purpose_check->id;
            }

            if(!$row['payment_type'] || !in_array($row['payment_type'],['cash','installment','cash or installment','Cash','Installment','Cash or Installment','كاش','تقسيط','كاش او تقسيط'])){
                $errors[$i]['payment_type'] = __('Invalid payment type');
            }

            if(!$row['client_name']){
                $errors[$i]['client_name'] = __('Invalid Client Name');
            }

            if(!$row['client_mobile']){
                $errors[$i]['client_mobile'] = __('Invalid Client Mobile');
            }

            if(!$row['price']){
                $errors[$i]['price'] = __('Invalid Price');
            }else{
                $row['price'] = (int)str_replace(',','',$row['price']);
            }

            if(!$row['space']){
                $errors[$i]['space'] = __('Invalid Space');
            }else{
                $row['space'] = (int)str_replace(',','',$row['space']);
            }

            if(!$row['address']){
                $errors[$i]['address'] = __('Invalid Address');
            }
            // check invalid parameter

            if(!isset($errors[$i])){
                $data[$i] = $row;
                $data[$i]['parameters'] = $rowParameters;
            }
            $i++;
        }

        if(!empty($errors)){
            return $this->response(
                false,
                11000,
                __('Excel Error'),
                $errors
            );
        }elseif (empty($data)){
            return $this->response(
                false,
                11001,
                __('There are no data to insert')
            );
        }

        foreach ($data as $key => $value){
            $value['client_mobile'] = preg_replace("/[^0-9]/", "", $value['client_mobile']);

            // Check Client
            $client = Client::where(function($query) use ($value) {
                $query->where('mobile1',$value['client_mobile'])
                    ->orWhere('mobile2',$value['client_mobile']);
            })->first();

            //handel client
            if(!$client){
                $client = Client::create([
                    'type'=> $request->client_type,
                    'investor_type'=> $request->client_investor_type ? $request->client_investor_type  : null,
                    'name'=> $value['client_name'],
                    'company_name'=> $request->client_company_name ? $request->client_company_name  : null,
                    'mobile1'=> $value['client_mobile'],
                    'created_by_staff_id'=> Auth::id()
                ]);
            }


            //handel payment type
            if(in_array($value['payment_type'],['Cash','cash','كاش'])){
                $payment_type_value = 'cash';
            }elseif(in_array($value['payment_type'],['installment','Installment','تقسيط','قسط'])){
                $payment_type_value = 'installment';
            }else{
                $payment_type_value = 'cash_installment';
            }

            //$purpose_check = Purpose::where('name_ar',$row['purpose_id'])->orWhere('name_en',$row['purpose_id'])->first();

            $propertyDataInsert = [
                'property_type_id'=> $value['property_type'],//$request->property_type_id,
                'purpose_id'=>  $value['purpose_id'],//$request->purpose_id,
                'data_source_id'=> $request->data_source_id,
                'client_id'=> $client->id,
                'area_id'=> $request->area_id,
                'building_number'=> $value['building_number'],
                'flat_number'=> $value['flat_number'],
                'property_status_id'=> $request->property_status_id,
                'name'=> $value['name'],
                'description'=> $value['description'],
                'remarks'=> $value['remarks'],
                'payment_type'=> $payment_type_value,//$request->payment_type,
                'years_of_installment'=> $value['years_of_installment'],
                'deposit'=> $value['deposit'],
                'price'=> $value['price'],
                'currency'=> $request->currency,
                'negotiable'=> $request->negotiable,
                'space'=> $value['space'],
                'space_type'=> 'meter',
                'address'=> $value['address'],
                'sales_id'=> $request->sales_id,
                'created_by_staff_id'=> Auth::id(),
                'call_update'=> date('Y-m-d H:i:s')
            ];
            $insertData = Property::create($propertyDataInsert);
            if($insertData){

                $parametersData = Parameter::where(['property_type_id'=>$value['property_type'],'show_in_property'=>'yes'])
                    ->get([
                        'column_name',
                        'type',
                        'options',
                        'required'
                    ]);

                $parametersDataInsert = [
                    'property_id'=> $insertData->id
                ];

                if($parametersData->isNotEmpty()){
                    foreach ($parametersData as $pKey => $pValue){
                        $parameterValue = @$value['parameters'][$pValue->column_name];
                        if($parameterValue){
                            $parametersDataInsert[$pValue->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                        }
                    }
                }

                PropertyParameter::create($parametersDataInsert);

                if(setting('send_notification_on_upload_property_xls') == 'yes'){
                    // --- Notification
                    $numRequests = $insertData->requests()->count();
                    if($numRequests){
                        $allStaffToNotify = array_column(
                            App\Models\Staff::get(['id'])->toArray(),
                            'id'
                        );
                        notifyStaff(
                            [
                                'type'  => 'staff',
                                'ids'   => $allStaffToNotify
                            ],
                            __('Property Notification'),
                            __('There are :number requests related to property',['number'=> $numRequests]),
                            route('system.property.show',$insertData->id)
                        );
                    }
                    // --- Notification

                }

                // ----log
                save_log(__('Upload property sheet'),'App\Models\Property',$insertData->id);
                // ----log

            }

        }


        $allStaffToNotify = array_column(
            App\Models\Staff::get(['id'])->toArray(),
            'id'
        );
        notifyStaff(
            [
                'type'  => 'staff',
                'ids'   => $allStaffToNotify
            ],
            __('Property Notification'),
            __('There are :num new Properties added',['num'=> count($data)]),
            route('system.property.index')
        );


        return $this->response(
            true,
            200,
            __(':num Properties added successfully',['num'=> count($data)]),
            [
                'url'=> route('system.property.index')
            ]
        );

    }


}