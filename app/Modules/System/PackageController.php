<?php

namespace App\Modules\System;

use App\Http\Requests\PackageFormRequest;
use App\Models\Image;
use App\Models\Service;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class PackageController extends SystemController
{

    private function createEditData(){
        $this->viewData['services'] = App\Models\Service::where('parent_id',0)->get([
            'id',
            'title_'.App::getLocale().' as name'
        ]);
    }

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
                'price',
                'status',
                'created_at'
            ])->where('parent_id','!=',0);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

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
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Package Price'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Packages')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Packages');
            }else{
                $this->viewData['pageTitle'] = __('Packages');
            }

           // $this->createEditData();

            return $this->view('package.index',$this->viewData);
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
            'text'=> __('Packages'),
            'url'=> route('system.package.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Package'),
        ];

        $this->viewData['pageTitle'] = __('Create Package');
        $this->viewData['randKey'] = md5(rand().time());

        $this->createEditData();

        return $this->view('package.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PackageFormRequest $request){

        $service = Service::find($request->service_id);

        $packageDataInsert = [
            'parent_id'=>$request->service_id,
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
            'type'=> $service ? $service->type : NULL,
            'type_count'=> $request->type_count,
            'properties_count'=> $request->properties_count,
            'discount_code'=> $request->discount_code,
            'discount_code_value'=> $request->discount_code_value,
            'discount_code_from'=> $request->discount_code_from,
            'discount_code_to'=> $request->discount_code_to,
            'percentage'=> $request->percentage,
//            'subscribers_count'=> $request->subscribers_count,
//            'unsubscribers_count'=> $request->unsubscribers_count,
//            'subscribe_monthly'=> $request->subscribe_monthly,
//            'subscribe_from'=> $request->subscribe_from,
//            'subscribe_to'=> $request->subscribe_to,
        ];


        $insertData = Service::create($packageDataInsert);
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
                    'url'=> route('system.package.show',$insertData->id)
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
    public function show(Service $package,Request $request){

        if(!$package->parent_id)
        {
            abort(404);
        }

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Packages'),
                'url' => route('system.package.index'),
            ],
            [
                'text' => $package->{'title_'.App::getLocale()},
            ]
        ];

        $this->viewData['pageTitle'] =  $package->{'title_'.App::getLocale()};

        $this->viewData['result'] = $package;

        return $this->view('package.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $package,Request $request){

        // Main View Vars
        if(!$package->parent_id)
        {
            abort(404);
        }

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Packages'),
            'url'=> route('system.package.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $package->{'title_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Package Date');
        $this->viewData['result'] = $package;
        $this->viewData['randKey'] = md5(rand().time());

        $this->createEditData();

        return $this->view('package.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PackageFormRequest $request, Service $package)
    {
        $service = Service::find($request->service_id);

        $packageDataUpdate = [
            'parent_id'=>$request->service_id,
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
            'type'=> $service ? $service->type : NULL,
            'type_count'=> $request->type_count,
            'properties_count'=> $request->properties_count,
            'discount_code'=> $request->discount_code,
            'discount_code_value'=> $request->discount_code_value,
            'discount_code_from'=> $request->discount_code_from,
            'discount_code_to'=> $request->discount_code_to,
            'percentage'=> $request->percentage,
//            'subscribers_count'=> $request->subscribers_count,
//            'unsubscribers_count'=> $request->unsubscribers_count,
//            'subscribe_monthly'=> $request->subscribe_monthly,
//            'subscribe_from'=> $request->subscribe_from,
//            'subscribe_to'=> $request->subscribe_to,
        ];


        $updateData = $package->update($packageDataUpdate);

        if($updateData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $package->id,
                'sign_type'=> 'App\Models\Service'
            ]);

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.package.show',$package->id)
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
    public function destroy(Service $package)
    {
        $message = __('Package deleted successfully');
        $images = $package->images;
        if(!empty($images)){
            foreach ($images as $img){
                if(is_file($img->path)){
                    unlink($img->path);
                }
            }
            $package->images()->delete();
        }

        $package->delete();

        return $this->response(true,200,$message);
    }


}