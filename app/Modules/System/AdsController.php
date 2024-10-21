<?php

namespace App\Modules\System;

use App\Http\Requests\AdsFormRequest;
use App\Models\Ads;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class AdsController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Ads::select([
                'id',
                'image',
                'title_ar',
                'title_en',
                'type',
                'page',
                \DB::raw('CONCAT(" '.__('From').' ",date_from," '.__('To').' ",date_to) as duration'),
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
                    if($data->type == 'osouly'){
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Osouly').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__(ucfirst($data->type)).'</span>';
                })
                ->addColumn('page', function($data){
                        return '<span  class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->page))).'</span>';
                })
                ->addColumn('duration', function($data){
                   // if($data->date_to && strtotime($data->date_to) < strtotime(date('Y-m-d')))
                   // return '<span  class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('Expired').'</span>';
                    return $data->duration ? $data->duration : '---';
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
                                <a class="dropdown-item" href="'.route('system.ads.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.ads.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.ads.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> 
                            
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
                __('Page'),
                __('Duration'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Ads')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Ads');
            }else{
                $this->viewData['pageTitle'] = __('Ads');
            }

            return $this->view('ads.index',$this->viewData);
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
            'text'=> __('Ads'),
            'url'=> route('system.ads.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Ads'),
        ];

        $this->viewData['pageTitle'] = __('Create Ads');

        return $this->view('ads.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdsFormRequest $request){

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
           // addWaterMarker($image);
        }
        $adsDataInsert = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'date_from'=>$request->date_from,
            'date_to'=>$request->date_to,
            'image'=> isset($image) ? $image : '',
            'url'=>$request->url,
            'type'=>$request->type,
            'page'=>$request->page,
        ];


        $insertData = Ads::create($adsDataInsert);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.ads.show',$insertData->id)
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
    public function show($id,Request $request){
        $ads = Ads::find($id);
        if(!$ads){
            abort('404');
        }
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Ads'),
                'url' => route('system.ads.index'),
            ],
            [
                'text' => $ads->{'title_'.App::getLocale()},
            ]
        ];

        $this->viewData['pageTitle'] =  $ads->{'title_'.App::getLocale()};

        $this->viewData['result'] = $ads;

        return $this->view('ads.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request){
        $ads = Ads::find($id);
        if(!$ads){
            abort('404');
        }
        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Ads'),
            'url'=> route('system.ads.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $ads->{'title_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Ads');
        $this->viewData['result'] = $ads;

        return $this->view('ads.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(AdsFormRequest $request, $id)
    {
        $ads = Ads::find($id);
        if(!$ads){
            abort('404');
        }
        $adsDataUpdate = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'date_from'=>$request->date_from,
            'date_to'=>$request->date_to,
            'url'=>$request->url,
            'type'=>$request->type,
            'page'=>$request->page,
        ];

        if($request->hasFile('image')) {
            $image = $request->file('image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
           // addWaterMarker($image);
            $adsDataUpdate['image'] = $image;
            if(!empty($ads->image) && is_file($ads->image)){ // remove old image
                unlink($ads->image);
            }
        }

        $updateData = $ads->update($adsDataUpdate);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.ads.show',$ads->id)
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
    public function destroy($id)
    {
        $ads = Ads::find($id);
        if(!$ads){
            abort('404');
        }
        $message = __('Ads deleted successfully');
        if(!empty($ads->image) && is_file($ads->image)){ // remove image
            unlink($ads->image);
        }

        $ads->delete();

        return $this->response(true,200,$message);
    }



}