<?php

namespace App\Modules\System;

use App\Http\Requests\CampaignFormRequest;
use App\Models\Image;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class CampaignController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Campaign::select([
                'id',
                'title',
                'sent',
                'status',
                'created_at',
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('title','{{$title}}')
                ->addColumn('sent','{{$sent}}')
                ->addColumn('status', function($data){
                    if($data->status == 'new'){
                        return  '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__(ucfirst($data->status)).'</span>';
                    }
                    if ($data->status == 'progress'){
                        return  '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst($data->status)).'</span>';
                    }
                    return  '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst($data->status)).'</span>';
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
                                <a class="dropdown-item" href="'.route('system.campaign.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.campaign.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>  
                              
                            </div>                           
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Title'),
                __('Sent'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Campaigns')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Campaigns');
            }else{
                $this->viewData['pageTitle'] = __('Campaigns');
            }

            return $this->view('campaign.index',$this->viewData);
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
            'text'=> __('Campaigns'),
            'url'=> route('system.campaign.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Campaign'),
        ];

        $this->viewData['pageTitle'] = __('Create Campaign');

        return $this->view('campaign.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CampaignFormRequest $request){

        $campaignDataInsert = [
            'title'=>$request->campaign_title,
            'content'=>$request->campaign_content ? uploadImagesByTextEditor($request->campaign_content) : '',
            'status'=>'new',
            'sent'=>0,
        ];

        $insertData = Campaign::create($campaignDataInsert);

        if($insertData){

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.campaign.show',$insertData->id)
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
    public function show(Campaign $campaign,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Campaigns'),
                'url' => route('system.campaign.index'),
            ],
            [
                'text' => $campaign->title,
            ]
        ];

        $this->viewData['pageTitle'] = $campaign->title;

        $this->viewData['result'] = $campaign;

        return $this->view('campaign.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign,Request $request)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(CampaignFormRequest $request, Campaign $campaign)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        $message = __('Campaign deleted successfully');

        $campaign->delete();

        return $this->response(true,200,$message);
    }



}