<?php

namespace App\Modules\System;

use App\Http\Requests\ServiceFormRequest;
use App\Models\Image;
use App\Models\Service;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class ServiceController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Service::select([
                'id',
                'title_ar',
                'title_en',
                'status',
                'created_at'
            ])->where('parent_id',0);

            whereBetween($eloquentData,'DATE(services.created_at)',$request->created_at1,$request->created_at2);
            whereBetween($eloquentData,'services.price',$request->price1,$request->price2);
            whereBetween($eloquentData,'services.offer',$request->offer1,$request->offer2);
            whereBetween($eloquentData,'services.discount_value',$request->discount_value1,$request->discount_value2);
            whereBetween($eloquentData,'services.discount_code_value',$request->discount_code_value1,$request->discount_code_value2);
            whereBetween($eloquentData,'DATE(services.discount_from)',$request->discount_from1,$request->discount_from2);
            whereBetween($eloquentData,'DATE(services.discount_to)',$request->discount_to1,$request->discount_to2);
            whereBetween($eloquentData,'DATE(services.subscribe_from)',$request->subscribe_from1,$request->subscribe_from2);
            whereBetween($eloquentData,'DATE(services.subscribe_to)',$request->subscribe_to1,$request->subscribe_to2);

            if($request->id){
                $eloquentData->where('invoices.id',$request->id);
            }

            if($request->property_id){
                $eloquentData->where('invoices.property_id',$request->property_id);
            }

            if($request->client_id){
                $eloquentData->where('invoices.client_id',$request->client_id);
            }

            if($request->property_due_id){
                $eloquentData->where('invoices.property_due_id',$request->property_due_id);
            }

            if($request->installment_id){
                $eloquentData->where('invoices.installment_id',$request->installment_id);
            }

            if($request->notes){
                $eloquentData->where('invoices.notes','LIKE','%'.$request->notes.'%');
            }

            if($request->status){
                $eloquentData->where('invoices.status',$request->status);
            }

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('title_ar','{{$title_ar}}')
                ->addColumn('title_en','{{$title_en}}')
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('In-Active').'</span>';
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
                                <a class="dropdown-item" href="'.route('system.service.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.service.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.service.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                              
                            </div>
                            
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Services')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Services');
            }else{
                $this->viewData['pageTitle'] = __('Services');
            }

            return $this->view('service.index',$this->viewData);
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
            'text'=> __('Services'),
            'url'=> route('system.service.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Service'),
        ];

        $this->viewData['pageTitle'] = __('Create Service');
        $this->viewData['randKey'] = md5(rand().time());

        return $this->view('service.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceFormRequest $request){

        $serviceDataInsert = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'content_ar'=>$request->content_ar ? uploadImagesByTextEditor($request->content_ar) : '',
            'content_en'=>$request->content_en ? uploadImagesByTextEditor($request->content_en) : '',
            'price'=>$request->price,
            'offer'=>$request->offer,
            'duration'=>$request->duration,
            'status'=>$request->status,
            'meta_key_ar'=> $request->meta_key_ar,
            'meta_key_en'=> $request->meta_key_en,
            'meta_description_ar'=> $request->meta_description_ar,
            'meta_description_en'=> $request->meta_description_en,
            'discount_type'=> $request->discount_type,
            'discount_value'=> $request->discount_value,
            'discount_from'=> $request->discount_from,
            'discount_to'=> $request->discount_to,
            'type'=> $request->type,
            'type_count'=> $request->type_count,
            'properties_count'=> $request->properties_count,
            'discount_code'=> $request->discount_code,
            'discount_code_value'=> $request->discount_code_value,
            'discount_code_from'=> $request->discount_code_from,
            'discount_code_to'=> $request->discount_code_to,
            'percentage'=> $request->percentage,
            //'subscribers_count'=> $request->subscribers_count,
            //'unsubscribers_count'=> $request->unsubscribers_count,
            //'subscribe_monthly'=> $request->subscribe_monthly,
            //'subscribe_from'=> $request->subscribe_from,
            //'subscribe_to'=> $request->subscribe_to,
        ];


        $insertData = Service::create($serviceDataInsert);
        if($insertData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $insertData->id,
                'sign_type'=> 'App\Models\Service'
            ]);

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.service.show',$insertData->id)
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
    public function show(Service $service,Request $request){

        if($request->isDataTable == 'true'){
            $eloquentData = $service->packages()->select([
                'id',
                'title_ar',
                'title_en',
                'price',
                'status',
                'created_at'
            ])->orderByDesc('id');

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('title_ar','{{$title_ar}}')
                ->addColumn('title_en','{{$title_en}}')
                ->addColumn('price', function($data){
                    return $data->price ? amount($data->price,true) : '0.000';
                })
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('In-Active').'</span>';
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
                                <a class="dropdown-item" href="'.route('system.package.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.package.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.package.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                              
                            </div>
                            
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);


        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Services'),
                    'url' => route('system.service.index'),
                ],
                [
                    'text' => $service->{'title_'.App::getLocale()},
                ]
            ];

            $this->viewData['pageTitle'] =  $service->{'title_'.App::getLocale()};

            $this->viewData['result'] = $service;

            return $this->view('service.show', $this->viewData);

        }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Services'),
            'url'=> route('system.service.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $service->{'title_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Service');
        $this->viewData['result'] = $service;
        $this->viewData['randKey'] = md5(rand().time());

        return $this->view('service.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceFormRequest $request, Service $service)
    {

        $serviceDataUpdate = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'content_ar'=>$request->content_ar ? uploadImagesByTextEditor($request->content_ar) : '',
            'content_en'=>$request->content_en ? uploadImagesByTextEditor($request->content_en): '',
            'price'=>$request->price,
            'offer'=>$request->offer,
            'duration'=>$request->duration,
            'status'=>$request->status,
            'meta_key_ar'=> $request->meta_key_ar,
            'meta_key_en'=> $request->meta_key_en,
            'meta_description_ar'=> $request->meta_description_ar,
            'meta_description_en'=> $request->meta_description_en,
            'discount_type'=> $request->discount_type,
            'discount_value'=> $request->discount_value,
            'discount_from'=> $request->discount_from,
            'discount_to'=> $request->discount_to,
            'type'=> $request->type,
            'type_count'=> $request->type_count,
            'properties_count'=> $request->properties_count,
            'discount_code'=> $request->discount_code,
            'discount_code_value'=> $request->discount_code_value,
            'discount_code_from'=> $request->discount_code_from,
            'discount_code_to'=> $request->discount_code_to,
            'percentage'=> $request->percentage,
            //'subscribers_count'=> $request->subscribers_count,
            //'unsubscribers_count'=> $request->unsubscribers_count,
            //'subscribe_monthly'=> $request->subscribe_monthly,
            //'subscribe_from'=> $request->subscribe_from,
            //'subscribe_to'=> $request->subscribe_to,
        ];


        $updateData = $service->update($serviceDataUpdate);

        if($updateData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $service->id,
                'sign_type'=> 'App\Models\Service'
            ]);

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.service.show',$service->id)
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
            //addWaterMarker($path);
            $image = Image::create([
                'custom_key'=> $request->key,
                'path'=> $path,
                'sign_id'=> !empty($request->service_id) ? $request->service_id : NULL ,
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
    public function destroy(Service $service)
    {
        $message = __('Service deleted successfully');
        //delete service images
        $images = $service->images;
        if(!empty($images)){
            foreach ($images as $img){
                if(is_file($img->path)){
                    unlink($img->path);
                }
            }
            $service->images()->delete();
        }

        //delete service packages with its images
        $packages = Service::with(['images'])->where('parent_id',$service->id)->get();
        if(!empty($packages)){
            foreach ($packages as $pack){
               $images = $pack->images;
               if(!empty($images)){
                   foreach ($images as $img){
                       Image::where('id',$img->id)->delete();
                       if(is_file($img->path)){
                           unlink($img->path);
                       }
                   }
               }
            }

            $service->packages()->delete();
        }

        $service->delete();

        return $this->response(true,200,$message);
    }



}