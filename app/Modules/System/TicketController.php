<?php

namespace App\Modules\System;

use App\Http\Requests\TicketFormRequest;
use App\Models\TicketComment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class TicketController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Ticket::select([
                'id',
                'title',
                'client_id',
                'status',
                'created_at'
            ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('title','{{$title}}')
                ->addColumn('client_id', function($data){
                   return $data->client ?  '<a href="'.route('system.'.$data->client->type.'.show',$data->client_id).'" target="_blank">'.$data->client->fullname.'<br>('.__(ucfirst($data->client->type)).')</a>'.'<br /><a href="tel:'.$data->client->mobile.'">'.$data->client->mobile.'</a>' : '--' ;
                })
                ->addColumn('status', function($data){
                    if($data->status == 'new'){
                        return  '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->status))).'</span>';
                    } elseif ($data->status == 'pending_support'){
                        return  '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->status))).'</span>';
                    } elseif ($data->status == 'pending_client'){
                        return  '<span class="k-badge  k-badge--warning k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->status))).'</span>';
                    } elseif ($data->status == 'solve'){
                        return  '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->status))).'</span>';
                    } else{
                        return  '<span class="k-badge  k-badge--dark k-badge--inline k-badge--pill">'.__(ucfirst(str_replace('_',' ',$data->status))).'</span>';
                    }
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
                                <a class="dropdown-item" href="'.route('system.ticket.show',$data->id).'" target="_blank"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.ticket.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a>                               
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
                __('Client'),
                __('Status'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('TS Tickets')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Tickets');
            }else{
                $this->viewData['pageTitle'] = __('TS Tickets');
            }

            return $this->view('ticket.index',$this->viewData);
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
            'text'=> __('TS Tickets'),
            'url'=> route('system.ticket.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create TS Ticket'),
        ];

        $this->viewData['pageTitle'] = __('Create TS Ticket');

        return $this->view('ticket.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TicketFormRequest $request){


        $ticketDataInsert = [
            'title'=>$request->ticket_title,
            'client_id'=>$request->client_id,
            'status'=>'new',
        ];

        $insertData = Ticket::create($ticketDataInsert);
        if($insertData) {
            // Ticket Comments
            $ticketCommentDataInsert = [
                'comment'=>$request->ticket_content ? uploadImagesByTextEditor($request->ticket_content): '',
                'ticket_id'=>$insertData->id,
                'client_id'=>$request->client_id,
               // 'staff_id'=>Auth::id(),
            ];
            if($request->hasFile('ticket_image')) {
                $image = $request->file('ticket_image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
                addWaterMarker($image);
                if($image)
                    $ticketCommentDataInsert['image'] = $image;
            }
            $insertCommentData = TicketComment::create($ticketCommentDataInsert);
        }

        if($insertData && $insertCommentData) {
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.ticket.show',$insertData->id)
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
    public function show(Ticket $ticket,Request $request){

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Tickets'),
                'url' => route('system.ticket.index'),
            ],
            [
                'text' => $ticket->title,
            ]
        ];

        $this->viewData['pageTitle'] =  $ticket->title;

        $this->viewData['result'] = $ticket;

        return $this->view('ticket.show', $this->viewData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket,Request $request){
        abort(404);
    }

    /**
     * Update Ticket Status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateTicketStatus($id,Request $request)
    {
        $ticket = Ticket::find($id);
        if(!$ticket){
            abort(404);
        }

        $updateTicketData = $ticket->update([
            'status'=>$request->status,
        ]);

        if($updateTicketData){

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.ticket.show',$ticket->id)
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(TicketFormRequest $request, Ticket $ticket)
    {

        $updateTicketData = $ticket->update([
            'status'=>'pending_client'
        ]);

        if($updateTicketData) {
            // Ticket Comment
            $ticketCommentDataInsert = [
                'comment' => $request->comment ? uploadImagesByTextEditor($request->comment) : '',
                'ticket_id' => $ticket->id,
                'client_id' => $ticket->client_id,
                'staff_id' => Auth::id(),
            ];

            if($request->hasFile('ticket_image')) {
                $image = $request->file('ticket_image')->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
                addWaterMarker($image);
                if($image)
                    $ticketCommentDataInsert['image'] = $image;
            }

            $insertCommentData = TicketComment::create($ticketCommentDataInsert);
        }

        if($updateTicketData && !empty($insertCommentData)){

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.ticket.show',$ticket->id)
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
    public function destroy(Ticket $ticket)
    {
        $message = __('Ticket deleted successfully');
        if($ticket->contact_us){
            $ticket->contact_us()->delete();
        }
        $ticket->comments()->delete();
        $ticket->delete();

        return $this->response(true,200,$message);
    }



}