<?php

namespace App\Modules\System;

use App\Http\Requests\PageFormRequest;
use App\Models\Image;
use App\Models\Page;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class PageController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Page::select([
                'id',
                'title_ar',
                'title_en',
                'sort',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('title_ar','{{$title_ar}}')
                ->addColumn('title_en','{{$title_en}}')
                ->addColumn('sort','{{$sort}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.page.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.page.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.page.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  
                              
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
                __('Sort'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Pages')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Pages');
            }else{
                $this->viewData['pageTitle'] = __('Pages');
            }

            return $this->view('page.index',$this->viewData);
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
            'text'=> __('Pages'),
            'url'=> route('system.page.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create page'),
        ];

        $this->viewData['pageTitle'] = __('Create Page');
        $this->viewData['randKey'] = md5(rand().time());

        return $this->view('page.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageFormRequest $request){

        ///print_r($request->all());die;

        $pageDataInsert = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'content_ar'=>$request->content_ar,
            'content_en'=>$request->content_en,
            'video_url'=> $request->video_url,
            'meta_key_ar'=> $request->meta_key_ar,
            'meta_key_en'=> $request->meta_key_en,
            'meta_description_ar'=> $request->meta_description_ar,
            'meta_description_en'=> $request->meta_description_en,
            'sort'=> $request->sort,
        ];

            $p_array = array();
            $p_title_ar = $request->p_title_ar;
            $p_content_ar = $request->p_content_ar;

            $p_title_en = $request->p_title_en;
            $p_content_en = $request->p_content_en;

        if(!empty($p_title_ar)){
            foreach ($p_title_ar as $key => $value){
                  $p_array[] = array(
                      'title_ar'   => $p_title_ar[$key],
                      'title_en'   => $p_title_en[$key],
                      'content_ar' => $p_content_ar[$key],
                      'content_en' => $p_content_en[$key],
                  );
            }

            $pageDataInsert['added_paragraphs'] = json_encode($p_array);
         }
           // echo json_encode($p_array);die;



        $insertData = Page::create($pageDataInsert);
        if($insertData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $insertData->id,
                'sign_type'=> 'App\Models\Page'
            ]);

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.page.show',$insertData->id)
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
    public function show(Page $page,Request $request){

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Pages'),
                    'url' => route('system.page.index'),
                ],
                [
                    'text' => $page->{'title_'.App::getLocale()},
                ]
            ];

            $this->viewData['pageTitle'] =  $page->{'title_'.App::getLocale()};

            $this->viewData['result'] = $page;

            $this->viewData['p_data'] = $page->added_paragraphs ? json_decode($page->added_paragraphs) : '';


        return $this->view('page.show', $this->viewData);
     }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Pages'),
            'url'=> route('system.page.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $page->{'title_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Page');
        $this->viewData['result'] = $page;
        $this->viewData['p_data'] = $page->added_paragraphs ? json_decode($page->added_paragraphs) : '';
        $this->viewData['randKey'] = md5(rand().time());

        return $this->view('page.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PageFormRequest $request, Page $page)
    {

       // $description_ar =  uploadImagesByTextEditor($request->content_ar);
       // $description_en =  uploadImagesByTextEditor($request->content_en);

        $pageDataUpdate = [
            'title_ar'=>$request->title_ar,
            'title_en'=>$request->title_en,
            'content_ar'=>$request->content_ar ? uploadImagesByTextEditor($request->content_ar) : '',
            'content_en'=>$request->content_en ? uploadImagesByTextEditor($request->content_en) : '',
            'video_url'=> $request->video_url,
            'meta_key_ar'=> $request->meta_key_ar,
            'meta_key_en'=> $request->meta_key_en,
            'meta_description_ar'=> $request->meta_description_ar,
            'meta_description_en'=> $request->meta_description_en,
            'sort'=> $request->sort,
        ];

        $p_array = array();
        $p_title_ar = $request->p_title_ar;
        $p_content_ar = $request->p_content_ar;

        $p_title_en = $request->p_title_en;
        $p_content_en = $request->p_content_en;

        if(!empty($p_title_ar)){
            foreach ($p_title_ar as $key => $value){
                $p_array[] = array(
                    'title_ar'   => $p_title_ar[$key],
                    'title_en'   => $p_title_en[$key],
                    'content_ar' => $p_content_ar[$key],
                    'content_en' => $p_content_en[$key],
                );
            }

            $pageDataUpdate['added_paragraphs'] = json_encode($p_array);
        }


        $updateData = $page->update($pageDataUpdate);

        if($updateData){

            // Images
            Image::where('custom_key',$request->key)->update([
                'sign_id'=> $page->id,
                'sign_type'=> 'App\Models\Page'
            ]);

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.page.show',$page->id)
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
                'sign_id'=> !empty($request->page_id) ? $request->page_id : NULL ,
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
    public function destroy(Page $page)
    {
        $message = __('Page deleted successfully');

        $images = $page->images;
        if(!empty($images)){
            foreach ($images as $img){
                if(is_file($img->path)){
                    unlink($img->path);
                }
            }
            $page->images()->delete();
        }

        $page->delete();

        return $this->response(true,200,$message);
    }



}