<?php

namespace App\Modules\System;

use App\Http\Requests\NewsletterFormRequest;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class NewsletterController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Newsletter::select([
                'id',
                'email',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('email','{{$email}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.newsletter.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.newsletter.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>                             
                            </div>
                            
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Email'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Newsletter')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Newsletters');
            }else{
                $this->viewData['pageTitle'] = __('Newsletter');
            }

            return $this->view('newsletter.index',$this->viewData);
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
            'text'=> __('Newsletter'),
            'url'=> route('system.newsletter.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Newsletter'),
        ];

        $this->viewData['pageTitle'] = __('Create Newsletter');

        return $this->view('newsletter.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsletterFormRequest $request){

        $insertData = Newsletter::create(['email'=>$request->email]);

        if($insertData){

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.newsletter.index',$insertData->id)
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
    public function show(Newsletter $newsletter,Request $request){
      abort(404);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Newsletter $newsletter,Request $request){

        // Main View Vars

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Newsletter'),
            'url'=> route('system.newsletter.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $newsletter->{'title_'.App::getLocale()}]),
        ];

        $this->viewData['pageTitle'] = __('Edit Newsletter');
        $this->viewData['result'] = $newsletter;

        return $this->view('newsletter.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(NewsletterFormRequest $request, Newsletter $newsletter)
    {

        $updateData = $newsletter->update(['email'=>$request->email]);

        if($updateData){

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.newsletter.index',$newsletter->id)
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
    public function destroy(Newsletter $newsletter)
    {
        $message = __('Newsletter deleted successfully');

        $newsletter->delete();

        return $this->response(true,200,$message);
    }



}