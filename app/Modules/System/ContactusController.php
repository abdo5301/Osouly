<?php

namespace App\Modules\System;

use App\Http\Requests\ContactFormRequest;
use App\Models\Contactus;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class ContactusController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData =  Contactus::select([
                'id',
                'name',
                'mobile',
                'email',
                'subject',
                'is_read',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('mobile','{{$mobile}}')
                ->addColumn('email','{{$email}}')
                ->addColumn('subject','{{$subject}}')
                ->addColumn('is_read', function($data){
                    if($data->is_read > 0){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Is Read').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('Pending').'</span>';
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
                                <a class="dropdown-item" href="'.route('system.contact.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>                           
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.contact.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>                             
                            </div>
                            
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Mobile'),
                __('Email'),
                __('Subject'),
                __('Is Read'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Contact US')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Messages');
            }else{
                $this->viewData['pageTitle'] = __('Contact US');
            }

            return $this->view('contact-us.index',$this->viewData);
        }
    }


    /**
     * Convert contact us from clients to technical support ticket.
     *
     * @return \Illuminate\Http\Response
     */
    public function toTicket($id,Request $request){

        $contactus = Contactus::find($id);
        if(!$contactus  || empty($contactus->client_id)){
            abort(404);
        }

        $client_check = App\Models\Client::find($contactus->client_id);

        if(!$client_check){
            return $this->response(
                false,
                11001,
                __('Sorry, client not found')
            );
        }

         if(!empty($contactus->replay)){
             $ticket_status = 'pending_client';
         }else{
             $ticket_status = 'new';
         }

         $insertTicket = App\Models\Ticket::create(['title'=>$contactus->subject,'status'=>$ticket_status,'client_id'=>$contactus->client_id]);

         if($insertTicket){
             $contactus->update(['ticket_id'=>$insertTicket->id]);

             $insertTicketComment_client = App\Models\TicketComment::create(['ticket_id'=>$insertTicket->id,'comment'=>$contactus->message,'client_id'=>$contactus->client_id]);

             if($ticket_status == 'pending_client'){
                 $insertTicketComment_staff = App\Models\TicketComment::create(['ticket_id'=>$insertTicket->id,'comment'=>$contactus->replay,'staff_id'=> Auth::id()]);
             }
         }

         if($insertTicket && $insertTicketComment_client){
             return $this->response(
                 true,
                 200,
                 __('Ticket created successfully')
             );
         }else {
             return $this->response(
                 false,
                 11001,
                 __('Sorry, we could not create the ticket')
             );
         }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        abort(404);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(){

        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request){
        $contactus = Contactus::find($id);
        if(!$contactus){
            abort(404);
        }

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Contact Us'),
                'url' => route('system.contact.index'),
            ],
            [
                'text' => $contactus->subject,
            ]
        ];

        $this->viewData['pageTitle'] =  $contactus->subject;

        $this->viewData['result'] = $contactus;

        $contactus->update(['is_read'=> 1]);

        return $this->view('contact-us.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Contactus $contactus,Request $request){
       abort('404');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ContactFormRequest $request, $id)
    {
        $contactus = Contactus::find($id);
        if(!$contactus){
            abort(404);
        }

        $contactusDataUpdate = [
            'replay'=>$request->replay,
            'is_read'=> 1,
        ];

        $updateData = $contactus->update($contactusDataUpdate);


        if($updateData){
            //send replay mail here:
            send_email($contactus->email,$contactus->subject,$request->replay);

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.contact.show',$contactus->id)
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
    public function destroy(Contactus $contactus)
    {
        $message = __('Contact deleted successfully');

        $contactus->delete();

        return $this->response(true,200,$message);
    }



}