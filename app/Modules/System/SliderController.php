<?php

namespace App\Modules\System;

use App\Http\Requests\SliderFormRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class SliderController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Slider::select([
                'id',
                'image',
                'title_ar',
                'title_en',
                'type',
                'sort',
                'status',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('image', function($data){
                    return ( $data->image && is_file($data->image) ) ? '<a target="_blank" href="'.asset($data->image).'"><img style="width:70px;height: 70px;" src="'.asset($data->image).'"></a>' : '--';
                })
                ->addColumn('title_ar', function($data){
                    return $data->title_ar ? $data->title_ar : '--';
                })
                ->addColumn('title_en', function($data){
                    return $data->title_en ? $data->title_en : '--';
                })
                ->addColumn('type', function($data){
                    return  '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->type)).' slider').'</span>';
                })
                ->addColumn('sort', function($data){
                    return  '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.$data->sort.'</span>';
                })
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
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
                                <a class="dropdown-item" href="'.route('system.slider.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.slider.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.slider.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                              
                            </div>
                            
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Image'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Type'),
                __('Sort'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Sliders')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Sliders');
            }else{
                $this->viewData['pageTitle'] = __('Slider');
            }

            return $this->view('slider.index',$this->viewData);
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
            'text'=> __('Slider'),
            'url'=> route('system.slider.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Slider'),
        ];

        $this->viewData['pageTitle'] = __('Create Slider');

        return $this->view('slider.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SliderFormRequest $request){

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
           // addWaterMarker($image);
        }
        $sliderDataInsert = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'type'=>$request->type,
            'description_ar'=>$request->description_ar,
            'description_en'=>$request->description_en,
            'image'=> isset($image) ? $image : '',
            'video_url'=>$request->video_url,
            'url'=>$request->url,
            'sort'=>$request->sort ? $request->sort : 0,
            'status'=>$request->status,
        ];


        $insertData = Slider::create($sliderDataInsert);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.slider.show',$insertData->id)
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
    public function show(Slider $slider,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Slider'),
                'url' => route('system.slider.index'),
            ],
            [
                'text' => $slider->{'title_'.App::getLocale()},
            ]
        ];

        $this->viewData['pageTitle'] =  $slider->{'title_'.App::getLocale()};

        $this->viewData['result'] = $slider;

        return $this->view('slider.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Slider'),
            'url'=> route('system.slider.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $slider->{'title_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Slider');
        $this->viewData['result'] = $slider;

        return $this->view('slider.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(SliderFormRequest $request, Slider $slider)
    {

        $sliderDataUpdate = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'type'=>$request->type,
            'description_ar'=>$request->description_ar,
            'description_en'=>$request->description_en,
            'video_url'=>$request->video_url,
            'url'=>$request->url,
            'sort'=>$request->sort ? $request->sort : 0,
            'status'=>$request->status,
        ];

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
           // addWaterMarker($image);
            $sliderDataUpdate['image'] = $image;
            if(!empty($slider->image) && is_file($slider->image)){ // remove old image
                unlink($slider->image);
            }
        }

        $updateData = $slider->update($sliderDataUpdate);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.slider.show',$slider->id)
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
    public function destroy(Slider $slider)
    {
        $message = __('Slider deleted successfully');
        if(!empty($slider->image) && is_file($slider->image)){ // remove image
            unlink($slider->image);
        }

        $slider->delete();

        return $this->response(true,200,$message);
    }



}